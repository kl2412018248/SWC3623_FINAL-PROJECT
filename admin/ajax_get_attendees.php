<?php
// Include the main database connection file, going up one directory level
require_once '../includes/db.php';

// Check if an event_id was sent via the POST request from our JavaScript
if(isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    
    // Prepare a SQL query to select the username, email, and booking date for all users
    // who have booked the specified event.
    $sql = "SELECT u.username, u.email, b.booking_date 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id 
            WHERE b.event_id = ? 
            ORDER BY b.booking_date ASC";
            
    if($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If we find one or more attendees...
        if($result->num_rows > 0) {
            // Start building an HTML table as a string
            $output = '<table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>';
            
            // Loop through each attendee and add a row to the table string
            while($row = $result->fetch_assoc()) {
                $output .= '<tr>
                                <td>'.htmlspecialchars($row['username']).'</td>
                                <td>'.htmlspecialchars($row['email']).'</td>
                                <td>'.date("M j, Y, g:i a", strtotime($row['booking_date'])).'</td>
                            </tr>';
            }
            
            // Close the table tags
            $output .= '</tbody></table>';
            
            // Echo the final HTML table string. This is what gets sent back to the JavaScript.
            echo $output;
        } else {
            // If no attendees are found, send back a simple message.
            echo '<p class="text-center text-muted">No bookings have been made for this event yet.</p>';
        }
        $stmt->close();
    } else {
        // If the SQL query fails for some reason
        echo '<p class="text-danger text-center">Error preparing the database query.</p>';
    }
} else {
    // If the event_id wasn't sent in the request
    echo '<p class="text-warning text-center">No event specified.</p>';
}
?>