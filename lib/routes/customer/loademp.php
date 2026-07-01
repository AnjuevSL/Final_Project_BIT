<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$result = $customerObj->loaddata();

echo($result);

?>