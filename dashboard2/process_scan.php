<?php
session_start();

// Include config file
require_once "db.php";

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // First check in students table
    $sql = "SELECT * FROM students WHERE student_id = '$user_id' AND validation_status = 'Validated'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_type'] = 'student';
        echo "success";
    } else {
        // If not found in students, check faculty table
        $sql = "SELECT * FROM faculty WHERE faculty_id = '$user_id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = 'faculty';
            echo "success";
        } else {
            // Check if it's an unvalidated student
            $sql = "SELECT * FROM students WHERE student_id = '$user_id' AND validation_status != 'Validated'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo "not_validated";
            } else {
                echo "unregistered";
            }
        }
    }
}

$conn->close();
?>
