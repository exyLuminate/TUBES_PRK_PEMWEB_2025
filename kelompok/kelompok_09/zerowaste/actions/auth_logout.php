<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';


if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'LOGOUT', 'User logout dari sistem');
}


session_unset();
session_destroy();


header('Location: ../index.php');
exit();
?>