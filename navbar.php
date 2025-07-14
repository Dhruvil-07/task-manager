<?php
require_once("auth.php");

$current_page = basename($_SERVER['PHP_SELF']);
$hide_logout = in_array($current_page, ['index.php', 'register.php', 'forgot_password.php', 'reset_password.php']);

if (isset($_POST['logout'])) {
  session_unset();    // Remove all session variables
  session_destroy();  // Destroy the session

  //  Delete the session cookie from the browser
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    // This sets the session cookie to expire in the past
    setcookie(
      session_name(),
      '',
      time() - 3600,
      $params["path"],
      $params["domain"],
      $params["secure"],
      $params["httponly"]
    );
  }

  header("Location: index.php"); // Redirect to login page
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Navbar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <!-- navbar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container-fluid px-4">
      <!-- title with visible emoji/icon -->
      <span class="navbar-brand fw-bold text-primary fs-4">
        📝 Task Manager
      </span>

      <!-- Logout Button -->
      <?php if (!$hide_logout): ?>
        <div class="ms-auto">
          <button class="btn btn-outline-danger d-flex align-items-center gap-2 px-3 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-box-arrow-right"></i>
            Logout
          </button>
        </div>
      <?php endif; ?>
    </div>
  </nav>


  <!-- logout confirmation model -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="post">
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