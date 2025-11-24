<?php 
include 'includes/header.php'; 

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
    
    // Image Upload Handling - IMPORTANT: images are uploaded to the main /images/ folder
    $image_path = 'default.jpg'; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/"; // Go up one level to the main images folder
        $image_name = uniqid() . '-' . basename($_FILES["image"]["name"]);
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

<h1 class="h3 mb-4 text-gray-800">Create New Event</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php echo $message; ?>
        <form action="create_event.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="event_date">Date</label>
                    <input type="date" class="form-control" id="event_date" name="event_date" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="event_time">Time</label>
                    <input type="time" class="form-control" id="event_time" name="event_time" required>
                </div>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" placeholder="e.g., Music, Tech" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="price">Price (RM)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="0.00" required>
                </div>
            </div>
            <div class="form-group">
                <label for="image">Event Image</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>