<?php
require_once("db.php");
require_once('auth.php');
require_once('validation.php');

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

            echo "Task added sucessfully";
            $title = $description = '';
        } catch (Exception $e) {
            echo $e->getMessage();
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <?php include('navbar.php'); ?>

    <!-- New Task Navigation -->
    <a href="./dashboard.php" class="btn btn-primary">Back To Dashboard</a>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Add New Task</h4>

                        <form method="POST" action="">

                            <!-- task title -->
                            <div class="mb-3">
                                <label for="task_title" class="form-label">Task Title</label>
                                <input type="text"
                                    class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" name="title"
                                    id="task_title" value="<?= htmlspecialchars($title ?? '') ?>">
                                <?php if (isset($errors['title'])): ?>
                                    <div class="invalid-feedback"><?= $errors['title'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- task description -->
                            <div class="mb-3">
                                <label for="task_description" class="form-label">Task Description</label>
                                <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                    id="task_description" name="description"
                                    rows="4"><?= htmlspecialchars($description ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                <?php endif; ?>
                            </div>


                            <!-- submit button  -->
                            <div class="d-grid">
                                <button type="submit" name="add_task" class="btn btn-primary">Submit Task</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>