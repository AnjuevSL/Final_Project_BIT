<?php
// Report class - handles all reporting logic (Order, Product, Category, Supplier, Inventory)

include_once('main.php');

class Report extends Main
{
    public function getOrderReport($status = null, $from = null, $to = null)
    {
        $sql = "SELECT orderid, customer_name, phone, email, city, payment_method,
                       subtotal, delivery_fee, total, order_status, created_at
                FROM orders_tbl WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($status)) {
            $sql .= " AND order_status = ?";
            $types .= "s";
            $params[] = $status;
        }

        if (!empty($from)) {
            $sql .= " AND DATE(created_at) >= ?";
            $types .= "s";
            $params[] = $from;
        }

        if (!empty($to)) {
            $sql .= " AND DATE(created_at) <= ?";
            $types .= "s";
            $params[] = $to;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }

    /**
     * ORDER REPORT SUMMARY
     * Returns totals used for the summary cards at the top of the report
     */
    public function getOrderReportSummary($status = null, $from = null, $to = null)
    {
        $sql = "SELECT COUNT(*) AS total_orders,
                       COALESCE(SUM(total), 0) AS total_revenue,
                       COALESCE(SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_count,
                       COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END), 0) AS delivered_count,
                       COALESCE(SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END), 0) AS cancelled_count
                FROM orders_tbl WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($status)) {
            $sql .= " AND order_status = ?";
            $types .= "s";
            $params[] = $status;
        }

        if (!empty($from)) {
            $sql .= " AND DATE(created_at) >= ?";
            $types .= "s";
            $params[] = $from;
        }

        if (!empty($to)) {
            $sql .= " AND DATE(created_at) <= ?";
            $types .= "s";
            $params[] = $to;
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * PRODUCT REPORT
     * Joins product_tbl with categories_tbl and suppliers_tbl to show real names
     * instead of raw IDs. Filterable by category, supplier, and active status.
     */
    public function getProductReport($category = null, $supplier = null, $status = null)
    {
        $sql = "SELECT p.productid, p.productName, p.price, p.d_status,
                       c.categoryName, s.supplierName
                FROM product_tbl p
                LEFT JOIN categories_tbl c ON p.category = c.categoryid
                LEFT JOIN suppliers_tbl s ON p.supplier = s.supplierid
                WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($category)) {
            $sql .= " AND p.category = ?";
            $types .= "s";
            $params[] = $category;
        }

        if (!empty($supplier)) {
            $sql .= " AND p.supplier = ?";
            $types .= "s";
            $params[] = $supplier;
        }

        if ($status !== null && $status !== '') {
            $sql .= " AND p.d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $sql .= " ORDER BY p.productName ASC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }

    public function getProductReportSummary($category = null, $supplier = null, $status = null)
    {
        $sql = "SELECT COUNT(*) AS total_products,
                       COALESCE(SUM(CASE WHEN p.d_status = 1 THEN 1 ELSE 0 END), 0) AS active_count,
                       COALESCE(SUM(CASE WHEN p.d_status = 0 THEN 1 ELSE 0 END), 0) AS inactive_count,
                       COALESCE(AVG(p.price), 0) AS avg_price
                FROM product_tbl p WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($category)) {
            $sql .= " AND p.category = ?";
            $types .= "s";
            $params[] = $category;
        }

        if (!empty($supplier)) {
            $sql .= " AND p.supplier = ?";
            $types .= "s";
            $params[] = $supplier;
        }

        if ($status !== null && $status !== '') {
            $sql .= " AND p.d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Helper: list of categories/suppliers for filter dropdowns
     */
    public function getAllCategoriesList()
    {
        $sql = "SELECT categoryid, categoryName FROM categories_tbl WHERE d_status = 1 ORDER BY categoryName ASC";
        $result = $this->dbResult->query($sql);

        $list = [];
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    public function getAllSuppliersList()
    {
        $sql = "SELECT supplierid, supplierName FROM suppliers_tbl WHERE d_status = 1 ORDER BY supplierName ASC";
        $result = $this->dbResult->query($sql);

        $list = [];
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    /**
     * CATEGORY REPORT
     * Each category with a count of how many products belong to it
     */
    public function getCategoryReport($status = null)
    {
        $sql = "SELECT c.categoryid, c.categoryName, c.description, c.d_status, c.created_at,
                       COUNT(p.productid) AS product_count
                FROM categories_tbl c
                LEFT JOIN product_tbl p ON p.category = c.categoryid
                WHERE 1=1";

        $params = [];
        $types  = "";

        if ($status !== null && $status !== '') {
            $sql .= " AND c.d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $sql .= " GROUP BY c.categoryid, c.categoryName, c.description, c.d_status, c.created_at
                  ORDER BY c.categoryName ASC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function getCategoryReportSummary($status = null)
    {
        $sql = "SELECT COUNT(*) AS total_categories,
                       COALESCE(SUM(CASE WHEN d_status = 1 THEN 1 ELSE 0 END), 0) AS active_count,
                       COALESCE(SUM(CASE WHEN d_status = 0 THEN 1 ELSE 0 END), 0) AS inactive_count
                FROM categories_tbl WHERE 1=1";

        $params = [];
        $types  = "";

        if ($status !== null && $status !== '') {
            $sql .= " AND d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * SUPPLIER REPORT
     * Each supplier with a count of how many products they supply
     */
    public function getSupplierReport($status = null)
    {
        $sql = "SELECT s.supplierid, s.supplierName, s.email, s.phone, s.address, s.d_status, s.created_at,
                       COUNT(p.productid) AS product_count
                FROM suppliers_tbl s
                LEFT JOIN product_tbl p ON p.supplier = s.supplierid
                WHERE 1=1";

        $params = [];
        $types  = "";

        if ($status !== null && $status !== '') {
            $sql .= " AND s.d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $sql .= " GROUP BY s.supplierid, s.supplierName, s.email, s.phone, s.address, s.d_status, s.created_at
                  ORDER BY s.supplierName ASC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $suppliers = [];
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }

        return $suppliers;
    }

    public function getSupplierReportSummary($status = null)
    {
        $sql = "SELECT COUNT(*) AS total_suppliers,
                       COALESCE(SUM(CASE WHEN d_status = 1 THEN 1 ELSE 0 END), 0) AS active_count,
                       COALESCE(SUM(CASE WHEN d_status = 0 THEN 1 ELSE 0 END), 0) AS inactive_count
                FROM suppliers_tbl WHERE 1=1";

        $params = [];
        $types  = "";

        if ($status !== null && $status !== '') {
            $sql .= " AND d_status = ?";
            $types .= "i";
            $params[] = (int) $status;
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * INVENTORY — STOCK LEVELS REPORT
     * Each product with its current quantity, reorder level, and category name.
     * Filterable by category and stock status ('low' = at/below reorder level, 'ok' = above it).
     */
    public function getStockReport($category = null, $status = null)
    {
        $sql = "SELECT p.productid, p.productName, p.category, c.categoryName,
                       p.quantity, p.reorder_level, p.d_status
                FROM product_tbl p
                INNER JOIN categories_tbl c ON p.category = c.categoryid
                WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($category)) {
            $sql .= " AND p.category = ?";
            $types .= "s";
            $params[] = $category;
        }

        if ($status === 'low') {
            $sql .= " AND p.quantity <= p.reorder_level";
        } elseif ($status === 'ok') {
            $sql .= " AND p.quantity > p.reorder_level";
        }

        $sql .= " ORDER BY p.productid DESC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    }

    public function getStockReportSummary($category = null, $status = null)
    {
        $sql = "SELECT COUNT(*) AS total_products,
                       COALESCE(SUM(p.quantity), 0) AS total_quantity,
                       COALESCE(SUM(CASE WHEN p.quantity <= p.reorder_level THEN 1 ELSE 0 END), 0) AS low_stock_count,
                       COALESCE(SUM(CASE WHEN p.quantity > p.reorder_level THEN 1 ELSE 0 END), 0) AS ok_stock_count
                FROM product_tbl p
                WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($category)) {
            $sql .= " AND p.category = ?";
            $types .= "s";
            $params[] = $category;
        }

        if ($status === 'low') {
            $sql .= " AND p.quantity <= p.reorder_level";
        } elseif ($status === 'ok') {
            $sql .= " AND p.quantity > p.reorder_level";
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * INVENTORY — MOVEMENT HISTORY REPORT
     * Every stock movement (IN/OUT/ADJUSTMENT) joined with the product name.
     * Filterable by date range, product, and movement type.
     */
    public function getMovementReport($dateFrom = null, $dateTo = null, $productId = null, $movementType = null)
    {
        $sql = "SELECT m.movementid, m.productid, p.productName, m.movement_type,
                       m.quantity_change, m.previous_quantity, m.new_quantity,
                       m.reason, m.created_by, m.created_at
                FROM stock_movements_tbl m
                INNER JOIN product_tbl p ON m.productid = p.productid
                WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($dateFrom)) {
            $sql .= " AND DATE(m.created_at) >= ?";
            $types .= "s";
            $params[] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $sql .= " AND DATE(m.created_at) <= ?";
            $types .= "s";
            $params[] = $dateTo;
        }

        if (!empty($productId)) {
            $sql .= " AND m.productid = ?";
            $types .= "s";
            $params[] = $productId;
        }

        if (!empty($movementType)) {
            $sql .= " AND m.movement_type = ?";
            $types .= "s";
            $params[] = $movementType;
        }

        $sql .= " ORDER BY m.created_at DESC";

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $movements = [];
        while ($row = $result->fetch_assoc()) {
            $movements[] = $row;
        }

        return $movements;
    }

    public function getMovementReportSummary($dateFrom = null, $dateTo = null, $productId = null, $movementType = null)
    {
        $sql = "SELECT COUNT(*) AS total_movements,
                       COALESCE(SUM(CASE WHEN m.movement_type = 'IN' THEN m.quantity_change ELSE 0 END), 0) AS total_in,
                       COALESCE(SUM(CASE WHEN m.movement_type IN ('OUT','ADJUSTMENT') THEN ABS(m.quantity_change) ELSE 0 END), 0) AS total_out
                FROM stock_movements_tbl m
                WHERE 1=1";

        $params = [];
        $types  = "";

        if (!empty($dateFrom)) {
            $sql .= " AND DATE(m.created_at) >= ?";
            $types .= "s";
            $params[] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $sql .= " AND DATE(m.created_at) <= ?";
            $types .= "s";
            $params[] = $dateTo;
        }

        if (!empty($productId)) {
            $sql .= " AND m.productid = ?";
            $types .= "s";
            $params[] = $productId;
        }

        if (!empty($movementType)) {
            $sql .= " AND m.movement_type = ?";
            $types .= "s";
            $params[] = $movementType;
        }

        $stmt = $this->dbResult->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}