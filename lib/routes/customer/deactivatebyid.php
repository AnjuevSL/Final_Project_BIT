<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$id = $_GET['userid'];

$result = $customerObj->deactivatebyid($id);

echo($result);

?>