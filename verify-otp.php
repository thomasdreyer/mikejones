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

// Check if OTP exists and hasn't expired
$otpData = getOTP($email);
if (!$otpData) {
    unset($_SESSION['pending_email']);
    header('Location: /login.php?error=expired');
    exit;
}

$timeLeft = $otpData['expires_at'] - time();
$minutesLeft = max(0, floor($timeLeft / 60));
$secondsLeft = max(0, $timeLeft % 60);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    
    if (empty($otp)) {
        $error = 'Please enter the access code';
    } elseif (strlen($otp) !== 6 || !ctype_digit($otp)) {
        $error = 'Please enter a valid 6-digit access code';
    } else {
        $result = verifyOTP($email, $otp);
        
        if ($result['success']) {
            unset($_SESSION['pending_email']);
            header('Location: /dashboard/index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Access Code - Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Enter Access Code</h1>
                <p>We've sent a 6-digit code to:</p>
                <div class="email-display"><?= htmlspecialchars($email) ?></div>
            </div>
            
            <div class="timer-display">
                <span class="timer-label">Code expires in:</span>
                <span class="timer-value" id="timer"><?= sprintf('%02d:%02d', $minutesLeft, $secondsLeft) ?></span>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" class="otp-form">
                <div class="form-group">
                    <label for="otp">Access Code</label>
                    <input type="text" id="otp" name="otp" required 
                           maxlength="6" pattern="[0-9]{6}"
                           placeholder="000000" class="otp-input">
                </div>
                
                <button type="submit" class="btn-primary">
                    <span class="btn-text">Verify Code</span>
                    <span class="btn-loading" style="display: none;">Verifying...</span>
                </button>
            </form>
            
            <div class="otp-actions">
                <a href="resend-otp.php" class="resend-link">Didn't receive the code? Resend</a>
                <a href="login.php" class="back-link">← Back to email entry</a>
            </div>
            
            <div class="security-info">
                <h4>🔒 Security Notice</h4>
                <ul>
                    <li>Enter the 6-digit code from your email</li>
                    <li>Code expires in 5 minutes</li>
                    <li>Maximum 3 attempts allowed</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus OTP input
        document.getElementById('otp').focus();
        
        // Timer countdown
        let timeLeft = <?= $timeLeft ?>;
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            if (timeLeft <= 0) {
                timerElement.textContent = '00:00';
                timerElement.parentElement.classList.add('expired');
                return;
            }
            
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            timeLeft--;
        }
        
        // Update timer every second
        setInterval(updateTimer, 1000);
        
        // OTP input formatting
        document.getElementById('otp').addEventListener('input', function(e) {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                setTimeout(() => {
                    this.form.submit();
                }, 100);
            }
        });
        
        // Form submission handling
        document.querySelector('.otp-form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            submitBtn.disabled = true;
        });
        
        // Paste handling
        document.getElementById('otp').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            this.value = pastedData.slice(0, 6);
            
            if (this.value.length === 6) {
                setTimeout(() => {
                    this.form.submit();
                }, 100);
            }
        });
    </script>
</body>
</html>
