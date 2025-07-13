<?php
require_once("./auth.php");
require_once('./db.php');
require_once('./validation.php');
require_once("./navigate.php");

$token = $_GET['token'] ?? '';
$errors = [];
$showForm = false;
$tokenError = '';
$userId = 0;
$new_password = '';
$confirm_password = '';


//token check handler
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($token)) {
    $stmt = null;
    try {
        $stmt = $conn->prepare("select * from users where reset_token = ?");
        if (!$stmt) {
            throw new Exception("Query Error " . $conn->error);
        }

        $stmt->bind_param("s", $token);
        if (!$stmt->execute()) {
            throw new Exception("Execution Error ", $conn->error);
        }
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $expiresAt = $user['token_expiry'];
            $currentTime = date('Y-m-d H:i:s');

            if ($expiresAt >= $currentTime) {
                // Valid token & not expired
                $showForm = true;
                $userId = $user['id'];
            } else {
                $tokenError = "This reset link has expired.";
            }
        } else {
            $tokenError = "Token Not Found";
        }
    } catch (Exception $e) {

    } finally {
        if ($stmt) {
            $stmt->close();
        }
    }

} else if ($_SERVER["REQUEST_METHOD"] === "GET" && empty($token)) {
    $tokenError = "Token Not Provided";
}

//resert password handler
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset_password"])) {
    $showForm = true;
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $userId = $_POST["user_id"];

    if (validateResetPassword($new_password, $confirm_password, $errors)) {
        $stmt = null;
        try {   
            //create hash of password
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("update users set password=?,reset_token=?,token_expiry=? where id=?");
            if (!$stmt) {
                throw new Exception("Query Error " . $conn->error);
            }

            $stmt->bind_param("sssi", $hashedPassword, $null, $null, $userId);
            if (!$stmt->execute()) {
                throw new Exception("Execution Errro ." . $conn->error);
            }

            //navigate to login 
            Navigate("success", "Password Reset Successfully", "./index.php");
            exit;
        } catch (Exception $e) {
            //show errir alert
            Navigate("danger", $e->getMessage());
        } finally {
            if ($stmt) {
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
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Page Wrapper -->
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 500px;">
            <div class="card-body p-4">

                <!-- Title -->
                <h3 class="text-center text-primary fw-semibold mb-4">
                    <i class="bi bi-shield-lock me-2"></i> Reset Your Password
                </h3>

                <!-- Token Error Alert -->
                <?php if (!empty($tokenError)): ?>
                    <div class="alert alert-danger">
                        <?= $tokenError ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <?php if ($showForm): ?>
                    <form method="post">
                        <!-- New Password -->

                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password"
                                class="form-control border border-dark  <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['new_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password"
                                class="form-control border border-dark <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['confirm_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="reset_password" class="btn btn-primary fw-semibold">
                                <i class="bi bi-arrow-repeat me-1"></i> Reset Password
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>