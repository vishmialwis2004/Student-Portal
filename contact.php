<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.html');
    exit;
}

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

// Function to save contact form submission
function saveContactSubmission($data) {
    // Create data directory if it doesn't exist
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    $contactFile = 'data/contacts.json';
    $submissions = [];
    
    // Load existing submissions
    if (file_exists($contactFile)) {
        $submissions = json_decode(file_get_contents($contactFile), true);
        if (!$submissions) {
            $submissions = [];
        }
    }
    
    // Add new submission
    $submissions[] = $data;
    
    // Save to file
    return file_put_contents($contactFile, json_encode($submissions, JSON_PRETTY_PRINT));
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $priority = sanitizeInput($_POST['priority'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($priority)) {
        $errors[] = 'Priority level is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    if (empty($errors)) {
        // Create submission data
        $submissionData = [
            'id' => uniqid(),
            'userId' => $_SESSION['user_id'],
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'priority' => $priority,
            'message' => $message,
            'submissionDate' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
        
        // Save submission
        if (saveContactSubmission($submissionData)) {
            $response['success'] = true;
            $response['message'] = 'Thank you for contacting us! We\'ll get back to you soon.';
            
            // Log the submission
            error_log("Contact form submitted by: " . $email);
        } else {
            $response['message'] = 'Failed to save your message. Please try again.';
        }
    } else {
        $response['message'] = implode('. ', $errors);
    }
}

// Return JSON response for AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For regular form submission, set session message and reload page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($response['success']) {
        $_SESSION['success_message'] = $response['message'];
    } else {
        $_SESSION['error_message'] = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Contact</title>
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
                    <a href="profile.php" class="nav-link">Profile</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link active">Contact</a>
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
        <div class="contact-container">
            <div class="contact-header">
                <h1>Get in Touch</h1>
                <p>We're here to help! Send us a message and we'll respond as soon as possible.</p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h2>Contact Information</h2>
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon"></div>
                            <div>
                                <h3>Email</h3>
                                <p>support@studentportal.edu</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"></div>
                            <div>
                                <h3>Phone</h3>
                                <p>011 2147852</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"></div>
                            <div>
                                <h3>Office</h3>
                                <p>Student Services Building<br>Room 201</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"></div>
                            <div>
                                <h3>Hours</h3>
                                <p>Mon-Fri: 8:00 AM - 6:00 PM<br>Sat: 9:00 AM - 4:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-section">
                    <form id="contactForm" action="contact.php" method="POST" class="form">
                        <div class="form-group">
                            <label for="contactName">Full Name</label>
                            <input type="text" id="contactName" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
                            <span class="error-message" id="contactNameError"></span>
                        </div>

                        <div class="form-group">
                            <label for="contactEmail">Email Address</label>
                            <input type="email" id="contactEmail" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                            <span class="error-message" id="contactEmailError"></span>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="technical">Technical Support</option>
                                <option value="academic">Academic Support</option>
                                <option value="billing">Billing Question</option>
                                <option value="feedback">Feedback</option>
                            </select>
                            <span class="error-message" id="subjectError"></span>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority Level</label>
                            <div class="rating-container">
                                <input type="radio" id="priority1" name="priority" value="1" required>
                                <label for="priority1" class="rating-label">1 - Low</label>
                                
                                <input type="radio" id="priority2" name="priority" value="2" required>
                                <label for="priority2" class="rating-label">2 - Medium</label>
                                
                                <input type="radio" id="priority3" name="priority" value="3" required>
                                <label for="priority3" class="rating-label">3 - High</label>
                                
                                <input type="radio" id="priority4" name="priority" value="4" required>
                                <label for="priority4" class="rating-label">4 - Urgent</label>
                                
                                <input type="radio" id="priority5" name="priority" value="5" required>
                                <label for="priority5" class="rating-label">5 - Critical</label>
                            </div>
                            <span class="error-message" id="priorityError"></span>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" placeholder="Please describe your inquiry in detail..." required></textarea>
                            <span class="error-message" id="messageError"></span>
                        </div>

                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal for success/error messages -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h3 id="modalTitle">Message Sent!</h3>
            <p id="modalMessage">Thank you for contacting us. We'll get back to you soon.</p>
            <button id="modalButton" class="btn btn-primary">OK</button>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('Success!', '<?php echo addslashes($_SESSION['success_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('Error', '<?php echo addslashes($_SESSION['error_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <script src="script.js"></script>
</body>
</html>