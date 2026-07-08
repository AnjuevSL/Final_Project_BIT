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

    // READ — active products only (for the customer-facing site)
    public function getActiveProducts()
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier
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
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier, d_status
             FROM product_tbl
             ORDER BY productid DESC"
        );
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
    public function getProductById($productId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier, d_status
             FROM product_tbl
             WHERE productid = ?"
        );
        $sql->bind_param("s", $productId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }
}
