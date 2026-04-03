<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
$customer_id = $_SESSION['id'];
$error = '';

// Fetch Room ID from URL
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    header("Location: dashboard.php");
    exit();
}
$room_id = (int) $_GET['room_id'];

// Image Mapping
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

// Fetch specific room details from Database
$query = "SELECT * FROM rooms WHERE id = $room_id";
$result = mysqli_query($db, $query);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    header("Location: dashboard.php");
    exit();
}

$image = $room_images[$room['room_name']] ?? '/Roomora/assets/media/image-1.jpg';

// Handle Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start = mysqli_real_escape_string($db, $_POST['start_time']);
    $end = mysqli_real_escape_string($db, $_POST['end_time']);

    if (strtotime($end) <= strtotime($start)) {
        $error = "Check-out must be after check-in.";
    } else {
        $insert = "INSERT INTO bookings (customer_id, room_id, start_time, end_time) 
                   VALUES ($customer_id, $room_id, '$start', '$end')";
        if (mysqli_query($db, $insert)) {
            header("Location: my-bookings.php");
            exit();
        }
    }
}

include("includes/header.php");
?>

<div class="container mt-5 mb-5">
    <div class="row g-5">
        <div class="col-lg-7">
            <img src="<?php echo htmlspecialchars($image); ?>" class="img-fluid rounded shadow-lg mb-4" style="height: 500px; width: 100%; object-fit: cover; border: 1px solid var(--border);">
            
            <div class="p-4 rounded" style="background: var(--bg-card); border: 1px solid var(--border);">
                <h4 class="text-uppercase mb-3" style="color: var(--gold);">Room Description</h4>
                <p class="text-muted"><?php echo htmlspecialchars($room['description']); ?></p>
                <hr style="border-color: var(--border);">
                <div class="d-flex justify-content-between">
                    <span><strong>Capacity:</strong> <?php echo $room['capacity']; ?> Guests</span>
                    <span style="color: var(--gold);"><strong>Rate:</strong> $<?php echo number_format($room['price_per_hour'], 2); ?>/hr</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="p-4 rounded h-100" style="background: var(--bg-card); border: 1px solid var(--border);">
                <h1 class="display-6 text-uppercase mb-3" style="color: var(--gold); letter-spacing: 2px;">
                    <?php echo htmlspecialchars($room['room_name']); ?>
                </h1>

                <div class="mb-4">
                    <?php 
                    $tags = explode(',', $room['amenities']);
                    foreach($tags as $tag): ?>
                        <span class="badge me-1" style="background: var(--gold-dim); color: var(--gold); border: 1px solid var(--border);">
                            <?php echo trim($tag); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <form method="POST">
                    <?php if($error): ?> <div class="alert alert-danger small"><?php echo $error; ?></div> <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="small text-muted text-uppercase">Check-In</label>
                        <input type="datetime-local" name="start_time" id="start_time" class="form-control" required style="background: transparent; color: white; border-color: var(--border);">
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted text-uppercase">Check-Out</label>
                        <input type="datetime-local" name="end_time" id="end_time" class="form-control" required style="background: transparent; color: white; border-color: var(--border);">
                    </div>

                    <div id="price-box" class="p-3 mb-3 text-center d-none" style="background: var(--gold-dim); border: 1px solid var(--gold);">
                        <div id="duration" class="small text-uppercase"></div>
                        <div id="total" class="h4 mb-0" style="color: var(--gold);"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 text-uppercase fw-bold" style="background: var(--gold); color: black; border: none;">
                        Reserve Room
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const start = document.getElementById('start_time');
    const end = document.getElementById('end_time');
    const priceBox = document.getElementById('price-box');
    const rate = <?php echo $room['price_per_hour']; ?>;

    function update() {
        if(start.value && end.value) {
            let diff = (new Date(end.value) - new Date(start.value)) / 3600000;
            if(diff > 0) {
                document.getElementById('duration').innerText = diff.toFixed(1) + " Hours";
                document.getElementById('total').innerText = "Total: $" + (diff * rate).toFixed(2);
                priceBox.classList.remove('d-none');
            } else { priceBox.classList.add('d-none'); }
        }
    }
    start.addEventListener('change', update);
    end.addEventListener('change', update);
</script>

<?php include("includes/footer.php"); ?>