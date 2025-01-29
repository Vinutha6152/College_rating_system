<?php
// Include the database connection file at the top of this file
require 'db.php';

// Registration Logic (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Collect data from the form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Prepare the SQL statement to insert data into the 'users' table
    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$full_name, $email, $password]);
        // Redirect to the homepage or a success page after registration
        header('Location: ../index.html?success=registered');
        exit; // Always call exit after redirect to prevent further code execution
    } catch (PDOException $e) {
        // Handle errors (e.g., duplicate email error or other database issues)
        echo "Error: " . $e->getMessage();
    }
}

// Login Logic (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Collect login data from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement to fetch the user by email
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify if the user exists and the password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Start a session and store user info in session variables
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect to the colleges page after successful login
            header('Location: ../colleges.php');
            exit; // Always call exit after redirect
        } else {
            // Invalid login credentials
            echo "Invalid login credentials";
        }
    } catch (PDOException $e) {
        // Handle database connection or query errors
        echo "Error: " . $e->getMessage();
    }
}
?>
<?php
// auth.php

header('Content-Type: application/json');

$email = $_POST['email'];
$password = $_POST['password'];

// Example validation, replace with your actual login logic
if ($email === 'user@example.com' && $password === 'password123') {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>

