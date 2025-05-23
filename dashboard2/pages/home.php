<?php

require_once 'db.php';


// Fetch total unique student count
$totalStudentsQuery = "SELECT COUNT( student_id) AS total_students FROM students_attendance";
$totalStudentsResult = $conn->query($totalStudentsQuery);
$totalStudents = ($totalStudentsResult->num_rows > 0) ? $totalStudentsResult->fetch_assoc()['total_students'] : 0;

// Fetch total faculty count
$totalFacultyQuery = "SELECT COUNT(DISTINCT faculty_id) AS total_faculty FROM faculty_attendance";
$totalFacultyResult = $conn->query($totalFacultyQuery);
$totalFaculty = ($totalFacultyResult->num_rows > 0) ? $totalFacultyResult->fetch_assoc()['total_faculty'] : 0;

// Fetch top 5 courses based on student attendance
$topCoursesQuery = "SELECT students.course, COUNT( students_attendance.student_id) AS student_count 
                    FROM students 
                    INNER JOIN students_attendance ON students.student_id = students_attendance.student_id 
                    GROUP BY students.course 
                    ORDER BY student_count DESC 
                    LIMIT 5";
$topCoursesResult = $conn->query($topCoursesQuery);

$topCourses = [];
while ($row = $topCoursesResult->fetch_assoc()) {
    $topCourses[] = $row;
}

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
    "Bachelor of Science in Industrial Technology" => "BIT"
];


