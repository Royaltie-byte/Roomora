<?php
include("includes/header.php");
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
require_once 'includes/config.php';

$property = $db->query("SELECT * FROM properties WHERE id = $id")->fetch_assoc();

if (!$property) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $user_id = $_SESSION['user_id'];

    $check = $db->query("SELECT * FROM bookings WHERE property_id = $id 
                         AND status='confirmed'
                         AND ((check_in BETWEEN '$check_in' AND '$check_out') 
                         OR (check_out BETWEEN '$check_in' AND '$check_out'))");

    if ($check->num_rows > 0) {
        $error = "Sorry, these dates are not available";
    } else {
        $days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        $total = $days * $property['price_per_night'];

        $sql = "INSERT INTO bookings (user_id, property_id, check_in, check_out, total_price) 
                VALUES ($user_id, $id, '$check_in', '$check_out', $total)";

        if ($db->query($sql)) {
            $success = "Booking confirmed! Total: KES " . number_format($total * 130);
        } else {
            $error = "Booking failed. Please try again.";
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $property['image_url']; ?>" class="img-fluid rounded shadow" alt="<?php echo $property['name']; ?>">
        </div>
        <div class="col-md-6">
            <h2><?php echo $property['name']; ?></h2>
            <p class="text-muted">📍 <?php echo $property['location']; ?></p>
            <p><?php echo $property['description']; ?></p>
            <h3 class="text-primary">KES <?php echo number_format($property['price_per_night'] * 130); ?> <small class="text-muted">/ night</small></h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card shadow p-3 mt-4">
                    <h5>Book this property</h5>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Check In</label>
                                <input type="date" name="check_in" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Check Out</label>
                                <input type="date" name="check_out" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-3">
                    Please <a href="login.php">login</a> to book this property.
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn btn-outline-secondary mt-3">← Back to Properties</a>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
