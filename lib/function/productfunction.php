<?php
//include main.php
include_once('main.php');

//include auto_id
include_once('auto_id.php');

//include image upload function page
include_once('img_upload.php');

class Product extends Main
{

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

    // Get all active products (for the home page)
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

    // Get a single product by its ID
    public function getProductById($productId)
    {
        $sql = $this->dbResult->prepare(
            "SELECT productid, productName, productDetails, price, category, image, supplier
             FROM product_tbl
             WHERE productid = ? AND d_status = 1"
        );
        $sql->bind_param("s", $productId);
        $sql->execute();
        $result = $sql->get_result();

        return $result->fetch_assoc();
    }
}