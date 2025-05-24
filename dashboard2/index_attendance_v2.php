<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Library Card Scanner - Modern Interface</title>
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
        }

        .scanner-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        .scanner-icon {
            font-size: 4rem;
            color: #010049;
            margin-bottom: 1rem;
        }

        .scanner-title {
            color: #010049;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .scanner-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        .scan-area {
            border: 3px dashed #010049;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            background: rgba(1, 0, 73, 0.05);
        }

        #output {
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .error {
            background: #ffebee;
            color: #c62828;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        /* Scanner animation */
        .scan-line {
            height: 2px;
            background: #010049;
            width: 100%;
            position: relative;
            animation: scan 1.5s linear infinite;
        }

        @keyframes scan {
            0% {
                transform: translateY(-50px);
            }
            100% {
                transform: translateY(50px);
            }
        }

        /* Modal styles */
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
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .modal-title {
            color: #c62828;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .modal-text {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .modal-button {
            background: #010049;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .modal-button:hover {
            background: #1a237e;
        }

        /* Status indicator */
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
            background: #4caf50;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <div class="scanner-icon">📱</div>
        <h1 class="scanner-title">Library Card Scanner</h1>
        <p class="scanner-subtitle">Please scan your library card to record attendance</p>
        
        <div class="scan-area">
            <div class="status-indicator"></div>
            <span>Scanner Ready</span>
            <div class="scan-line"></div>
        </div>
        
        <div id="output"></div>
    </div>

    <!-- Modal -->
    <div id="notValidatedModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title">⚠️ Access Denied</h2>
            <p class="modal-text">Your library card is not yet validated. Please contact the librarian.</p>
            <button id="closeModal" class="modal-button">Okay</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let user_id = "";
            const output = document.getElementById("output");

            document.addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    // Show loading state
                    output.innerHTML = "Processing...";
                    output.className = "";

                    fetch("process_scan.php", {
                        method: "POST",
                        body: new URLSearchParams({ user_id: user_id }),
                        headers: { "Content-Type": "application/x-www-form-urlencoded" }
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === "success") {
                            output.textContent = "Success! Redirecting...";
                            output.className = "success";
                            setTimeout(() => {
                                window.location.href = "welcome_v2.php";
                            }, 1000);
                        } else if (data === "not_validated") {
                            document.getElementById("notValidatedModal").style.display = "flex";
                            output.textContent = "";
                        } else if (data === "unregistered") {
                            output.textContent = "Unregistered ID. Please check your ID or contact administrator.";
                            output.className = "error";
                        } else {
                            output.textContent = "An error occurred. Please try again.";
                            output.className = "error";
                        }
                        user_id = "";
                    })
                    .catch(error => {
                        output.textContent = "An error occurred. Please try again.";
                        output.className = "error";
                        user_id = "";
                    });
                } else {
                    user_id += event.key;
                }
            });

            document.getElementById("closeModal").addEventListener("click", function () {
                document.getElementById("notValidatedModal").style.display = "none";
            });
        });
    </script>
</body>
</html> 