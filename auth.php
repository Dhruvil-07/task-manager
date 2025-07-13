<?php
    //no session then stat -> becuase we call this file on every page 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    //current page
    $currentPage = basename($_SERVER['PHP_SELF']);

    $publicPages = ["index.php", "register.php", "forgot_password.php", "reset_password.php"];

    //if not login and access protected page  -> navigate index.php
    if( !isset($_SESSION['user_id']) &&  !in_array($currentPage, $publicPages))
    {
        header("Location: index.php");
        exit;
    }

    //already login then navigate to dashboard page
    if(isset($_SESSION["user_id"]) &&  in_array($currentPage, $publicPages) )
    {
        header("Location: dashboard.php");
        exit;
    }
?>