<?php
include_once('../../function/categoryfunction.php');

$categoryId = $_POST['categoryId'];

$proobject = new category();

$result = $proobject->deactivatecategory($categoryId);

echo ($result);