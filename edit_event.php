<?php
include 'includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

$event_id = $_GET['id'];
$organizer_id = $_SESSION['id'];
$message = '';

// Fetch the event to ensure it belongs to the logged-in admin
$sql_fetch = "SELECT * FROM events WHERE id = ? AND organizer_id = ?";
if($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("ii", $event_id, $organizer_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        // Event not found or doesn't belong to this admin
        header("location: manage_events.php");
        exit;
    }
    $stmt_fetch->close();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data and update the event
    $title = $_POST['title'];
    // ... get all other fields
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    $sql_update = "UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, category=?, price=? WHERE id=? AND organizer_id=?";
    
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("ssssssdii", $title, $description, $event_date, $event_time, $location, $category, $price, $event_id, $organizer_id);
        if ($stmt_update->execute()) {
            $message = "<div class='alert alert-success'>Event updated successfully!</div>";
            // Re-fetch data to show updated values in the form
            $result = $conn->query("SELECT * FROM events WHERE id = $event_id");
            $event = $result->fetch_assoc();
        } else {
            $message = "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
        $stmt_update->close();
    }
}
?>

<h1 class="mb-4">Edit Event</h1>
<?php echo $message; ?>
<!-- The form here is the same as create_event.php, but pre-filled with values -->
<form action="edit_event.php?id=<?php echo $event_id; ?>" method="post">
    <div class="mb-3">
        <label for="title" class="form-label">Event Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($event['description']); ?></textarea>
    </div>
    <!-- ... add all other form fields pre-filled like the ones above ... -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="event_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="event_time" class="form-label">Time</label>
            <input type="time" class="form-control" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($event['category']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="price" class="form-label">Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($event['price']); ?>" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Update Event</button>
    <a href="manage_events.php" class="btn btn-secondary">Back to Manage Events</a>
</form>

<?php include 'includes/footer.php'; ?>