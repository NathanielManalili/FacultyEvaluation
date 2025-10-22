<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: login.html");
        exit();
    }
    
    $conn = getDBConnection();
    
    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Check if user type matches
            if ($user['user_type'] === $user_type) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Redirect based on user type
                if ($user_type === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Invalid user type selected.";
                header("Location: login.html");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.html");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.html");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>