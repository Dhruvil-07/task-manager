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

      <!-- Logout Button triggers modal -->
      <div class="d-flex ms-auto pe-3">
        <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#logoutModal">LogOut</button>
      </div>

    </div>
  </nav>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="post"> <!-- This form submits on confirmation -->
          <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            Are you sure you want to logout?
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="logout" class="btn btn-danger">Yes, Logout</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>