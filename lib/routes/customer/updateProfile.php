
<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

include_once('../../function/customerFunction.php');

$userid   = $_SESSION['user'];
$email    = $_POST['email'] ?? '';
$name     = $_POST['name'] ?? '';
$phone    = $_POST['phone'] ?? '';
$nic      = $_POST['nic'] ?? '';
$gender   = $_POST['gender'] ?? '';
$birthday = $_POST['birthday'] ?? '';

$customerObj = new Customer();
$result = $customerObj->editCustomer($userid, $email, $name, $phone, $nic, $gender, $birthday);

if ($result == "success") {
    echo json_encode(['status' => 'success']);
} elseif ($result == "Phone number Exists") {
    echo json_encode(['status' => 'Phone number Exists']);
} else {
    echo json_encode(['status' => 'error', 'message' => $result]);
}
?>