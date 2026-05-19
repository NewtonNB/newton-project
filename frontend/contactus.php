<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Nyabikoni Secondary School</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="contactus.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="modern-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Dynamic Contact Styles -->
    <style>
        /* Enhanced Dynamic Features */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .notification {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 10px;
            box-shadow: 0 8px 32px rgba(39, 174, 96, 0.3);
            transform: translateX(450px);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.error {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .notification.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .notification .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .notification .close-btn:hover {
            opacity: 1;
        }
        
        /* Enhanced Form Styles */
        .form-container {
            position: relative;
            overflow: hidden;
        }
        
        .form-step {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .form-step.slide-out {
            transform: translateX(-100%);
            opacity: 0;
        }
        
        .form-step.slide-in {
            transform: translateX(0);
            opacity: 1;
        }
        
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #3949ab, #1a237e);
            border-radius: 2px;
            transition: width 0.5s ease;
            width: 0%;
        }
        
        /* Real-time Validation */
        .inputBox {
            position: relative;
        }
        
        .validation-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .inputBox.valid .validation-icon {
            opacity: 1;
            color: #27ae60;
        }
        
        .inputBox.invalid .validation-icon {
            opacity: 1;
            color: #e74c3c;
        }
        
        .validation-message {
            font-size: 0.9rem;
            margin-top: 8px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            min-height: 20px;
            font-weight: 500;
        }
        
        .validation-message.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .validation-message.error {
            color: #e74c3c;
            font-weight: 600;
        }
        
        .validation-message.success {
            color: #27ae60;
            font-weight: 600;
        }
        
        .inputBox input.error,
        .inputBox textarea.error {
            border-color: #e74c3c !important;
            background-color: #fdeaea !important;
        }
        
        .inputBox input.valid,
        .inputBox textarea.valid {
            border-color: #27ae60 !important;
            background-color: #eafaf1 !important;
        }
        
        /* Character Counter */
        .char-counter {
            font-size: 0.8rem;
            color: #666;
            text-align: right;
            margin-top: 5px;
            transition: color 0.3s ease;
        }
        
        .char-counter.warning {
            color: #f39c12;
        }
        
        .char-counter.danger {
            color: #e74c3c;
        }
        
        /* Enhanced Submit Button */
        .submit-btn {
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Contact Info Enhancements */
        .contact-info-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .contact-info-item:hover {
            transform: translateX(10px) scale(1.02);
            background: rgba(255,255,255,0.2);
        }
        
        .contact-info-item.clicked {
            animation: pulse 0.6s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* FAQ Section */
        .faq-section {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 10px 40px rgba(26,35,126,0.10);
        }
        
        .faq-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 20px 0;
        }
        
        .faq-question {
            font-weight: 600;
            color: #1a237e;
            cursor: pointer;
            display: flex;
            justify-content: between;
            align-items: center;
            transition: color 0.3s ease;
        }
        
        .faq-question:hover {
            color: #3949ab;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            color: #666;
            padding-top: 0;
        }
        
        .faq-answer.open {
            max-height: 200px;
            padding-top: 15px;
        }
        
        .faq-toggle {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .faq-toggle.open {
            transform: rotate(180deg);
        }
        
        /* Live Chat Button */
        .live-chat-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            box-shadow: 0 8px 32px rgba(37, 211, 102, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            animation: bounce 2s infinite;
        }
        
        .live-chat-btn:hover {
            transform: scale(1.1);
            color: white;
            box-shadow: 0 12px 40px rgba(37, 211, 102, 0.4);
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Response Time Indicator */
        .response-time {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeInUp 1s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Office Hours */
        .office-hours {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .office-status {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .office-status.open {
            color: #27ae60;
        }
        
        .office-status.closed {
            color: #e74c3c;
        }
        
        /* Mobile Enhancements */
        @media (max-width: 768px) {
            .notification-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
            
            .notification {
                transform: translateY(-100px);
            }
            
            .notification.show {
                transform: translateY(0);
            }
            
            .live-chat-btn {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once '../shared/config.php'; ?>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <!-- Header Section -->
    <section class="header">
        <div class="text-box wow animate__animated animate__fadeInUp animate__delay-1s">
            <h1>CONTACT US</h1>
            <p>Get in touch with us for any inquiries or information</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <div class="contactus fade-in-section">
        <div class="title fade-in-section">
            <h2>Get in Touch</h2>
        </div>
        <div class="box">
            <!-- Enhanced Contact Form -->
            <div class="contct form fade-in-section">
                <div class="response-time">
                    <i class="fas fa-clock"></i> We typically respond within 2-4 hours during business hours
                </div>
                
                <h3>Send a Message</h3>
                
                <!-- Progress Bar -->
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                
                <div class="form-container">
                    <form id="dynamicContactForm" class="form-step" novalidate>
                        <div class="formBox">
                            <div class="row50">
                                <div class="inputBox">
                                    <span>First Name *</span>
                                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                                    <i class="validation-icon fas fa-check"></i>
                                    <div class="validation-message"></div>
                                </div>
                                <div class="inputBox">
                                    <span>Last Name *</span>
                                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                                    <i class="validation-icon fas fa-check"></i>
                                    <div class="validation-message"></div>
                                </div>
                            </div>
                            <div class="row50">
                                <div class="inputBox">
                                    <span>Email Address *</span>
                                    <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                                    <i class="validation-icon fas fa-check"></i>
                                    <div class="validation-message"></div>
                                </div>
                                <div class="inputBox">
                                    <span>Phone Number *</span>
                                    <input type="tel" id="phone" name="phone" placeholder="+256 XXX XXX XXX" required>
                                    <i class="validation-icon fas fa-check"></i>
                                    <div class="validation-message"></div>
                                </div>
                            </div>
                            <div class="row100">
                                <div class="inputBox">
                                    <span>Subject</span>
                                    <select id="subject" name="subject">
                                        <option value="">Select a subject</option>
                                        <option value="admission">Admission Inquiry</option>
                                        <option value="academic">Academic Information</option>
                                        <option value="fees">Fees & Payment</option>
                                        <option value="events">Events & Activities</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="complaint">Complaint</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row100">
                                <div class="inputBox">
                                    <span>Message *</span>
                                    <textarea id="message" name="message" placeholder="Please describe your inquiry in detail..." required maxlength="1000"></textarea>
                                    <div class="char-counter" id="charCounter">0 / 1000 characters</div>
                                    <div class="validation-message"></div>
                                </div>
                            </div>
                            <div class="row100">
                                <div class="inputBox">
                                    <button type="submit" class="submit-btn" id="submitBtn">
                                        <span class="loading-spinner" id="loadingSpinner"></span>
                                        <span id="submitText">Send Message</span>
                                        <i class="fas fa-paper-plane" style="margin-left: 10px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enhanced Contact Information -->
            <div class="contact info fade-in-section">
                <h3>Contact Information</h3>
                
                <!-- Office Hours -->
                <div class="office-hours">
                    <div class="office-status" id="officeStatus">
                        <i class="fas fa-circle"></i> <span id="statusText">Office Open</span>
                    </div>
                    <div style="font-size: 0.9rem;">
                        Monday - Friday: 8:00 AM - 5:00 PM<br>
                        Saturday: 8:00 AM - 1:00 PM<br>
                        Sunday: Closed
                    </div>
                </div>
                
                <div class="infoBox">
                    <div class="contact-info-item" onclick="copyToClipboard('Kabale District, Kabale Municipality, Nyabikoni Ward, Uganda')">
                        <span><i class="fas fa-map-marker-alt"></i></span>
                        <p>Kabale District, Kabale Municipality<br>Nyabikoni Ward, Uganda</p>
                    </div>
                    <div class="contact-info-item" onclick="copyToClipboard('nyabikonisecschool@gmail.com')">
                        <span><i class="fas fa-envelope"></i></span>
                        <a href="mailto:nyabikonisecschool@gmail.com">nyabikonisecschool@gmail.com</a>
                    </div>
                    <div class="contact-info-item" onclick="copyToClipboard('+256703599882')">
                        <span><i class="fas fa-phone"></i></span>
                        <a href="tel:+256703599882">+256 703 599 882</a>
                    </div>
                    <div class="contact-info-item" onclick="copyToClipboard('+256775475629')">
                        <span><i class="fas fa-phone"></i></span>
                        <a href="tel:+256775475629">+256 775 475 629</a>
                    </div>

                    <!-- Social Media Links -->
                    <ul class="sci fade show transition-opacity">
                        <li><a href="https://www.facebook.com/profile.php?id=100094514101119" aria-label="Facebook" target="_blank"><i class="fab fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/nyabikoniss" aria-label="Twitter" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://www.youtube.com/channel/UCpiBxBBifIwLdhXDrqZggMA" aria-label="YouTube" target="_blank"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="https://www.instagram.com/nyabikoniss/" aria-label="Instagram" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>

            <!-- Enhanced Map -->
            <div class="contact map fade-in-section">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13898.983399327251!2d29.973432353126448!3d-1.2468836724494496!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dc0df04d1debb7%3A0xcf77785b2b3d1338!2sNyabikoni%20Secondary%20School!5e0!3m2!1sen!2sug!4v1732612844840!5m2!1sen!2sug" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Nyabikoni Secondary School Location">
                </iframe>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section fade-in-section">
            <h3 style="text-align: center; color: #1a237e; margin-bottom: 30px;">
                <i class="fas fa-question-circle"></i> Frequently Asked Questions
            </h3>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What are the admission requirements?</span>
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </div>
                <div class="faq-answer">
                    Students need to have completed primary education with a Primary Leaving Examination (PLE) certificate. Additional requirements include completed application forms, birth certificate, and passport photos.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What are the school fees?</span>
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </div>
                <div class="faq-answer">
                    School fees vary by class level and program. Please contact our administration office for current fee structures and payment plans. We also offer scholarship opportunities for qualified students.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>Do you offer boarding facilities?</span>
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </div>
                <div class="faq-answer">
                    Yes, we provide both boarding and day school options. Our boarding facilities are well-maintained with proper supervision and meals provided.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>What extracurricular activities are available?</span>
                    <i class="fas fa-chevron-down faq-toggle"></i>
                </div>
                <div class="faq-answer">
                    We offer various activities including sports (football, basketball, athletics), music, drama, debate club, science club, and community service programs.
                </div>
            </div>
        </div>
    </div>

    <!-- Live Chat Button -->
    <a href="https://wa.me/256703599882" class="live-chat-btn" target="_blank" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Modern Footer -->
    <?php include 'modern-footer.html'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    
    <script>
        // Dynamic Contact Form Management System
        class DynamicContactManager {
            constructor() {
                this.form = document.getElementById('dynamicContactForm');
                this.progressFill = document.getElementById('progressFill');
                this.submitBtn = document.getElementById('submitBtn');
                this.loadingSpinner = document.getElementById('loadingSpinner');
                this.submitText = document.getElementById('submitText');
                this.charCounter = document.getElementById('charCounter');
                this.isSubmitting = false;
                
                this.validationRules = {
                    firstName: { required: true, minLength: 2, pattern: /^[a-zA-Z\s]+$/ },
                    lastName: { required: true, minLength: 2, pattern: /^[a-zA-Z\s]+$/ },
                    email: { required: true, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
                    phone: { required: true, pattern: /^[\+]?[0-9\s\-\(\)]{10,15}$/ },
                    message: { required: true, minLength: 10, maxLength: 1000 }
                };
                
                this.init();
            }
            
            init() {
                this.setupEventListeners();
                this.updateOfficeStatus();
                this.startProgressTracking();
                
                // Initialize WOW.js
                new WOW().init();
                
                // Setup scroll animations
                this.setupScrollAnimations();
            }
            
            setupEventListeners() {
                // Real-time validation
                Object.keys(this.validationRules).forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field) {
                        field.addEventListener('input', () => this.validateField(fieldName));
                        field.addEventListener('blur', () => this.validateField(fieldName));
                        field.addEventListener('focus', () => this.clearFieldValidation(fieldName));
                    }
                });
                
                // Character counter for message
                const messageField = document.getElementById('message');
                if (messageField) {
                    messageField.addEventListener('input', () => this.updateCharCounter());
                }
                
                // Form submission
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                
                // Phone number formatting
                const phoneField = document.getElementById('phone');
                if (phoneField) {
                    phoneField.addEventListener('input', () => this.formatPhoneNumber());
                }
            }
            
            validateField(fieldName) {
                const field = document.getElementById(fieldName);
                if (!field) return true; // Field doesn't exist, skip validation
                
                const inputBox = field.closest('.inputBox');
                if (!inputBox) return true; // No inputBox found, skip validation
                
                const validationIcon = inputBox.querySelector('.validation-icon');
                const validationMessage = inputBox.querySelector('.validation-message');
                const rules = this.validationRules[fieldName];
                
                let isValid = true;
                let message = '';
                
                // Required validation
                if (rules.required && !field.value.trim()) {
                    isValid = false;
                    message = `${this.getFieldLabel(fieldName)} is required`;
                }
                
                // Pattern validation
                else if (field.value.trim() && rules.pattern && !rules.pattern.test(field.value)) {
                    isValid = false;
                    message = this.getPatternMessage(fieldName);
                }
                
                // Length validation
                else if (field.value.trim()) {
                    if (rules.minLength && field.value.length < rules.minLength) {
                        isValid = false;
                        message = `${this.getFieldLabel(fieldName)} must be at least ${rules.minLength} characters`;
                    } else if (rules.maxLength && field.value.length > rules.maxLength) {
                        isValid = false;
                        message = `${this.getFieldLabel(fieldName)} must not exceed ${rules.maxLength} characters`;
                    }
                }
                
                // Update UI
                inputBox.classList.remove('valid', 'invalid');
                field.classList.remove('valid', 'error');
                if (validationMessage) {
                    validationMessage.classList.remove('show', 'error', 'success');
                }
                
                if (field.value.trim()) {
                    if (isValid) {
                        inputBox.classList.add('valid');
                        field.classList.add('valid');
                        if (validationIcon) {
                            validationIcon.className = 'validation-icon fas fa-check';
                        }
                        if (validationMessage) {
                            validationMessage.textContent = 'Looks good!';
                            validationMessage.classList.add('show', 'success');
                        }
                    } else {
                        inputBox.classList.add('invalid');
                        field.classList.add('error');
                        if (validationIcon) {
                            validationIcon.className = 'validation-icon fas fa-times';
                        }
                        if (validationMessage) {
                            validationMessage.textContent = message;
                            validationMessage.classList.add('show', 'error');
                        }
                    }
                }
                
                this.updateProgress();
                return isValid;
            }
            
            clearFieldValidation(fieldName) {
                const field = document.getElementById(fieldName);
                if (!field) return;
                
                const inputBox = field.closest('.inputBox');
                if (!inputBox) return;
                
                const validationMessage = inputBox.querySelector('.validation-message');
                
                if (!field.value.trim()) {
                    inputBox.classList.remove('valid', 'invalid');
                    if (validationMessage) {
                        validationMessage.classList.remove('show');
                    }
                }
            }
            
            getFieldLabel(fieldName) {
                const labels = {
                    firstName: 'First name',
                    lastName: 'Last name',
                    email: 'Email address',
                    phone: 'Phone number',
                    message: 'Message'
                };
                return labels[fieldName] || fieldName;
            }
            
            getPatternMessage(fieldName) {
                const messages = {
                    firstName: 'First name should only contain letters',
                    lastName: 'Last name should only contain letters',
                    email: 'Please enter a valid email address',
                    phone: 'Please enter a valid phone number',
                };
                return messages[fieldName] || 'Invalid format';
            }
            
            updateCharCounter() {
                const messageField = document.getElementById('message');
                const currentLength = messageField.value.length;
                const maxLength = 1000;
                
                this.charCounter.textContent = `${currentLength} / ${maxLength} characters`;
                
                this.charCounter.classList.remove('warning', 'danger');
                if (currentLength > maxLength * 0.8) {
                    this.charCounter.classList.add('warning');
                }
                if (currentLength > maxLength * 0.95) {
                    this.charCounter.classList.add('danger');
                }
            }
            
            formatPhoneNumber() {
                const phoneField = document.getElementById('phone');
                let value = phoneField.value.replace(/\D/g, '');
                
                if (value.startsWith('256')) {
                    value = '+' + value;
                } else if (value.startsWith('0')) {
                    value = '+256' + value.substring(1);
                }
                
                phoneField.value = value;
            }
            
            updateProgress() {
                const fields = Object.keys(this.validationRules);
                let validFields = 0;
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    const inputBox = field.closest('.inputBox');
                    if (inputBox.classList.contains('valid') || (field.value.trim() && !inputBox.classList.contains('invalid'))) {
                        validFields++;
                    }
                });
                
                const progress = (validFields / fields.length) * 100;
                this.progressFill.style.width = `${progress}%`;
            }
            
            startProgressTracking() {
                // Initial progress update
                this.updateProgress();
                
                // Update progress every second
                setInterval(() => {
                    this.updateProgress();
                }, 1000);
            }
            
            async handleSubmit(e) {
                e.preventDefault();
                
                // Prevent duplicate submissions
                if (this.isSubmitting) {
                    return;
                }
                
                // Validate all fields
                let isFormValid = true;
                Object.keys(this.validationRules).forEach(fieldName => {
                    if (!this.validateField(fieldName)) {
                        isFormValid = false;
                    }
                });
                
                if (!isFormValid) {
                    this.showNotification('Please fix the errors before submitting', 'error');
                    return;
                }
                
                // Show loading state
                this.isSubmitting = true;
                this.setLoadingState(true);
                
                try {
                    const formData = new FormData(this.form);
                    
                    const response = await fetch('contactus_process_ajax.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
                        this.form.reset();
                        this.resetValidation();
                        this.updateProgress();
                        
                        // Confetti effect
                        this.showConfetti();
                    } else {
                        this.showNotification(result.message || 'Failed to send message. Please try again.', 'error');
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    this.showNotification('Network error. Please check your connection and try again.', 'error');
                } finally {
                    this.setLoadingState(false);
                    this.isSubmitting = false;
                }
            }
            
            setLoadingState(loading) {
                if (loading) {
                    this.submitBtn.disabled = true;
                    this.loadingSpinner.style.display = 'inline-block';
                    this.submitText.textContent = 'Sending...';
                } else {
                    this.submitBtn.disabled = false;
                    this.loadingSpinner.style.display = 'none';
                    this.submitText.textContent = 'Send Message';
                }
            }
            
            resetValidation() {
                document.querySelectorAll('.inputBox').forEach(inputBox => {
                    inputBox.classList.remove('valid', 'invalid');
                    const validationMessage = inputBox.querySelector('.validation-message');
                    validationMessage.classList.remove('show');
                });
                
                this.charCounter.textContent = '0 / 1000 characters';
                this.charCounter.classList.remove('warning', 'danger');
            }
            
            showNotification(message, type = 'success') {
                const container = document.getElementById('notificationContainer');
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                
                const icon = type === 'success' ? 'fa-check-circle' : 
                           type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
                
                notification.innerHTML = `
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                    <button class="close-btn" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                container.appendChild(notification);
                
                // Show notification
                setTimeout(() => notification.classList.add('show'), 100);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 400);
                }, 5000);
            }
            
            showConfetti() {
                // Simple confetti effect
                const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'];
                
                for (let i = 0; i < 50; i++) {
                    setTimeout(() => {
                        const confetti = document.createElement('div');
                        confetti.style.cssText = `
                            position: fixed;
                            width: 10px;
                            height: 10px;
                            background: ${colors[Math.floor(Math.random() * colors.length)]};
                            left: ${Math.random() * 100}vw;
                            top: -10px;
                            z-index: 10000;
                            pointer-events: none;
                            animation: confetti-fall 3s linear forwards;
                        `;
                        
                        document.body.appendChild(confetti);
                        
                        setTimeout(() => confetti.remove(), 3000);
                    }, i * 50);
                }
            }
            
            updateOfficeStatus() {
                const now = new Date();
                const day = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
                const hour = now.getHours();
                
                const statusElement = document.getElementById('officeStatus');
                const statusText = document.getElementById('statusText');
                
                let isOpen = false;
                
                if (day >= 1 && day <= 5) { // Monday to Friday
                    isOpen = hour >= 8 && hour < 17;
                } else if (day === 6) { // Saturday
                    isOpen = hour >= 8 && hour < 13;
                }
                
                if (isOpen) {
                    statusElement.className = 'office-status open';
                    statusText.innerHTML = '<i class="fas fa-circle"></i> Office Open';
                } else {
                    statusElement.className = 'office-status closed';
                    statusText.innerHTML = '<i class="fas fa-circle"></i> Office Closed';
                }
            }
            
            setupScrollAnimations() {
                const faders = document.querySelectorAll(".fade-in-section");
                
                const appearOptions = {
                    threshold: 0.1,
                    rootMargin: "0px 0px -50px 0px"
                };
                
                const appearOnScroll = new IntersectionObserver(function (entries, appearOnScroll) {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add("is-visible");
                        appearOnScroll.unobserve(entry.target);
                    });
                }, appearOptions);
                
                faders.forEach(fader => {
                    appearOnScroll.observe(fader);
                });
            }
        }
        
        // Global functions
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Find the clicked element and add animation
                event.currentTarget.classList.add('clicked');
                setTimeout(() => {
                    event.currentTarget.classList.remove('clicked');
                }, 600);
                
                // Show notification
                contactManager.showNotification(`Copied: ${text}`, 'success');
            }).catch(() => {
                contactManager.showNotification('Failed to copy to clipboard', 'error');
            });
        }
        
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const toggle = element.querySelector('.faq-toggle');
            
            answer.classList.toggle('open');
            toggle.classList.toggle('open');
        }
        
        // Add confetti animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes confetti-fall {
                0% {
                    transform: translateY(-10px) rotate(0deg);
                    opacity: 1;
                }
                100% {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Initialize the system
        let contactManager;
        document.addEventListener('DOMContentLoaded', function() {
            contactManager = new DynamicContactManager();
        });
    </script>
</body>
</html>