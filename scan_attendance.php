<?php
include("includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
    $today = date('Y-m-d');

    // Check if attendance already exists
    $check = mysqli_query($conn, "SELECT id FROM attendance WHERE student_id = $student_id AND date = '$today'");

    if (mysqli_num_rows($check) == 0) {
        // Insert new attendance record
        mysqli_query($conn, "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$today', 'Present')");
        $message = "✅ Attendance marked successfully!";
        $status = "success";
    } else {
        $message = "⚠️ Attendance already marked for today.";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Attendance - SmartTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            text-align: center;
            padding: 30px;
        }

        h1 {
            color: #800000;
            margin-bottom: 20px;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 1em;
            width: 80%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin: 10px 0;
        }

        button {
            background-color: #800000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #a00000;
        }

        p {
            color: #555;
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <h1>📸 SmartTrack Attendance</h1>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <p>Scan the QR or enter your Student ID manually below:</p>

        <form method="get">
            <input type="text" name="student_id" placeholder="Enter your Student ID" required>
            <br>
            <button type="submit">Submit Attendance</button>
        </form>
    </div>

</body>
</html>
