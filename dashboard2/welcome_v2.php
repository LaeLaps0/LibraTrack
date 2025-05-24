<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: index_attendance_v2.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$is_faculty = ($user_type === 'faculty');

// Retrieve user data based on user type
if ($is_faculty) {
    $sql = "SELECT * FROM faculty WHERE faculty_id = '$user_id'";
} else {
    $sql = "SELECT * FROM students WHERE student_id = '$user_id'";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: index_attendance_v2.php");
    exit();
}

$showModal = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["purpose"])) {
    $purpose = $_POST["purpose"];

    if ($purpose === "Other" && isset($_POST["other_reason"]) && !empty($_POST["other_reason"])) {
        $purpose = $_POST["other_reason"];
    }

    $purpose = mysqli_real_escape_string($conn, $purpose);
    
    if ($is_faculty) {
        $insertSql = "INSERT INTO faculty_attendance (faculty_id, date, time, purpose) VALUES ('$user_id', CURDATE(), CURTIME(), '$purpose')";
    } else {
        $insertSql = "INSERT INTO students_attendance (student_id, date, time, purpose) VALUES ('$user_id', CURDATE(), CURTIME(), '$purpose')";
    }

    if ($conn->query($insertSql) === TRUE) {
        $showModal = true;
    } else {
        echo "<p>Error registering attendance: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Library</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #010049, #1a237e);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .welcome-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #F7C600;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .student-info {
            background: linear-gradient(to right bottom, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
            padding: 1rem;
            border-radius: 20px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .info-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1.5rem;
            margin-bottom: 1.2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .info-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #010049;
            border-radius: 2px;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: 600;
            color: #010049;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
            padding-left: 0.5rem;
        }

        .info-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(1, 0, 73, 0.1);
            border-radius: 6px;
            margin-right: 0.5rem;
        }

        .purpose-form {
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: #010049;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid rgba(1, 0, 73, 0.1);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: white;
        }

        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: #010049;
        }

        button {
            background: #010049;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }

        button:hover {
            background: #1a237e;
        }

        #other-purpose {
            display: none;
            margin-top: 1rem;
        }

        /* Success Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content h2 {
            color: #4CAF50;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .modal-content p {
            color: #666;
            margin-bottom: 1rem;
        }

        /* Icons */
        .icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .faculty-badge {
            background: #F7C600;
            color: #010049;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>

    <script>
        function toggleOtherPurpose() {
            const purposeSelect = document.getElementById('purpose');
            const otherPurpose = document.getElementById('other-purpose');
            otherPurpose.style.display = purposeSelect.value === 'Other' ? 'block' : 'none';
        }

        <?php if ($showModal): ?>
        window.onload = function() {
            const modal = document.getElementById('successModal');
            modal.style.display = 'flex';

            setTimeout(() => {
                window.location.href = 'index_attendance_v2.php';
            }, 2000);
        };
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="welcome-container">
        <div class="header">
            <h1>📚 Welcome to ISATU MC Library</h1>
            <?php if ($is_faculty): ?>
                <div class="faculty-badge">Faculty Member</div>
            <?php endif; ?>
        </div>

        <div class="student-info">
            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">🆔</div>
                    <?php echo $is_faculty ? 'Faculty ID' : 'Student ID'; ?>
                </span>
                <span class="info-value"><?php echo htmlspecialchars($user_id); ?></span>
            </div>

            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">📝</div>
                    Name
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row['first_name'] . " " . $row['middle_initial'] . " " . $row['last_name']); ?></span>
            </div>

            <?php if ($is_faculty): ?>
        

            <?php else: ?>
            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">🎓</div>
                    Course
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row['course'] ?? 'N/A'); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <form class="purpose-form" method="POST">
            <div class="form-group">
                <label class="form-label" for="purpose">Purpose of Visit</label>
                <select name="purpose" id="purpose" required onchange="toggleOtherPurpose()">
                    <option value="">Select Purpose</option>
                    <?php if ($is_faculty): ?>
                    <option value="Research">Research</option>
                    <option value="Preparation">Class Preparation</option>
                    <option value="Meeting">Faculty Meeting</option>
                    <option value="Consultation">Student Consultation</option>
                    <?php else: ?>
                    <option value="Study">Study</option>
                    <option value="Research">Research</option>
                    <option value="Borrow">Borrow Books</option>
                    <option value="Return">Return Books</option>
                    <?php endif; ?>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group" id="other-purpose" style="display: none;">
                <label class="form-label" for="other_reason">Specify Other Purpose</label>
                <input type="text" name="other_reason" id="other_reason">
            </div>

            <button type="submit">Submit Attendance</button>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h2>✅ Attendance Recorded!</h2>
            <p>Redirecting you back to the scanner...</p>
        </div>
    </div>
</body>
</html> 