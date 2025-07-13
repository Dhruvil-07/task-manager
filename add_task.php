<?php
require_once("db.php");
require_once('auth.php');
require_once('validation.php');
require_once('navigate.php');

$title = ''; //for store title
$desripption = ''; //for store description
$user_id = $_SESSION["user_id"]; // login user id
$status = "Pending"; //default status
$errors = []; // for store validation errors

//add task handler
if (($_SERVER["REQUEST_METHOD"] === "POST") && (isset($_POST["add_task"]))) {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);

    //validate input and process
    if (validateAddTask($title, $description, $errors)) {
        $stmt = null;
        try {
            $stmt = $conn->prepare("insert into tasks(user_id,title,description,status) values (?,?,?,?)");
            if (!$stmt) {
                throw new Exception("Query error: " . $conn->error);
            }

            $stmt->bind_param("isss", $user_id, $title, $description, $status);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $title = $description = '';

            //set alert message
            Navigate("success", "Register Task Successfully");
        } catch (Exception $e) {
            //set alert navigate
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
    <title>Add New Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <!-- alert component set -->
    <?php include('./alert_component.php'); ?>

    <!-- navbar -->
    <?php include('navbar.php'); ?>

    <!-- back to dashboard button -->
    <div class="container mt-4">
        <a href="./dashboard.php" class="btn btn-outline-secondary mb-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- add task form -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 border border-secondary rounded-3">
                        <h4 class="card-title mb-4 text-center text-primary fw-semibold">Add New Task</h4>

                        <form method="POST" action="">

                            <!-- Task Title -->
                            <div class="mb-3">
                                <label for="task_title" class="form-label fw-medium">Task Title</label>
                                <input type="text"
                                    class="form-control  border border-dark  <?= isset($errors['title']) ? 'is-invalid' : '' ?>" name="title"
                                    id="task_title" value="<?= htmlspecialchars($title ?? '') ?>">
                                <?php if (isset($errors['title'])): ?>
                                    <div class="invalid-feedback"><?= $errors['title'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Task Description -->
                            <div class="mb-3">
                                <label for="task_description" class="form-label fw-medium">Task Description</label>
                                <textarea class="form-control border border-dark  <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                    id="task_description" name="description"
                                    rows="4"><?= htmlspecialchars($description ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" name="add_task" class="btn btn-primary py-2 fw-semibold">
                                    <i class="bi bi-plus-circle"></i> Submit Task
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>