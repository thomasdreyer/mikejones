<?php
require __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

// Redirect if no pending email
if (!isset($_SESSION['pending_email'])) {
    header('Location: /login.php');
    exit;
}

$email = $_SESSION['pending_email'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate new OTP
    $otp = generateOTP();
    $otpData = storeOTP($email, $otp);
    
    if (sendOTPEmail($email, $otp)) {
        $success = 'New access code sent to your email';
    } else {
        $error = 'Failed to send new access code. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Access Code - Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Resend Access Code</h1>
                <p>Send a new access code to:</p>
                <div class="email-display"><?= htmlspecialchars($email) ?></div>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <div class="redirect-info">
                    <p>Redirecting to verification page...</p>
                    <script>
                        setTimeout(() => {
                            window.location.href = '/verify-otp.php';
                        }, 2000);
                    </script>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <form method="POST" class="resend-form">
                    <div class="resend-info">
                        <p>Click the button below to send a new access code to your email address.</p>
                        <p><strong>Note:</strong> The previous code will be invalidated.</p>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <span class="btn-text">Send New Access Code</span>
                        <span class="btn-loading" style="display: none;">Sending...</span>
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="resend-actions">
                <a href="verify-otp.php" class="back-link">← Back to code entry</a>
                <a href="login.php" class="back-link">← Use different email</a>
            </div>
            
            <div class="security-info">
                <h4>🔒 Security Notice</h4>
                <ul>
                    <li>New codes expire in 5 minutes</li>
                    <li>Previous codes become invalid</li>
                    <li>Maximum 3 attempts per code</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Form submission handling
        const form = document.querySelector('.resend-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
                submitBtn.disabled = true;
            });
        }
    </script>
</body>
</html>
