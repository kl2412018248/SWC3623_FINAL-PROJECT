<?php require_once(dirname(__FILE__) . '/includes/header.php'); ?>

<!-- Hero Section with Background Image and Search Bar -->
<style>
    /* Add this style block here or move it to your css/style.css file */
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/hero-bg.jpg') no-repeat center center;
        background-size: cover;
        color: white;
        padding: 120px 0;
        text-align: center;
    }
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
    }
    .hero-section p {
        font-size: 1.25rem;
    }
    .category-card {
        transition: transform 0.2s;
    }
    .category-card:hover {
        transform: translateY(-10px);
    }
</style>

<div class="hero-section mb-5">
    <div class="container">
        <h1>Find Your Next Experience</h1>
        <p class="lead mb-4">The ultimate platform for discovering and booking live events.</p>
        
        <!-- Search Form -->
        <form action="events.php" method="GET" class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Search for events, artists, or venues...">
                    <button class="btn btn-primary btn-lg" type="submit">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Event Categories Section -->
<div class="container">
    <h2 class="text-center mb-4">Browse by Category</h2>
    <div class="row text-center">
        <!-- You can link these to a filtered events page, e.g., events.php?category=Music -->
        <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <a href="events.php?category=Music" class="card category-card text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <i class="fas fa-music fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Music & Concerts</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <a href="events.php?category=Tech" class="card category-card text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <i class="fas fa-laptop-code fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Tech Conferences</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <a href="events.php?category=Workshop" class="card category-card text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <i class="fas fa-chalkboard-teacher fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Workshops</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
        <a href="events.php?category=Community" class="card category-card text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Community</h5>
                </div>
            </a>
        </div>
    </div>
</div>

<hr class="my-5">

<!-- Featured Events Section  -->
<!-- Featured Events Section (Corrected and Improved) -->
<div class="container">
    <h2 class="text-center mb-4" data-aos="fade-up">Featured Events</h2>
    <div class="row">
        <?php
        // Fetch 3 featured events (e.g., the earliest upcoming ones)
        $sql = "SELECT id, title, description, image_path, event_date, location FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $delay = 100; // Initialize delay for staggered animation
            while($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                echo '  <div class="card h-100 shadow-sm event-card">';
                
                // --- New Card Structure from previous step ---
                echo '    <div class="card-img-container">';
                echo '      <img src="images/' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="' . htmlspecialchars($row['title']) . '">';
                echo '      <div class="card-img-overlay">';
                echo '          <h5 class="card-overlay-title">' . htmlspecialchars($row['title']) . '</h5>';
                echo '          <p class="card-overlay-text"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['location']) . '</p>';
                echo '      </div>';
                echo '    </div>';
                echo '    <div class="card-body">';
                echo '      <p class="card-text text-muted"><i class="fas fa-calendar-alt"></i> ' . date("D, M j, Y", strtotime($row['event_date'])) . '</p>';
                echo '      <p class="card-text">' . substr(htmlspecialchars($row['description']), 0, 90) . '...</p>';
                echo '      <a href="event_details.php?id=' . $row['id'] . '" class="btn btn-primary mt-auto">View Details</a>';
                echo '    </div>';
                // --- End of New Card Structure ---

                echo '  </div>';
                echo '</div>';
                $delay += 100; // Increment delay for the next card
            }
        } else {
            // This message is shown if the database query returns no events.
            echo "<div class='col-12 text-center'><p class='lead text-muted'>No featured events found at the moment. Please check back later!</p></div>";
        }
        ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>