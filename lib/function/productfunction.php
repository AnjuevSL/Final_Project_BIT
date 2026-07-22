<?php
//include main.php
include_once('main.php');

//include auto_id
include_once('auto_id.php');

//include image upload function page
include_once('img_upload.php');

class Product extends Main
{

    // Add product option
    public function addproduct($productname, $details, $price, $category, $supplier, $productimageName, $productimageSize, $productimageType, $productimageLocation)
    {

        $autonumber = new AutoNumber;
        $id = $autonumber->NumberGenaration("productId", "product_tbl", "PRO");


        $imageupload = new ImageUpload;
        $imageurl = $imageupload->imgUpload($productimageName, $productimageType, 'product', $productimageLocation, $id);

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        // Column order: productid, productName, productDetails, price, category, image, supplier, d_status(=1)
        $sqlinsertproduct = $this->dbResult->prepare("INSERT INTO product_tbl VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

        $sqlinsertproduct->bind_param("sssdsss", $id, $productname, $details, $price, $category, $imageurl, $supplier);

        if ($sqlinsertproduct->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
    }

    // Update Product
    public function updateproduct($productId, $productname, $details, $price, $category, $supplier, $productimageName = null, $productimageType = null, $productimageLocation = null)
    {
        // If a new image was uploaded, replace it. Otherwise keep the existing image.
        if (!empty($productimageName)) {
            $imageupload = new ImageUpload;
            $imageurl = $imageupload->imgUpload($productimageName, $productimageType, 'product', $productimageLocation, $productId);

            $sql = $this->dbResult->prepare(
                "UPDATE product_tbl
                 SET productName = ?, productDetails = ?, price = ?, category = ?, image = ?, supplier = ?
                 WHERE productid = ?"
            );
            $sql->bind_param("ssdssss", $productname, $details, $price, $category, $imageurl, $supplier, $productId);
        } else {
            $sql = $this->dbResult->prepare(
                "UPDATE product_tbl
                 SET productName = ?, productDetails = ?, price = ?, category = ?, supplier = ?
                 WHERE productid = ?"
            );
            $sql->bind_param("ssdsss", $productname, $details, $price, $category, $supplier, $productId);
        }

        if ($sql->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
    }

    // Delete option
    public function deleteproduct($productId)
    {
        $sql = $this->dbResult->prepare("DELETE FROM product_tbl WHERE productid = ?");
        $sql->bind_param("s", $productId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }

    // Deactivate option
    public function deactivateproduct($productId)
    {
        $sql = $this->dbResult->prepare("UPDATE product_tbl SET d_status = 0 WHERE productid = ?");
        $sql->bind_param("s", $productId);

        if ($sql->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
    }

    // READ — current stock quantity for a list of product IDs at once
    // (used by the cart/checkout page to validate stock before order placement)
    public function getStockForIds($productIds)
    {
        if (empty($productIds)) {
            return [];
        }

        // Build a "?,?,?" placeholder list matching the number of IDs
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $types = str_repeat('s', count($productIds));

        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, quantity FROM product_tbl WHERE productid IN ($placeholders)"
        );
        $sql->bind_param($types, ...$productIds);
        $sql->execute();
        $result = $sql->get_result();

        $stockMap = [];
        while ($row = $result->fetch_assoc()) {
            $stockMap[$row['productid']] = [
                'quantity' => (int) $row['quantity'],
                'productName' => $row['productName'],
            ];
        }

        return $stockMap;
    }

    // READ — active products only (for the customer-facing site)
    // Includes quantity + reorder_level so the storefront can show "Out of Stock" correctly.
    public function getActiveProducts()
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier,
                    quantity, reorder_level
             FROM product_tbl
             WHERE d_status = 1
             ORDER BY productid DESC"
        );
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // READ — all products including inactive (for the admin panel)
    public function getAllProducts()
    {
        $sql = $this->dbResult->prepare("
        SELECT
            p.productid,
            p.productName,
            p.productDetails,
            p.price,
            p.category,
            c.categoryName,
            p.supplier,
            s.supplierName,
            p.image,
            p.quantity,
            p.reorder_level,
            p.d_status
        FROM product_tbl p
        INNER JOIN categories_tbl c
            ON p.category = c.categoryid
        INNER JOIN suppliers_tbl s
            ON p.supplier = s.supplierid
        ORDER BY p.productid DESC
    ");

        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // TOGGLE STATUS — activate (d_status=1) or deactivate (d_status=0)
    public function toggleStatus($productId, $status)
    {
        $sql = $this->dbResult->prepare("UPDATE product_tbl SET d_status = ? WHERE productid = ?");
        $sql->bind_param("is", $status, $productId);

        if ($sql->execute()) {
            return "success";
        } else {
            return "error";
        }
    }
    // READ — a single product by ID
    // Includes quantity so callers (e.g. order placement) can validate stock.
    public function getProductById($productId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier,
                    quantity, reorder_level, d_status
             FROM product_tbl
             WHERE productid = ?"
        );
        $sql->bind_param("s", $productId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }
}