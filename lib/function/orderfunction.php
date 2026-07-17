<?php
include_once('main.php');
include_once('auto_id.php');

class Order extends Main
{

    /**
     * Places an order: inserts one row into orders_tbl and one row
     * per cart item into order_items_tbl, inside a transaction so
     * either everything saves or nothing does.
     *
     * @param array $customer  ['fullname','phone','email','address','city','postal_code','notes','payment_method']
     * @param array $cart      list of ['id','name','price','qty']
     * @param float $deliveryFee
     * @return array ['status' => 'success'|'error', 'orderid' => string|null, 'message' => string]
     */
    public function placeOrder($customer, $cart, $deliveryFee = 350.00)
    {
        if (empty($cart)) {
            return ['status' => 'error', 'orderid' => null, 'message' => 'Cart is empty'];
        }

        // Calculate subtotal from the cart (never trust totals sent from the client)
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['qty']);
        }
        $total = $subtotal + $deliveryFee;

        $autonumber = new AutoNumber;
        $orderid = $autonumber->NumberGenaration("orderid", "orders_tbl", "ORD");

        $this->dbResult->begin_transaction();

        try {
            // Insert the order
            $sqlOrder = $this->dbResult->prepare(
                "INSERT INTO orders_tbl
                    (orderid, customer_id, customer_name, phone, email, address, city, postal_code, notes, payment_method, subtotal, delivery_fee, total, order_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
            );

            $sqlOrder->bind_param(
                "ssssssssssddd",
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
                $subtotal,
                $deliveryFee,
                $total
            );

            if (!$sqlOrder->execute()) {
                throw new Exception($sqlOrder->error);
            }

            // Insert each cart item
            $sqlItem = $this->dbResult->prepare(
                "INSERT INTO order_items_tbl (orderid, productid, product_name, price, qty, line_total)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            foreach ($cart as $item) {
                $productId  = $item['id'];
                $name       = $item['name'];
                $price      = (float) $item['price'];
                $qty        = (int) $item['qty'];
                $lineTotal  = $price * $qty;

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
            }

            $this->dbResult->commit();

            return ['status' => 'success', 'orderid' => $orderid, 'message' => 'Order placed successfully'];
        } catch (Exception $e) {
            $this->dbResult->rollback();
            return ['status' => 'error', 'orderid' => null, 'message' => $e->getMessage()];
        }
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

    /**
     * READ — all orders (for admin panel)
     */
    public function getAllOrders()
    {
        $sql = $this->dbResult->prepare(
            "SELECT orderid, customer_name, phone, email, address, city, postal_code, 
                    subtotal, delivery_fee, total, payment_method, order_status, created_at
             FROM orders_tbl
             ORDER BY created_at DESC"
        );
        $sql->execute();
        $result = $sql->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * READ — orders filtered by status (for admin panel tabs)
     */
    public function getOrdersByStatus($status)
    {
        $sql = $this->dbResult->prepare(
            "SELECT orderid, customer_name, phone, email, address, city, postal_code,
                    subtotal, delivery_fee, total, payment_method, order_status, created_at
             FROM orders_tbl
             WHERE order_status = ?
             ORDER BY created_at DESC"
        );
        $sql->bind_param("s", $status);
        $sql->execute();
        $result = $sql->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * UPDATE — change order status (admin action)
     */
    public function updateOrderStatus($orderId, $newStatus)
    {
        $allowedStatuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];

        if (!in_array($newStatus, $allowedStatuses)) {
            return "error";
        }

        $sql = $this->dbResult->prepare("UPDATE orders_tbl SET order_status = ? WHERE orderid = ?");
        $sql->bind_param("ss", $newStatus, $orderId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    /**
     * READ — count of orders by each status (for stats)
     */
    public function getOrderCountByStatus()
    {
        $statuses = ['pending', 'billing', 'ready_to_delivery', 'delivery', 'delivered', 'hold', 'cancelled'];
        $counts = [];

        foreach ($statuses as $status) {
            $sql = $this->dbResult->prepare("SELECT COUNT(*) as count FROM orders_tbl WHERE order_status = ?");
            $sql->bind_param("s", $status);
            $sql->execute();
            $result = $sql->get_result();
            $row = $result->fetch_assoc();
            $counts[$status] = $row['count'];
        }

        return $counts;
    }
}