<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<body>

<div class="d-flex" id="wrapper">
  <!-- Sidebar --><?php $currentPage = $_GET['page'] ?? 'home'; ?>
<div class="bg-dark text-white" id="sidebar-wrapper">
  <div class="sidebar-heading text-center pt-3 pb-1 fs-4 fw-bold">
    <img src="assets/img/logo.png" alt="Admin Logo" class="img-fluid mb-1" style="width: 120px; height: 100px; object-fit: cover;">
    <div class="mt-0 mb-1">Administrator</div>
  </div>



  <div class="list-group list-group-flush">
    <!-- Dashboard -->
    <a href="?page=home" class="list-group-item <?= $currentPage === 'home' ? 'active' : '' ?>">
      <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <!-- Entry Record -->
    <a href="?page=entryRecord" class="list-group-item <?= $currentPage === 'entryRecord' ? 'active' : '' ?>">
      <i class="bi bi-people me-2"></i> Attendance Record
    </a>

    <!-- Manage Record Dropdown Toggle -->
    <button class="list-group-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#manageDropdown" aria-expanded="false" aria-controls="manageDropdown">
      <span><i class="bi bi-gear me-2"></i> Manage Record</span>
      <i class="bi bi-caret-down-fill"></i>
    </button>

    <!-- Dropdown Items -->
    <div class="collapse" id="manageDropdown" style="padding-left: 1rem;">
      <a href="?page=viewStudent" class="list-group-item <?= $currentPage === 'viewStudent' ? 'active' : '' ?>">
        <i class="bi bi-person-lines-fill me-2"></i> Students Record
      </a>
      <a href="?page=addFaculty" class="list-group-item <?= $currentPage === 'addFaculty' ? 'active' : '' ?>">
        <i class="bi bi-person-badge me-2"></i> Faculty Record
      </a>
    </div>

     <!-- Report-->
    <a href="?page=report" class="list-group-item <?= $currentPage === 'report' ? 'active' : '' ?>">
      <i class="bi bi-people me-2"></i> Report
    </a>

  </div>
</div>



  <!-- Page content -->
  <div id="page-content-wrapper" class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light border-bottom fixed-top">
      <div class="container-fluid">
        <button class="btn" id="menu-toggle">☰</button>
       <div class="dropdown ms-auto">
      <button class="btn btn-light dropdown-toggle" type="button" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
        <?php $adminName = $_SESSION['username'] ?? 'Admin'; ?>
<i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($adminName) ?>

      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
    </nav>

    <div class="container-fluid p-4">
      <?php
        $file = "pages/" . basename($page) . ".php";
        if (file_exists($file)) {
          include $file;
        } else {
          echo "<h4>Page not found.</h4>";
        }
      ?>
    </div>
  </div>
</div>

<script>
  document.getElementById("menu-toggle").addEventListener("click", function () {
    document.getElementById("wrapper").classList.toggle("toggled");
  });
</script>

</body>
</html>
