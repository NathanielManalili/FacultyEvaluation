<?php
require_once 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/@wesleyan\.edu\.ph$/i', $email)) {
        $error = "Only @wesleyan.edu.ph email addresses are allowed.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/', $password)) {
        $error = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, 'student')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Registration failed. Please try again.";
            }
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
    <title>Sign Up - Faculty Evaluation System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <header>
            <div class="logo">
                <img src="wesleyan-university-philippines-cabanatuan-city-logo-removebg-preview.png" alt="Faculty Evaluation System Logo">
            </div>
            <h1>Faculty Evaluation System</h1>
            <p>Register to access your account</p>
        </header>

        <!-- SIGNUP SECTION -->
        <div id="signup" class="content-section signup active">
            <div class="signup-form">
                <h2>Create an Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="signup.php">
                    <div class="form-group">
                        <label for="signup-name">Full Name</label>
                        <input type="text" id="signup-name" name="name" placeholder="Enter your full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="signup-email">Email</label>
                        <input type="email" id="signup-email" name="email" placeholder="example@wesleyan.edu.ph" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" pattern="[a-zA-Z0-9._%+-]+@wesleyan\.edu\.ph$" title="Please use a valid @wesleyan.edu.ph email address">
                        <small style="color: #666; font-size: 12px;">Must use @wesleyan.edu.ph email</small>
                    </div>

                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <input type="password" id="signup-password" name="password" placeholder="Create a password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$" title="Password must be at least 8 characters with uppercase, lowercase, number, and special character">
                        <small style="color: #666; font-size: 12px;">Min 8 chars: uppercase, lowercase, number, special char (@$!%*?&#)</small>
                    </div>

                    <div class="form-group">
                        <label for="signup-confirm">Confirm Password</label>
                        <input type="password" id="signup-confirm" name="confirm_password" placeholder="Re-enter your password" required>
                    </div>

                    <div class="btn_signup">
                        <button type="submit" class="btn">Sign Up</button>
                    </div>

                    <p style="text-align: center; margin-top: 25px; color: #2d5016;">
                        Already have an account? 
                        <a href="login.php" style="color: #f5d547; text-decoration: none; font-weight: bold;">Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>