<?php
include("includes/db.php");

// Process QR code scan
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token']) && isset($_GET['student_id'])) {
    $token = $_GET['token'];
    $student_id = intval($_GET['student_id']);
    $today = date('Y-m-d');
    
    // Verify token (in a real app, you'd have more robust verification)
    $expected_token = md5('attendance_' . $today . '_' . 'admin'); // Replace with actual verification
    
    if ($token === $expected_token) {
        // Check if attendance already exists
        $check = mysqli_query($conn, "SELECT id FROM attendance WHERE student_id = $student_id AND date = '$today'");
        
        if (mysqli_num_rows($check) == 0) {
            // Insert new attendance record
            mysqli_query($conn, "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$today', 'Present')");
            $message = "Attendance marked successfully!";
        } else {
            $message = "Attendance already marked for today";
        }
    } else {
        $message = "Invalid QR code";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scan Attendance - SmartTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background-color: #f0f0f0;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <h1>SmartTrack Attendance</h1>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <p>Point your camera at the QR code displayed by your teacher</p>
    <p>or</p>
    
    <form method="get">
        <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
        <label for="student_id">Enter your Student ID:</label><br>
        <input type="text" id="student_id" name="student_id" required><br><br>
        <button type="submit">Submit Attendance</button>
    </form>
</body>
</html>