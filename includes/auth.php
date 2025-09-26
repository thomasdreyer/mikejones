<?php
session_start();

// OTP configuration
define('OTP_EXPIRY_MINUTES', 5);
define('OTP_LENGTH', 6);

// Simple authentication system with OTP
function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0 && isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function generateOTP() {
    return str_pad(random_int(0, 999999), OTP_LENGTH, '0', STR_PAD_LEFT);
}

function storeOTP($email, $otp) {
    $otpData = [
        'otp' => $otp,
        'email' => $email,
        'created_at' => time(),
        'expires_at' => time() + (OTP_EXPIRY_MINUTES * 60),
        'attempts' => 0
    ];
    
    $otpFile = __DIR__ . '/../data/otp_' . md5($email) . '.json';
    $dataDir = dirname($otpFile);
    
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    file_put_contents($otpFile, json_encode($otpData));
    return $otpData;
}

function getOTP($email) {
    $otpFile = __DIR__ . '/../data/otp_' . md5($email) . '.json';
    
    if (!file_exists($otpFile)) {
        return null;
    }
    
    $otpData = json_decode(file_get_contents($otpFile), true);
    
    if (!$otpData || $otpData['expires_at'] < time()) {
        // Clean up expired OTP
        if (file_exists($otpFile)) {
            unlink($otpFile);
        }
        return null;
    }
    
    return $otpData;
}

function verifyOTP($email, $otp) {
    $otpData = getOTP($email);
    
    if (!$otpData) {
        return ['success' => false, 'message' => 'OTP has expired or does not exist'];
    }
    
    if ($otpData['attempts'] >= 3) {
        return ['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP'];
    }
    
    if ($otpData['otp'] === $otp) {
        // OTP is correct, clean up the file
        $otpFile = __DIR__ . '/../data/otp_' . md5($email) . '.json';
        if (file_exists($otpFile)) {
            unlink($otpFile);
        }
        
        // Set session
        $_SESSION['user_id'] = 1;
        $_SESSION['email'] = $email;
        $_SESSION['authenticated'] = true;
        
        return ['success' => true, 'message' => 'Authentication successful'];
    } else {
        // Increment attempts
        $otpData['attempts']++;
        $otpFile = __DIR__ . '/../data/otp_' . md5($email) . '.json';
        file_put_contents($otpFile, json_encode($otpData));
        
        return ['success' => false, 'message' => 'Invalid OTP. Attempts remaining: ' . (3 - $otpData['attempts'])];
    }
}

function sendOTPEmail($email, $otp) {
    // In a real application, you would use PHPMailer, SwiftMailer, or similar
    // For demo purposes, we'll simulate sending the email
    
    $subject = "Your Dashboard Login OTP";
    $message = "
    <html>
    <head>
        <title>Dashboard Login OTP</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #667eea; text-align: center; margin: 20px 0; padding: 20px; background: white; border-radius: 5px; border: 2px dashed #667eea; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Dashboard Access Code</h2>
            </div>
            <div class='content'>
                <p>Hello,</p>
                <p>You requested access to the dashboard. Use the following code to log in:</p>
                
                <div class='otp-code'>$otp</div>
                
                <div class='warning'>
                    <strong>Important:</strong> This code will expire in " . OTP_EXPIRY_MINUTES . " minutes and can only be used once.
                </div>
                
                <p>If you didn't request this code, please ignore this email.</p>
                
                <p>Best regards,<br>Your Dashboard System</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Dashboard System <noreply@dashboard.local>',
        'Reply-To: noreply@dashboard.local',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    // For demo purposes, we'll log the email instead of sending it
    // In production, use: mail($email, $subject, $message, implode("\r\n", $headers));
    
    $logFile = __DIR__ . '/../data/email_log.txt';
    $logEntry = "[" . date('Y-m-d H:i:s') . "] Email sent to: $email\nSubject: $subject\nOTP: $otp\n\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    return true;
}

function logout() {
    session_destroy();
    session_start();
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? 0,
        'email' => $_SESSION['email'] ?? ''
    ];
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function cleanupExpiredOTPs() {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        return;
    }
    
    $files = glob($dataDir . '/otp_*.json');
    foreach ($files as $file) {
        $otpData = json_decode(file_get_contents($file), true);
        if ($otpData && $otpData['expires_at'] < time()) {
            unlink($file);
        }
    }
}

// Clean up expired OTPs on each request
cleanupExpiredOTPs();
?>
