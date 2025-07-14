<?php
require_once("./auth.php");
require_once('./db.php');
require_once('./validation.php');
require_once("./navigate.php");

$email = $password = $cnfpassword = '';
$errors = [];

// registraction handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registraction'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cnfpassword = $_POST['cnfpassword'] ?? '';
    $checkStmt = null;
    $stmt = null;

    //validation
    if (validateRegistration($email, $password, $cnfpassword, $errors)) {
        try {
            // check email already exsists
            $checkStmt = $conn->prepare("select * from users where email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $errors['email'] = "Email already registered.";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $conn->prepare("insert into users (email, password) values (?, ?)");
                if (!$stmt)
                    throw new Exception("Prepare failed: " . $conn->error);

                $stmt->bind_param("ss", $email, $hashedPassword);
                if (!$stmt->execute())
                    throw new Exception("Execute failed: " . $stmt->error);

                // Reset form fields
                $email = $password = $cnfpassword = '';

                //set alert message with navigation
                Navigate("success", "Register SuccessFully...", "./index.php");
                exit;
            }
        } catch (Exception $e) {
            // show error message
            Navigate("danger", $e->getMessage());
        } finally {
            if ($checkStmt)
                $checkStmt->close();
            if ($stmt)
                $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

  <!-- Alert Component -->
  <?php require_once("./alert_component.php") ?>

  <!-- Navbar -->
   <?php require_once("navbar.php") ?>

  <!-- Register Form Section -->
  <div class="d-flex justify-content-center align-items-center min-vh-100 px-3">
    <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 420px;">
      <div class="card-body p-4">
        <h4 class="mb-4 text-center text-success fw-semibold">Create Account</h4>

        <form action="" method="POST">
          <!-- Email -->
          <div class="mb-3">
            <label for="registerEmail" class="form-label">Email</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid border-danger' : 'border-dark' ?>" name="email"
              value="<?= htmlspecialchars($email) ?>">
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="registerPassword" class="form-label">Password</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid border-danger' : 'border-dark' ?>"
              name="password"  id="password">
            <?php if (isset($errors['password'])): ?>
              <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label for="cnfpassword" class="form-label">Confirm Password</label>
            <input type="password"class="form-control border <?= isset($errors['confirm']) ? 'is-invalid border-danger' : 'border-dark' ?>"
              name="cnfpassword" id="confirm_password" required>
            <?php if (isset($errors['confirm'])): ?>
              <div class="invalid-feedback"><?= $errors['confirm'] ?></div>
            <?php endif; ?>
          </div>

           <!-- show password checkbox -->
           <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showPasswordCheck">
            <label class="form-check-label" for="showPasswordCheck">
              Show Password
            </label>
          </div>

          <!-- Submit Button -->
          <div class="d-grid">
            <button type="submit" class="btn btn-success fw-semibold" name="registraction">
              <i class="bi bi-person-plus me-1"></i> Register
            </button>
          </div>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-3 small">
          Already have an account? <a href="index.php" class="text-decoration-none text-success fw-semibold">Login</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("showPasswordCheck").addEventListener('change',function(){
      const passwordInput = document.getElementById("password");
      const confirm_password_Input = document.getElementById("confirm_password");
      passwordInput.type = this.checked ? 'text' : 'password';
      confirm_password_Input.type = this.checked ? 'text' : 'password';
    })
  </script>
</body>

</html>
