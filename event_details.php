<?php
include 'includes/header.php';

// Check if an event ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container py-5'><div class='alert alert-danger'>No event specified.</div></div>";
    include 'includes/footer.php';
    exit;
}

$event_id = $_GET['id'];
$message = '';

// ... (Your booking logic remains the same here) ...
// Handle the booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_event'])) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }
    $user_id = $_SESSION['id'];
    $check_sql = "SELECT id FROM bookings WHERE user_id = ? AND event_id = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("ii", $user_id, $event_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $message = "<div class='alert alert-warning'>You have already booked this event.</div>";
        } else {
            $sql_insert_booking = "INSERT INTO bookings (user_id, event_id) VALUES (?, ?)"; // Assuming a simple booking for now
            if ($stmt_insert = $conn->prepare($sql_insert_booking)) {
                $stmt_insert->bind_param("ii", $user_id, $event_id);
                if ($stmt_insert->execute()) {
                    $message = "<div class='alert alert-success'>Booking successful! You can view it in 'My Bookings'.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
                }
                $stmt_insert->close();
            }
        }
        $check_stmt->close();
    }
}


// Fetch event details
$sql = "SELECT e.*, u.username as organizer_name FROM events e JOIN admins u ON e.organizer_id = u.id WHERE e.id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        echo "<div class='container py-5'><div class='alert alert-danger'>Event not found.</div></div>";
        include 'includes/footer.php';
        exit;
    }
    $stmt->close();
}
?>

<style>
    /* Page-specific styles for Event Details */
    .event-hero {
        padding: 150px 0;
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
        position: relative;
    }
    .event-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6));
    }
    .event-hero h1 {
        position: relative;
        font-size: 4rem;
        color: white;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
    }
    .details-card {
        background-color: #ffffff;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 2rem;
    }
    .details-list .list-group-item {
        border: none;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    .details-list .list-group-item:last-child {
        border-bottom: none;
    }
    .details-list .list-group-item i {
        color: var(--primary-color);
        margin-right: 15px;
        width: 20px;
        text-align: center;
    }
</style>

<!-- Hero Section with the Event Image as Background -->
<div class="event-hero" style="background-image: url('images/<?php echo htmlspecialchars($event['image_path']); ?>');">
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Left Column: Description -->
        <div class="col-lg-8">
            <div class="details-card mb-4" data-aos="fade-up">
                <h2>About this Event</h2>
                <hr>
                <p class="lead"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>
        </div>

        <!-- Right Column: Details & Booking -->
        <div class="col-lg-4">
            <div class="details-card sticky-top" style="top: 100px;" data-aos="fade-up" data-aos-delay="200">
                <h4 class="mb-3">Event Details</h4>
                <?php if ($message) { echo $message; } // Display booking messages here ?>
                
                <ul class="list-group list-group-flush details-list">
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-calendar-alt"></i> <strong><?php echo date("F j, Y", strtotime($event['event_date'])); ?></strong></li>
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-clock"></i> <strong><?php echo date("g:i A", strtotime($event['event_time'])); ?></strong></li>
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-map-marker-alt"></i> <strong><?php echo htmlspecialchars($event['location']); ?></strong></li>
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-tag"></i> <strong><?php echo htmlspecialchars($event['category']); ?></strong></li>
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-dollar-sign"></i> <strong>RM <?php echo number_format($event['price'], 2); ?></strong></li>
                    <li class="list-group-item d-flex align-items-center"><i class="fas fa-user-tie"></i> <span>Organized by <strong><?php echo htmlspecialchars($event['organizer_name']); ?></strong></span></li>
                </ul>

                <div class="mt-4">
                    <?php if (isset($_SESSION['loggedin'])): ?>
                        <?php if ($_SESSION['role'] === 'user'): ?>
                            <form action="" method="post">
                                <input type="hidden" name="book_event" value="1">
                                <button type="submit" class="btn btn-primary w-100 btn-lg">Book Now</button>
                            </form>
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                             <div class="alert alert-info">Admins cannot book events.</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-100 btn-lg">Login to Book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>