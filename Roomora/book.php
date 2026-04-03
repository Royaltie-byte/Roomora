<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$customer_id = $_SESSION['id'];
$error = '';

// Validate room_id
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    header("Location: dashboard.php");
    exit();
}

$room_id = (int) $_GET['room_id'];

// Fetch room
$room_result = mysqli_query($db, "SELECT * FROM rooms WHERE id = $room_id");
if (mysqli_num_rows($room_result) === 0) {
    header("Location: dashboard.php");
    exit();
}
$room = mysqli_fetch_assoc($room_result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_time = $_POST['start_time'] ?? '';
    $end_time   = $_POST['end_time'] ?? '';

    if (empty($start_time) || empty($end_time)) {
        $error = "Please fill in both start and end times.";
    } elseif (strtotime($end_time) <= strtotime($start_time)) {
        $error = "End time must be after start time.";
    } elseif (strtotime($start_time) < time()) {
        $error = "Start time cannot be in the past.";
    } else {
        // Escape for query safety
        $start_escaped = mysqli_real_escape_string($db, $start_time);
        $end_escaped   = mysqli_real_escape_string($db, $end_time);

        // Overlap check — covers all overlap scenarios:
        // existing booking overlaps if it starts before new end AND ends after new start
        $conflict_query = "SELECT id FROM bookings 
                           WHERE room_id = $room_id 
                             AND start_time < '$end_escaped' 
                             AND end_time > '$start_escaped'";
        $conflict_result = mysqli_query($db, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $error = "This room is already booked during that time. Please choose a different slot.";
        } else {
            // Insert booking
            $insert = "INSERT INTO bookings (customer_id, room_id, start_time, end_time)
                       VALUES ($customer_id, $room_id, '$start_escaped', '$end_escaped')";
            $result = mysqli_query($db, $insert);

            if ($result) {
                header("Location: dashboard.php?success=1");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include("includes/header.php");
?>

<div class="container mt-5" style="max-width: 550px;">
    <div class="card shadow p-4">

        <h3 class="text-center mb-1">Book a Room</h3>
        <p class="text-center text-muted mb-4">
            You're booking: <strong><?php echo htmlspecialchars($room['room_name']); ?></strong>
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="book.php?room_id=<?php echo $room_id; ?>">

            <div class="mb-3">
                <label class="form-label">Start Date & Time</label>
                <input
                    type="datetime-local"
                    name="start_time"
                    id="start_time"
                    class="form-control"
                    value="<?php echo htmlspecialchars($_POST['start_time'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">End Date & Time</label>
                <input
                    type="datetime-local"
                    name="end_time"
                    id="end_time"
                    class="form-control"
                    value="<?php echo htmlspecialchars($_POST['end_time'] ?? ''); ?>"
                    required
                >
            </div>

            <!-- Duration preview -->
            <div id="duration-display" class="alert alert-info d-none mb-3"></div>

            <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
        </form>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="text-decoration-none">← Back to Dashboard</a>
        </div>
    </div>
</div>

<script>
    const startInput = document.getElementById('start_time');
    const endInput   = document.getElementById('end_time');
    const durationBox = document.getElementById('duration-display');

    // Prevent booking in the past
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const nowStr = now.toISOString().slice(0, 16);
    startInput.min = nowStr;
    endInput.min   = nowStr;

    // Live duration display
    function updateDuration() {
        const start = new Date(startInput.value);
        const end   = new Date(endInput.value);
        if (startInput.value && endInput.value && end > start) {
            const diffMs  = end - start;
            const hours   = Math.floor(diffMs / 3600000);
            const minutes = Math.floor((diffMs % 3600000) / 60000);
            durationBox.textContent = `Duration: ${hours}h ${minutes}m`;
            durationBox.classList.remove('d-none');
        } else {
            durationBox.classList.add('d-none');
        }
    }

    startInput.addEventListener('change', updateDuration);
    endInput.addEventListener('change', updateDuration);
</script>

<?php include("includes/footer.php"); ?>