<?php
require_once("./auth.php");
require_once("./db.php");
require_once("./validation.php");
require_once("./navigate.php");

$id = $_GET["id"]; //task id
$user_id = $_SESSION["user_id"]; //login user id
$task_title = ""; //task title
$task_description = ""; //task description
$task_status = ""; //task status
$errors = []; //array to store validation errrors

//navigate to dashboard when no id provided
if (!$id) {
    //set alert message
    Navigate("danger", "Task ID Not Provided", "./dashboard.php");
    exit;
}

//get task handler
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = null;
    try {
        $stmt = $conn->prepare("select * from tasks where id=? and user_id=?");
        if (!$stmt) {
            throw new Exception("Query Error : " . $conn->error);
        }

        $stmt->bind_param("ii", $id, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Execution Fail : " . $conn->error);
        }

        $result = $stmt->get_result();
        // record not found becuase of the invalide id then redirect to dashboard with error
        if ($result->num_rows === 0) {
            //set alert message
            Navigate("danger", "Invalid Task ID", "./dashboard.php");
            exit;
        }

        $task_data = $result->fetch_assoc();
        $task_title = $task_data["title"];
        $task_description = $task_data["description"];
        $task_status = $task_data["status"];
    } catch (Exception $e) {
        //set alert message
        Navigate("danger", $e->getMessage());
    } finally {
        if (!$stmt) {
            $stmt->close();
        }
    }
}




//edit task handler 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $task_title = trim($_POST["title"]);
    $task_description = trim($_POST["description"]);
    $task_status = isset($_POST["status"]) ? "Completed " : "Pending";
    $stmt = null;

    //validate input and process
    if (validateEditTask($task_description, $errors)) {
        try {
            $stmt = $conn->prepare("update tasks set description=? , status=? where id=?");
            if (!$stmt) {
                throw new Exception("Query Error " . $conn->error);
            }
            $stmt->bind_param("ssi", $task_description, $task_status, $id);
            if (!$stmt->execute()) {
                throw new Exception("Execution Error " . $conn->error);
            }

            //set alert message
            Navigate("success", "Task Update successful!", "./dashboard.php");
            exit;
        } catch (Exception $e) {
            //set alert message
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
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <!-- Alert Component -->
    <?php require_once("./alert_component.php") ?>

    <!-- Navbar -->
    <?php require_once("./navbar.php") ?>

    <!-- Back Button -->
    <div class="container mt-4">
        <a href="./dashboard.php" class="btn btn-outline-secondary mb-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Edit Task Form -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4 text-center text-primary fw-semibold">Edit Task</h4>

                        <form method="POST">

                            <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">

                            <!-- Title (read-only) -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Title</label>
                                <input type="text" class="form-control border border-dark " name="title"
                                    value="<?= htmlspecialchars($task_title) ?>" readonly>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label fw-medium">Description</label>
                                <textarea
                                    class="form-control <?= isset($errors['description']) ? 'is-invalid border-danger' : 'border-dark' ?>"
                                    name="description" rows="4"><?= htmlspecialchars($task_description) ?></textarea>
                                <?php if (isset($errors['description'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errors['description'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Status Checkbox -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="status" id="statusCheck"
                                    value="Completed" <?= $task_status === 'Completed' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="statusCheck">
                                    Mark as Completed
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" name="update" class="btn btn-primary py-2 fw-semibold">
                                    <i class="bi bi-save"></i> Update Task
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