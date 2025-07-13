<?php
require_once("auth.php");
require_once("db.php");
require_once("navigate.php");


$id = $_POST["task_id"]; //task id
$userId = $_SESSION["user_id"]; // login user id

//delete task handler
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = null;
    try {
        $stmt = $conn->prepare("delete from tasks where id=? and user_id=?");
        if (!$stmt) {
            throw new Exception("Query error : " . $conn->error);
        }

        $stmt->bind_param("ii", $id, $userId);
        if (!$stmt->execute()) {
            throw new Exception("Execution Failed :", $conn->error);
        }

        //set alert message
        Navigate("success","Task Deleted SuccessFully","./dashboard.php");
        exit;
    } catch (Exception $e) {
        //set alert message
        Navigate("danger",$e->getMessage(),"./dashboard.php");
    } finally {
        if (!$stmt) {
            $stmt->close();
        }
    }
}


?>