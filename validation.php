
<!-- utility functions -->
<?php
//utility function for emial valiadtion
function isValidEmail($email) {
    return strlen($email) >= 8 && strlen($email) <= 15;
}

//utility function for password validation
function isValidPassword($password) {
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_|@])[A-Za-z\d_|@]{8}$/";
    return preg_match($pattern, $password);
}
?>


<!-- specific page validation function -->
<?php

// registration page validation
function validateRegistration($email, $password, $confirmPassword, &$errors) {
    if (!isValidEmail($email)) {
        $errors['email'] = "Email must be 8–15 characters.";
    }

    if (strlen($password) !== 8) {
        $errors['password'] = "Password must be exactly 8 characters.";
    } elseif (!isValidPassword($password)) {
        $errors['password'] = "Password must contain uppercase, lowercase, digit, and _ | @.";
    }

    if ($password !== $confirmPassword) {
        $errors['confirm'] = "Passwords do not match.";
    }

    return empty($errors);
}



//login page validation
function validateLogin($email, $password, &$errors) {
    if (!isValidEmail($email)) {
        $errors['email'] = "Invalid email length.";
    }

    if (!isValidPassword($password)) {
        $errors['password'] = "Invalid password format.";
    }

    return empty($errors);
}
?>