// Dashboard JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadContacts();
});

function initializeDashboard() {
    // Tab navigation
    const navLinks = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all nav links and tab contents
            navLinks.forEach(nl => nl.classList.remove('active'));
            tabContents.forEach(tc => tc.classList.remove('active'));
            
            // Add active class to clicked nav link and corresponding tab content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Form submissions
    setupFormHandlers();
}

function setupFormHandlers() {
    // Website settings form
    const websiteSettingsForm = document.getElementById('website-settings');
    if (websiteSettingsForm) {
        websiteSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveWebsiteSettings();
        });
    }
    
    // Email config form
    const emailConfigForm = document.getElementById('email-config');
    if (emailConfigForm) {
        emailConfigForm.addEventListener('submit', function(e) {
            e.preventDefault();
            testEmailConnection();
        });
    }
    
    // Profile form
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateProfile();
        });
    }
    
    // Password form
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword();
        });
    }
}

// Website Editor Functions
function previewWebsite() {
    const content = document.getElementById('website-content').value;
    
    // Create a temporary file for preview
    const formData = new FormData();
    formData.append('action', 'preview');
    formData.append('content', content);
    
    // Show modal with preview
    const modal = document.getElementById('preview-modal');
    const iframe = document.getElementById('preview-frame');
    
    // Update iframe source with current content
    const blob = new Blob([content], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    iframe.src = url;
    
    modal.style.display = 'block';
}

function closePreview() {
    const modal = document.getElementById('preview-modal');
    modal.style.display = 'none';
    
    // Clean up blob URL
    const iframe = document.getElementById('preview-frame');
    if (iframe.src.startsWith('blob:')) {
        URL.revokeObjectURL(iframe.src);
    }
}

function saveWebsite() {
    const content = document.getElementById('website-content').value;
    
    fetch('/api.php?action=save-website', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Website saved successfully!', 'success');
        } else {
            showMessage('Error saving website: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error saving website: ' + error.message, 'error');
    });
}

function resetWebsite() {
    if (confirm('Are you sure you want to reset the website content? This will restore the original template.')) {
        fetch('/api.php?action=get-website-template')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('website-content').value = data.content;
                showMessage('Website content reset to template', 'success');
            }
        })
        .catch(error => {
            showMessage('Error resetting website: ' + error.message, 'error');
        });
    }
}

function saveWebsiteSettings() {
    const settings = {
        title: document.getElementById('site-title').value,
        description: document.getElementById('site-description').value,
        keywords: document.getElementById('site-keywords').value
    };
    
    fetch('/api.php?action=save-website-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Website settings saved successfully!', 'success');
        } else {
            showMessage('Error saving settings: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error saving settings: ' + error.message, 'error');
    });
}

// Email Functions
function testEmailConnection() {
    const config = {
        server: document.getElementById('email-server').value,
        port: document.getElementById('email-port').value,
        username: document.getElementById('email-username').value,
        password: document.getElementById('email-password').value
    };
    
    fetch('/api.php?action=test-email-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Email connection successful!', 'success');
        } else {
            showMessage('Email connection failed: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error testing connection: ' + error.message, 'error');
    });
}

function refreshEmails() {
    fetch('/api.php?action=get-emails')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateEmailList(data.emails);
            showMessage('Emails refreshed successfully!', 'success');
        } else {
            showMessage('Error fetching emails: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error fetching emails: ' + error.message, 'error');
    });
}

function updateEmailList(emails) {
    const emailList = document.querySelector('.email-list');
    emailList.innerHTML = '';
    
    emails.forEach(email => {
        const emailItem = document.createElement('div');
        emailItem.className = 'email-item';
        emailItem.innerHTML = `
            <div class="email-header">
                <span class="email-from">${email.from}</span>
                <span class="email-date">${email.date}</span>
            </div>
            <div class="email-subject">${email.subject}</div>
            <div class="email-preview">${email.preview}</div>
        `;
        emailList.appendChild(emailItem);
    });
}

// Profile Functions
function updateProfile() {
    const profile = {
        email: document.getElementById('profile-email').value,
        name: document.getElementById('profile-name').value,
        company: document.getElementById('profile-company').value
    };
    
    fetch('/api.php?action=update-profile', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(profile)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Profile updated successfully!', 'success');
        } else {
            showMessage('Error updating profile: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error updating profile: ' + error.message, 'error');
    });
}

function changePassword() {
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (newPassword !== confirmPassword) {
        showMessage('New passwords do not match!', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showMessage('New password must be at least 6 characters long!', 'error');
        return;
    }
    
    fetch('/api.php?action=change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            currentPassword: currentPassword,
            newPassword: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Password changed successfully!', 'success');
            document.getElementById('password-form').reset();
        } else {
            showMessage('Error changing password: ' + data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('Error changing password: ' + error.message, 'error');
    });
}

// Contacts Functions (from existing functionality)
function loadContacts() {
    fetch('/api.php?action=list')
    .then(response => response.json())
    .then(data => {
        updateContactsTable(data);
    })
    .catch(error => {
        console.error('Error loading contacts:', error);
    });
}

function updateContactsTable(contacts) {
    const tbody = document.querySelector('#contactsTable tbody');
    tbody.innerHTML = '';
    
    contacts.forEach(contact => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${contact.id}</td>
            <td>${contact.name}</td>
            <td>${contact.email}</td>
            <td>${contact.phone || '-'}</td>
            <td>
                <button class="btn-small" onclick="editContact(${contact.id})">Edit</button>
                <button class="btn-small" onclick="deleteContact(${contact.id})" style="color: #c33;">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function editContact(id) {
    // Implementation for editing contact
    showMessage('Edit contact functionality - implement as needed', 'success');
}

function deleteContact(id) {
    if (confirm('Are you sure you want to delete this contact?')) {
        fetch(`/api.php?action=delete&id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Contact deleted successfully!', 'success');
                loadContacts();
            } else {
                showMessage('Error deleting contact: ' + data.error, 'error');
            }
        })
        .catch(error => {
            showMessage('Error deleting contact: ' + error.message, 'error');
        });
    }
}

// Utility Functions
function showMessage(text, type = 'success') {
    const message = document.getElementById('message');
    message.textContent = text;
    message.className = `message ${type}`;
    message.style.display = 'block';
    
    setTimeout(() => {
        message.style.display = 'none';
    }, 5000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('preview-modal');
    if (event.target === modal) {
        closePreview();
    }
}
