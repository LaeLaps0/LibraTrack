<?php
require_once "db.php";


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<div class="container-fluid mt-1 p-1" style="max-width: 1600px;">
  <div class="card shadow rounded-4 border-0">
    <div class="card-header text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
         style="background: linear-gradient(90deg, #0d47a1, #0d47a1);">
      <div>
        <h5 class="mb-1 fw-bold">
          <i class="bi bi-people-fill me-2"></i> Faculty Records
        </h5>
        <?php if (!empty($search)): ?>
          <small class="text-white-75">
            🔍 Showing results for: <strong><?= htmlspecialchars($search) ?></strong>
          </small>
        <?php endif; ?>
      </div>

      <div class="d-flex flex-wrap gap-2 align-items-center">
        <form method="GET" action="index.php" class="search-form">
          <input type="hidden" name="page" value="faculty">
          <input type="text" 
                 name="search" 
                 placeholder="Search faculty..." 
                 value="<?= htmlspecialchars($search) ?>">
          <button type="submit" class="search-btn">
            <i class="bi bi-search me-2"></i>
          </button>
          <a href="index.php?page=faculty" class="reset-link">Reset</a>
        </form>

        <a href="index.php?page=addFaculty" 
           class="btn btn-warning btn-sm text-dark rounded-pill shadow-lg d-flex align-items-center gap-1 px-4 fw-bold btn-add-faculty"
           style="box-shadow: 0 4px 10px rgba(255, 193, 7, 0.7); transition: transform 0.2s ease;">
          <i class="bi bi-plus-circle"></i> Add Faculty
        </a>
      </div>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>Faculty ID</th>
              <th>Last Name</th>
              <th>First Name</th>
              <th>Middle Initial</th>
              <th>Sex</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($search)) {
                $escaped = mysqli_real_escape_string($conn, $search);
                $query = "SELECT * FROM faculty
                          WHERE faculty_id LIKE '%$escaped%' 
                          OR first_name LIKE '%$escaped%' 
                          OR last_name LIKE '%$escaped%' 
                          OR middle_initial LIKE '%$escaped%' 
                          ORDER BY last_name, first_name";
            } else {
                $query = "SELECT * FROM faculty ORDER BY last_name, first_name";
            }

            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['faculty_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['middle_initial']) . "</td>";
                    echo "<td>" . htmlspecialchars(ucfirst($row['sex'])) . "</td>";
                    echo "<td>" . htmlspecialchars(ucfirst($row['active_status'])) . "</td>";
                    echo "<td class='action-icons'>
                            <a href='index.php?page=editFaculty&facultyId=" . urlencode($row['faculty_id']) . "' class='text-primary'><i class='ri-edit-line'></i></a>
                            <a href='index.php?page=deleteFaculty&facultyId=" . urlencode($row['faculty_id']) . "' class='text-danger' onclick='return confirm(\"Are you sure you want to delete Faculty with ID " . htmlspecialchars($row['faculty_id']) . "?\");'><i class='ri-delete-bin-line'></i></a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="7" class="text-muted">';
                echo $search ? "No results found for <strong>" . htmlspecialchars($search) . "</strong>." : "No Faculty found.";
                echo '</td></tr>';
            }

            mysqli_close($conn);
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
