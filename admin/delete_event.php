<?php
// We need the database connection and to start the session
require_once '../includes/db.php';

// Security check: ensure the user is a logged-in admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Check if an event ID was passed in the URL
if(isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $admin_id = $_SESSION['id'];

    // Prepare the DELETE statement. 
    // CRITICAL: We also check if organizer_id matches the logged-in admin's ID.
    // This prevents one admin from deleting another admin's events.
    $sql = "DELETE FROM events WHERE id = ? AND organizer_id = ?";

    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("ii", $event_id, $admin_id);
        
        if($stmt->execute()){
            // If deletion is successful, redirect back to the list
            header("location: manage_events.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
} else {
    // If no ID was provided, just redirect back
    header("location: manage_events.php");
    exit();
}

$conn->close();
?>