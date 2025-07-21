// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            navToggle.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                navToggle.classList.remove('active');
            });
        });
    }

    // Password Toggle Functionality
    setupPasswordToggles();

    // Form Validation Setup
    setupFormValidation();

    // Modal Functionality
    setupModal();

    // Load Profile Data (if on profile page)
    loadProfileData();
});

// Password Toggle Functions
function setupPasswordToggles() {
    const toggles = [
        { toggleId: 'passwordToggle', inputId: 'password' },
        { toggleId: 'confirmPasswordToggle', inputId: 'confirmPassword' },
        { toggleId: 'loginPasswordToggle', inputId: 'loginPassword' }
    ];

    toggles.forEach(({ toggleId, inputId }) => {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);

        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                toggle.textContent = type === 'password' ? '👁️' : '👁️';
            });
        }
    });
}

// Form Validation Functions
function setupFormValidation() {
    // Registration Form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateRegistrationForm()) {
                submitForm(registerForm, 'Registration successful!', 'Your account has been created successfully. You can now log in.');
            }
        });
    }

    // Login Form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateLoginForm()) {
                submitForm(loginForm, 'Welcome back!', 'You have been logged in successfully.');
            }
        });
    }

    // Contact Form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateContactForm()) {
                submitForm(contactForm, 'Message sent!', 'Thank you for contacting us. We\'ll get back to you soon.');
            }
        });
    }
}

function validateRegistrationForm() {
    let isValid = true;

    // Clear previous errors
    clearErrors();

    // Validate First Name
    const firstName = document.getElementById('firstName').value.trim();
    if (!firstName) {
        showError('firstNameError', 'First name is required');
        isValid = false;
    }

    // Validate Last Name
    const lastName = document.getElementById('lastName').value.trim();
    if (!lastName) {
        showError('lastNameError', 'Last name is required');
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('email').value.trim();
    if (!email) {
        showError('emailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('emailError', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate Student ID
    const studentId = document.getElementById('studentId').value.trim();
    if (!studentId) {
        showError('studentIdError', 'Student ID is required');
        isValid = false;
    }

    // Validate Phone
    const phone = document.getElementById('phone').value.trim();
    if (!phone) {
        showError('phoneError', 'Phone number is required');
        isValid = false;
    }

    // Validate Password
    const password = document.getElementById('password').value;
    if (!password) {
        showError('passwordError', 'Password is required');
        isValid = false;
    } else if (password.length < 8) {
        showError('passwordError', 'Password must be at least 8 characters long');
        isValid = false;
    }

    // Validate Confirm Password
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (!confirmPassword) {
        showError('confirmPasswordError', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirmPasswordError', 'Passwords do not match');
        isValid = false;
    }

    return isValid;
}

function validateLoginForm() {
    let isValid = true;

    // Clear previous errors
    clearErrors();

    // Validate Email
    const email = document.getElementById('loginEmail').value.trim();
    if (!email) {
        showError('loginEmailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('loginEmailError', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate Password
    const password = document.getElementById('loginPassword').value;
    if (!password) {
        showError('loginPasswordError', 'Password is required');
        isValid = false;
    }

    return isValid;
}

function validateContactForm() {
    let isValid = true;

    // Clear previous errors
    clearErrors();

    // Validate Name
    const name = document.getElementById('contactName').value.trim();
    if (!name) {
        showError('contactNameError', 'Name is required');
        isValid = false;
    }

    // Validate Email
    const email = document.getElementById('contactEmail').value.trim();
    if (!email) {
        showError('contactEmailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('contactEmailError', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate Subject
    const subject = document.getElementById('subject').value;
    if (!subject) {
        showError('subjectError', 'Please select a subject');
        isValid = false;
    }

    // Validate Priority
    const priority = document.querySelector('input[name="priority"]:checked');
    if (!priority) {
        showError('priorityError', 'Please select a priority level');
        isValid = false;
    }

    // Validate Message
    const message = document.getElementById('message').value.trim();
    if (!message) {
        showError('messageError', 'Message is required');
        isValid = false;
    } else if (message.length < 10) {
        showError('messageError', 'Message must be at least 10 characters long');
        isValid = false;
    }

    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.classList.remove('show');
    });
}

// Form Submission
function submitForm(form, title, message) {
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;

        // Show success modal
        showModal(title, message);

        // Reset form
        form.reset();

        // For login form, redirect to profile
        if (form.id === 'loginForm') {
            setTimeout(() => {
                window.location.href = 'profile.html';
            }, 2000);
        }
    }, 1500);
}

// Modal Functions
function setupModal() {
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('closeModal');
    const modalButton = document.getElementById('modalButton');

    if (closeModal) {
        closeModal.addEventListener('click', hideModal);
    }

    if (modalButton) {
        modalButton.addEventListener('click', hideModal);
    }

    if (modal) {
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal();
            }
        });
    }
}

function showModal(title, message) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');

    if (modal && modalTitle && modalMessage) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Profile Data Loading
function loadProfileData() {
    // This would typically fetch data from the server
    // For demo purposes, we'll use sample data
    if (window.location.pathname.includes('profile.html')) {
        const profileData = {
            firstName: 'John',
            lastName: 'Doe',
            email: 'john.doe@student.edu',
            studentId: 'STU2024001',
            phone: '0714589685',
            registrationDate: 'January 15, 2024'
        };

        updateProfileDisplay(profileData);
    }
}

function updateProfileDisplay(data) {
    const elements = {
        profileName: `${data.firstName} ${data.lastName}`,
        profileEmail: data.email,
        displayFirstName: data.firstName,
        displayLastName: data.lastName,
        displayEmail: data.email,
        displayStudentId: data.studentId,
        displayPhone: data.phone,
        displayRegistrationDate: data.registrationDate
    };

    Object.entries(elements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });
}

