<?php
//start sessions
session_start();

if (isset($_SESSION['user'])) {

    if (isset($_SESSION['usertype'])) {

        $usertype = $_SESSION['usertype'];
        if ($usertype == "Customer") {
        } else {
            header('Location:../../index.php');
        }
    } else {
        header('Location:../../index.php');
    }
} else {
    header('Location:../../index.php');
}


?>
<a href="logout.php"><button type="button" href="" class="btn btn-secondary">logout</button></a>
welcome customer