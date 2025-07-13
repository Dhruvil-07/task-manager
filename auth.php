<?php
    //no session then stat -> becuase we call this file on every page 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    //current page
    $currentPage = basename($_SERVER['PHP_SELF']);

    //if not login and access protected page  -> navigate index.php
    if( !isset($_SESSION['user_id']) && $currentPage !== "index.php" )
    {
        header("Location: index.php");
        exit;
    }

    //already login then navigate to dashboard page
    if(isset($_SESSION["user_id"]) &&  $currentPage === "index.php" )
    {
        header("Location: dashboard.php");
        exit;
    }
?>