<?php
require_once "db.php";

// Define council mapping
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

// Initialize council counts
$councilCounts = [
    "Computer Studies" => 0,
    "Education" => 0,
    "HBM" => 0,
    "BindTech" => 0
];

// Get total count
$totalCount = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Entry Records</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>Student Entry Records</h1>
    </div>

    <?php
    $query = "SELECT students_attendance.attendance_id, 
                    students_attendance.date, 
                    students_attendance.time, 
                    CONCAT(students.first_name, ' ', students.middle_initial, ' ', students.last_name) AS studentName, 
                    students.course, 
                    students.year_level, 
                    students_attendance.purpose 
              FROM students_attendance 
              LEFT JOIN students ON students.student_id = students_attendance.student_id
              ORDER BY students_attendance.attendance_id DESC";

    $result = $conn->query($query);
    
    // Calculate totals
    while ($row = $result->fetch_assoc()) {
        $course = $row['course'];
        $council = $councilMapping[$course] ?? 'N/A';
        
        if (isset($councilCounts[$council])) {
            $councilCounts[$council]++;
            $totalCount++;
        }
        $rows[] = $row;  // Store rows for later display
    }
    ?>

    <div class="stats-container">
        <div class="stat-card">
            <h3><?php echo $totalCount; ?></h3>
            <p>Total Entries</p>
        </div>
        <?php foreach ($councilCounts as $council => $count): ?>
        <div class="stat-card">
            <h3><?php echo $count; ?></h3>
            <p><?php echo htmlspecialchars($council); ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="records-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Council</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($rows)) {
                    foreach ($rows as $row) {
                        $course = $row['course'];
                        $council = $councilMapping[$course] ?? 'N/A';
                        
                        echo "<tr>
                                <td>" . htmlspecialchars($row['date']) . "</td>
                                <td>" . htmlspecialchars(date("h:i A", strtotime($row['time']))) . "</td>
                                <td>" . htmlspecialchars($row['studentName']) . "</td>
                                <td>" . htmlspecialchars($course) . "</td>
                                <td>" . htmlspecialchars($row['year_level']) . "</td>
                                <td>" . htmlspecialchars($council) . "</td>
                                <td>" . htmlspecialchars($row['purpose']) . "</td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
