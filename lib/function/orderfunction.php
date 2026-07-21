<?php
include_once('main.php');
include_once('auto_id.php');

class Order extends Main
{

    private function generateMovementId()
    {
        $result = $this->dbResult->query(
            "SELECT movementid FROM stock_movements_tbl ORDER BY movementid DESC LIMIT 1"
        );

        if ($result && $result->num_rows > 0) {
            $prevId = $result->fetch_assoc()['movementid'];
            $num = intval(substr($prevId, 3)) + 1;
            return "MOV" . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        return "MOV001";
    }

    public function placeOrder($customer, $cart, $deliveryFee = 350.00)
    {
        if (empty($cart)) {
            return ['status' => 'error', 'orderid' => null, 'message' => 'Cart is empty'];
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['qty']);
        }
        $total = $subtotal + $deliveryFee;

        $autonumber = new AutoNumber;
        $orderid = $autonumber->NumberGenaration("orderid", "orders_tbl", "ORD");

        // COD orders are considered "verified" immediately.
        // Bank transfer orders stay unverified until admin approves the slip.
        $paymentVerified = ($customer['payment_method'] === 'bank_transfer') ? 0 : 1;

        $this->dbResult->begin_transaction();

        try {
            // ---- STEP 1: Check stock for every cart item first (lock the rows) ----
            // FOR UPDATE locks these product rows until commit/rollback, so two
            // customers checking out the same low-stock item at once can't both pass.
            $sqlStockCheck = $this->dbResult->prepare(
                "SELECT productName, quantity FROM product_tbl WHERE productid = ? FOR UPDATE"
            );

            foreach ($cart as $item) {
                $productId    = $item['id'];
                $requestedQty = (int) $item['qty'];

                $sqlStockCheck->bind_param("s", $productId);
                $sqlStockCheck->execute();
                $row = $sqlStockCheck->get_result()->fetch_assoc();

                if (!$row) {
                    throw new Exception("Product {$productId} no longer exists.");
                }

                if ((int) $row['quantity'] < $requestedQty) {
                    // Roll back and tell the customer exactly which item is short.
                    $this->dbResult->rollback();
                    return [
                        'status'  => 'insufficient_stock',
                        'orderid' => null,
                        'message' => "Only {$row['quantity']} unit(s) of \"{$row['productName']}\" left in stock."
                    ];
                }
            }

            // ---- STEP 2: Insert the order ----
            $sqlOrder = $this->dbResult->prepare(
                "INSERT INTO orders_tbl
                    (orderid, customer_id, customer_name, phone, email, address, city, postal_code, notes, payment_method, payment_verified, subtotal, delivery_fee, total, order_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
            );

            $sqlOrder->bind_param(
                "ssssssssssiddd",
                $orderid,
                $customer['cusid'],
                $customer['fullname'],
                $customer['phone'],
                $customer['email'],
                $customer['address'],
                $customer['city'],
                $customer['postal_code'],
                $customer['notes'],
                $customer['payment_method'],
                $paymentVerified,
                $subtotal,
                $deliveryFee,
                $total
            );

            if (!$sqlOrder->execute()) {
                throw new Exception($sqlOrder->error);
            }

            // ---- STEP 3: Insert order items + deduct stock + log the movement ----
            $sqlItem = $this->dbResult->prepare(
                "INSERT INTO order_items_tbl (orderid, productid, product_name, price, qty, line_total)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $sqlDeductStock = $this->dbResult->prepare(
                "UPDATE product_tbl SET quantity = quantity - ? WHERE productid = ?"
            );

            $sqlCurrent = $this->dbResult->prepare(
                "SELECT quantity FROM product_tbl WHERE productid = ?"
            );

            $sqlMovement = $this->dbResult->prepare(
                "INSERT INTO stock_movements_tbl
                    (movementid, productid, movement_type, quantity_change, previous_quantity, new_quantity, reason, created_by, created_at)
                 VALUES (?, ?, 'OUT', ?, ?, ?, ?, ?, NOW())"
            );

            foreach ($cart as $item) {
                $productId = $item['id'];
                $name      = $item['name'];
                $price     = (float) $item['price'];
                $qty       = (int) $item['qty'];
                $lineTotal = $price * $qty;

                $sqlItem->bind_param(
                    "sssdid",
                    $orderid,
                    $productId,
                    $name,
                    $price,
                    $qty,
                    $lineTotal
                );

                if (!$sqlItem->execute()) {
                    throw new Exception($sqlItem->error);
                }

                // Read the current (locked) quantity again for accurate before/after values
                $sqlCurrent->bind_param("s", $productId);
                $sqlCurrent->execute();
                $currentRow = $sqlCurrent->get_result()->fetch_assoc();
                $before = (int) $currentRow['quantity'];
                $after  = $before - $qty;

                $sqlDeductStock->bind_param("is", $qty, $productId);
                if (!$sqlDeductStock->execute()) {
                    throw new Exception($sqlDeductStock->error);
                }

                $movementQtyChange = -$qty; // negative because stock is going OUT
                $movementId = $this->generateMovementId(); // uses $this->dbResult — same connection/transaction
                $reason     = "Order #{$orderid}";
                $createdBy  = $customer['cusid'];

                $sqlMovement->bind_param(
                    "ssiiiss",
                    $movementId,
                    $productId,
                    $movementQtyChange,
                    $before,
                    $after,
                    $reason,
                    $createdBy
                );

                if (!$sqlMovement->execute()) {
                    throw new Exception($sqlMovement->error);
                }
            }

            $this->dbResult->commit();

            return ['status' => 'success', 'orderid' => $orderid, 'message' => 'Order placed successfully'];
        } catch (Exception $e) {
            $this->dbResult->rollback();
            return ['status' => 'error', 'orderid' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * Saves an uploaded bank transfer slip and links it to the order.
     * Called right after placeOrder() when payment_method === 'bank_transfer'.
     */
    public function uploadPaymentSlip($orderid, $file)
    {
        $uploadDir = __DIR__ . '/../../uploads/payment_slips/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileType = ($ext === 'pdf') ? 'pdf' : 'image';
        $newFileName = 'slip_' . $orderid . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['status' => 'error', 'message' => 'Failed to upload slip'];
        }

        $relativePath = 'uploads/payment_slips/' . $newFileName;

        $sql = $this->dbResult->prepare(
            "INSERT INTO payment_slips_tbl (orderid, file_path, file_type, status)
             VALUES (?, ?, ?, 'pending')"
        );
        $sql->bind_param("sss", $orderid, $relativePath, $fileType);

        if (!$sql->execute()) {
            return ['status' => 'error', 'message' => $sql->error];
        }

        return ['status' => 'success', 'file_path' => $relativePath];
    }

    /**
     * Fetches an order with its items — used on the confirmation page.
     */
    public function getOrderById($orderid)
    {
        $sqlOrder = $this->dbResult->prepare("SELECT * FROM orders_tbl WHERE orderid = ?");
        $sqlOrder->bind_param("s", $orderid);
        $sqlOrder->execute();
        $order = $sqlOrder->get_result()->fetch_assoc();

        if (!$order) {
            return null;
        }

        $sqlItems = $this->dbResult->prepare("SELECT * FROM order_items_tbl WHERE orderid = ?");
        $sqlItems->bind_param("s", $orderid);
        $sqlItems->execute();
        $order['items'] = $sqlItems->get_result()->fetch_all(MYSQLI_ASSOC);

        return $order;
    }

    // ========== ADMIN ORDER MANAGEMENT METHODS ==========

    public function getAllOrders()
    {
        $sql = $this->dbResult->prepare(
            "SELECT o.orderid, o.customer_name, o.phone, o.email, o.address, o.city, o.postal_code, 
                o.subtotal, o.delivery_fee, o.total, o.payment_method, o.payment_verified, 
                o.order_status, o.created_at,
                ps.status AS slip_status, ps.uploaded_at AS slip_uploaded_at
         FROM orders_tbl o
         LEFT JOIN payment_slips_tbl ps ON ps.id = (
             SELECT id FROM payment_slips_tbl 
             WHERE orderid = o.orderid 
             ORDER BY uploaded_at DESC 
             LIMIT 1
         )
         ORDER BY o.created_at DESC"
        );
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrdersByStatus($status)
    {
        $sql = $this->dbResult->prepare(
            "SELECT o.orderid, o.customer_name, o.phone, o.email, o.address, o.city, o.postal_code,
                o.subtotal, o.delivery_fee, o.total, o.payment_method, o.payment_verified, 
                o.order_status, o.created_at,
                ps.status AS slip_status, ps.uploaded_at AS slip_uploaded_at
         FROM orders_tbl o
         LEFT JOIN payment_slips_tbl ps ON ps.id = (
             SELECT id FROM payment_slips_tbl 
             WHERE orderid = o.orderid 
             ORDER BY uploaded_at DESC 
             LIMIT 1
         )
         WHERE o.order_status = ?
         ORDER BY o.created_at DESC"
        );
        $sql->bind_param("s", $status);
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateOrderStatus($orderId, $newStatus)
    {
        $allowedStatuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];

        if (!in_array($newStatus, $allowedStatuses)) {
            return "error";
        }

        // Check payment verification first
        $check = $this->dbResult->prepare("SELECT payment_method, payment_verified FROM orders_tbl WHERE orderid = ?");
        $check->bind_param("s", $orderId);
        $check->execute();
        $order = $check->get_result()->fetch_assoc();

        if (!$order) {
            return "error";
        }

        if ($order['payment_method'] === 'bank_transfer' && (int) $order['payment_verified'] === 0) {
            return "unverified"; // caller should show a "verify payment first" message
        }

        $sql = $this->dbResult->prepare("UPDATE orders_tbl SET order_status = ? WHERE orderid = ?");
        $sql->bind_param("ss", $newStatus, $orderId);

        return $sql->execute() ? "success" : "error";
    }

    public function getOrderCountByStatus()
    {
        $statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
        $counts = [];

        foreach ($statuses as $status) {
            $sql = $this->dbResult->prepare("SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?");
            $sql->bind_param("s", $status);
            $sql->execute();
            $row = $sql->get_result()->fetch_assoc();
            $counts[$status] = $row['count'];
        }

        return $counts;
    }

    // ========== PAYMENT SLIP VERIFICATION (ADMIN) ==========

    /**
     * READ — all pending bank transfer slips waiting for admin review
     */
    public function getPendingPaymentSlips()
    {
        $sql = $this->dbResult->prepare(
            "SELECT ps.id, ps.orderid, ps.file_path, ps.file_type, ps.status, ps.uploaded_at,
                    o.customer_name, o.total
             FROM payment_slips_tbl ps
             JOIN orders_tbl o ON o.orderid = ps.orderid
             WHERE ps.status = 'pending'
             ORDER BY ps.uploaded_at ASC"
        );
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * UPDATE — admin approves or rejects a slip.
     * On approval, orders_tbl.payment_verified is set to 1 so the order
     * can now move through the normal status flow.
     */
    public function reviewPaymentSlip($slipId, $decision, $adminName, $reason = null)
    {
        if (!in_array($decision, ['approved', 'rejected'])) {
            return ['status' => 'error', 'message' => 'Invalid decision'];
        }

        $get = $this->dbResult->prepare("SELECT orderid FROM payment_slips_tbl WHERE id = ?");
        $get->bind_param("i", $slipId);
        $get->execute();
        $slip = $get->get_result()->fetch_assoc();

        if (!$slip) {
            return ['status' => 'error', 'message' => 'Slip not found'];
        }

        $this->dbResult->begin_transaction();

        try {
            $update = $this->dbResult->prepare(
                "UPDATE payment_slips_tbl 
                 SET status = ?, reviewed_at = NOW(), reviewed_by = ?, rejection_reason = ? 
                 WHERE id = ?"
            );
            $update->bind_param("sssi", $decision, $adminName, $reason, $slipId);
            if (!$update->execute()) {
                throw new Exception($update->error);
            }

            if ($decision === 'approved') {
                $verify = $this->dbResult->prepare("UPDATE orders_tbl SET payment_verified = 1 WHERE orderid = ?");
                $verify->bind_param("s", $slip['orderid']);
                if (!$verify->execute()) {
                    throw new Exception($verify->error);
                }
            }

            $this->dbResult->commit();
            return ['status' => 'success'];
        } catch (Exception $e) {
            $this->dbResult->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function getSlipByOrderId($orderid)
    {
        $sql = $this->dbResult->prepare(
            "SELECT id, orderid, file_path, file_type, status, uploaded_at
         FROM payment_slips_tbl
         WHERE orderid = ?
         ORDER BY uploaded_at DESC
         LIMIT 1"
        );
        $sql->bind_param("s", $orderid);
        $sql->execute();
        return $sql->get_result()->fetch_assoc();
    }

    public function reuploadPaymentSlip($orderid, $file)
    {
        $result = $this->uploadPaymentSlip($orderid, $file);

        if ($result['status'] === 'success') {
            $update = $this->dbResult->prepare("UPDATE orders_tbl SET payment_verified = 0 WHERE orderid = ?");
            $update->bind_param("s", $orderid);
            $update->execute();
        }

        return $result;
    }

    public function getAllPaymentSlips()
    {
        $sql = $this->dbResult->prepare(
            "SELECT ps.id, ps.orderid, ps.file_path, ps.file_type, ps.status, 
                ps.uploaded_at, ps.reviewed_at, ps.reviewed_by, ps.rejection_reason,
                o.customer_name, o.total, o.order_status
         FROM payment_slips_tbl ps
         JOIN orders_tbl o ON o.orderid = ps.orderid
         ORDER BY ps.uploaded_at DESC"
        );
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}