<?php
//include main.php
include_once('main.php');

//include auto_id
include_once('auto_id.php');

//include image upload function page
include_once('img_upload.php');

class Product extends Main
{

    public function addproduct($productname, $details, $category, $supplier, $productimageName, $productimageSize, $productimageType, $productimageLocation)
    {

        $autonumber = new AutoNumber;
        $id = $autonumber->NumberGenaration("productId", "product_tbl", "PRO");


        $imageupload = new ImageUpload;
        $imageurl = $imageupload->imgUpload($productimageName, $productimageType, 'product', $productimageLocation, $id);

        if ($this->dbResult->error) {
            echo ($this->dbResult->error);
            exit;
        }

        $sqlinsertproduct = $this->dbResult->prepare("INSERT INTO product_tbl VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

        $sqlinsertproduct->bind_param("sssssss", $id, $productname, $details, $category, $imageurl, $supplier);

        if ($sqlinsertproduct->execute()) {
            return ("success");
        } else {
            return ("error2");
        }
    }
}
