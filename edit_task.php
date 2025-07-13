<?php
require_once("auth.php");
require_once("db.php");
require_once("validation.php");

$id = $_GET["id"]; //task id
$user_id = $_SESSION["user_id"]; //login user id
$task_title = ""; //task title
$task_description = ""; //task description
$task_status = ""; //task status
$errors = []; //array to store validation errrors

//navigate to dashboard when no id provided
if (!$id) {
    header("Location:Dashboard.php");
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
            header("Location: ./dashboard.php");
        }

        $task_data = $result->fetch_assoc();
        $task_title = $task_data["title"];
        $task_description = $task_data["description"];
        $task_status = $task_data["status"];
    } catch (Exception $e) {
        echo $e->getMessage();
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
    if (validateEditTask($task_description,$errors)) {
        try {
            $stmt = $conn->prepare("update tasks set description=? , status=? where id=?");
            if (!$stmt) {
                throw new Exception("Query Error " . $conn->error);
            }
            $stmt->bind_param("ssi", $task_description, $task_status, $id);
            if (!$stmt->execute()) {
                throw new Exception("Execution Error " . $conn->error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } finally {
            if ($stmt) {
                $stmt->close();
            }
        }

        //navigate to dashboard
        header("Location:./dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container-fuild">

    <?php require_once("./navbar.php") ?>
    <h2 class="mb-4">Edit Task</h2>

    <form method="POST" class="border p-4 rounded shadow-sm bg-light">

        <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">

        <!-- task title -->
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($task_title) ?>" readonly>
        </div>

        <!-- task description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" name="description"
                rows="4"><?= htmlspecialchars($task_description) ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <div class="invalid-feedback">
                    <?= $errors['description'] ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- task completed checkbox -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" value="Completed"
                <?= $task_status === 'Completed' ? 'checked' : '' ?>>
            <label class="form-check-label" for="statusCheck">
                Completed
            </label>
        </div>

        <!-- submit button -->
        <button type="submit" name="update" class="btn btn-primary">Update Task</button>
    </form>

</body>

</html>