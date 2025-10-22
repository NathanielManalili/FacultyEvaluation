<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    $conn = getDBConnection();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already registered.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, 'student')");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.html");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        $_SESSION['form_data'] = $_POST;
        header("Location: signup.html");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: signup.html");
    exit();
}
?>