// Input Enhancement Functions
document.addEventListener('DOMContentLoaded', function() {
    // Add real-time validation feedback
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateSingleField(this);
        });

        input.addEventListener('input', function() {
            // Clear error on input change
            const errorId = this.id + 'Error';
            const errorElement = document.getElementById(errorId);
            if (errorElement && errorElement.classList.contains('show')) {
                errorElement.classList.remove('show');
            }
        });
    });
});

function validateSingleField(field) {
    const value = field.value.trim();
    const fieldId = field.id;
    const errorId = fieldId + 'Error';

    // Basic validation
    if (field.hasAttribute('required') && !value) {
        showError(errorId, 'This field is required');
        return false;
    }

    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        showError(errorId, 'Please enter a valid email address');
        return false;
    }

    // Password validation
    if (fieldId === 'password' && value && value.length < 8) {
        showError(errorId, 'Password must be at least 8 characters long');
        return false;
    }

    // Confirm password validation
    if (fieldId === 'confirmPassword') {
        const password = document.getElementById('password')?.value;
        if (value && password && value !== password) {
            showError(errorId, 'Passwords do not match');
            return false;
        }
    }

    return true;
}

// Enhanced UX Features
function addLoadingAnimation(button) {
    button.innerHTML = '<span class="loading-spinner"></span> Loading...';
    button.disabled = true;
}

function removeLoadingAnimation(button, originalText) {
    button.innerHTML = originalText;
    button.disabled = false;
}

// Form Auto-save (for contact form)
function setupAutoSave() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const inputs = contactForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', debounce(saveFormData, 1000));
        });

        // Load saved data on page load
        loadFormData();
    }
}

function saveFormData() {
    const formData = {};
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const formDataObj = new FormData(contactForm);
        for (let [key, value] of formDataObj.entries()) {
            formData[key] = value;
        }
        localStorage.setItem('contactFormData', JSON.stringify(formData));
    }
}

function loadFormData() {
    const savedData = localStorage.getItem('contactFormData');
    if (savedData) {
        const formData = JSON.parse(savedData);
        Object.entries(formData).forEach(([key, value]) => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = value;
            }
        });
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize auto-save on contact page
if (window.location.pathname.includes('contact.html')) {
    setupAutoSave();
}