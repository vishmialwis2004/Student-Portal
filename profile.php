<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Check for remember me cookie
    if (isset($_COOKIE['remember_me'])) {
        $cookieData = base64_decode($_COOKIE['remember_me']);
        list($userId, $timestamp) = explode(':', $cookieData);
        
        // Verify cookie is not too old (30 days)
        if (time() - $timestamp < (30 * 24 * 60 * 60)) {
            // Find user by ID and log them in
            $usersFile = 'data/users.json';
            if (file_exists($usersFile)) {
                $users = json_decode(file_get_contents($usersFile), true);
                foreach ($users as $user) {
                    if ($user['id'] === $userId) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login_time'] = time();
                        break;
                    }
                }
            }
        }
    }
    
    // If still not logged in, redirect to login page
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: login.html');
        exit;
    }
}

// Get user data
function getUserData($userId) {
    $usersFile = 'data/users.json';
    
    if (!file_exists($usersFile)) {
        return null;
    }
    
    $users = json_decode(file_get_contents($usersFile), true);
    if (!$users) {
        return null;
    }
    
    foreach ($users as $user) {
        if ($user['id'] === $userId) {
            return $user;
        }
    }
    
    return null;
}

$userData = getUserData($_SESSION['user_id']);

if (!$userData) {
    // User data not found, logout
    session_destroy();
    header('Location: login.html');
    exit;
}

// Format registration date
$registrationDate = date('F j, Y', strtotime($userData['registrationDate']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Student Portal</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link active">Profile</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
            </ul>
            <div class="nav-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <div class="avatar-placeholder">👤</div>
                </div>
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($userData['firstName'] . ' ' . $userData['lastName']); ?></h1>
                    <p><?php echo htmlspecialchars($userData['email']); ?></p>
                    <span class="profile-badge">Student</span>
                </div>
            </div>

            <div class="profile-content">
                <div class="profile-section">
                    <h2>Personal Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>First Name</label>
                            <span><?php echo htmlspecialchars($userData['firstName']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Last Name</label>
                            <span><?php echo htmlspecialchars($userData['lastName']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <span><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Student ID</label>
                            <span><?php echo htmlspecialchars($userData['studentId']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <span><?php echo htmlspecialchars($userData['phone']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Registration Date</label>
                            <span><?php echo htmlspecialchars($registrationDate); ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Account Settings</h2>
                    <div class="settings-options">
                        <button class="btn btn-secondary">Edit Profile</button>
                        <button class="btn btn-secondary">Change Password</button>
                        <button class="btn btn-secondary">Privacy Settings</button>
                    </div>
                </div>

                <div class="profile-section">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions">
                        <a href="contact.php" class="action-card">
                            <div class="action-icon"></div>
                            <h3>Contact Support</h3>
                            <p>Get help from our support team</p>
                        </a>
                        <a href="index.html" class="action-card">
                            <div class="action-icon"></div>
                            <h3>Dashboard</h3>
                            <p>Return to main dashboard</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('Welcome!', '<?php echo addslashes($_SESSION['success_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <script src="script.js"></script>
</body>
</html>