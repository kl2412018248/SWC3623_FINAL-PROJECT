<?php 
include 'includes/header.php'; 

// Check if an event ID is provided in the URL, otherwise redirect
if (!isset($_GET['id'])) {
    header("Location: manage_events.php");
    exit;
}

$event_id = $_GET['id'];
$admin_id = $_SESSION['id'];
$message = '';

// --- Handle Form Submission for UPDATING the event ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // SQL to update the record in the database
    $sql_update = "UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, category=?, price=? WHERE id=? AND organizer_id=?";
    
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("ssssssdii", $title, $description, $event_date, $event_time, $location, $category, $price, $event_id, $admin_id);
        if ($stmt_update->execute()) {
            $message = "<div class='alert alert-success'>Event updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
        $stmt_update->close();
    }
}


// --- Fetch the Current Event Data to pre-fill the form ---
$sql_fetch = "SELECT * FROM events WHERE id = ? AND organizer_id = ?";
if($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("ii", $event_id, $admin_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        // If event not found or doesn't belong to this admin, redirect
        header("location: manage_events.php");
        exit;
    }
    $stmt_fetch->close();
}
?>

<h1 class="h3 mb-4 text-gray-800">Edit Event</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Editing: <?php echo htmlspecialchars($event['title']); ?></h6>
    </div>
    <div class="card-body">
        <?php echo $message; // Display success or error message here ?>
        <form action="edit_event.php?id=<?php echo $event_id; ?>" method="post">
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="event_date">Date</label>
                    <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="event_time">Time</label>
                    <input type="time" class="form-control" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($event['category']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="price">Price (RM)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($event['price']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Event</button>
            <a href="manage_events.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>