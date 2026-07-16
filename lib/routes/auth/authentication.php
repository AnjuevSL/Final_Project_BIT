<?php

include_once('../../function/AuthFunction.php');

header('Content-Type: application/json');

if (isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {

    $authObj = new Auth();
    $result = $authObj->authentication($_POST['loginEmail'], $_POST['loginPassword']);

    echo $result;
} else {
    echo json_encode([
        'loginstatus' => false,
        'message' => 'fill all inputs!'
    ]);
}