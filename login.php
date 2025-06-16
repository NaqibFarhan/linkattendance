<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("includes/db.php");

$error = "";
$success = "";

if (isset($_GET['registered'])) {
    $success = "Registration successful. Please login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = md5(trim($_POST["password"]));

    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['temp_user'] = $user; // Store user data temporarily
        
        // Check if 2FA is enabled for this user
        if ($user['is_2fa_enabled']) {
            header("Location: verify-2fa.php");
            exit();
        } else {
            // No 2FA - proceed directly to dashboard
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page" style="background: url('images/kovokesyen-e1603864966925.jpg') no-repeat center center fixed; background-size: cover;">

  <div class="login-box">
    <!-- Logo Section -->
    <div class="logo-container">
      <img src="images/logo.png" alt="System Logo" class="login-logo">
      <h2 class="system-name"><strong>Student Attendance Management System</strong></h2>
    </div>

    <?php if ($error): ?>
      <p class="error-msg"><?php echo $error; ?></p>
    <?php elseif ($success): ?>
      <p class="success-msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post" action="">
      <label for="username">Username:</label><br>
      <input type="text" id="username" name="username" required><br><br>

      <label for="password">Password:</label><br>
      <input type="password" id="password" name="password" required><br><br>

      <button type="submit" class="btn-action">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
  </div>

</body>
</html>