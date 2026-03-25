<?php
session_start();

// security
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
include("includes/header.php");

$customer_id = $_SESSION['id'];

// stats quetries
$total_bookings = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS total FROM bookings WHERE customer_id = $customer_id"))['total'];

$available_rooms_count = mysqli_fetch_assoc(mysqli_query($db, "
    SELECT COUNT(*) AS total FROM rooms 
    WHERE id NOT IN (
        SELECT room_id FROM bookings 
        WHERE NOW() BETWEEN start_time AND end_time
    )
"))['total'];

$upcoming = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS total FROM bookings WHERE customer_id = $customer_id AND start_time > NOW()"))['total'];

// images
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

// main query
$rooms_result = mysqli_query($db, "SELECT * FROM rooms");
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="mb-0 text-uppercase" style="letter-spacing: 2px; color: var(--gold);">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
            <div id="clock"></div>
        </div>
        <a href="my-bookings.php" class="btn btn-outline-primary btn-sm px-4">My Bookings</a>
    </div>

    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card p-3 text-center" style="background: var(--bg-card); border: 1px solid var(--border);">
                <h3 class="mb-0"><?php echo $total_bookings; ?></h3>
                <small class="text-muted text-uppercase">Total Stays</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center" style="background: var(--bg-card); border: 1px solid var(--border);">
                <h3 class="mb-0"><?php echo $upcoming; ?></h3>
                <small class="text-muted text-uppercase">Upcoming</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center" style="background: var(--bg-card); border: 1px solid var(--border);">
                <h3 class="mb-0"><?php echo $available_rooms_count; ?></h3>
                <small class="text-muted text-uppercase">Live Availability</small>
            </div>
        </div>
    </div>

    <h4 class="mb-4 text-uppercase" style="letter-spacing: 1px; font-weight: 300;">Available Collections</h4>
    
    <div class="row">
        <?php while ($room = mysqli_fetch_assoc($rooms_result)): 
            // to check if this specific room is occupied right now
            $room_id = $room['id'];
            $check_query = "SELECT * FROM bookings WHERE room_id = $room_id AND NOW() BETWEEN start_time AND end_time";
            $occupied = mysqli_num_rows(mysqli_query($db, $check_query)) > 0;
            
            // Gets the image path
            $image = $room_images[$room['room_name']] ?? '/Roomora/assets/media/image-1.jpg';
        ?>
            <div class="col-md-4 mb-4">
                <a href="room-details.php?room_id=<?php echo $room['id']; ?>" class="text-decoration-none">
                    <div class="card h-100 room-card" style="background: var(--bg-card); border: 1px solid var(--border); transition: 0.3s;">
                        <img src="<?php echo htmlspecialchars($image); ?>" 
                             class="card-img-top" 
                             alt="Room Image" 
                             style="height: 250px; object-fit: cover; opacity: 0.8;">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title text-uppercase mb-1" style="color: var(--text); letter-spacing: 1px;">
                                <?php echo htmlspecialchars($room['room_name']); ?>
                            </h5>
                            <p class="text-muted small mb-3">Executive Suite</p>

                            <?php if ($occupied): ?>
                                <span class="badge rounded-pill" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; border: 1px solid #dc3545;">Occupied</span>
                            <?php else: ?>
                                <span class="badge rounded-pill" style="background: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid #28a745;">Available</span>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <span class="btn btn-sm btn-outline-primary w-100 py-2">View & Book</span>
                            </div>
                        </div>
                    </div>
                </a>
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