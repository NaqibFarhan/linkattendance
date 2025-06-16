<?php
session_start();

require_once __DIR__.'/lib/GoogleAuthenticator.php';
require_once __DIR__.'/lib/GoogleQrUrl.php';


// Check authentication
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("includes/db.php");

// Get user data
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$ga = new GoogleAuthenticator();
$error = $success = "";

// Generate new secret if none exists
if (empty($user['two_factor_secret'])) {
    $secret = $ga->generateSecret();
    $update_query = "UPDATE users SET two_factor_secret = ? WHERE username = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ss", $secret, $username);
    mysqli_stmt_execute($stmt);
} else {
    $secret = $user['two_factor_secret'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['enable_2fa'])) {
        $code = trim($_POST['code']);
        if ($ga->checkCode($secret, $code)) {
            $update_query = "UPDATE users SET is_2fa_enabled = TRUE WHERE username = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $success = "Two-factor authentication has been enabled";
        } else {
            $error = "Invalid verification code";
        }
    } elseif (isset($_POST['disable_2fa'])) {
        $update_query = "UPDATE users SET is_2fa_enabled = FALSE WHERE username = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $success = "Two-factor authentication has been disabled";
    }
}

// Generate QR code URL
$qrUrl = GoogleQrUrl::generate(
    $username,
    $secret,
    'SmartTrack Attendance System'
);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Security - SmartTrack</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .qr-container {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            max-width: 300px;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <?php include("includes/sidebar.php"); ?>
    
    <div class="main-content">
        <h1>Two-Factor Authentication</h1>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Current Status</h3>
            <p>Two-factor authentication is currently: 
                <strong><?php echo $user['is_2fa_enabled'] ? 'ENABLED' : 'DISABLED'; ?></strong>
            </p>
            
            <?php if (!$user['is_2fa_enabled']): ?>
                <div class="qr-container">
                    <h3>Setup Instructions</h3>
                    <ol style="text-align: left; padding-left: 20px;">
                        <li>Install Google Authenticator or similar app</li>
                        <li>Scan this QR code:</li>
                    </ol>
                    
                    <img src="<?php echo $qrUrl; ?>" alt="QR Code" class="qr-code">
                    
                    <p style="font-size: 12px; margin: 10px 0;">
                        <strong>Secret Key:</strong><br>
                        <?php echo chunk_split($secret, 4, ' '); ?>
                    </p>
                    
                    <form method="post" style="margin-top: 15px;">
                        <label for="code">Enter 6-digit code:</label><br>
                        <input type="text" id="code" name="code" required 
                               pattern="\d{6}" maxlength="6" placeholder="123456"
                               style="padding: 8px; width: 100%; margin: 5px 0 10px;">
                        <button type="submit" name="enable_2fa" class="btn-action">
                            Enable 2FA
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <form method="post" style="margin-top: 20px;">
                    <button type="submit" name="disable_2fa" class="btn-action" 
                            style="background-color: #e53935;">
                        Disable 2FA
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>