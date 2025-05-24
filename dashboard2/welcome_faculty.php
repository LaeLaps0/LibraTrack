<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index_attendance_v2.php");
    exit();
}

require_once "db.php";

$faculty_id = $_SESSION['user_id'];

// Retrieve faculty data
$sql = "SELECT * FROM faculty WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

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

    $insertSql = "INSERT INTO faculty_attendance (faculty_id, date, time, purpose) VALUES (?, CURDATE(), CURTIME(), ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ss", $faculty_id, $purpose);
    
    if ($stmt->execute()) {
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
            background: linear-gradient(135deg, #1a237e, #0d47a1);
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
            color: #1a237e;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .faculty-info {
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
            background: #1a237e;
            border-radius: 2px;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: 600;
            color: #1a237e;
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
            background: rgba(26, 35, 126, 0.1);
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
            color: #1a237e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid rgba(26, 35, 126, 0.1);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: white;
        }

        select:focus, input[type="text"]:focus {
            outline: none;
            border-color: #1a237e;
        }

        button {
            background: #1a237e;
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
            background: #0d47a1;
        }

        #other-purpose {
            display: none;
            margin-top: 1rem;
        }

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
    </style>

    <script>
        function checkPurpose() {
            var purpose = document.getElementById("purpose").value;
            var otherInput = document.getElementById("other-purpose");
            
            if (purpose === "Other") {
                otherInput.style.display = "block";
            } else {
                otherInput.style.display = "none";
            }
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
        </div>

        <div class="faculty-info">
            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">🆔</div>
                    Faculty ID
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row["faculty_id"]); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">👤</div>
                    Full Name
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row["first_name"] . " " . $row["middle_initial"] . " " . $row["last_name"]); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">
                    <div class="info-icon">📊</div>
                    Status
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row["status"]); ?></span>
            </div>
        </div>

        <form class="purpose-form" action="" method="post">
            <div class="form-group">
                <label class="form-label" for="purpose">Select Purpose</label>
                <select name="purpose" id="purpose" onchange="checkPurpose()" required>
                    <option value="" disabled selected hidden>Choose your purpose</option>
                    <option value="Research">🔍 Research</option>
                    <option value="Borrow Books">📖 Borrow Books</option>
                    <option value="Meeting">👥 Meeting</option>
                    <option value="Consultation">💬 Consultation</option>
                    <option value="Other">✏️ Other</option>
                </select>

                <input type="text" name="other_reason" id="other-purpose" placeholder="Please specify your purpose">
            </div>

            <button type="submit">Submit Purpose</button>
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