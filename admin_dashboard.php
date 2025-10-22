<?php
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$success = '';
$error = '';

$conn = getDBConnection();

// Handle Faculty CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ADD FACULTY
    if (isset($_POST['add_faculty'])) {
        $name = trim($_POST['faculty_name']);
        $email = trim($_POST['faculty_email']);
        $department = $_POST['faculty_department'];
        
        if (!empty($name) && !empty($email) && !empty($department)) {
            $stmt = $conn->prepare("INSERT INTO faculty (name, email, department, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
            $stmt->bind_param("sss", $name, $email, $department);
            
            if ($stmt->execute()) {
                $success = "Faculty member added successfully!";
            } else {
                $error = "Failed to add faculty member.";
            }
            $stmt->close();
        } else {
            $error = "All fields are required.";
        }
    }
    
    // UPDATE FACULTY
    if (isset($_POST['update_faculty'])) {
        $id = $_POST['faculty_id'];
        $name = trim($_POST['faculty_name']);
        $email = trim($_POST['faculty_email']);
        $department = $_POST['faculty_department'];
        $status = $_POST['faculty_status'];
        
        if (!empty($name) && !empty($email) && !empty($department)) {
            $stmt = $conn->prepare("UPDATE faculty SET name = ?, email = ?, department = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $department, $status, $id);
            
            if ($stmt->execute()) {
                $success = "Faculty member updated successfully!";
            } else {
                $error = "Failed to update faculty member.";
            }
            $stmt->close();
        }
    }
    
    // DELETE FACULTY
    if (isset($_POST['delete_faculty'])) {
        $id = $_POST['faculty_id'];
        $stmt = $conn->prepare("DELETE FROM faculty WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = "Faculty member deleted successfully!";
        } else {
            $error = "Failed to delete faculty member.";
        }
        $stmt->close();
    }
    
    // ADD QUESTION
    if (isset($_POST['add_question'])) {
        $question_text = trim($_POST['question_text']);
        
        if (!empty($question_text)) {
            // Get the next order number
            $result = $conn->query("SELECT MAX(question_order) as max_order FROM questions");
            $row = $result->fetch_assoc();
            $next_order = ($row['max_order'] ?? 0) + 1;
            
            $stmt = $conn->prepare("INSERT INTO questions (question_text, question_order, is_active, created_at) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("si", $question_text, $next_order);
            
            if ($stmt->execute()) {
                $success = "Question added successfully!";
            } else {
                $error = "Failed to add question.";
            }
            $stmt->close();
        } else {
            $error = "Question text is required.";
        }
    }
    
    // UPDATE QUESTION
    if (isset($_POST['update_question'])) {
        $id = $_POST['question_id'];
        $question_text = trim($_POST['question_text']);
        $is_active = $_POST['is_active'];
        
        if (!empty($question_text)) {
            $stmt = $conn->prepare("UPDATE questions SET question_text = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sii", $question_text, $is_active, $id);
            
            if ($stmt->execute()) {
                $success = "Question updated successfully!";
            } else {
                $error = "Failed to update question.";
            }
            $stmt->close();
        }
    }
    
    // DELETE QUESTION
    if (isset($_POST['delete_question'])) {
        $id = $_POST['question_id'];
        $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = "Question deleted successfully!";
        } else {
            $error = "Failed to delete question.";
        }
        $stmt->close();
    }
}

// Fetch all faculty members
$faculty_list = $conn->query("SELECT * FROM faculty ORDER BY department, name");

// Fetch all questions
$questions_list = $conn->query("SELECT * FROM questions ORDER BY question_order");

// Fetch statistics
$total_evaluations = $conn->query("SELECT COUNT(*) as count FROM evaluations")->fetch_assoc()['count'];
$total_faculty = $conn->query("SELECT COUNT(*) as count FROM faculty WHERE status = 'active'")->fetch_assoc()['count'];
$avg_rating = $conn->query("SELECT AVG((question1 + question2 + question3) / 3) as avg FROM evaluations")->fetch_assoc()['avg'];
$avg_rating = $avg_rating ? number_format($avg_rating, 1) : '0.0';

// Fetch evaluations
$evaluations = $conn->query("SELECT e.*, u.name as student_name FROM evaluations e LEFT JOIN users u ON e.user_id = u.id ORDER BY e.created_at DESC");

// Include the view file
include 'admin_dashboard_view.php';

$conn->close();
?>