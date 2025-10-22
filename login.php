<?php
require_once 'db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!preg_match('/@wesleyan\.edu\.ph$/i', $email)) {
        $error = "Only @wesleyan.edu.ph email addresses are allowed.";
    } else {
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
                        header("Location: student_portal.php");
                    }
                    exit();
                } else {
                    $error = "Invalid user type selected.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Evaluation System</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="wesleyan-university-philippines-cabanatuan-city-logo-removebg-preview.png" alt="wesleyan-logo">
            </div>
            <h1>Faculty Evaluation System</h1>
            <p>Comprehensive Performance Assessment & Feedback Management</p>
        </header>

        <!-- Login Section -->
        <div id="login" class="content-section active">
            <div class="login-form">
                <h2>Login</h2>
                <div class="alert alert-info">
                    Secure authentication for authorized users only
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="email" placeholder="example@wesleyan.edu.ph" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" pattern="[a-zA-Z0-9._%+-]+@wesleyan\.edu\.ph$" title="Please use a valid @wesleyan.edu.ph email address">
                        <small style="color: #666; font-size: 12px;">Must use @wesleyan.edu.ph email</small>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="form-group">
                        <label for="user-type">Login As</label>
                        <select id="user-type" name="user_type">
                            <option value="student" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Login</button>

                    <p style="text-align: center; margin-top: 25px; color: #2d5016;">
                        Don't have an account? 
                        <a href="signup.php" style="color: #f5d547; text-decoration: none; font-weight: bold;">Sign Up</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>