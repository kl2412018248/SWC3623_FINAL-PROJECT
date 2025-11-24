<?php 
include 'includes/header.php'; 

// --- 1. GET DATA FOR THE STAT CARDS ---
$admin_id = $_SESSION['id'];

// Total Events Created by this Admin
$sql_total_events = "SELECT COUNT(id) as total FROM events WHERE organizer_id = ?";
$stmt_total = $conn->prepare($sql_total_events);
$stmt_total->bind_param("i", $admin_id);
$stmt_total->execute();
$total_events = $stmt_total->get_result()->fetch_assoc()['total'];
$stmt_total->close();

// Total Bookings for this Admin's Events
$sql_total_bookings = "SELECT COUNT(b.id) as total FROM bookings b JOIN events e ON b.event_id = e.id WHERE e.organizer_id = ?";
$stmt_bookings = $conn->prepare($sql_total_bookings);
$stmt_bookings->bind_param("i", $admin_id);
$stmt_bookings->execute();
$total_bookings = $stmt_bookings->get_result()->fetch_assoc()['total'];
$stmt_bookings->close();

// Upcoming Events for this Admin
$sql_upcoming_count = "SELECT COUNT(id) as total FROM events WHERE organizer_id = ? AND event_date >= CURDATE()";
$stmt_upcoming = $conn->prepare($sql_upcoming_count);
$stmt_upcoming->bind_param("i", $admin_id);
$stmt_upcoming->execute();
$upcoming_events = $stmt_upcoming->get_result()->fetch_assoc()['total'];
$stmt_upcoming->close();

// Total Registered Public Users (System-wide stat)
$sql_total_users = "SELECT COUNT(id) as total FROM users";
$total_users = $conn->query($sql_total_users)->fetch_assoc()['total'];


// --- 2. GET DATA FOR THE BAR CHART ---
$chart_labels = [];
$chart_data = [];

// Get the top 5 events with the most bookings for this admin
$sql_chart = "SELECT e.title, COUNT(b.id) as booking_count 
              FROM events e 
              LEFT JOIN bookings b ON e.id = b.event_id 
              WHERE e.organizer_id = ? 
              GROUP BY e.id 
              ORDER BY booking_count DESC 
              LIMIT 5";
              
if ($stmt_chart = $conn->prepare($sql_chart)) {
    $stmt_chart->bind_param("i", $admin_id);
    $stmt_chart->execute();
    $result_chart = $stmt_chart->get_result();
    while($row = $result_chart->fetch_assoc()){
        // Shorten long event titles for the chart label
        $chart_labels[] = (strlen($row['title']) > 20) ? substr($row['title'], 0, 20) . '...' : $row['title'];
        $chart_data[] = $row['booking_count'];
    }
    $stmt_chart->close();
}
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <a href="create_event.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
            class="fas fa-plus fa-sm text-white-50"></i> Create New Event</a>
</div>

<!-- Stat Cards Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Your Total Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_events; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_bookings; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-book-open fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Upcoming Events</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $upcoming_events; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Registered Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Row -->
<div class="row">
    <div class="col-lg-12">
        <!-- Bar Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Events by Bookings</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar" style="height: 320px;">
                    <canvas id="myBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js at the bottom, before the footer -->
<script src="vendor/chart.js/Chart.min.js"></script>
<script>
// Set new default font family and color for Chart.js
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($chart_labels); ?>, // PHP passes event titles here
    datasets: [{
      label: "Bookings",
      backgroundColor: "#4e73df",
      hoverBackgroundColor: "#2e59d9",
      borderColor: "#4e73df",
      data: <?php echo json_encode($chart_data); ?>, // PHP passes booking counts here
      maxBarThickness: 50,
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
    scales: {
      xAxes: [{
        gridLines: { display: false, drawBorder: false },
        ticks: { maxTicksLimit: 5 } // Limits number of labels if too crowded
      }],
      yAxes: [{
        ticks: {
          min: 0,
          maxTicksLimit: 5,
          padding: 10,
          callback: function(value) { if (Number.isInteger(value)) { return value; } }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: { display: false },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
  }
});
</script>

<?php include 'includes/footer.php'; ?>