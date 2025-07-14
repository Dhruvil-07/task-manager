<!-- utility functions -->
<?php
//utility function for emial valiadtion
function isValidEmail($email)
{

    if (empty($email)) {
        return "Email is required.";
    }

    $length = strlen($email);
    if ($length < 8 || $length > 15) {
        return "Email must be between 8 and 15 characters.";
    }

    return null;

}

//utility function for password validation
function isValidPassword($password)
{
    if (empty($password)) {
        return "Password is required.";
    }

    if (strlen($password) !== 8) {
        return "Password must be exactly 8 characters long.";
    }

    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_|@])[A-Za-z\d_|@]{8}$/";
    if (!preg_match($pattern, $password)) {
        return "Password must include uppercase, lowercase, digit, and one special character (_, |, or @).";
    }

    return null; 
}
?>


<!-- specific page validation function -->
<?php

// registration page validation
function validateRegistration($email, $password, $confirmPassword, &$errors)
{
    $emailValidation = isValidEmail($email);
    if($emailValidation !== null) { 
        $errors["email"] = $emailValidation;
    }

    $passwordValidation = isValidPassword($password);
    if($passwordValidation !== null)
    {
        $errors["password"] = $passwordValidation;
    }

    if ($password !== $confirmPassword) {
        $errors['confirm'] = "Passwords do not match.";
    }

    return empty($errors);
}



//login page validation
function validateLogin($email, $password, &$errors)
{
    $emailValidation = isValidEmail($email);
    if ($emailValidation !== null) {
        $errors['email'] = $emailValidation;
    }

    $passwordValidation = isValidPassword($password);
    if ($passwordValidation !== null) {
        $errors['password'] = $passwordValidation;
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
    $emailValidation = isValidEmail($email);
    if($emailValidation !== null)
    {
        $errors["email"] = $emailValidation;
    }
    return empty($errors);
}

//reset password validation 
function validateResetPassword($new_password, $confirm_password, &$errors)
{
    $passwordValidation = isValidPassword($new_password);
    if($passwordValidation !== null)
    {
        $errors["new_password"] = $passwordValidation;
    }

    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    return empty($errors);
}
?>