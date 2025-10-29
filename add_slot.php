<?php 
include 'config.php';
checkAdminAuth($conn);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $date = $conn->real_escape_string($_POST['slot_date']);
  $time = $conn->real_escape_string($_POST['slot_time']);
  $conn->query("INSERT INTO slots (slot_date, slot_time) VALUES ('$date', '$time')");
  header("Location: dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Slot - Admin Panel</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 1rem 2rem;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 600;
      color: white !important;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 60px;
      left: 0;
      bottom: 0;
      width: 250px;
      background: white;
      box-shadow: 2px 0 5px rgba(0,0,0,0.05);
      padding-top: 2rem;
    }

    .sidebar a {
      display: block;
      padding: 1rem 1.5rem;
      color: #495057;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .sidebar a:hover {
      background-color: #f8f9fa;
      color: #667eea;
      border-left-color: #667eea;
    }

    .sidebar a.active {
      background-color: #e7e9fd;
      color: #667eea;
      border-left-color: #667eea;
    }

    /* Content */
    .content {
      margin-left: 250px;
      margin-top: 80px;
      padding: 2rem;
      min-height: calc(100vh - 80px);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border: none;
      max-width: 600px;
      width: 100%;
    }

    .card h4 {
      color: #2d3748;
      font-weight: 600;
    }

    .form-label {
      color: #2d3748;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    .form-control {
      border-radius: 6px;
      border: 1px solid #cbd5e0;
      padding: 0.75rem;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn {
      border-radius: 6px;
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
    }

    .btn-primary