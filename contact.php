<?php 
include 'includes/header.php'; 

$message = '';
// This is a simple PHP mailer script. 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST['subject']));
    $user_message = strip_tags(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($subject) || empty($user_message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Please fill out all fields correctly.</div>';
    } else {
        // --- SIMULATION ---
        // On a real server, you would use the mail() function. For this project, a success message is enough.
        $message = '<div class="alert alert-success">Thank you for your message! We will get back to you shortly. (Email sending is simulated)</div>';
    }
}
?>

<style>
    /* Page-specific styles for Contact Us */
    .contact-hero {
        padding: 80px 0;
        background-color: #f1f3f5;
        text-align: center;
        margin-top: 75px; /* Adjust for fixed navbar */
    }
    .contact-section {
        padding: 4rem 0;
    }
    .contact-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    .contact-info-item i {
        font-size: 1.5rem;
        width: 50px; height: 50px;
        line-height: 50px; text-align: center;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        margin-right: 20px;
    }
    .contact-info-item a {
        text-decoration: none; color: var(--text-color);
        font-weight: 600; transition: color 0.3s ease;
    }
    .contact-info-item a:hover { color: var(--primary-color); }
    .map-container {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        height: 300px;
    }
</style>

<div class="contact-hero">
    <div class="container" data-aos="fade-in">
        <h1 class="display-4">Get In Touch</h1>
        <p class="lead text-muted">Have a question or a great idea? We'd love to hear from you.</p>
    </div>
</div>

<div class="contact-section">
    <div class="container">
        <div class="row">
            <!-- Left Column: Contact Form -->
            <div class="col-lg-7 mb-5 mb-lg-0" data-aos="fade-right">
                <div class="card shadow-lg border-0 p-4">
                    <h3 class="mb-4">Send us a Message</h3>
                    <?php echo $message; // Display success/error message for the form ?>
                    <form action="contact.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Contact Info & Map -->
            <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
                <div class="contact-info">
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div><strong>Email:</strong><br><a href="mailto:support.it@eventura.com">support.it@eventura.com</a></div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div><strong>Phone:</strong><br><a href="tel:+60399973015">+60 3 9997 3015</a></div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Address:</strong><br>The Vertical Corporate Office Tower A, Bangsar South, Kuala Lumpur</div>
                    </div>
                </div>
                
                <div class="map-container mt-4">
                
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.9387056697074!2d101.663249873944!3d3.110920053388597!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4ba617b94b55%3A0xcdc435ee7451c3c0!2sThe%20Vertical%20Corporate%20Office%20Tower%20A!5e0!3m2!1sen!2smy!4v1763531872253!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        width="100%"
                        height="300"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=The+Vertical+Corporate+Office+Tower+A,Kuala+Lumpur">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>