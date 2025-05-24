<?php
require_once "db.php";

// Processing form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = $_POST['faculty_id'];
    $middle_initial = $_POST['middle_initial'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $sex = $_POST['sex'];
    $active_status = $_POST['active_status'];

    $result = $conn->query("SELECT * FROM faculty WHERE faculty_id = '$faculty_id'");
    
    if ($result->num_rows > 0) {
        echo "<script>
            alert('Faculty ID already exists!');
            window.location.href = 'index.php?page=addFaculty';
        </script>";
        exit();
    } else {
        $sql = "INSERT INTO faculty (faculty_id, first_name, middle_initial, last_name, sex, active_status)
                VALUES ('$faculty_id', '$first_name', '$middle_initial', '$last_name', '$sex', '$active_status')";
        
        if ($conn->query($sql)) {
            echo "<script>
                alert('Faculty Successfully Added!');
                window.location.href = 'index.php?page=addFaculty';
            </script>";
        } else {
            echo "<script>
                alert('Error: Could not save faculty.');
            </script>";
        }

        $conn->close();
        exit();
    }
}
?>

<div class="container-fluid mt-2 p-2 custom-container">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-11 col-xxl-10">
      <div class="card shadow rounded-4 border-0 custom-card">
        <div class="card-header bg-primary text-white d-flex align-items-center">
          <i class="bi bi-person-plus-fill me-2 fs-4"></i>
          <h5 class="mb-0">Add Faculty Record</h5>
        </div>
        <div class="card-body p-4">
          <form action="index.php?page=addFaculty" method="POST">
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label for="faculty_id" class="form-label">Faculty ID</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control form-control-lg" id="faculty_id" name="faculty_id" required>
                </div>
              </div>
              <div class="col-md-4">
                <label for="first_name" class="form-label">First Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" required>
                </div>
              </div>
              <div class="col-md-4">
                <label for="middle_initial" class="form-label">Middle Initial</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                  <input type="text" class="form-control form-control-lg" id="middle_initial" name="middle_initial" maxlength="2" required>
                </div>
              </div>
              <div class="col-md-4">
                <label for="last_name" class="form-label">Last Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" required>
                </div>
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label d-block">Sex</label>
                <div class="sex-radio-group">
                  <div class="form-check form-check-inline me-4">
                    <input class="form-check-input" type="radio" name="sex" id="male" value="Male" required>
                    <label class="form-check-label" for="male">Male</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="sex" id="female" value="Female" required>
                    <label class="form-check-label" for="female">Female</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="active_status" class="form-label">Activation Status</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                  <select class="form-select form-select-sm" id="active_status" name="active_status" required>
                    <option value="">-- Select Status --</option>
                    <option value="Active">Active</option>
                    <option value="Not Active">Not Active</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-save2 me-1"></i> Save Faculty
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
