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
        Navigate("danger", $e->getMessage());
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f5f2ff;
        }
    </style>
</head>

<body>

    <!-- Alert Component -->
    <?php require_once("./alert_component.php") ?>

    <!--  Navbar -->
    <?php require_once("./navbar.php") ?>

    <!-- add new task navigation -->
    <div class="container mt-4">
        <div
            class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 border border-primary rounded shadow-sm px-4 py-3 mb-4">
            <h4 class="mb-0 text-primary fw-semibold">
                <i class="bi bi-card-checklist me-2"></i> Your Tasks
            </h4>
            <a href="./add_task.php" class="btn btn-primary fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Add New Task
            </a>
        </div>
    </div>

    <!-- task card container -->
    <div class="container mt-4">
        <div class="row g-4">
            <?php if (empty($tasks)): ?>
                <div class="col-12">
                    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                        <div class="text-center text-muted fs-5">
                            <i class="bi bi-clipboard-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">No tasks assigned yet.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <?php
                    $status = strtolower($task['status']);
                    //set
                    $badgeClass = match ($status) {
                        'pending' => 'bg-warning text-dark',
                        'in progress' => 'bg-info text-dark',
                        'completed' => 'bg-success',
                        default => 'bg-secondary',
                    };

                    // Set card background color based on status
                    $cardBgColor = match ($status) {
                        'completed' => '#d1e7dd',
                        'pending' => '#fff3cd',
                    };
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow rounded-4" style="background-color: <?= $cardBgColor ?>;">
                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title fw-semibold mb-0"><?= htmlspecialchars($task['title']) ?></h5>
                                    <span
                                        class="badge <?= $badgeClass ?> px-3 py-2 text-capitalize"><?= htmlspecialchars($task['status']) ?></span>
                                </div>

                                <div class="mb-4">
                                    <h6 class="mb-2 text-muted">Description</h6>
                                    <div class="border rounded-3 p-3"
                                        style="background-color: #f8f9fa; min-height: 180px; max-height: 180px; overflow-y: auto; font-size: 0.95rem; line-height: 1.4;">
                                        <?= nl2br(htmlspecialchars($task['description'] ?? 'No description')) ?>
                                    </div>
                                </div>

                                <p class="card-text text-muted mb-4">
                                    <small><strong>Created:</strong>
                                        <?= date('d-m-Y', strtotime($task['created_at'])) ?></small>
                                </p>

                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="./edit_task.php?id=<?= $task["id"] ?>"
                                        class="btn btn-outline-primary btn-sm px-3">Edit</a>
                                    <button class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal"
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