<?php
// Simple development server starter
echo "🚀 Starting Website Management System with OTP Authentication...\n\n";

// Check if data directory exists
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
    echo "✅ Created data directory\n";
}

echo "📁 Project Structure:\n";
echo "   Frontend: http://localhost:8000/frontend/index.html\n";
echo "   Dashboard: http://localhost:8000/dashboard/index.php\n";
echo "   Login: http://localhost:8000/login.php\n\n";

echo "🔐 OTP Authentication System:\n";
echo "   ✅ Email-based login (no passwords!)\n";
echo "   ✅ 6-digit OTP codes\n";
echo "   ✅ 5-minute expiration\n";
echo "   ✅ Maximum 3 attempts per code\n";
echo "   ✅ Single-use codes only\n\n";

echo "📋 Setup Instructions:\n";
echo "1. Import database.sql into your MySQL database\n";
echo "2. Update database credentials in includes/config.php if needed\n";
echo "3. Start PHP server: php -S localhost:8000\n\n";

echo "📧 Demo Mode:\n";
echo "   OTP codes are logged to data/email_log.txt instead of being sent via email.\n";
echo "   Check the log file to see the generated OTP codes.\n\n";

echo "🎯 Features:\n";
echo "   ✅ Secure OTP-based authentication\n";
echo "   ✅ Backend dashboard with tabs\n";
echo "   ✅ Edit website functionality\n";
echo "   ✅ Email management\n";
echo "   ✅ User account management\n";
echo "   ✅ Contact form management\n";
echo "   ✅ Modern responsive frontend\n\n";

echo "Starting PHP development server on http://localhost:8000...\n";
echo "Press Ctrl+C to stop the server.\n\n";

// Start the server
exec('php -S localhost:8000');
?>