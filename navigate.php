<?php

    require_once("./auth.php");

    //common utitlity function for navigation with message
    function Navigate($type,$message,$path = null)
    {
        $_SESSION['alert'] = [
            'type' => $type,
            'message' => $message,
        ];

        if($path)
        {
            header("Location:".$path);
        }
    }
?>