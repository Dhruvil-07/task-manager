<?php
require_once("./auth.php");
require_once('./db.php');
require_once("./validation.php");
require_once("./navigate.php");

$email = ''; //for store email
$resetLink = ''; //for store reset link
$errors = []; //store validation error

//reset token handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["forgot_password"])) {
    $email = $_POST["email"];

    if (validateForgetPassword($email, $errors)) {
        $checkstmt = null;
        $stmt = null;
        try {

            //flow to check email exist or not
            $checkstmt = $conn->prepare("select * from users where email = ?");
            if (!$checkstmt) {
                throw new Exception("Query Error" . $conn->error);
            }

            $checkstmt->bind_param("s", $email);
            if (!$checkstmt->execute()) {
                throw new Exception("Execution Fail " . $conn->error);
            }
            $result = $checkstmt->get_result();
            $user = $result->fetch_assoc();

            if ($result->num_rows > 0) {
                //genrate token
                $token = bin2hex(random_bytes(12)); // 24-character token

                //expiry date
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

                //flow to add reset token and expiry at database
                $stmt = $conn->prepare("update users set reset_token=? , token_expiry=? where id=?");
                if (!$stmt) {
                    throw new Exception("Query Error", $conn->error);
                }

                $stmt->bind_param("ssi", $token, $expiresAt, $user["id"]);
                if (!$stmt->execute()) {
                    throw new Exception("Execution Fail", $conn->error);
                }

                //genrate reset link
                $resetLink = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER['PHP_SELF']) ."/reset_password.php?token=" . $token;
            } else {
                throw new Exception("No Rocord Found With This Email");
            }
        } catch (Exception $e) {
            //show error alert
            Navigate("danger", $e->getMessage());
        } finally {
            if ($checkstmt) {
                $checkstmt->close();
            }

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
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <!-- Alert Component -->
    <?php require_once("./alert_component.php") ?>

    <!-- Navbar -->
     <?php require_once("./navbar.php") ?>

    <!-- Forgot Password Section -->
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 500px;">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center text-primary fw-semibold">
                    <i class="bi bi-key me-2"></i> Forgot Password
                </h3>

                <!-- Forgot Password Form -->
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Enter your registered email</label>
                        <input type="email" name="email"
                            class="form-control border  <?= isset($errors['email']) ? 'is-invalid border-danger' : 'border-dark' ?>"
                            value="<?= htmlspecialchars($email) ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="forgot_password" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-send me-1"></i> Send Reset Link
                    </button>
                </form>

                <!-- Reset Link Demo Display -->
                <?php if (!empty($resetLink)): ?>
                    <div class="mt-4 border rounded-3 p-4 bg-light shadow-sm">
                        <h5 class="fw-bold text-success mb-2">
                            <i class="bi bi-link-45deg me-1"></i> Reset Password Link
                        </h5>
                        <p class="mb-2">
                            A password reset link has been generated for demo purposes.
                            You can click the link below to reset your password.
                        </p>

                        <div class="bg-white border rounded p-3">
                            <a href="<?= htmlspecialchars($resetLink) ?>" target="_blank"
                                class="text-decoration-underline text-primary">
                                <?= htmlspecialchars($resetLink) ?>
                            </a>
                        </div>

                        <small class="text-muted d-block mt-3">
                            ⏳ This link is valid for <strong>24 hours</strong>.<br>
                            ⚠️ Displayed here for demo only.
                        </small>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>