<?php
require_once("auth.php");

if (isset($_POST['logout'])) {
   echo "logout click";
    session_unset();    // Remove all session variables
    session_destroy();  // Destroy the session
    header("Location: index.php"); // Redirect to login page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>    
</head>
<body>
    
<!-- navabar -->
<nav class="navbar navbar-expand-lg" style="background-color: #e9ecef;">
  <div class="container-fluid">
    <!-- Title -->
    <span class="navbar-brand fw-bold text-dark">Task Manager</span>

    <!-- LogOut button aligned to the right --> 
    <div class="d-flex ms-auto pe-3">
      <form class="d-flex" method="POST">
        <button class="btn btn-danger px-4" type="submit" name="logout">LogOut</button>
      </form>
    </div>
  </div>
</nav>

</body>
</html>