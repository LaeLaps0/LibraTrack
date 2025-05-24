<?php
require_once "db.php";

// Processing form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $middle_initial = $_POST['middle_initial'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $sex = $_POST['sex'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $validation_status = $_POST['validation_status'];

    $result = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'");
    
    if ($result->num_rows > 0) {
        echo "<script>
            alert('Student ID already exists!');
            window.location.href = 'index.php?page=addStudent';
        </script>";
        exit();
    } else {
        $sql = "INSERT INTO students (student_id, first_name, middle_initial, last_name, sex, course, year_level, validation_status)
                    VALUES ('$student_id', '$first_name', '$middle_initial', '$last_name', '$sex', '$course', '$year_level','$validation_status')";
        
        if ($conn->query($sql)) {
            echo "<script>
                alert('Student Successfully Added!');
                window.location.href = 'index.php?page=addStudent';
            </script>";
        } else {
            echo "<script>
                alert('Error: Could not save student.');
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
          <h5 class="mb-0">Add Student Record</h5>
        </div>
        <div class="card-body p-4">
              <form action="index.php?page=addStudent" method="POST" class="needs-validation" novalidate>
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <label for="student_id" class="form-label">Student ID</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control form-control-lg" id="student_id" name="student_id" required>
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

              <div class="mb-4">
                <label for="course" class="form-label">Course</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-journal-code"></i></span>
                  <select class="form-select form-select-sm" id="course" name="course" required>
                    <option value="">-- Select Course --</option>
                    <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                    <option value="Bachelor of Science in Information System">Bachelor of Science in Information System</option>
                    <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                    <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                    <option value="Bachelor of Technical-Vocational Teacher Education">Bachelor of Technical-Vocational Teacher Education</option>
                    <option value="Bachelor of Technology and Livelihood Education">Bachelor of Technology and Livelihood Education</option>
                    <option value="Bachelor of Science in Hospital Management">Bachelor of Science in Hospital Management</option>
                    <option value="Bachelor of Science in Tourism Management">Bachelor of Science in Tourism Management</option>
                    <option value="Bachelor of Science in Entrepreneurship">Bachelor of Science in Entrepreneurship</option>
                    <option value="Bachelor of Science in Industrial Technology">Bachelor of Science in Industrial Technology</option>
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                  <label for="year_level" class="form-label">Year Level</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <select class="form-select form-select-sm" id="year_level" name="year_level" required>
                      <option value="">-- Select Year Level --</option>
                      <option value="1st Year">1st Year</option>
                      <option value="2nd Year">2nd Year</option>
                      <option value="3rd Year">3rd Year</option>
                      <option value="4th Year">4th Year</option>
                    </select>
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
                  <label for="validation_status" class="form-label">Validation Status</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <select class="form-select form-select-sm" id="validation_status" name="validation_status" required>
                      <option value="">-- Select Status --</option>
                      <option value="Validated">Validated</option>
                      <option value="Not Validated">Not Validated</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                  <i class="bi bi-save2 me-1"></i> Save Student
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Example bootstrap validation (optional)
    (() => {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
