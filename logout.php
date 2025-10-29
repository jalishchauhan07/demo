<?php
include 'config.php';

// Clear token from database
if (isset($_SESSION['admin_token'])) {
    $token = $_SESSION['admin_token'];
    $conn->query("UPDATE admin SET auth_token=NULL, token_expiry=NULL WHERE auth_token='$token'");
}

// Destroy session
session_destroy();
header("Location: login.php");
exit();
?>