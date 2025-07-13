<?php
require_once("./auth.php");
require_once('./db.php');
require_once('./validation.php');
require_once("./navigate.php");

$email = '';
$resetLink = ''; //fro store reset link
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
                $resetLink = "http://" . $_SERVER["HTTP_HOST"] . "/task-manager/reset_password.php?token=" . $token;

                session_destroy();
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
<html>

<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- alert component set -->
    <?php require_once("./alert_component.php") ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 bg-white shadow rounded p-4">
                <h3 class="mb-4 text-center">Forgot Password</h3>

                <!-- forget password form -->
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Enter your registered email</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            name="email" value="<?= htmlspecialchars($email) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="forgot_password" class="btn btn-primary w-100">Send Reset Link</button>
                </form>

                <!-- reset link after genration -->
                <?php if (!empty($resetLink)): ?>
                    <div class="mt-4 border rounded p-4 bg-light shadow-sm">
                        <h5 class="fw-bold text-success mb-3">🔗 Reset Password Link</h5>
                        <p class="mb-2">
                            A password reset link has been generated for demo purposes.
                            You can click the link below to reset your password.
                        </p>

                        <div class="bg-white border rounded p-3">
                            <a href="<?= htmlspecialchars($resetLink) ?>" class="text-primary text-decoration-underline"
                                target="_blank">
                                <?= htmlspecialchars($resetLink) ?>
                            </a>
                        </div>

                        <small class="text-muted d-block mt-3">
                            ⏳ This link is valid for <strong>24 hours</strong>. <br>
                            ⚠️ This is shown on-screen for demo purposes only.
                        </small>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    </div>
</body>

</html>