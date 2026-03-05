<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
include("includes/header.php");

$customer_id = $_SESSION['id'];

// Total bookings by this customer
$total_bookings = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS total FROM bookings WHERE customer_id = $customer_id"))['total'];

// Rooms that have NO ongoing booking right now
$available_rooms = mysqli_fetch_assoc(mysqli_query($db, "
    SELECT COUNT(*) AS total FROM rooms 
    WHERE id NOT IN (
        SELECT room_id FROM bookings 
        WHERE NOW() BETWEEN start_time AND end_time
    )
"))['total'];

// Upcoming bookings by this customer
$upcoming = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS total FROM bookings WHERE customer_id = $customer_id AND start_time > NOW()"))['total'];

// Image map
$room_images = [
    'room A' => '/Roomora/assets/media/image-1.jpg',
    'room B' => '/Roomora/assets/media/image-2.jpg',
    'room C' => '/Roomora/assets/media/image-3.jpg',
    'room D' => '/Roomora/assets/media/image-4.jpg',
    'room E' => '/Roomora/assets/media/image-5.jpg',
    'room F' => '/Roomora/assets/media/image-6.jpg',
    'room G' => '/Roomora/assets/media/image-7.jpg',
    'room H' => '/Roomora/assets/media/image-8.jpg',
    'room I' => '/Roomora/assets/media/image-9.jpg',
    'room J' => '/Roomora/assets/media/image-10.jpg',
];
?>

<div class="container mt-5">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋. Nice to have you back!</h2>
    <p id="clock" class="text-muted"></p>

    <!-- Stats -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Total Bookings</h5>
                <h3><?php echo $total_bookings; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Available Rooms</h5>
                <h3><?php echo $available_rooms; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Upcoming Reservations</h5>
                <h3><?php echo $upcoming; ?></h3>
            </div>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mt-4">Room booked successfully! 🎉</div>
    <?php endif; ?>
    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-warning mt-4">Booking cancelled.</div>
    <?php endif; ?>

    <!-- Rooms -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
        <h4>All Rooms</h4>
        <a href="my-bookings.php" class="btn btn-outline-primary btn-sm">View My Bookings</a>
    </div>

    <div class="row">
        <?php
        $rooms_result = mysqli_query($db, "SELECT * FROM rooms ORDER BY room_name ASC");
        while ($room = mysqli_fetch_assoc($rooms_result)):
            $image = $room_images[$room['room_name']] ?? '/Roomora/assets/media/image-1.jpg';

            // Check if this specific room is currently occupied
            $rid = $room['id'];
            $occupied = mysqli_fetch_assoc(mysqli_query($db, "
                SELECT COUNT(*) AS total FROM bookings 
                WHERE room_id = $rid AND NOW() BETWEEN start_time AND end_time
            "))['total'];
        ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <img
                        src="<?php echo htmlspecialchars($image); ?>"
                        class="card-img-top"
                        alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                        style="height: 200px; object-fit: cover;"
                    >
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($room['room_name']); ?></h5>
                        <p class="text-muted small mb-1">Room #<?php echo $room['id']; ?></p>

                        <!-- Availability badge -->
                        <?php if ($occupied): ?>
                            <span class="badge bg-danger mb-3">Currently Occupied</span>
                        <?php else: ?>
                            <span class="badge bg-success mb-3">Available</span>
                        <?php endif; ?>

                        <a href="book.php?room_id=<?php echo $room['id']; ?>"
                           class="btn btn-primary mt-auto">Book This Room</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleString('en-KE', {
            weekday: 'long', year: 'numeric', month: 'long',
            day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

<?php include("includes/footer.php"); ?>