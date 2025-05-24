<?php

session_start();

require_once "db.php";

$facultyId = $_GET['facultyId'];

$query = mysqli_query($conn,"DELETE FROM faculty WHERE faculty_id = '$facultyId'");
    // Records created successfully. Redirect to landing page
    echo "<script>
    alert('Faculty with Faculty ID: $facultyId is deleted!');
    window.location.href='index.php?page=viewFaculty';
</script>";             
exit();
    
// close database connection
mysqli_close($conn);
?>