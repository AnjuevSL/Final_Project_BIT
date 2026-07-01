<?php
//start sessions
session_start();

//unset all session variales
$_SESSION = array();

//distroy the session
session_reset();
session_destroy();

header('Location:../../login.php');
exit;

?>