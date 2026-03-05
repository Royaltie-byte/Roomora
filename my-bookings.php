<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$customer_id = $_SESSION['id'];

// Handle cancellation — only allow cancelling own future bookings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id']) && is_numeric($_POST['cancel_id'])) {
    $cancel_id = (int) $_POST['cancel_id'];
    $delete = "DELETE FROM bookings 
               WHERE id = $cancel_id 
                 AND customer_id = $customer_id 
                 AND start_time > NOW()";
    mysqli_query($db, $delete);
    header("Location: my-bookings.php?cancelled=1");
    exit();
}

// Fetch this customer's bookings
$bookings_result = mysqli_query($db, "
    SELECT b.id, b.start_time, b.end_time, r.room_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.customer_id = $customer_id
    ORDER BY b.start_time DESC
");

include("includes/header.php");
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>My Bookings</h3>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">← Back to Dashboard</a>
    </div>

    <!-- Flash messages -->
    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-warning">Booking cancelled successfully.</div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($bookings_result) === 0): ?>
        <div class="alert alert-info">
            You have no bookings yet. 
            <a href="dashboard.php" class="alert-link">Book a room now</a>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Room</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($b = mysqli_fetch_assoc($bookings_result)):
                        $now   = new DateTime();
                        $start = new DateTime($b['start_time']);
                        $end   = new DateTime($b['end_time']);

                        if ($now < $start) {
                            $badge     = '<span class="badge bg-primary">Upcoming</span>';
                            $canCancel = true;
                        } elseif ($now >= $start && $now <= $end) {
                            $badge     = '<span class="badge bg-success">Ongoing</span>';
                            $canCancel = false;
                        } else {
                            $badge     = '<span class="badge bg-secondary">Completed</span>';
                            $canCancel = false;
                        }
                    ?>
                    <tr>
                        <td><?php echo $b['id']; ?></td>
                        <td><?php echo htmlspecialchars($b['room_name']); ?></td>
                        <td><?php echo date("D, d M Y H:i", strtotime($b['start_time'])); ?></td>
                        <td><?php echo date("D, d M Y H:i", strtotime($b['end_time'])); ?></td>
                        <td><?php echo $badge; ?></td>
                        <td>
                            <?php if ($canCancel): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                    <input type="hidden" name="cancel_id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>