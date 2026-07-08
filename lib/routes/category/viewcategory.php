<?php
include_once('../../function/categoryfunction.php');

$proobject = new category();

// If a specific category ID is requested, return just that one; otherwise return all
if (isset($_GET['categoryId'])) {
    $result = $proobject->getcategoryById($_GET['categoryId']);
} else {
    $result = $proobject->getAllCategories();
}

header('Content-Type: application/json');
echo json_encode($result);