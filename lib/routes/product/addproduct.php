<?php
include_once('../../function/productFunction.php');

$productname = $_POST['productname'];
$details = $_POST['details'];
$category = $_POST['category'];
$supplier = $_POST['supplier'];

$productimageName = $_FILES['formFile']['name'];
$productimageSize = $_FILES['formFile']['size'];
$productimageType = $_FILES['formFile']['type'];
$productimageLocation = $_FILES['formFile']['tmp_name'];

$proobject = new Product();

$result = $proobject->addproduct($productname, $details, $category, $supplier, $productimageName, $productimageSize, $productimageType, $productimageLocation);

echo($result);

?>