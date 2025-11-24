<?php
include 'includes/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}

if(isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $organizer_id = $_SESSION['id'];

    // SQL to delete a record
    // Ensure the event belongs to the logged-in admin before deleting
    $sql = "DELETE FROM events WHERE id = ? AND organizer_id = ?";

    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("ii", $event_id, $organizer_id);
        
        if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: manage_events.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    $stmt->close();
}
$conn->close();
?>