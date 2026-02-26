<?php
include("includes/header.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/config.php';
$user_id = $_SESSION['user_id'];

$bookings = $db->query("SELECT b.*, p.name, p.location, p.image_url 
                        FROM bookings b 
                        JOIN properties p ON b.property_id = p.id 
                        WHERE b.user_id = $user_id 
                        ORDER BY b.booking_date DESC");
?>

<div class="container mt-5">
    <h2>My Bookings</h2>

    <?php if ($bookings->num_rows == 0): ?>
        <div class="alert alert-info">You have no bookings yet.</div>
    <?php else: ?>
        <div class="row">
            <?php while ($booking = $bookings->fetch_assoc()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="<?php echo $booking['image_url']; ?>" class="img-fluid rounded-start" style="height: 100%; object-fit: cover;">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5><?php echo $booking['name']; ?></h5>
                                    <p><?php echo $booking['location']; ?></p>
                                    <p>Check in: <?php echo $booking['check_in']; ?></p>
                                    <p>Check out: <?php echo $booking['check_out']; ?></p>
                                    <p class="text-primary">Total: KES <?php echo number_format($booking['total_price'] * 130); ?></p>
                                    <span class="badge bg-success"><?php echo $booking['status']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>

<?php include("includes/footer.php"); ?>