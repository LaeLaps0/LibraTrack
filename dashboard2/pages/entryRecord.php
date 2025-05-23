<?php include 'db.php'; 

$selectedForm = $_GET['type'] ?? ''; // student or faculty

$councilMapping = [
    "Bachelor of Science in Information Technology" => "Computer Studies",
    "Bachelor of Science in Information System" => "Computer Studies",
    "Bachelor of Elementary Education" => "Education",
    "Bachelor of Secondary Education" => "Education",
    "Bachelor of Technical-Vocational Teacher Education" => "Education",
    "Bachelor of Technology and Livelihood Education" => "Education",
    "Bachelor of Science in Hospital Management" => "HBM",
    "Bachelor of Science in Tourism Management" => "HBM",
    "Bachelor of Science in Entrepreneurship" => "HBM",
    "Bachelor of Science in Industrial Technology" => "BindTech"
];

?>

<div class="container-fluid mt-2 p-2" style="max-width: 1500px;">
    <h3 class="mb-4">Library Entry Records</h3>

    <!-- Selection Buttons -->
    <div class="mb-4">
        <a href="entryRecord.php?type=student" class="btn btn-primary me-2 <?= $selectedForm === 'student' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Student Records
        </a>
        <a href="entryRecord.php?type=faculty" class="btn btn-success <?= $selectedForm === 'faculty' ? 'active' : '' ?>">
            <i class="bi bi-person-workspace"></i> Faculty Records
        </a>
    </div>

    <?php if ($selectedForm === 'student'): ?>
        <!-- Student Records Table -->
        <div class="card shadow rounded-4 border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-journal-text me-2 fs-4"></i>
                <h5 class="mb-0">Student Attendance Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year & Section</th>
                                <th>Council</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT students_attendance.attendance_id, 
                                    students_attendance.date, 
                                    students_attendance.time, 
                                    CONCAT(students.first_name, ' ', students.middle_initial, ' ', students.last_name) AS studentName, 
                                    students.course, 
                                    students.year_section, 
                                    students_attendance.purpose 
                                FROM students_attendance 
                                LEFT JOIN students ON students.student_id = students_attendance.student_id
                                ORDER BY students_attendance.attendance_id DESC";

                            $result = $conn->query($query);

                            while ($row = $result->fetch_assoc()) {
                                $course = $row['course'];
                                $council = $councilMapping[$course] ?? 'N/A';

                                if (isset($councilCounts[$council])) {
                                    $councilCounts[$council]++;
                                }
                                
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . htmlspecialchars(date("h:i A", strtotime($row['time']))) . "</td>
                                    <td>" . htmlspecialchars($row['studentName']) . "</td>
                                    <td>" . htmlspecialchars($course) . "</td>
                                    <td>" . htmlspecialchars($row['year_section']) . "</td>
                                    <td>" . htmlspecialchars($council) . "</td>
                                    <td>" . htmlspecialchars($row['purpose']) . "</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($selectedForm === 'faculty'): ?>
        <!-- Faculty Records Table -->
        <div class="card shadow rounded-4 border-0">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="bi bi-journal-text me-2 fs-4"></i>
                <h5 class="mb-0">Faculty Attendance Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $facultyQuery = "SELECT faculty_attendance.attendance_id, 
                                    faculty_attendance.date, 
                                    faculty_attendance.time, 
                                    CONCAT(faculty.first_name, ' ', faculty.middle_initial, ' ', faculty.last_name) AS facultyName,
                                    faculty.department,
                                    faculty_attendance.purpose 
                                FROM faculty_attendance 
                                LEFT JOIN faculty ON faculty.faculty_id = faculty_attendance.faculty_id
                                ORDER BY faculty_attendance.attendance_id DESC";

                            $facultyResult = $conn->query($facultyQuery);

                            while ($row = $facultyResult->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . htmlspecialchars(date("h:i A", strtotime($row['time']))) . "</td>
                                    <td>" . htmlspecialchars($row['facultyName']) . "</td>
                                    <td>" . htmlspecialchars($row['department']) . "</td>
                                    <td>" . htmlspecialchars($row['purpose']) . "</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Please select a record type to view the attendance records.
        </div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
