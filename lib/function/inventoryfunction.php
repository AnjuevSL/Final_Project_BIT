<?php
// include main.php
include_once('main.php');

// include auto_id
include_once('auto_id.php');

class Inventory extends Main
{

    // READ — all products with current stock, category, and low-stock flag
    public function getAllInventory()
    {
        $sql = $this->dbResult->prepare("
            SELECT
                p.productid,
                p.productName,
                p.image,
                p.category,
                c.categoryName,
                p.quantity,
                p.reorder_level,
                p.d_status
            FROM product_tbl p
            INNER JOIN categories_tbl c
                ON p.category = c.categoryid
            ORDER BY p.productid DESC
        ");

        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — products at or below their reorder level
    public function getLowStockProducts()
    {
        $sql = $this->dbResult->prepare("
            SELECT
                p.productid,
                p.productName,
                p.image,
                p.category,
                c.categoryName,
                p.quantity,
                p.reorder_level
            FROM product_tbl p
            INNER JOIN categories_tbl c
                ON p.category = c.categoryid
            WHERE p.quantity <= p.reorder_level
              AND p.d_status = 1
            ORDER BY p.quantity ASC
        ");

        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — current quantity + reorder level for one product
    public function getStockByProductId($productId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, quantity, reorder_level
             FROM product_tbl
             WHERE productid = ?"
        );
        $sql->bind_param("s", $productId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }

    // UPDATE — change the reorder (low-stock alert) threshold for a product
    public function updateReorderLevel($productId, $reorderLevel)
    {
        $sql = $this->dbResult->prepare(
            "UPDATE product_tbl SET reorder_level = ? WHERE productid = ?"
        );
        $sql->bind_param("is", $reorderLevel, $productId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // ADJUST STOCK — the core inventory action.
    // $movementType: 'IN' (stock arrival), 'OUT' (stock leaving, e.g. damage/sale correction), 'ADJUSTMENT' (manual correction)
    // $quantityChange: pass a positive number for IN, positive number for OUT/ADJUSTMENT too —
    //                  this method figures out the sign internally based on $movementType.
    public function adjustStock($productId, $movementType, $quantityChange, $reason, $userId)
    {
        $movementType   = strtoupper($movementType);
        $quantityChange = abs((int) $quantityChange);

        if (!in_array($movementType, ['IN', 'OUT', 'ADJUSTMENT'])) {
            return "error_invalid_type";
        }

        // Lock the current row's quantity for this transaction
        $this->dbResult->begin_transaction();

        try {
            $sqlSelect = $this->dbResult->prepare(
                "SELECT quantity FROM product_tbl WHERE productid = ? FOR UPDATE"
            );
            $sqlSelect->bind_param("s", $productId);
            $sqlSelect->execute();
            $row = $sqlSelect->get_result()->fetch_assoc();

            if (!$row) {
                $this->dbResult->rollback();
                return "error_product_not_found";
            }

            $previousQuantity = (int) $row['quantity'];

            // IN increases stock. OUT and ADJUSTMENT (used here for corrections/losses) decrease stock.
            $signedChange = ($movementType === 'IN') ? $quantityChange : -$quantityChange;
            $newQuantity  = $previousQuantity + $signedChange;

            if ($newQuantity < 0) {
                $this->dbResult->rollback();
                return "error_insufficient_stock";
            }

            // Update the live stock count on product_tbl
            $sqlUpdate = $this->dbResult->prepare(
                "UPDATE product_tbl SET quantity = ? WHERE productid = ?"
            );
            $sqlUpdate->bind_param("is", $newQuantity, $productId);
            $sqlUpdate->execute();

            // Record the movement in the history table
            $autonumber  = new AutoNumber;
            $movementId  = $autonumber->NumberGenaration("movementid", "stock_movements_tbl", "MOV");

            $sqlInsert = $this->dbResult->prepare(
                "INSERT INTO stock_movements_tbl
                 (movementid, productid, movement_type, quantity_change, previous_quantity, new_quantity, reason, created_by, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            $sqlInsert->bind_param(
                "sssiiiss",
                $movementId,
                $productId,
                $movementType,
                $signedChange,
                $previousQuantity,
                $newQuantity,
                $reason,
                $userId
            );
            $sqlInsert->execute();

            $this->dbResult->commit();
            return "success";
        } catch (\Throwable $e) {
            $this->dbResult->rollback();
            return "error";
        }
    }

    // READ — full movement history for one product (most recent first)
    public function getMovementHistory($productId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT movementid, productid, movement_type, quantity_change,
                    previous_quantity, new_quantity, reason, created_by, created_at
             FROM stock_movements_tbl
             WHERE productid = ?
             ORDER BY created_at DESC"
        );
        $sql->bind_param("s", $productId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — recent movements across all products (for a dashboard/activity feed)
    public function getRecentMovements($limit = 20)
    {
        $sql = $this->dbResult->prepare("
            SELECT
                m.movementid,
                m.productid,
                p.productName,
                m.movement_type,
                m.quantity_change,
                m.previous_quantity,
                m.new_quantity,
                m.reason,
                m.created_by,
                m.created_at
            FROM stock_movements_tbl m
            INNER JOIN product_tbl p
                ON m.productid = p.productid
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $sql->bind_param("i", $limit);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}