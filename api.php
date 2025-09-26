<?php
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

// Public endpoints (no authentication required)
$publicEndpoints = ['list', 'create', 'send-otp', 'verify-otp'];
if (!in_array($action, $publicEndpoints)) {
    // Require authentication for all other endpoints
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
}

// GET /api?action=list (Contacts)
if ($method === 'GET' && $action === 'list') {
    $stmt = $pdo->query('SELECT * FROM contacts ORDER BY id DESC');
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST /api?action=create (Contact form submission)
if ($method === 'POST' && $action === 'create') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? null);

    if (!$name || !$email) {
        http_response_code(422);
        echo json_encode(['error' => 'Name and email required']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $phone]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

// POST /api?action=send-otp (Send OTP to email)
if ($method === 'POST' && $action === 'send-otp') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    
    if (empty($email)) {
        http_response_code(422);
        echo json_encode(['error' => 'Email address is required']);
        exit;
    }
    
    if (!isValidEmail($email)) {
        http_response_code(422);
        echo json_encode(['error' => 'Please enter a valid email address']);
        exit;
    }
    
    // Generate and send OTP
    $otp = generateOTP();
    $otpData = storeOTP($email, $otp);
    
    if (sendOTPEmail($email, $otp)) {
        echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to send OTP. Please try again.']);
    }
    exit;
}

// POST /api?action=verify-otp (Verify OTP)
if ($method === 'POST' && $action === 'verify-otp') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $otp = trim($data['otp'] ?? '');
    
    if (empty($email) || empty($otp)) {
        http_response_code(422);
        echo json_encode(['error' => 'Email and OTP are required']);
        exit;
    }
    
    $result = verifyOTP($email, $otp);
    
    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Authentication successful']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => $result['message']]);
    }
    exit;
}

// PUT /api?action=update (Contact update)
if ($method === 'PUT' && $action === 'update') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? null);

    if (!$id || !$name || !$email) {
        http_response_code(422);
        echo json_encode(['error' => 'ID, name and email required']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone = ? WHERE id = ?');
    $stmt->execute([$name, $email, $phone, $id]);
    echo json_encode(['success' => true]);
    exit;
}

// DELETE /api?action=delete&id=1 (Contact deletion)
if ($method === 'DELETE' && $action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(422);
        echo json_encode(['error' => 'ID required']);
        exit;
    }
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

// POST /api?action=save-website (Save website content)
if ($method === 'POST' && $action === 'save-website') {
    $data = json_decode(file_get_contents('php://input'), true);
    $content = $data['content'] ?? '';
    
    if (empty($content)) {
        http_response_code(422);
        echo json_encode(['error' => 'Website content is required']);
        exit;
    }
    
    $frontendPath = __DIR__ . '/frontend/index.html';
    if (file_put_contents($frontendPath, $content) === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save website content']);
        exit;
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// GET /api?action=get-website-template (Get original template)
if ($method === 'GET' && $action === 'get-website-template') {
    $templatePath = __DIR__ . '/frontend/index.html';
    if (file_exists($templatePath)) {
        $content = file_get_contents($templatePath);
        echo json_encode(['success' => true, 'content' => $content]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Template not found']);
    }
    exit;
}

// POST /api?action=save-website-settings (Save website settings)
if ($method === 'POST' && $action === 'save-website-settings') {
    $data = json_decode(file_get_contents('php://input'), true);
    $settings = [
        'title' => $data['title'] ?? '',
        'description' => $data['description'] ?? '',
        'keywords' => $data['keywords'] ?? ''
    ];
    
    $settingsPath = __DIR__ . '/data/website-settings.json';
    $settingsDir = dirname($settingsPath);
    
    if (!is_dir($settingsDir)) {
        mkdir($settingsDir, 0755, true);
    }
    
    if (file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save website settings']);
        exit;
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// POST /api?action=test-email-connection (Test email connection)
if ($method === 'POST' && $action === 'test-email-connection') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Simulate email connection test
    // In a real implementation, you would use IMAP/POP3 to test the connection
    $server = $data['server'] ?? '';
    $port = $data['port'] ?? 993;
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($server) || empty($username) || empty($password)) {
        http_response_code(422);
        echo json_encode(['error' => 'Server, username, and password are required']);
        exit;
    }
    
    // Simulate connection test (replace with actual IMAP connection)
    $success = true; // Simulate successful connection
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Connection successful']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Connection failed']);
    }
    exit;
}

// GET /api?action=get-emails (Get emails)
if ($method === 'GET' && $action === 'get-emails') {
    // Simulate email data
    $emails = [
        [
            'from' => 'john@example.com',
            'subject' => 'Website Inquiry',
            'preview' => 'Hello, I\'m interested in your services and would like to discuss a potential project...',
            'date' => '2 hours ago'
        ],
        [
            'from' => 'sarah@company.com',
            'subject' => 'Partnership Proposal',
            'preview' => 'We would like to discuss a potential partnership opportunity with your company...',
            'date' => '1 day ago'
        ],
        [
            'from' => 'support@client.com',
            'subject' => 'Project Update',
            'preview' => 'Thank you for the excellent work on our website. We are very satisfied with the results...',
            'date' => '3 days ago'
        ]
    ];
    
    echo json_encode(['success' => true, 'emails' => $emails]);
    exit;
}

// POST /api?action=update-profile (Update user profile)
if ($method === 'POST' && $action === 'update-profile') {
    $data = json_decode(file_get_contents('php://input'), true);
    $profile = [
        'email' => $data['email'] ?? '',
        'name' => $data['name'] ?? '',
        'company' => $data['company'] ?? ''
    ];
    
    $profilePath = __DIR__ . '/data/user-profile.json';
    $profileDir = dirname($profilePath);
    
    if (!is_dir($profileDir)) {
        mkdir($profileDir, 0755, true);
    }
    
    if (file_put_contents($profilePath, json_encode($profile, JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update profile']);
        exit;
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// POST /api?action=change-password (Change password)
if ($method === 'POST' && $action === 'change-password') {
    $data = json_decode(file_get_contents('php://input'), true);
    $currentPassword = $data['currentPassword'] ?? '';
    $newPassword = $data['newPassword'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword)) {
        http_response_code(422);
        echo json_encode(['error' => 'Current password and new password are required']);
        exit;
    }
    
    // Simple password validation (in production, use proper validation)
    if ($newPassword !== 'admin123') {
        http_response_code(422);
        echo json_encode(['error' => 'Invalid current password']);
        exit;
    }
    
    // In production, you would hash the new password and save it
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
