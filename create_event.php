<?php
include 'includes/header.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $organizer_id = $_SESSION['id'];
    
    // Image Upload Handling
    $image_path = 'default.jpg'; // default image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $image_name;
        }
    }

    $sql = "INSERT INTO events (title, description, event_date, event_time, location, category, price, organizer_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssdss", $title, $description, $event_date, $event_time, $location, $category, $price, $organizer_id, $image_path);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Event created successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<h1 class="mb-4">Create New Event</h1>
<?php echo $message; ?>
<form action="create_event.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Event Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="event_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="event_date" name="event_date" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="event_time" class="form-label">Time</label>
            <input type="time" class="form-control" id="event_time" name="event_time" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" id="location" name="location" required>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Music, Tech, Workshop" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="price" class="form-label">Price (RM)</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="0.00" required>
        </div>
    </div>
     <div class="mb-3">
        <label for="image" class="form-label">Event Image</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>
    <button type="submit" class="btn btn-primary">Create Event</button>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</form>

<?php include 'includes/footer.php'; ?>