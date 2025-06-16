<?php
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$stmt = mysqli_prepare($conn,
  "SELECT a.date, s.name, s.class, a.status
   FROM attendance a
   JOIN students s ON a.student_id = s.id
   WHERE a.date = ?
   ORDER BY s.name ASC"
);
mysqli_stmt_bind_param($stmt, "s", $date);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Attendance Records - SmartTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
  <div class="main-content">
    <h2>Attendance Records</h2>

    <!-- Date Picker and View Button -->
    <form method="GET" style="margin-bottom: 20px;">
      <label for="date">Date:</label>
      <input type="date" id="date" name="date" required value="<?= htmlspecialchars($date) ?>">
      <button type="submit" class="btn-action">View</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Class</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($res)) : ?>
          <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['class']) ?></td>
            <td><?= htmlspecialchars($row['status'] ?: 'Attend') ?></td>

          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
