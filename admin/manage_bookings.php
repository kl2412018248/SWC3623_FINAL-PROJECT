<?php 
include 'includes/header.php'; 

$admin_id = $_SESSION['id'];
?>

<h1 class="h3 mb-2 text-gray-800">Manage Bookings</h1>
<p class="mb-4">View all bookings for the events you have created.</p>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Bookings List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Attendee Username</th>
                        <th>Attendee Email</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL to get bookings for events created by this admin
                    $sql = "SELECT e.title, u.username, u.email, b.booking_date 
                            FROM bookings b
                            JOIN users u ON b.user_id = u.id
                            JOIN events e ON b.event_id = e.id
                            WHERE e.organizer_id = ?
                            ORDER BY b.booking_date DESC";
                    
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("i", $admin_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . date("M j, Y, g:i a", strtotime($row['booking_date'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No bookings found for your events.</td></tr>";
                        }
                        $stmt->close();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>