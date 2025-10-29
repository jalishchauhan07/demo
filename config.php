<?php
$conn = new mysqli("localhost", "root", "", "appointment_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

// Function to validate admin token
function validateAdminToken($conn) {
    if (!isset($_SESSION['admin']) || !isset($_SESSION['admin_token'])) {
        return false;
    }
    
    $token = $_SESSION['admin_token'];
    $query = "SELECT * FROM admin WHERE auth_token='$token' AND token_expiry > NOW()";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        return true;
    } else {
        // Token expired or invalid, clear session
        session_destroy();
        return false;
    }
}

// Function to check if user is logged in (use in protected pages)
function checkAdminAuth($conn) {
    if (!validateAdminToken($conn)) {
        header("Location: login.php");
        exit();
    }
}
?>