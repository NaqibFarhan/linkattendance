<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is fully authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("includes/auth.php");
include("includes/db.php");
include("includes/sidebar.php");

// Get total students
$total_students_query = "SELECT COUNT(*) as total FROM students";
$total_students_result = mysqli_query($conn, $total_students_query);
$total_students = mysqli_fetch_assoc($total_students_result)['total'];

// Get today's attendance
$today = date('Y-m-d');
$attend_query = "SELECT COUNT(DISTINCT student_id) as count FROM attendance WHERE date = '$today' AND status = 'Attend'";
$absent_query = "SELECT COUNT(DISTINCT student_id) as count FROM attendance WHERE date = '$today' AND status = 'Absent'";

$attend_result = mysqli_query($conn, $attend_query);
$absent_result = mysqli_query($conn, $absent_query);

$attend_count = mysqli_fetch_assoc($attend_result)['count'];
$absent_count = mysqli_fetch_assoc($absent_result)['count'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard - SmartTrack</title>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .card-container {
      display: flex;
      justify-content: flex-start;
      gap: 20px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .card h1 {
      font-size: 2.5em;
      margin: 10px 0;
    }

    .card p {
      margin: 0;
      font-size: 1em;
    }

    .chart-container {
      margin-top: 40px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      max-width: 500px;
    }

    canvas {
      width: 100% !important;
      height: auto !important;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <div class="logo-container">
      <img src="images/logo.png" alt="System Logo" class="logo" style="width: 80px; height: auto;">
      <h2>SmartTrack</h2>
    </div>
    
  </div>

  <div class="main-content">
    <h1>Dashboard</h2>
    <p>Welcome to the Student Attendance Management System</p>

    <div class="card-container">
      <div class="card">
        <h3>Total Students</h3>
        <h1><?php echo $total_students; ?></h1>
        <p>Students registered</p>
      </div>
      <div class="card">
        <h3>Today's Attendance</h3>
        <h1><?php echo $attend_count + $absent_count; ?></h1>
        <p>
          Attend: <?php echo $attend_count; ?> |
          Absent: <?php echo $absent_count; ?>
        </p>
      </div>
    </div>

    <div class="chart-container">
      <h3>Today's Attendance Chart</h3>
      <canvas id="attendanceChart"></canvas>
    </div>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('attendanceChart').getContext('2d');
  const attendanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Attend', 'Absent'],
      datasets: [{
        label: 'No. of Students',
        data: [<?php echo $attend_count; ?>, <?php echo $absent_count; ?>],
        backgroundColor: ['#4CAF50', '#e53935'],
        borderColor: ['#388E3C', '#c62828'],
        borderWidth: 1,
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          precision: 0
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>
</body>
</html>
