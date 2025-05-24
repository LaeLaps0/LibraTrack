<?php

session_start();

require_once "db.php";

$studentId = $_GET['studentId'];

$query = mysqli_query($conn,"DELETE FROM students WHERE student_id = '$studentId'");
    // Records created successfully. Redirect to landing page
    echo "<script>
    alert('Student with Student ID: $studentId is deleted!');
    window.location.href='index.php?page=viewStudent';
</script>";             
exit();

// close database connection
mysqli_close($conn);
?>