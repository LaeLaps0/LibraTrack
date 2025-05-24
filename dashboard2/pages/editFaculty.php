<?php
include 'db.php';

// Get faculty ID from URL
$facultyId = $_GET['facultyId'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_initial = mysqli_real_escape_string($conn, $_POST['middle_initial']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $active_status = mysqli_real_escape_string($conn, $_POST['active_status']);

    $query = "UPDATE faculty SET 
              last_name = '$last_name',
              first_name = '$first_name',
              middle_initial = '$middle_initial',
              sex = '$sex',
              active_status = '$active_status'
              WHERE faculty_id = '$faculty_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Faculty record updated successfully!');
                window.location.href = 'index.php?page=viewFaculty';
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch faculty data
$query = "SELECT * FROM faculty WHERE faculty_id = '$facultyId'";
$result = mysqli_query($conn, $query);
$faculty = mysqli_fetch_assoc($result);

if (!$faculty) {
    echo "<script>
            alert('Faculty not found!');
            window.location.href = 'index.php?page=viewFaculty';
          </script>";
    exit;
}
?>

<div class="container-fluid mt-4 px-4">
    <div class="card shadow rounded-4 border-0">
        <div class="card-header text-white"
             style="background: linear-gradient(90deg, #0d47a1, #0d47a1);">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-pencil-square me-2"></i> Edit Faculty Record
            </h5>
        </div>

        <div class="card-body p-4">
            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="faculty_id" value="<?= htmlspecialchars($faculty['faculty_id']) ?>">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Faculty ID</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($faculty['faculty_id']) ?>" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" 
                               value="<?= htmlspecialchars($faculty['last_name']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" 
                               value="<?= htmlspecialchars($faculty['first_name']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Middle Initial</label>
                        <input type="text" class="form-control" name="middle_initial" 
                               value="<?= htmlspecialchars($faculty['middle_initial']) ?>" maxlength="1">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">Sex</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="male" 
                                   value="male" <?= $faculty['sex'] === 'male' ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sex" id="female" 
                                   value="female" <?= $faculty['sex'] === 'female' ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="active_status" required>
                            <option value="Active" <?= $faculty['active_status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= $faculty['active_status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                    <a href="index.php?page=viewFaculty" class="btn btn-secondary px-4">
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