<?php
include 'db.php';

// Get student ID from URL
$studentId = $_GET['studentId'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
    $validation_status = mysqli_real_escape_string($conn, $_POST['validation_status']);

    $query = "UPDATE students SET 
              last_name = '$last_name',
              first_name = '$first_name',
              middle_initial = '$middle_initial',
              sex = '$sex',
              course = '$course',
              year_level = '$year_level',
              validation_status = '$validation_status'
              WHERE student_id = '$student_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Student record updated successfully!');
                window.location.href = 'index.php?page=viewStudent';
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch student data
$query = "SELECT * FROM students WHERE student_id = '$studentId'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    echo "<script>
            alert('Student not found!');
            window.location.href = 'index.php?page=viewStudent';
          </script>";
    exit;
}
?>

<div class="container-fluid mt-4 px-4">
    <div class="card shadow rounded-4 border-0">
        <div class="card-header text-white"
             style="background: linear-gradient(90deg, #0d47a1, #0d47a1);">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-pencil-square me-2"></i> Edit Student Record
            </h5>
        </div>

        <div class="card-body p-4">
            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Student ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_id']) ?>" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" 
                               value="<?= htmlspecialchars($student['last_name']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" 
                               value="<?= htmlspecialchars($student['first_name']) ?>" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Middle Initial</label>
                        <input type="text" class="form-control" name="middle_initial" 
                               value="<?= htmlspecialchars($student['middle_initial']) ?>" maxlength="1">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sex</label>
                        <select class="form-select" name="sex" required>
                            <option value="male" <?= $student['sex'] === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= $student['sex'] === 'female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Course</label>
                        <select class="form-select" name="course" required>
    <option value="">-- Select Course --</option>
    <option value="Bachelor of Science in Information Technology" <?= $student['course'] === 'Bachelor of Science in Information Technology' ? 'selected' : '' ?>>Bachelor of Science in Information Technology</option>
    <option value="Bachelor of Science in Information System" <?= $student['course'] === 'Bachelor of Science in Information System' ? 'selected' : '' ?>>Bachelor of Science in Information System</option>
    <option value="Bachelor of Elementary Education" <?= $student['course'] === 'Bachelor of Elementary Education' ? 'selected' : '' ?>>Bachelor of Elementary Education</option>
    <option value="Bachelor of Secondary Education" <?= $student['course'] === 'Bachelor of Secondary Education' ? 'selected' : '' ?>>Bachelor of Secondary Education</option>
    <option value="Bachelor of Technical-Vocational Teacher Education" <?= $student['course'] === 'Bachelor of Technical-Vocational Teacher Education' ? 'selected' : '' ?>>Bachelor of Technical-Vocational Teacher Education</option>
    <option value="Bachelor of Technology and Livelihood Education" <?= $student['course'] === 'Bachelor of Technology and Livelihood Education' ? 'selected' : '' ?>>Bachelor of Technology and Livelihood Education</option>
    <option value="Bachelor of Science in Hospital Management" <?= $student['course'] === 'Bachelor of Science in Hospital Management' ? 'selected' : '' ?>>Bachelor of Science in Hospital Management</option>
    <option value="Bachelor of Science in Tourism Management" <?= $student['course'] === 'Bachelor of Science in Tourism Management' ? 'selected' : '' ?>>Bachelor of Science in Tourism Management</option>
    <option value="Bachelor of Science in Entrepreneurship" <?= $student['course'] === 'Bachelor of Science in Entrepreneurship' ? 'selected' : '' ?>>Bachelor of Science in Entrepreneurship</option>
    <option value="Bachelor of Science in Industrial Technology" <?= $student['course'] === 'Bachelor of Science in Industrial Technology' ? 'selected' : '' ?>>Bachelor of Science in Industrial Technology</option>
</select>

                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Year Level</label>
                        <select class="form-select" name="year_level" required>
                            <option value="1st" <?= $student['year_level'] === '1st' ? 'selected' : '' ?>>1st Year</option>
                            <option value="2nd" <?= $student['year_level'] === '2nd' ? 'selected' : '' ?>>2nd Year</option>
                            <option value="3rd" <?= $student['year_level'] === '3rd' ? 'selected' : '' ?>>3rd Year</option>
                            <option value="4th" <?= $student['year_level'] === '4th' ? 'selected' : '' ?>>4th Year</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Validation Status</label>
                        <select class="form-select" name="validation_status" required>
                            <option value="Validated" <?= $student['validation_status'] === 'Validated' ? 'selected' : '' ?>>Validated</option>
                            <option value="Not Validated" <?= $student['validation_status'] === 'Not Validated' ? 'selected' : '' ?>>Not Validated</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                    <a href="index.php?page=viewStudent" class="btn btn-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0d47a1;
    box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.25);
}

.btn-primary {
    background-color: #0d47a1;
    border-color: #0d47a1;
}

.btn-primary:hover {
    background-color: #093777;
    border-color: #093777;
}
</style> 