<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Ian Dev";
    header("Location: login.php");
    exit();
}

include("includes/header.php");
?>

<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?> 👋. Nice to have you back!</h2>
    <p id="clock" class="text-muted"></p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Total Bookings</h5>
                <h3>5</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Available Rooms</h5>
                <h3>12</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow text-center p-3">
                <h5>Upcoming Reservations</h5>
                <h3>2</h3>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>