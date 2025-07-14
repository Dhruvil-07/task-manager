# 📝 Task Manager – Core PHP & MySQL
---------------------------------------------
A lightweight Task Manager web application built with **Core PHP**, **MySQL**, and **Bootstrap**.
 Users can register, log in, create and manage tasks, and update their statuses — all with a clean UI and basic security features.

#import .sql file
------------------
Open phpMyAdmin  and simply import the provided taskmanager.sql file.


#databse file Configuration Steps
----------------------------------
1.) Open the db.php file in the project root.

2.) Locate the following code:

	$DBport = "3307"; // 👈 Change this if your MySQL uses a different port
	$host = 'localhost:' . $DBport;
	$dbname = 'taskmanager';
	$username = 'root';         // 👈 Your MySQL username
	$password = '';             // 👈 Your MySQL password

3) Update the values based on your system:

	$DBport → set to your MySQL port (commonly 3306)
	$username → your MySQL username (e.g., root)
	$password → your MySQL password (empty by default on XAMPP)


# Folder Structure (Provided in the Question)
----------------------------------------------
/ (project root)
│
├── index.php              → Login page
├── register.php           → User registration
├── forgot_password.php    → Request password reset
├── reset_password.php     → Reset password with token
├── dashboard.php          → Task list and actions
├── add_task.php           → Add a new task
├── edit_task.php          → Edit a task
├── delete_task.php        → Delete a task
│
├── navbar.php             → Reusable top navigation bar & logout feature
├── db.php                 → Database configuration (MySQL)
├── auth.php               → Session and auth management
├── validation.php         → Email and password validation functions
├── navigate.php           → Utility for redirects with messages
├── alert_component.php    → Bootstrap alert message renderer