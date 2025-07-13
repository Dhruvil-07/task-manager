<!-- utility functions -->
<?php
//utility function for emial valiadtion
function isValidEmail($email)
{
    return strlen($email) >= 8 && strlen($email) <= 15;
}

//utility function for password validation
function isValidPassword($password)
{
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_|@])[A-Za-z\d_|@]{8}$/";
    return preg_match($pattern, $password);
}
?>


<!-- specific page validation function -->
<?php

// registration page validation
function validateRegistration($email, $password, $confirmPassword, &$errors)
{
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
function validateLogin($email, $password, &$errors)
{
    if (!isValidEmail($email)) {
        $errors['email'] = "Invalid email length.";
    }

    if (!isValidPassword($password)) {
        $errors['password'] = "Invalid password format.";
    }

    return empty($errors);
}

//add task validation 
function validateAddTask($title, $ddescription, &$errors)
{
    if (empty($title)) {
        $errors["title"] = "title is require";
    }

    if (empty($ddescription)) {
        $errors["description"] = "Description is Require";
    }

    return empty($errors);
}

// edit task validation
function validateEditTask($description, &$errors)
{
    if (empty($description)) {
        $errors["description"] = "Please Enter Description";
    }

    return empty($errors);
}

//forget password validation
function validateForgetPassword($email, &$errors)
{
    if (!isValidEmail($email)) {
        $errors['email'] = "Email must be 8–15 characters.";
    }

    return empty($errors);
}

//reset password validation 
function validateResetPassword($new_password,$confirm_password,&$errors)
{
    if (strlen($new_password) !== 8) {
        $errors['new_password'] = "Password must be exactly 8 characters.";
    } elseif (!isValidPassword($new_password)) {
        $errors['new_password'] = "Password must contain uppercase, lowercase, digit, and _ | @.";
    }

    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    return empty($errors);
}
?>