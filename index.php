<?php
require_once('./db.php');
require_once('./validation.php');
require_once('./auth.php');
require_once("./navigate.php");

$email = ''; //store email
$errors = []; //store validation errors

// Handle login form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Validate using utility
  if (validateLogin($email, $password, $errors)) {
    $stmt = null;
    try {
      $stmt = $conn->prepare("select * from users where email = ?");
      if (!$stmt) {
        throw new Exception("Query error: " . $conn->error);
      }

      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
          $_SESSION['user_id'] = $user['id']; // set user id to session

          //navigate to dashboard with alert
          Navigate("success", "Login SuceessFully", "./dashboard.php");
          exit;
        } else {
          Navigate("danger", "Incorrect Password");
        }
      } else {
        Navigate("danger", "No user found with this email.");
      }
    } catch (Exception $e) {
      //show error alert
      Navigate("danger", $e->getMessage());
    } finally {
      if (!$stmt) {
        $stmt->close();
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

  <!-- Alert Component -->
  <?php require_once("./alert_component.php") ?>

  <!-- Navbar -->
  <?php require_once("./navbar.php") ?>

  <!-- Login Form Card -->
  <div class="d-flex justify-content-center align-items-center min-vh-100 px-3">
    <div class="card shadow-lg border-0 rounded-4" style="width: 100%; max-width: 400px;">
      <div class="card-body p-4">
        <h4 class="mb-4 text-center text-primary fw-semibold">Welcome Back 👋</h4>

        <form action="" method="POST">
          <!-- Email -->
          <div class="mb-3">
            <label for="loginEmail" class="form-label fw-medium">Email</label>
            <input type="email"
              class="form-control  <?= isset($errors['email']) ? 'is-invalid border-danger' : 'border-dark' ?>"
              name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="loginPassword" class="form-label fw-medium">Password</label>
            <input type="password"
              class="form-control  <?= isset($errors['password']) ? 'is-invalid border-danger' : 'border-dark' ?>"
              name="password" id="password">
            <?php if (isset($errors['password'])): ?>
              <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
          </div>

          <!-- show password checkbox -->
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showPasswordCheck">
            <label class="form-check-label" for="showPasswordCheck">
              Show Password
            </label>
          </div>

          <!-- Forgot Password -->
          <div class="text-end mb-3">
            <a href="forgot_password.php" class="text-decoration-none small">Forgot Password?</a>
          </div>

          <!-- Submit Button -->
          <div class="d-grid mb-3">
            <button type="submit" name="login" class="btn btn-primary fw-semibold py-2">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </button>
          </div>
        </form>

        <!-- Register Link -->
        <div class="text-center mt-2 small">
          Don't have an account?
          <a href="register.php" class="text-decoration-none">Register</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('showPasswordCheck').addEventListener('change', function () {
      const passwordInput = document.getElementById('password');
      passwordInput.type = this.checked ? 'text' : 'password';
    });
  </script>

</body>

</html>