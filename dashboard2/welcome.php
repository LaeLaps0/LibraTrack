<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: index_attendance.php");
    exit();
}

require_once "db.php";

$student_id = $_SESSION['student_id'];

// Retrieve student data
$sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    header("Location: index_attendance.php");
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

    $insertSql = "INSERT INTO students_attendance (student_id, date, time, purpose) VALUES ('$student_id', CURDATE(), CURTIME(), '$purpose')";

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
    <title>Welcome Page</title>
    <style>
        .background-wrapper {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100vh;
            overflow: hidden;
        }

        .background-wrapper img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
            filter: blur(8px);
        }

        .info-box {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            padding: 30px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            text-align: center;
            border-radius: 10px;
            font-size: 20px;
        }

        h1 { font-size: 36px; margin-bottom: 10px; }
        p { font-size: 24px; }

        select, button, input {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 18px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #other-purpose {
            display: none;
            margin-top: 10px;
            width: 80%;
            padding: 10px;
            font-size: 18px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Modal styles */
        #successModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 9999;
        }

        #successModal .modal-content {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        #successModal h2 {
            margin-bottom: 15px;
            color: Blue;
        }

        .modal-content h2 {
            color: Blue;
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
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
            modal.style.display = 'block';

            setTimeout(() => {
                window.location.href = 'index_attendance.php';
            }, 1000);
        };
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="background-wrapper">
        <img src="assets/img/isatu.jpg" alt="Background">
    </div>

    <div class="info-box">
        <h1>Welcome to Library!</h1>
        <p><strong>ID No:</strong> <?php echo htmlspecialchars($row["student_id"]); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($row["first_name"] . " " . $row["middle_initial"] . " " . $row["last_name"]); ?></p>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($row["course"]); ?></p>

        <form action="" method="post">
            <label for="purpose"><strong>Select Purpose:</strong></label>
            <select name="purpose" id="purpose" onchange="checkPurpose()" required>
                 <option value="" disabled selected hidden>Choose Purpose</option>
                <option value="Study">Study</option>
                <option value="Borrow Books">Borrow Book</option>
                <option value="Research">Research</option>
                <option value="Other">Other</option>
            </select>

            <input type="text" name="other_reason" id="other-purpose" placeholder="Enter your purpose">
            <button type="submit">Submit Purpose</button>
        </form>
    </div>

    <!-- Modal -->
    <div id="successModal">
        <div class="modal-content">
            <h2>Attendance Recorded!</h2>
        </div>
    </div>
</body>
</html>
