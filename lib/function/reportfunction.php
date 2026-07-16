<?php
// Report class - handles all reporting logic (Order, Product, Category, Supplier)

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
}
