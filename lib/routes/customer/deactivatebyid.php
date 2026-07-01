<?php
echo "File Loaded <br>";

include_once('../../function/customerFunction.php');

echo "Function File Loaded <br>";

$customerObj = new Customer();

echo "Object Created <br>";

echo "<pre>";
print_r($_GET);
echo "</pre>";

$userid = $_GET['userid'];

echo "User ID = " . $userid . "<br>";

$result = $customerObj->deactivatebyid($userid);

echo "Result = " . $result;