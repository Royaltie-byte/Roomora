<?php
include("includes/header.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/config.php';
$user_id = $_SESSION['user_id'];
$bookings_result = $db->query("SELECT COUNT(*) as total FROM bookings WHERE user_id = $user_id");
$bookings_count = $bookings_result->fetch_assoc()['total'];
$properties_result = $db->query("SELECT COUNT(*) as total FROM properties");
$properties_count = $properties_result->fetch_assoc()['total'];
?>
<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?> 👋. Nice to have you back!</h2>
    <p id="clock" class="text-muted"></p>
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow text-center p-3">
                <h5>My Bookings</h5>
                <h3><?php echo $bookings_count; ?></h3>
                <a href="my-bookings.php" class="btn btn-sm btn-outline-primary mt-2">View</a>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow text-center p-3">
                <h5>Available Rooms</h5>
                <h3><?php echo $properties_count; ?></h3>
                <a href="index.php" class="btn btn-sm btn-outline-primary mt-2">Browse</a>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow text-center p-3">
                <h5>Upcoming Reservations</h5>
                <h3><?php echo $bookings_count; ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>