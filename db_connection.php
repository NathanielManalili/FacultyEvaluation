<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'FacultyEvaluation');
define('DB_PASS', 'FacultyEvaluation123');
define('DB_NAME', 'faculty_evaluation');

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>