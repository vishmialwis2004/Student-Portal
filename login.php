<?php
session_start();

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Function to find user by email
function findUserByEmail($email) {
    $usersFile = 'data/users.json';
    
    if (!file_exists($usersFile)) {
        return null;
    }
    
    $users = json_decode(file_get_contents($usersFile), true);
    if (!$users) {
        return null;
    }
    
    foreach ($users as $index => $user) {
        if ($user['email'] === $email) {
            return ['index' => $index, 'user' => $user];
        }
    }
    
    return null;
}

// Function to update last login
function updateLastLogin($email) {
    $usersFile = 'data/users.json';
    
    if (!file_exists($usersFile)) {
        return false;
    }
    
    $users = json_decode(file_get_contents($usersFile), true);
    if (!$users) {
        return false;
    }
    
    foreach ($users as &$user) {
        if ($user['email'] === $email) {
            $user['lastLogin'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    return file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Function to set remember me cookie
function setRememberMeCookie($userId) {
    $cookieValue = base64_encode($userId . ':' . time());
    setcookie('remember_me', $cookieValue, time() + (30 * 24 * 60 * 60), '/'); // 30 days
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['rememberMe']);
    
    // Validation
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Find user
        $userResult = findUserByEmail($email);
        
        if ($userResult && verifyPassword($password, $userResult['user']['password'])) {
            $user = $userResult['user'];
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Update last login
            updateLastLogin($email);
            
            // Set remember me cookie if requested
            if ($rememberMe) {
                setRememberMeCookie($user['id']);
            }
            
            $response['success'] = true;
            $response['message'] = 'Login successful! Welcome back, ' . $user['firstName'] . '.';
            $response['redirect'] = 'profile.php';
            
            // Log the login
            error_log("User logged in: " . $email);
        } else {
            $response['message'] = 'Invalid email or password';
        }
    } else {
        $response['message'] = implode('. ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For regular form submission, redirect with message
if ($response['success']) {
    $_SESSION['success_message'] = $response['message'];
    header('Location: profile.php');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: login.html');
}
exit;
?>