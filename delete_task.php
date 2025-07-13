<?php
    require_once("auth.php");
    require_once("db.php");

    $id = $_POST["task_id"]; //task id
    $userId = $_SESSION["user_id"]; // login user id

    //delete task handler
    if($_SERVER["REQUEST_METHOD"] === "POST")
    {
        $stmt = null;
        try
        {
            $stmt = $conn->prepare("delete from tasks where id=? and user_id=?");
            if(!$stmt)
            {
                throw new Exception("Query error : ".$conn->error);
            }

            $stmt->bind_param("ii",$id,$userId);
            if(!$stmt->execute()) 
            {
                throw new Exception("Execution Failed :" , $conn->error);
            }

            echo "Delete SuccessFully";
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        finally
        {   
            if(!$stmt)
            {
                $stmt->close();
            }
        }
    }

    //navigate to dashboard
    header("Location: ./dashboard.php");
?>