// Define council mapping
$councilabbrevation = [
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

// Initialize council counts
$councilCounts = ["Computer Studies" => 0, "Education" => 0, "HBM" => 0, "BIT" => 0];

foreach ($topCourses as $row) {
    $course = $row['course'];
    $studentCount = $row['student_count'];

    if (isset($councilMapping[$course])) {
        $councilName = $councilMapping[$course];
        $councilCounts[$councilName] += $studentCount;
    }
}

// Fetch sex distribution
$sexQuery = "SELECT students.sex, COUNT(DISTINCT students_attendance.student_id) AS student_count 
             FROM students
             INNER JOIN students_attendance ON students.student_id = students_attendance.student_id 
             GROUP BY students.sex";
$sexResult = $conn->query($sexQuery);

$maleCount = 0;
$femaleCount = 0;

while ($row = $sexResult->fetch_assoc()) {
    if (strtolower($row['sex']) == "male") {
        $maleCount = $row['student_count'];
    } elseif (strtolower($row['sex']) == "female") {
        $femaleCount = $row['student_count'];
    }
}

// Fetch attendance by time
$timeQuery = "SELECT 
    CASE 
        WHEN TIME(time) BETWEEN '07:00:00' AND '07:59:59' THEN '7 AM'
        WHEN TIME(time) BETWEEN '08:00:00' AND '08:59:59' THEN '8 AM'
        WHEN TIME(time) BETWEEN '09:00:00' AND '09:59:59' THEN '9 AM'
        WHEN TIME(time) BETWEEN '10:00:00' AND '10:59:59' THEN '10 AM'
        WHEN TIME(time) BETWEEN '11:00:00' AND '11:59:59' THEN '11 AM'
        WHEN TIME(time) BETWEEN '12:00:00' AND '12:59:59' THEN '12 PM'
        WHEN TIME(time) BETWEEN '13:00:00' AND '13:59:59' THEN '1 PM'
        WHEN TIME(time) BETWEEN '14:00:00' AND '14:59:59' THEN '2 PM'
        WHEN TIME(time) BETWEEN '15:00:00' AND '15:59:59' THEN '3 PM'
        WHEN TIME(time) BETWEEN '16:00:00' AND '16:59:59' THEN '4 PM'
        WHEN TIME(time) BETWEEN '17:00:00' AND '17:59:59' THEN '5 PM'
        ELSE '6 PM'
    END AS time_range,
    COUNT(*) as count
FROM students_attendance 
GROUP BY time_range
ORDER BY 
    CASE time_range
        WHEN '7 AM' THEN 1
        WHEN '8 AM' THEN 2
        WHEN '9 AM' THEN 3
        WHEN '10 AM' THEN 4
        WHEN '11 AM' THEN 5
        WHEN '12 PM' THEN 6
        WHEN '1 PM' THEN 7
        WHEN '2 PM' THEN 8
        WHEN '3 PM' THEN 9
        WHEN '4 PM' THEN 10
        WHEN '5 PM' THEN 11
        WHEN '6 PM' THEN 12
    END";
$timeResult = $conn->query($timeQuery);

$timeRanges = [];
$timeCounts = [];

while ($row = $timeResult->fetch_assoc()) {
    $timeRanges[] = $row['time_range'];
    $timeCounts[] = $row['count'];
}

// Fetch attendance by date for the last 7 days
$dateQuery = "SELECT 
    date,
    COUNT(*) as count
FROM students_attendance 
WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY date
ORDER BY date";
$dateResult = $conn->query($dateQuery);

$dates = [];
$dateCounts = [];

while ($row = $dateResult->fetch_assoc()) {
    // Format date to be more readable (e.g., "Jan 15")
    $dates[] = date('M d', strtotime($row['date']));
    $dateCounts[] = $row['count'];
}

// Fetch purposes and their counts
$purposeQuery = "SELECT purpose, COUNT(*) as count 
                FROM students_attendance 
                GROUP BY purpose 
                ORDER BY count DESC";
$purposeResult = $conn->query($purposeQuery);

$purposes = [];
$purposeCounts = [];

while ($row = $purposeResult->fetch_assoc()) {
    $purposes[] = $row['purpose'];
    $purposeCounts[] = $row['count'];
}

// Peak Hours Analysis
$peakHoursQuery = "SELECT 
    HOUR(time) as hour,
    COUNT(*) as visit_count,
    ROUND((COUNT(*) / (SELECT COUNT(*) FROM students_attendance)) * 100, 2) as percentage
FROM students_attendance
GROUP BY HOUR(time)
ORDER BY hour";
$peakHoursResult = $conn->query($peakHoursQuery);

$hours = [];
$visitCounts = [];
$percentages = [];

while ($row = $peakHoursResult->fetch_assoc()) {
    $hours[] = date('ga', strtotime($row['hour'] . ':00'));
    $visitCounts[] = $row['visit_count'];
    $percentages[] = $row['percentage'];
}

// Course-wise Weekly Trends
$courseTrendsQuery = "SELECT 
    s.course,
    DATE(sa.date) as visit_date,
    COUNT(*) as visit_count
FROM students_attendance sa
JOIN students s ON s.student_id = sa.student_id
WHERE sa.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY s.course, DATE(sa.date)
ORDER BY visit_date";
$courseTrendsResult = $conn->query($courseTrendsQuery);

$courseTrends = [];
$uniqueDates = [];
$uniqueCourses = [];

while ($row = $courseTrendsResult->fetch_assoc()) {
    if (!in_array($row['visit_date'], $uniqueDates)) {
        $uniqueDates[] = $row['visit_date'];
    }
    if (!in_array($row['course'], $uniqueCourses)) {
        $uniqueCourses[] = $row['course'];
    }
    $courseTrends[$row['course']][$row['visit_date']] = $row['visit_count'];
}

// Most Active Days Analysis
$activeDaysQuery = "SELECT 
    DAYNAME(date) as day_name,
    COUNT(*) as visit_count
FROM students_attendance
GROUP BY DAYNAME(date)
ORDER BY FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$activeDaysResult = $conn->query($activeDaysQuery);

$dayNames = [];
$dayVisits = [];

while ($row = $activeDaysResult->fetch_assoc()) {
    $dayNames[] = substr($row['day_name'], 0, 3);
    $dayVisits[] = $row['visit_count'];
}

$conn->close();
?>

<div class="container-fluid p-4">
  <h2 class="mb-4 text-center fw-bold">
    <i class="bi bi-graph-up"></i> Dashboard Analytics
  </h2>

  <!-- Summary Cards Row -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card bg-primary text-white h-100 shadow-sm rounded-4">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-people-fill display-4 me-3"></i>
          <div>
            <h6 class="mb-0">Total Students</h6>
            <h2 class="mb-0"><?= $totalStudents ?></h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-success text-white h-100 shadow-sm rounded-4">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-person-workspace display-4 me-3"></i>
          <div>
            <h6 class="mb-0">Total Faculty</h6>
            <h2 class="mb-0"><?= $totalFaculty ?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Grid -->
  <div class="row g-3">
    <!-- Sex Distribution -->
    <div class="col-md-4">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-body">
          <h6 class="card-title fw-bold mb-3">
            <i class="bi bi-gender-ambiguous"></i> Sex Distribution
          </h6>
          <div style="height: 200px">
            <canvas id="sexChart"></canvas>
          </div>
          <div class="d-flex justify-content-center mt-3 gap-4">
            <div class="text-center">
              <i class="bi bi-gender-male text-primary"></i>
              <strong class="d-block">Male</strong>
              <span class="text-muted"><?php echo $maleCount; ?></span>
            </div>
            <div class="text-center">
              <i class="bi bi-gender-female text-danger"></i>
              <strong class="d-block">Female</strong>
              <span class="text-muted"><?php echo $femaleCount; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Students by Course -->
    <div class="col-md-4">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-body">
          <h6 class="card-title fw-bold mb-3">
            <i class="bi bi-mortarboard-fill"></i> Students by Course
          </h6>
          <div style="height: 250px">
            <canvas id="councilChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Time Distribution -->
    <div class="col-md-4">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-body">
          <h6 class="card-title fw-bold mb-3">
            <i class="bi bi-clock-history"></i> Time Distribution
          </h6>
          <div style="height: 250px">
            <canvas id="lineChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Date Distribution -->
    <div class="col-md-6">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-body">
          <h6 class="card-title fw-bold mb-3">
            <i class="bi bi-calendar-check"></i> Weekly Attendance
          </h6>
          <div style="height: 250px">
            <canvas id="dateChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Purpose Word Cloud -->
    <div class="col-md-6">
      <div class="card shadow-sm rounded-4 h-100">
        <div class="card-body">
          <h6 class="card-title fw-bold mb-3">
            <i class="bi bi-tags-fill"></i> Visit Purposes
          </h6>
          <div id="wordCloud" style="height: 250px;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- New Analytics Section -->
  <div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-4 text-center fw-bold">
            <i class="bi bi-graph-up-arrow"></i> Advanced Analytics
        </h4>
    </div>
    
    <!-- Peak Hours Analysis -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-3">
                    <i class="bi bi-clock"></i> Peak Hours Analysis
                </h6>
                <div style="height: 300px">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Active Days -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-3">
                    <i class="bi bi-calendar-week"></i> Most Active Days
                </h6>
                <div style="height: 300px">
                    <canvas id="activeDaysChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Course-wise Weekly Trends -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-3">
                    <i class="bi bi-graph-up"></i> Course-wise Weekly Trends
                </h6>
                <div style="height: 400px">
                    <canvas id="courseTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-cloud@1.2.5/build/d3.layout.cloud.min.js"></script>
<script>
    // Update chart options for better fit in smaller containers
    new Chart(document.getElementById("councilChart"), {
      type: 'bar',
      data: {
        labels: ["Computer Studies", "Education", "HBM", "BIT"],
        datasets: [{
          label: ' ',
          data: [<?php echo $councilCounts["Computer Studies"]; ?>, <?php echo $councilCounts["Education"]; ?>, <?php echo $councilCounts["HBM"]; ?>, <?php echo $councilCounts["BIT"]; ?>],
          backgroundColor: ["#FF6384", "#36A2EB", "#4BC0C0", "#FFCE56"]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              font: {
                size: 10
              }
            }
          },
          x: {
            ticks: {
              font: {
                size: 10
              }
            }
          }
        }
      }
    });

    new Chart(document.getElementById("sexChart"), {
      type: 'doughnut',
      data: {
        labels: ["Male", "Female"],
        datasets: [{
          data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
          backgroundColor: ["#36A2EB", "#FF6384"]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '60%',
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    new Chart(document.getElementById("lineChart"), {
      type: 'line',
      data: {
        labels: <?php echo json_encode($timeRanges); ?>,
        datasets: [{
          label: ' ',
          data: <?php echo json_encode($timeCounts); ?>,
          borderColor: '#36A2EB',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              font: {
                size: 10
              }
            }
          },
          x: {
            ticks: {
              font: {
                size: 10
              }
            }
          }
        }
      }
    });

    new Chart(document.getElementById("dateChart"), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
          label: 'Daily Visits',
          data: <?php echo json_encode($dateCounts); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              font: {
                size: 10
              }
            }
          },
          x: {
            ticks: {
              font: {
                size: 10
              }
            }
          }
        }
      }
    });

    // Word cloud with adjusted size
    const wordCloudData = <?php 
        $cloudData = array_map(function($purpose, $count) {
            return [
                'text' => $purpose,
                'size' => sqrt($count) * 20 + 20, // Scale the size
                'value' => $count
            ];
        }, $purposes, $purposeCounts);
        echo json_encode($cloudData);
    ?>;
    const color = d3.scaleOrdinal()
        .range(['#2E86C1', '#3498DB', '#85C1E9', '#AED6F1', 
                '#E67E22', '#F39C12', '#27AE60', '#2ECC71']);

    const layout = d3.layout.cloud()
        .size([document.getElementById('wordCloud').offsetWidth, 250])
        .words(wordCloudData)
        .padding(15)
        .rotate(0)
        .fontSize(d => Math.min(d.size * 0.7, 30))
        .on("end", draw);

    layout.start();

    function draw(words) {
        d3.select("#wordCloud svg").remove();
        
        const svg = d3.select("#wordCloud")
            .append("svg")
            .attr("width", '100%')
            .attr("height", '100%')
            .attr("viewBox", `0 0 ${layout.size()[0]} ${layout.size()[1]}`)
            .append("g")
            .attr("transform", `translate(${layout.size()[0] / 2},${layout.size()[1] / 2})`);

        const wordGroups = svg.selectAll("g")
            .data(words)
            .enter()
            .append("g")
            .attr("transform", d => `translate(${d.x},${d.y})`);

        wordGroups.append("circle")
            .attr("r", d => d.size / 2)
            .style("fill", (d, i) => color(i))
            .style("opacity", 0.8)
            .style("stroke", "white")
            .style("stroke-width", "1px");

        wordGroups.append("text")
            .style("font-size", d => `${d.size * 0.7}px`)
            .style("font-family", "Arial")
            .style("font-weight", "bold")
            .style("fill", "white")
            .attr("text-anchor", "middle")
            .attr("dominant-baseline", "middle")
            .text(d => d.text)
            .append("title")
            .text(d => `${d.text}: ${d.value} visits`);

        function resize() {
            const width = document.getElementById('wordCloud').offsetWidth;
            layout.size([width, 250]).start();
        }

        window.addEventListener('resize', resize);
    }

    // Peak Hours Chart
    new Chart(document.getElementById("peakHoursChart"), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($hours); ?>,
            datasets: [{
                label: 'Visit Count',
                data: <?php echo json_encode($visitCounts); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Percentage',
                data: <?php echo json_encode($percentages); ?>,
                type: 'line',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: false,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Visit Count'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Percentage'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Active Days Chart
    new Chart(document.getElementById("activeDaysChart"), {
        type: 'radar',
        data: {
            labels: <?php echo json_encode($dayNames); ?>,
            datasets: [{
                label: 'Visit Count',
                data: <?php echo json_encode($dayVisits); ?>,
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            elements: {
                line: {
                    borderWidth: 3
                }
            }
        }
    });

    // Course Trends Chart
    new Chart(document.getElementById("courseTrendsChart"), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_map(function($date) { 
                return date('M d', strtotime($date)); 
            }, $uniqueDates)); ?>,
            datasets: <?php 
                $datasets = [];
                $colors = [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ];
                foreach ($uniqueCourses as $index => $course) {
                    $data = [];
                    foreach ($uniqueDates as $date) {
                        $data[] = isset($courseTrends[$course][$date]) ? $courseTrends[$course][$date] : 0;
                    }
                    $datasets[] = [
                        'label' => $councilabbrevation[$course] ?? $course,
                        'data' => $data,
                        'borderColor' => $colors[$index % count($colors)],
                        'fill' => false,
                        'tension' => 0.4
                    ];
                }
                echo json_encode($datasets);
            ?>
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Weekly Attendance Trends by Course'
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
</script>