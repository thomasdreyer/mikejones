<?php
require __DIR__ . '/../includes/auth.php';
requireAuth();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>Dashboard</h2>
        </div>
        <div class="nav-user">
            <span>Welcome, <?= htmlspecialchars($user['email']) ?></span>
            <a href="../logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="#edit-website" class="nav-link active" data-tab="edit-website">
                    <span class="nav-icon">🌐</span>
                    Edit Website
                </a>
                <a href="#check-email" class="nav-link" data-tab="check-email">
                    <span class="nav-icon">📧</span>
                    Check Email
                </a>
                <a href="#my-account" class="nav-link" data-tab="my-account">
                    <span class="nav-icon">👤</span>
                    My Account
                </a>
                <a href="#contacts" class="nav-link" data-tab="contacts">
                    <span class="nav-icon">📞</span>
                    Contacts
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Edit Website Tab -->
            <div id="edit-website" class="tab-content active">
                <div class="tab-header">
                    <h2>Edit Website</h2>
                    <p>Manage your website content and settings</p>
                </div>
                
                <div class="content-grid">
                    <div class="card">
                        <h3>Website Editor</h3>
                        <div class="editor-container">
                            <div class="editor-toolbar">
                                <button class="btn-small" onclick="previewWebsite()">Preview</button>
                                <button class="btn-small btn-primary" onclick="saveWebsite()">Save Changes</button>
                                <button class="btn-small" onclick="resetWebsite()">Reset</button>
                            </div>
                            <div class="editor-content">
                                <textarea id="website-content" placeholder="Edit your website HTML content here..."><?= htmlspecialchars(file_get_contents(__DIR__ . '/../frontend/index.html')) ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>Website Settings</h3>
                        <form id="website-settings">
                            <div class="form-group">
                                <label for="site-title">Site Title</label>
                                <input type="text" id="site-title" value="My Website">
                            </div>
                            <div class="form-group">
                                <label for="site-description">Site Description</label>
                                <textarea id="site-description" rows="3">Welcome to my website</textarea>
                            </div>
                            <div class="form-group">
                                <label for="site-keywords">Keywords</label>
                                <input type="text" id="site-keywords" value="website, portfolio, business">
                            </div>
                            <button type="submit" class="btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Check Email Tab -->
            <div id="check-email" class="tab-content">
                <div class="tab-header">
                    <h2>Check Email</h2>
                    <p>View and manage incoming emails</p>
                </div>
                
                <div class="content-grid">
                    <div class="card">
                        <h3>Email Configuration</h3>
                        <form id="email-config">
                            <div class="form-group">
                                <label for="email-server">Email Server</label>
                                <input type="text" id="email-server" placeholder="imap.gmail.com">
                            </div>
                            <div class="form-group">
                                <label for="email-port">Port</label>
                                <input type="number" id="email-port" value="993">
                            </div>
                            <div class="form-group">
                                <label for="email-username">Username</label>
                                <input type="email" id="email-username" placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label for="email-password">Password</label>
                                <input type="password" id="email-password">
                            </div>
                            <button type="submit" class="btn-primary">Test Connection</button>
                        </form>
                    </div>
                    
                    <div class="card">
                        <h3>Recent Emails</h3>
                        <div class="email-list">
                            <div class="email-item">
                                <div class="email-header">
                                    <span class="email-from">john@example.com</span>
                                    <span class="email-date">2 hours ago</span>
                                </div>
                                <div class="email-subject">Website Inquiry</div>
                                <div class="email-preview">Hello, I'm interested in your services...</div>
                            </div>
                            <div class="email-item">
                                <div class="email-header">
                                    <span class="email-from">sarah@company.com</span>
                                    <span class="email-date">1 day ago</span>
                                </div>
                                <div class="email-subject">Partnership Proposal</div>
                                <div class="email-preview">We would like to discuss a potential partnership...</div>
                            </div>
                        </div>
                        <button class="btn-primary" onclick="refreshEmails()">Refresh Emails</button>
                    </div>
                </div>
            </div>

            <!-- My Account Tab -->
            <div id="my-account" class="tab-content">
                <div class="tab-header">
                    <h2>My Account</h2>
                    <p>Manage your account settings and profile</p>
                </div>
                
                <div class="content-grid">
                    <div class="card">
                        <h3>Profile Information</h3>
                        <form id="profile-form">
                            <div class="form-group">
                                <label for="profile-email">Email</label>
                                <input type="email" id="profile-email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="profile-name">Full Name</label>
                                <input type="text" id="profile-name" value="Administrator">
                            </div>
                            <div class="form-group">
                                <label for="profile-company">Company</label>
                                <input type="text" id="profile-company" value="My Company">
                            </div>
                            <button type="submit" class="btn-primary">Update Profile</button>
                        </form>
                    </div>
                    
                    <div class="card">
                        <h3>Security Settings</h3>
                        <div class="security-info">
                            <h4>Current Login Method</h4>
                            <p><strong>Email-based OTP Authentication</strong></p>
                            <ul>
                                <li>✅ Secure access codes sent to your email</li>
                                <li>✅ Codes expire in 5 minutes</li>
                                <li>✅ Maximum 3 attempts per code</li>
                                <li>✅ Single-use codes only</li>
                            </ul>
                        </div>
                        
                        <div class="security-actions">
                            <a href="../login.php" class="btn-primary">Change Email Address</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts Tab (existing functionality) -->
            <div id="contacts" class="tab-content">
                <div class="tab-header">
                    <h2>Contacts</h2>
                    <p>Manage contact submissions</p>
                </div>
                
                <div class="card">
                    <div class="contacts-container">
                        <div id="message" class="message"></div>
                        <table id="contactsTable" class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Website Preview</h3>
                <span class="close" onclick="closePreview()">&times;</span>
            </div>
            <div class="modal-body">
                <iframe id="preview-frame" src="../frontend/index.html" width="100%" height="500px"></iframe>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>