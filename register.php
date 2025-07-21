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

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to check if user already exists
function userExists($email, $studentId) {
    $usersFile = 'data/users.json';
    
    if (!file_exists($usersFile)) {
        return false;
    }
    
    $users = json_decode(file_get_contents($usersFile), true);
    if (!$users) {
        return false;
    }
    
    foreach ($users as $user) {
        if ($user['email'] === $email || $user['studentId'] === $studentId) {
            return true;
        }
    }
    
    return false;
}

// Function to save user data
function saveUser($userData) {
    // Create data directory if it doesn't exist
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    $usersFile = 'data/users.json';
    $users = [];
    
    // Load existing users
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        if (!$users) {
            $users = [];
        }
    }
    
    // Add new user
    $users[] = $userData;
    
    // Save to file
    return file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $studentId = sanitizeInput($_POST['studentId'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($studentId)) {
        $errors[] = 'Student ID is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (empty($confirmPassword)) {
        $errors[] = 'Please confirm your password';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    // Check if user already exists
    if (empty($errors) && userExists($email, $studentId)) {
        $errors[] = 'User with this email or student ID already exists';
    }
    
    if (empty($errors)) {
        // Create user data
        $userData = [
            'id' => uniqid(),
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'studentId' => $studentId,
            'phone' => $phone,
            'password' => hashPassword($password),
            'registrationDate' => date('Y-m-d H:i:s'),
            'lastLogin' => null
        ];
        
        // Save user
        if (saveUser($userData)) {
            $response['success'] = true;
            $response['message'] = 'Registration successful! You can now log in.';
            
            // Log the registration
            error_log("New user registered: " . $email);
        } else {
            $response['message'] = 'Failed to save user data. Please try again.';
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
    header('Location: login.html');
} else {
    $_SESSION['error_message'] = $response['message'];
    header('Location: register.html');
}
exit;
?>