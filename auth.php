<?php
require_once("./navigate.php");

//no session then stat -> becuase we call this file on every page 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//current page
$currentPage = basename($_SERVER['PHP_SELF']);

$publicPages = ["index.php", "register.php", "forgot_password.php", "reset_password.php"];

// direction message for each path
$directionMessages = [
    'index.php' => 'Session is already active.<br>Please logout to login with a different account.',
    'register.php' => 'Session is already active.<br>Please logout to create a new account.',
    'forgot_password.php' => 'Session is already active.<br>Please logout to reset another account\'s password.',
    'reset_password.php' => 'Session is already active.<br>Please logout to continue with password reset.'
];


//if not login and access protected page  -> navigate index.php
if (!isset($_SESSION['user_id']) && !in_array($currentPage, $publicPages)) {
    header("Location: index.php");
    exit;
}

//already login then navigate to dashboard page
if (isset($_SESSION["user_id"]) && in_array($currentPage, $publicPages)) {
    Navigate("danger",$directionMessages[$currentPage],"./dashboard.php");
    exit;
}
?>