
<?php

//include the function page
include_once('../../function/customerFunction.php');

$customerObj = new Customer();

$searchtext = $_GET['searchtext'];

$result = $customerObj->loaddatasearch($searchtext);

echo($result);

?>