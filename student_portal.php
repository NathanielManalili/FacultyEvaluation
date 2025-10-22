<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $department = $_POST['department'];
    $faculty_name = $_POST['faculty_name'];
    $subject = trim($_POST['subject']);
    $q1 = $_POST['q1'] ?? null;
    $q2 = $_POST['q2'] ?? null;
    $q3 = $_POST['q3'] ?? null;
    $comments = trim($_POST['comments']);
    
    // Allow anonymous submission - use "Anonymous" if name checkbox is unchecked
    $use_name = isset($_POST['use_name']) ? true : false;
    $student_name = $use_name ? $user_name : 'Anonymous';
    
    // Validation
    if (empty($student_id) || empty($department) || empty($faculty_name) || empty($subject)) {
        $error = "All fields are required.";
    } elseif (!$q1 || !$q2 || !$q3) {
        $error = "Please answer all evaluation questions.";
    } else {
        $conn = getDBConnection();
        
        // Insert evaluation
        $stmt = $conn->prepare("INSERT INTO evaluations (user_id, student_name, student_id, department, faculty_name, subject, question1, question2, question3, comments, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isssssiiss", $_SESSION['user_id'], $student_name, $student_id, $department, $faculty_name, $subject, $q1, $q2, $q3, $comments);
        
        if ($stmt->execute()) {
            $success = "Evaluation submitted successfully!";
        } else {
            $error = "Failed to submit evaluation. Please try again.";
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
    <title>Student Portal - Faculty Evaluation System</title>
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
            <div style="text-align: right; margin-top: 10px;">
                <span style="color: #2d5016;">Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></span> | 
                <a href="logout.php" style="color: #154403ff; text-decoration: none; font-weight: bold;">Logout</a>
            </div>
        </header>

        <!-- Student Portal Section -->
        <div id="student" class="content-section active">
            <h2>Submit Faculty Evaluation</h2>
            <div class="alert alert-success">
                Your feedback helps improve teaching quality
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="student_portal.php">
                <div class="form-group">
                    <label for="student-name">Student Name (Optional)</label>
                    <input type="text" id="student-name" value="<?php echo htmlspecialchars($user_name); ?>" readonly style="background-color: #f0f0f0;">
                    <div style="margin-top: 8px;">
                        <label style="display: inline-flex; align-items: center; cursor: pointer; font-weight: normal;">
                            <input type="checkbox" name="use_name" value="1" style="margin-right: 8px; width: auto;" <?php echo (isset($_POST['use_name']) || !isset($_POST['student_id'])) ? 'checked' : ''; ?>>
                            <span style="color: #666;">Include my name in this evaluation (uncheck to submit anonymously)</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="student-id">Student ID</label>
                    <input type="text" id="student-id" name="student_id" placeholder="Enter your student ID" required value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="department">Department/College</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="CECT" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CECT') ? 'selected' : ''; ?>>CECT - College of Engineering and Computer Technology</option>
                        <option value="CCJE" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CCJE') ? 'selected' : ''; ?>>CCJE - College of Criminal Justice Education</option>
                        <option value="CON" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CON') ? 'selected' : ''; ?>>CON - College of Nursing</option>
                        <option value="CBA" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CBA') ? 'selected' : ''; ?>>CBA - College of Business Administration</option>
                        <option value="CHTM" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CHTM') ? 'selected' : ''; ?>>CHTM - College of Hospitality and Tourism Management</option>
                        <option value="CAS" <?php echo (isset($_POST['department']) && $_POST['department'] === 'CAS') ? 'selected' : ''; ?>>CAS - College of Arts and Sciences</option>
                        <option value="COED" <?php echo (isset($_POST['department']) && $_POST['department'] === 'COED') ? 'selected' : ''; ?>>COED - College of Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty-name">Faculty Member</label>
                    <select id="faculty-name" name="faculty_name" required>
                        <option value="">Select Faculty</option>
                        <option value="prof-santos" <?php echo (isset($_POST['faculty_name']) && $_POST['faculty_name'] === 'prof-santos') ? 'selected' : ''; ?>>Prof. Maria Santos</option>
                        <option value="prof-reyes" <?php echo (isset($_POST['faculty_name']) && $_POST['faculty_name'] === 'prof-reyes') ? 'selected' : ''; ?>>Prof. Juan Reyes</option>
                        <option value="prof-cruz" <?php echo (isset($_POST['faculty_name']) && $_POST['faculty_name'] === 'prof-cruz') ? 'selected' : ''; ?>>Prof. Ana Cruz</option>
                        <option value="prof-garcia" <?php echo (isset($_POST['faculty_name']) && $_POST['faculty_name'] === 'prof-garcia') ? 'selected' : ''; ?>>Prof. Pedro Garcia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject/Course</label>
                    <input type="text" id="subject" name="subject" placeholder="e.g., Computer Programming 101" required value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                </div>

                <div class="card">
                    <h3>Evaluation Questions</h3>
                    
                    <div class="form-group">
                        <label>1. The instructor demonstrates mastery of the subject matter</label>
                        <div class="rating-group">
                            <div class="rating-option">
                                <input type="radio" name="q1" value="5" id="q1-5" required>
                                <label for="q1-5">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q1" value="4" id="q1-4">
                                <label for="q1-4">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q1" value="3" id="q1-3">
                                <label for="q1-3">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q1" value="2" id="q1-2">
                                <label for="q1-2">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q1" value="1" id="q1-1">
                                <label for="q1-1">Poor</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>2. The instructor explains concepts clearly and effectively</label>
                        <div class="rating-group">
                            <div class="rating-option">
                                <input type="radio" name="q2" value="5" id="q2-5" required>
                                <label for="q2-5">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q2" value="4" id="q2-4">
                                <label for="q2-4">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q2" value="3" id="q2-3">
                                <label for="q2-3">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q2" value="2" id="q2-2">
                                <label for="q2-2">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q2" value="1" id="q2-1">
                                <label for="q2-1">Poor</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>3. The instructor is approachable and responsive to student concerns</label>
                        <div class="rating-group">
                            <div class="rating-option">
                                <input type="radio" name="q3" value="5" id="q3-5" required>
                                <label for="q3-5">Excellent</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q3" value="4" id="q3-4">
                                <label for="q3-4">Very Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q3" value="3" id="q3-3">
                                <label for="q3-3">Good</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q3" value="2" id="q3-2">
                                <label for="q3-2">Fair</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" name="q3" value="1" id="q3-1">
                                <label for="q3-1">Poor</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comments">Additional Comments and Suggestions</label>
                    <textarea id="comments" name="comments" placeholder="Share your thoughts and suggestions for improvement..."><?php echo isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn">Submit Evaluation</button>
            </form>
        </div>

    </div>
</body>
</html>