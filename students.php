<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Students - SmartTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
  <div class="sidebar">
    <div class="logo-container">
      <img src="images/logo.png" alt="System Logo" class="logo" style="width: 80px; height: auto;">
      <h2>SmartTrack</h2>
    </div>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="attendance.php">Take Attendance</a>
    <a href="view_attendance.php">Attendance Records</a>
    <a href="profile.php">Account Settings</a>
    <a href="qr_attendance.php">QR Attendance</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
  </div>
  <div class="main-content">
    <h2>Students</h2>
    <a href="add_student.php" class="btn-action" style="text-decoration: none; margin-bottom: 20px; display: inline-block;">Add Student</a>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Class</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = "SELECT * FROM students";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>".$row['id']."</td>";
          echo "<td>".$row['name']."</td>";
          echo "<td>".$row['class']."</td>";
          echo "<td>
                  <a href='edit_student.php?id=".$row['id']."' class='btn-action' style='text-decoration: none;'>Edit</a> |
                  <a href='delete.php?id=".$row['id']."' class='btn-action' style='text-decoration: none;' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                </td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
