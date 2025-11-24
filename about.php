<?php include 'includes/header.php'; ?>

<style>
    .about-hero {
        padding: 100px 0;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/hero-bg.jpg') no-repeat center center;
        background-size: cover;
        text-align: center;
        color: white;
        margin-top: 75px; /* Adjust for fixed navbar */
    }
    .about-hero h1 {
        color: white;
        font-size: 3.5rem;
    }
    .about-content {
        padding: 4rem 0;
    }
    .about-content .icon {
        font-size: 2rem;
        color: var(--primary-color);
        margin-right: 1rem;
    }
    .about-content h3 {
        margin-bottom: 1rem;
    }
</style>

<div class="about-hero">
    <div class="container" data-aos="fade-in">
        <h1 class="display-4">About Eventura</h1>
        <p class="lead">Connecting people, creating memories.</p>
    </div>
</div>

<div class="about-content">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <!-- SIMPLE RELATIVE PATH -->
                <img src="images/about-us-image.jpg" class="img-fluid rounded shadow-lg" alt="People enjoying an event">
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <h2>Our Mission</h2>
                <p class="lead text-muted">To create the world's most trusted and user-centric platform for live events, fostering community and creating lasting memories for everyone involved.</p>
                <hr class="my-4">
                <div class="d-flex mb-3">
                    <i class="fas fa-users icon"></i>
                    <div>
                        <h4>For Attendees</h4>
                        <p>Discover a world of events at your fingertips. We make it easy to find events that match your interests and book your spot securely.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <i class="fas fa-rocket icon"></i>
                    <div>
                        <h4>For Organizers</h4>
                        <p>We provide a powerful dashboard for organizers to create, promote, and track their events, helping them reach a wider audience and manage bookings effortlessly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>