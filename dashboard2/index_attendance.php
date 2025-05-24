<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Card Scanner</title>
  <style>
    .background-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      overflow: hidden;
    }

    .background-wrapper img {
      width: 100%;
      height: 100vh;
      object-fit: cover;
      filter: blur(8px);
    }

    .moving-text {
      position: absolute;
      top: 40%;
      white-space: nowrap;
      font-size: 80px;
      font-weight: bold;
      color: white;
      padding: 10px;
      border-radius: 5px;
      animation: moveText 8s linear infinite;
    }

    @keyframes moveText {
      from {
        right: -100%;
      }
      to {
        right: 100%;
      }
    }

    #output {
      position: absolute;
      top: 70%;
      width: 100%;
      text-align: center;
      font-size: 24px;
      color: red;
      font-weight: bold;
    }

    /* Modal for not validated */
    #notValidatedModal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.75);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 10;
    }

    .modal-content {
      background: white;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 0 20px black;
      max-width: 400px;
    }

    .modal-content h2 {
      color: red;
      margin-bottom: 15px;
      font-size: 20px;
    }

    .modal-content button {
      padding: 10px 20px;
      background-color: #010049;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let student_id = "";
      document.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
          fetch("process_scan.php", {
            method: "POST",
            body: new URLSearchParams({ student_id: student_id }),
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
          })
          .then(response => response.text())
          .then(data => {
            if (data === "success") {
              window.location.href = "welcome.php";
            } else if (data === "not_validated") {
              document.getElementById("notValidatedModal").style.display = "flex";
            } else {
              document.getElementById("output").innerHTML = data;
            }
            student_id = "";
          });
        } else {
          student_id += event.key;
        }
      });

      document.getElementById("closeModal").addEventListener("click", function () {
        document.getElementById("notValidatedModal").style.display = "none";
      });
    });
  </script>
</head>
<body>
  <div class="background-wrapper">
    <img src="assets/img/isatu.jpg" alt="Background" />
  </div>

  <div class="moving-text">Scan Your Library Card</div>
  <div id="output"></div>

  <!-- Not Validated Modal -->
  <div id="notValidatedModal">
    <div class="modal-content">
      <h2>Access Denied</h2>
      <p>Your library card is not yet validated. Please contact the librarian.</p>
      <button id="closeModal">OK</button>
    </div>
  </div>
</body>
</html>
