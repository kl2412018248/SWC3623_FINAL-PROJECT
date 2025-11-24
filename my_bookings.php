<?php
include 'includes/header.php';

// Ensure user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
?>

<h1 class="mb-4">My Bookings</h1>

<div class="list-group">
    <?php
    $sql = "SELECT e.title, e.event_date, e.location, b.booking_date 
            FROM bookings b 
            JOIN events e ON b.event_id = e.id 
            WHERE b.user_id = ? 
            ORDER BY e.event_date ASC";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="list-group-item list-group-item-action flex-column align-items-start">';
                echo '  <div class="d-flex w-100 justify-content-between">';
                echo '    <h5 class="mb-1">' . htmlspecialchars($row['title']) . '</h5>';
                echo '    <small>Event Date: ' . date("F j, Y", strtotime($row['event_date'])) . '</small>';
                echo '  </div>';
                echo '  <p class="mb-1">' . htmlspecialchars($row['location']) . '</p>';
                echo '  <small>Booked on: ' . date("M d, Y", strtotime($row['booking_date'])) . '</small>';
                echo '</div>';
            }
        } else {
            echo "<p>You have no bookings yet. <a href='events.php'>Find an event</a> to book!</p>";
        }
        $stmt->close();
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>