<?php
require_once('./db.php');
require_once('./validation.php');

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

            }
        } catch (Exception $e) {
            echo $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h4 class="mb-4 text-center">Register</h4>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            name="email" value="<?= htmlspecialchars($email) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Password</label>
                        <input type="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" name="password"
                            required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= $errors['password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cnfpassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control <?= isset($errors['confirm']) ? 'is-invalid' : '' ?>"
                            name="cnfpassword" required>
                        <?php if (isset($errors['confirm'])): ?>
                            <div class="invalid-feedback"><?= $errors['confirm'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-success w-100" name="registraction">Register</button>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php">Already have an account? Login</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>