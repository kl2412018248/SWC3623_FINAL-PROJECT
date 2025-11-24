<?php 
// --- START OF PHP BLOCK ---

// Include the header file, which starts the HTML document
require_once(dirname(__FILE__) . '/includes/header.php'); 

// --- Logic for Filtering and Searching Events ---

// Base SQL query
$sql = "SELECT id, title, description, event_date, location, image_path FROM events WHERE event_date >= CURDATE()";
$params = [];
$types = '';
$page_title = "All Events"; // Default page title

// Check if a category is specified in the URL
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
    $page_title = htmlspecialchars($category) . " Events";
}

// Check if a search term is specified in the URL
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $sql .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $search_param = "%" . $search_term . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
    $page_title = "Search Results for '" . htmlspecialchars($search_term) . "'";
}

// Always order the results
$sql .= " ORDER BY event_date ASC";

// --- END OF PHP BLOCK, HTML BEGINS BELOW ---
?>

<style>
    .events-hero {
        padding: 80px 0;
        background-color: #f1f3f5; /* A light grey background */
        text-align: center;
        margin-top: 75px; /* Add margin to account for fixed navbar height */
    }
</style>

<div class="events-hero">
    <div class="container">
        <h1 class="display-5" data-aos="fade-in"><?php echo $page_title; ?></h1>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <?php
        // Prepare and execute the SQL query built above
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $delay = 0; // Start delay for animation
            while($row = $result->fetch_assoc()) {
                $delay += 100;
                echo '<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="' . $delay . '">';
                echo '  <div class="card h-100 shadow-sm event-card">';
                
                echo '    <div class="card-img-container">';
                echo '      <img src="images/' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="' . htmlspecialchars($row['title']) . '">';
                echo '      <div class="card-img-overlay">';
                echo '          <h5 class="card-overlay-title">' . htmlspecialchars($row['title']) . '</h5>';
                echo '          <p class="card-overlay-text"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($row['location']) . '</p>';
                echo '      </div>';
                echo '    </div>';
                echo '    <div class="card-body d-flex flex-column">';
                echo '      <p class="card-text text-muted"><i class="fas fa-calendar-alt"></i> ' . date("D, M j, Y", strtotime($row['event_date'])) . '</p>';
                echo '      <p class="card-text">' . substr(htmlspecialchars($row['description']), 0, 90) . '...</p>';
                echo '      <a href="event_details.php?id=' . $row['id'] . '" class="btn btn-primary mt-auto">View Details</a>';
                echo '    </div>';

                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-info' data-aos='fade-up'>No events found matching your criteria.</div></div>";
        }
        $stmt->close();
        ?>
    </div>
</div>

<?php 
// Include the footer to close the HTML document
require_once(dirname(__FILE__) . '/includes/footer.php'); 
?>