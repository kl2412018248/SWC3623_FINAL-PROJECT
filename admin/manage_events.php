<?php 
// Include the header which starts the page and session
require_once(dirname(__FILE__) . '/includes/header.php'); 

// Get the organizer's ID from the session to fetch only their events
$organizer_id = $_SESSION['id'];
?>

<!-- Add the CSS link specifically for DataTables at the top -->
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Manage My Events</h1>
<p class="mb-4">Here you can view, edit, or delete the events you have created. Use the search box and click on the column headers to sort the data.</p>

<!-- DataTables Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Your Events List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL to fetch events for the currently logged-in admin
                    $sql = "SELECT e.id, e.title, e.event_date, e.location, 
                                   (SELECT COUNT(b.id) FROM bookings b WHERE b.event_id = e.id) as booking_count 
                            FROM events e 
                            WHERE e.organizer_id = ? 
                            ORDER BY e.event_date DESC";

                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("i", $organizer_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                echo "<td>" . date("M j, Y", strtotime($row['event_date'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                echo "<td>" . $row['booking_count'] . "</td>";
                                echo '<td class="text-center">';
                                // New "View Bookings" Button
                                echo '  <button class="btn btn-info btn-circle btn-sm view-bookings-btn" data-toggle="modal" data-target="#bookingsModal" data-eventid="'.$row['id'].'" data-eventtitle="'.htmlspecialchars($row['title']).'" title="View Bookings">
                                            <i class="fas fa-users"></i>
                                        </button> ';
                                // Existing Edit and Delete Buttons
                                echo '  <a href="edit_event.php?id=' . $row['id'] . '" class="btn btn-warning btn-circle btn-sm" title="Edit Event">
                                            <i class="fas fa-edit"></i>
                                        </a> ';
                                echo '  <a href="delete_event.php?id=' . $row['id'] . '" class="btn btn-danger btn-circle btn-sm" onclick="return confirm(\'Are you sure you want to delete this event permanently?\')" title="Delete Event">
                                            <i class="fas fa-trash"></i>
                                        </a>';
                                echo '</td>';
                                echo "</tr>";
                            }
                        }
                        $stmt->close();
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- NEW: Attendee Bookings Modal -->
<div class="modal fade" id="bookingsModal" tabindex="-1" role="dialog" aria-labelledby="bookingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingsModalLabel">Attendees for: <span>Event Title</span></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Attendee list will be loaded here by AJAX -->
                <div id="attendeeList">
                    <p class="text-center">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Modal -->


<?php 
// Include the standard admin footer
require_once(dirname(__FILE__) . '/includes/footer.php'); 
?>

<!-- Add the JS scripts for DataTables at the very bottom -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page-level custom script to activate DataTables and the Modal AJAX -->
<script>
$(document).ready(function() {
  // Activate DataTables
  $('#dataTable').DataTable({
    "order": [[ 1, "desc" ]] 
  });

  // Handle the click event for the view bookings button
  $('.view-bookings-btn').on('click', function() {
    var eventId = $(this).data('eventid');
    var eventTitle = $(this).data('eventtitle');
    
    // Update the modal title with the specific event's title
    $('#bookingsModalLabel span').text(eventTitle);
    
    // Set the body of the modal to a loading state
    $('#attendeeList').html('<p class="text-center">Loading attendees...</p>');
    
    // Use AJAX to fetch the attendee list from our new helper file
    $.ajax({
      url: 'ajax_get_attendees.php', // The new file we will create
      type: 'POST',
      data: { event_id: eventId },
      success: function(response) {
        // When successful, replace the "Loading..." text with the response (the HTML table)
        $('#attendeeList').html(response);
      },
      error: function() {
        // If the AJAX call fails, show an error message
        $('#attendeeList').html('<p class="text-danger text-center">Failed to load attendee data. Please try again.</p>');
      }
    });
  });
});
</script>