<?php
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    foreach ($_POST['attendance'] as $student_id => $status) {
        $student_id = intval($student_id);
        $status = mysqli_real_escape_string($conn, $status);
        mysqli_query($conn,
            "REPLACE INTO attendance (student_id, date, status)
             VALUES ($student_id, '$date', '$status')"
        );
    }
    echo "<script>alert('Attendance saved successfully for $date');</script>";
}

$students = mysqli_query($conn, "SELECT * FROM students ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Take Attendance - SmartTrack</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <div class="main-content">
    <h2>Take Attendance</h2>
    <form method="POST">
      <label>Date:</label>
      <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
      <div class="table-container">
        <table>
          <thead>
            <tr><th>ID</th><th>Name</th><th>Class</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($students)): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['class']) ?></td>
                <td>
                  <select name="attendance[<?= $row['id'] ?>]">
                  <option value="Attend" selected>Attend</option>
                  <option value="Absent">Absent</option>
                  </select>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <button type="submit" class="btn-action" style="margin-top:10px;width:100%;">Submit Attendance</button>
      </div>
    </form>
  </div>
</body>
</html>
