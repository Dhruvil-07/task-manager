<?php
require_once('./db.php');
require_once('./validation.php');
require_once('./auth.php');

// Initialize variables
$email = '';
$errors = [];

// Handle login form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate using utility
    if (validateLogin($email, $password, $errors)) {
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
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $errors['password'] = "Incorrect password.";
                }
            } else {
                $errors['email'] = "No user found with this email.";
            }

            $stmt->close();
        } catch (Exception $e) {
            echo $e->getMessage();
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
</head>
<body class="bg-light">
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
      <div class="card-body">
        <h4 class="mb-4 text-center">Login</h4>
        
        <form action="" method="POST">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"  name="email" value="<?= htmlspecialchars($email) ?>" required>
            <?php if (isset($errors['email'])): ?>
              <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="loginPassword" class="form-label">Password</label>
            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"  name="password" required>
            <?php if (isset($errors['password'])): ?>
              <div class="invalid-feedback"><?= $errors['password'] ?></div>
            <?php endif; ?>
          </div>

          <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
          <a href="register.php">Don't have an account? Register</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
