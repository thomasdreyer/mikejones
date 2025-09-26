# Website Management System with OTP Authentication

A comprehensive PHP web application with secure OTP-based authentication, backend dashboard, and frontend website that can be easily edited and extended.

## 🔐 Security Features

### OTP Authentication System
- **Email-based Login** - No passwords required!
- **6-digit OTP codes** - Secure access codes
- **5-minute expiration** - Codes automatically expire
- **Maximum 3 attempts** - Prevents brute force attacks
- **Single-use codes** - Each code can only be used once
- **Session management** - Secure session handling

## Features

### Backend Dashboard
- **Secure OTP Authentication** - Email-based login system
- **Edit Website Tab** - Live HTML editor with preview functionality
- **Check Email Tab** - Email management and configuration
- **My Account Tab** - User profile and security settings
- **Contacts Tab** - Manage contact form submissions

### Frontend Website
- **Modern Design** - Responsive, mobile-first design
- **Contact Form** - Integrated contact form with validation
- **Editable Content** - HTML content can be edited through dashboard
- **Extensible** - Easy to add new sections and features

### Technical Features
- RESTful API endpoints
- MySQL database integration
- OTP-based authentication
- File-based settings storage
- Responsive design
- Modern UI/UX

## Quick Setup

1. **Database Setup**
   ```bash
   # Import the SQL file into your MySQL database
   mysql -u username -p < database.sql
   ```

2. **Start Web Server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the Application**
   - Frontend: `http://localhost:8000/frontend/index.html`
   - Dashboard: `http://localhost:8000/dashboard/index.php`
   - Login: `http://localhost:8000/login.php`

## 🔑 Authentication Flow

1. **Enter Email** - User enters their email address on login page
2. **Receive OTP** - 6-digit code is sent to their email (logged in demo mode)
3. **Enter OTP** - User enters the code on verification page
4. **Access Dashboard** - Upon successful verification, user gains access

## 📧 Demo Mode

In demo mode, OTP codes are logged to `data/email_log.txt` instead of being sent via email. This allows you to test the system without email configuration.

## API Endpoints

### Public Endpoints
- `GET /api.php?action=list` - List all contacts
- `POST /api.php?action=create` - Create new contact (contact form)

### Protected Endpoints (requires authentication)
- `POST /api.php?action=save-website` - Save website content
- `GET /api.php?action=get-website-template` - Get original template
- `POST /api.php?action=save-website-settings` - Save website settings
- `POST /api.php?action=test-email-connection` - Test email connection
- `GET /api.php?action=get-emails` - Get emails
- `POST /api.php?action=update-profile` - Update user profile
- `POST /api.php?action=change-password` - Change password
- `PUT /api.php?action=update` - Update contact
- `DELETE /api.php?action=delete&id=1` - Delete contact

## File Structure

```
├── api.php                  # API endpoints
├── dashboard.php            # Admin dashboard
├── login.php                # Login page
├── logout.php               # Logout handler
├── setup.php                # Database setup script
├── index.php                # Original contacts page
├── frontend/
│   └── index.html           # Main website (editable)
├── includes/
│   ├── auth.php             # Authentication functions
│   ├── config.php           # Database configuration
│   └── db.php              # Database connection
├── assets/
│   ├── css/
│   │   ├── styles.css       # Original styles
│   │   ├── dashboard.css    # Dashboard styles
│   │   └── frontend.css     # Frontend website styles
│   └── js/
│       ├── main.js          # Original JavaScript
│       ├── dashboard.js     # Dashboard functionality
│       └── frontend.js      # Frontend website JavaScript
└── data/                    # Data storage directory
    ├── website-settings.json
    └── user-profile.json
```

## Dashboard Features

### Edit Website Tab
- Live HTML editor with syntax highlighting
- Preview functionality with modal
- Save and reset website content
- Website settings management (title, description, keywords)

### Check Email Tab
- Email server configuration
- Connection testing
- Email listing and management
- Simulated email data (can be connected to real IMAP/POP3)

### My Account Tab
- User profile management
- Password change functionality
- Account information display

### Contacts Tab
- View all contact form submissions
- Delete contacts
- Export functionality (can be added)

## Frontend Website Features

- **Hero Section** - Eye-catching landing area
- **About Section** - Company information
- **Services Section** - Service offerings
- **Contact Section** - Contact form and information
- **Footer** - Additional links and information
- **Responsive Design** - Works on all devices
- **Modern Animations** - Smooth scrolling and transitions

## Customization

### Adding New Dashboard Tabs
1. Add navigation link in `dashboard.php`
2. Create tab content div with unique ID
3. Add JavaScript functionality in `dashboard.js`
4. Create API endpoints in `api.php`

### Extending Frontend
1. Edit `frontend/index.html` directly
2. Use the dashboard editor for live editing
3. Add new CSS in `assets/css/frontend.css`
4. Add JavaScript functionality in `assets/js/frontend.js`

### Database Modifications
1. Modify `setup.php` for new tables
2. Update API endpoints as needed
3. Run setup script to apply changes

## Security Notes

- Change default admin credentials in production
- Implement proper password hashing
- Add CSRF protection
- Use HTTPS in production
- Validate all user inputs
- Sanitize file uploads

## Development

The system is built with:
- **Backend:** PHP 7.4+, MySQL 5.7+
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Database:** MySQL with PDO
- **Authentication:** Session-based
- **API:** RESTful JSON API

## License

This project is open source and available under the MIT License.