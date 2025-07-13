<?php
require_once("./db.php");
require_once('./auth.php');
require_once("./navigate.php");

$tasks = []; //for store all fethched task

//fetch all task handler
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = null;
    try {
        $stmt = $conn->prepare("select * from tasks where user_id=?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC); 
    } catch (Exception $e) {
        //show error alert
        Navigate("danger",$e->getMessage());   
    } finally {
        if (!$stmt) {
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>

    <!-- alert component set -->
    <?php require_once("./alert_component.php") ?>

    <!--  navbar component set -->
    <?php require_once("./navbar.php") ?>

    <!-- new task navigation-->
    <a href="./add_task.php" class="btn btn-primary">+ Add New Task</a>

    <!-- task card -->
    <div class="container mt-4">
        <div class="row g-4">
            <?php if (empty($tasks)): ?>
                <div class="col-12 text-center text-muted">
                    <p>No tasks created.</p>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <?php
                    $status = strtolower($task['status']);
                    $badgeClass = match ($status) {
                        'pending' => 'bg-warning text-dark',
                        'in progress' => 'bg-primary',
                        'completed' => 'bg-success',
                        default => 'bg-secondary',
                    };
                    ?>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($task['title']) ?></h5>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($task['status']) ?>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <strong>Description:</strong>
                                    <div class="border rounded p-2 bg-light"
                                        style="max-height: 100px; overflow-y: auto; font-size: 0.9rem;">
                                        <?= nl2br(htmlspecialchars($task['description'] ?? 'No description')) ?>
                                    </div>
                                </div>

                                <p class="card-text">
                                    <small class="text-muted">Created at: <?= htmlspecialchars($task['created_at']) ?></small>
                                </p>

                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="./edit_task.php?id=<?= $task["id"] ?>"
                                        class="btn btn-outline-primary btn-sm">Edit</a>
                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" data-id="<?= $task['id'] ?>">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- model code -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="delete_task.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this task?
                        <input type="hidden" name="task_id" id="deleteTaskId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

<!-- script to provide task id to model -->
<script>
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const taskId = button.getAttribute('data-id');
        document.getElementById('deleteTaskId').value = taskId;
    });
</script>

</html>