<?php

// ini_set('display_errors', 0);
// error_reporting(0);

// Define DB config via query-style params
$host = 'localhost:3307';
$dbname = 'taskmanager';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo '
  <!DOCTYPE html>
<html>
<head>
    <title>Database Connection Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .error-box {
            text-align: center;
            background-color: #fff;
            border: 1px solid #f5c2c7;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        .error-icon {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .error-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #dc3545;
        }
        .error-message {
            font-size: 16px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">
         <img src="https://cdn-icons-png.flaticon.com/512/463/463612.png" alt="Error Icon" width="60" height="60" class="mb-3">
        </div>
        <div class="error-title">Database Connection Failed</div>
        <div class="error-message">Please try again after some time.</div>
    </div>
</body>
</html>
    ';
    exit;
}

?>