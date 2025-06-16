<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['temp_user'])) {
    header("Location: login.php");
    exit();
}

require 'vendor/autoload.php';
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$ga = new GoogleAuthenticator();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST["code"]);
    $secret = $_SESSION['temp_user']['two_factor_secret'];
    
    if ($ga->checkCode($secret, $code)) {
        // 2FA successful - complete login
        $_SESSION['username'] = $_SESSION['temp_user']['username'];
        unset($_SESSION['temp_user']);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid verification code";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>2FA Verification - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page" style="background: url('images/kovokesyen-e1603864966925.jpg') no-repeat center center fixed; background-size: cover;">

  <div class="login-box">
    <div class="logo-container">
      <img src="images/logo.png" alt="System Logo" class="login-logo">
      <h2 class="system-name">Two-Factor Authentication</h2>
    </div>

    <?php if ($error): ?>
      <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <p>Please enter the 6-digit code from your authenticator app</p>
    
    <form method="post" action="">
      <label for="code">Verification Code:</label><br>
      <input type="text" id="code" name="code" required maxlength="6" pattern="\d{6}"><br><br>
      
      <button type="submit" class="btn-action">Verify</button>
    </form>

    <p><a href="login.php">Back to Login</a></p>
  </div>

</body>
</html>