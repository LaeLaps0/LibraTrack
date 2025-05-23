<?php
include 'db.php';

// Course abbreviations
$courseAbbreviations = [
    "Bachelor of Science in Information Technology" => "BSIT",
    "Bachelor of Science in Information System" => "BSIS",
    "Bachelor of Elementary Education" => "BEED",
    "Bachelor of Secondary Education" => "BSED",
    "Bachelor of Technical-Vocational Teacher Education" => "BTVTED",
    "Bachelor of Technology and Livelihood Education" => "BTLED",
    "Bachelor of Science in Hospital Management" => "BSHM",
    "Bachelor of Science in Tourism Management" => "BSTM",
    "Bachelor of Science in Entrepreneurship" => "BSENTREP",
    "Bachelor of Science in Industrial Technology" => "BIT"
];

// Get filter input with defaults
$start_date = $_POST['start_date'] ?? date('Y-m-01', strtotime('-3 months'));
$end_date = $_POST['end_date'] ?? date('Y-m-t');
$group_by = $_POST['group_by'] ?? 'month';

// Define groupings
switch ($group_by) {
    case 'day':
        $date_format = '%Y-%m-%d';
        $label = 'Daily';
        break;
    case 'week':
        $date_format = '%Y-%u';
        $label = 'Weekly';
        break;
    case 'month':
    default:
        $date_format = '%Y-%m';
        $label = 'Monthly';
        break;
}

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify table structure
$check_table = mysqli_query($conn, "DESCRIBE students_attendance");
if (!$check_table) {
    die("Table structure error: " . mysqli_error($conn));
}

// Query for attendance data
$query = "
    SELECT 
        DATE_FORMAT(date, ?) as period,
        s.course,
        COUNT(sa.student_id) as visit_count
    FROM students_attendance sa
    JOIN students s ON sa.student_id = s.student_id
    WHERE sa.date BETWEEN ? AND ?
    GROUP BY period, s.course
    ORDER BY period, s.course
";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "sss", $date_format, $start_date, $end_date);
if (!mysqli_stmt_execute($stmt)) {
    die("Query execution failed: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    die("Failed to get result: " . mysqli_stmt_error($stmt));
}

// Initialize arrays
$periods = [];
$courses = [];
$data = [];

// Organize data by period and course
while ($row = mysqli_fetch_assoc($result)) {
    $period = $row['period'];
    $course = $row['course'];
    
    if (!in_array($period, $periods)) {
        $periods[] = $period;
    }
    if (!in_array($course, $courses)) {
        $courses[] = $course;
    }
    
    $data[$period][$course] = $row['visit_count'];
}

// Calculate totals
$period_totals = [];
$course_totals = [];
foreach ($periods as $period) {
    $period_totals[$period] = 0;
    foreach ($courses as $course) {
        if (isset($data[$period][$course])) {
            $period_totals[$period] += $data[$period][$course];
            if (!isset($course_totals[$course])) {
                $course_totals[$course] = 0;
            }
            $course_totals[$course] += $data[$period][$course];
        }
    }
}

// Calculate grand total from course totals
$grand_total = array_sum($course_totals);

?>

    <div class="dashboard-header">
        <div class="container">
            <h1 class="mb-0"><i class="bi bi-graph-up"></i> Library Attendance Report</h1>
        </div>
    </div>

    <div class="container mb-4">
        <div class="filters">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-calendar3"></i> Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-calendar3"></i> End Date</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="bi bi-grid"></i> Group By</label>
                    <select class="form-select" name="group_by">
                        <option value="day" <?php if ($group_by == 'day') echo 'selected'; ?>>Daily</option>
                        <option value="week" <?php if ($group_by == 'week') echo 'selected'; ?>>Weekly</option>
                        <option value="month" <?php if ($group_by == 'month') echo 'selected'; ?>>Monthly</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Generate
                    </button>
                </div>
            </form>
        </div>

        
        <div class="chart-container"> 
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0"><i class="bi bi-table"></i> Attendance Report</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-table">
                    <thead>
                        <tr>
                            <th><?php echo $label; ?></th>
                            <?php foreach ($courses as $course): ?>
                            <th title="<?php echo htmlspecialchars($course); ?>"><?php echo htmlspecialchars($courseAbbreviations[$course] ?? $course); ?></th>
                            <?php endforeach; ?>
                            <th class="table-primary">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($periods as $period): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($period); ?></td>
                            <?php foreach ($courses as $course): ?>
                            <td><?php echo number_format($data[$period][$course] ?? 0); ?></td>
                            <?php endforeach; ?>
                            <td class="table-primary fw-bold"><?php echo number_format($period_totals[$period]); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-secondary fw-bold">
                            <td>Total</td>
                            <?php foreach ($courses as $course): ?>
                            <td><?php echo number_format($course_totals[$course]); ?></td>
                            <?php endforeach; ?>
                            <td class="table-primary"><?php echo number_format($grand_total); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <div class="chart-container">
                    <h5 class="card-title mb-4"><i class="bi bi-graph-up"></i> Attendance Trends</h5>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="card-title mb-4"><i class="bi bi-bar-chart"></i> Course Distribution</h5>
                    <canvas id="courseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Trend Chart - Line chart showing attendance over time
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($periods); ?>,
                datasets: [
                    <?php foreach ($courses as $index => $course): ?>
                    {
                        label: '<?php echo $courseAbbreviations[$course] ?? $course; ?>',
                        data: [<?php 
                            $courseData = [];
                            foreach ($periods as $period) {
                                $courseData[] = $data[$period][$course] ?? 0;
                            }
                            echo implode(',', $courseData);
                        ?>],
                        borderColor: getColor(<?php echo $index; ?>),
                        backgroundColor: getColor(<?php echo $index; ?>, 0.1),
                        tension: 0.4,
                        fill: false
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Library Visits by Course Over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Visits'
                        }
                    }
                }
            }
        });

        // Course Chart - Bar chart showing total visits by course
        new Chart(document.getElementById('courseChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function($course) use ($courseAbbreviations) {
                    return $courseAbbreviations[$course] ?? $course;
                }, $courses)); ?>,
                datasets: [{
                    label: 'Total Visits',
                    data: <?php echo json_encode(array_values($course_totals)); ?>,
                    backgroundColor: Array(<?php echo count($courses); ?>).fill().map((_, i) => getColor(i, 0.7)),
                    borderColor: Array(<?php echo count($courses); ?>).fill().map((_, i) => getColor(i)),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Total Visits by Course'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Visits'
                        }
                    }
                }
            }
        });

        // Color generator function
        function getColor(index, alpha = 1) {
            const colors = [
                `rgba(0, 123, 255, ${alpha})`,     // Blue
                `rgba(40, 167, 69, ${alpha})`,     // Green
                `rgba(220, 53, 69, ${alpha})`,     // Red
                `rgba(255, 193, 7, ${alpha})`,     // Yellow
                `rgba(111, 66, 193, ${alpha})`,    // Purple
                `rgba(23, 162, 184, ${alpha})`,    // Cyan
                `rgba(253, 126, 20, ${alpha})`,    // Orange
                `rgba(102, 16, 242, ${alpha})`,    // Indigo
                `rgba(32, 201, 151, ${alpha})`,    // Teal
                `rgba(233, 30, 99, ${alpha})`      // Pink
            ];
            return colors[index % colors.length];
        }
    </script>
