<?php
require __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email address is required';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address';
    } else {
        // Generate and send OTP
        $otp = generateOTP();
        $otpData = storeOTP($email, $otp);
        
        if (sendOTPEmail($email, $otp)) {
            $_SESSION['pending_email'] = $email;
            header('Location: /verify-otp.php');
            exit;
        } else {
            $error = 'Failed to send OTP. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Dashboard Login</h1>
                <p>Enter your email to receive a secure access code</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="Enter your email address">
                </div>
                
                <button type="submit" class="btn-primary">
                    <span class="btn-text">Send Access Code</span>
                    <span class="btn-loading" style="display: none;">Sending...</span>
                </button>
            </form>
            
            <div class="login-footer">
                <div class="security-info">
                    <h4>🔒 Secure Authentication</h4>
                    <ul>
                        <li>Access codes expire in 5 minutes</li>
                        <li>Maximum 3 attempts per code</li>
                        <li>Codes are single-use only</li>
                    </ul>
                </div>
                
                <div class="demo-info">
                    <p><strong>Demo Mode:</strong></p>
                    <p>OTP codes are logged to <code>data/email_log.txt</code> instead of being sent via email.</p>
                    <p>Check the log file to see the generated OTP codes.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission handling
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            submitBtn.disabled = true;
        });
        
        // Auto-focus email field
        document.getElementById('email').focus();
    </script>
</body>
</html>
