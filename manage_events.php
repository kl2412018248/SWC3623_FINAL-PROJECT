<?php
include 'includes/header.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$organizer_id = $_SESSION['id'];
?>

<h1 class="mb-4">Manage My Events</h1>
<a href="create_event.php" class="btn btn-primary mb-3">Create New Event</a>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Title</th>
            <th>Date</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT id, title, event_date, location FROM events WHERE organizer_id = ? ORDER BY event_date DESC";
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
                    echo '<td>';
                    echo '  <a href="edit_event.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                    echo '  <a href="delete_event.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this event?\')">Delete</a>';
                    echo '</td>';
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>You have not created any events yet.</td></tr>";
            }
            $stmt->close();
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>