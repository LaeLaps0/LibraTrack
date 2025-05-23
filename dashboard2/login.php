<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM user WHERE username='$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row["password"])) {
        $_SESSION["username"] = $username;
        $_SESSION["usertype"] = $row["usertype"]; // Store user type

        echo "<script>alert('✅ Login successful! User type: " . $row["usertype"] . "'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('❌ Invalid username or password!');</script>";
    }
}
?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">

  <div class="card p-4 shadow" style="min-width: 300px; max-width: 400px; width: 100%;">
    <h3 class="text-center mb-3">Admin Login</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-dark">Login</button>
      </div>
    </form>
  </div>

</body>
</html>
