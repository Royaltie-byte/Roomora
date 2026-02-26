<?php include("includes/header.php");
require_once 'includes/config.php';
$properties = $db->query("SELECT * FROM properties LIMIT 3");
?>

<div class="container text-center mt-5">
    <h1 class="display-4">Welcome to Roomora</h1>
    <p class="lead">Book rooms easily and efficiently.</p>

    <div class="mt-4">
        <a href="register.php" class="btn btn-primary btn-lg me-3">Get Started</a>
        <a href="login.php" class="btn btn-outline-dark btn-lg">Login</a>
    </div>
</div>

<div class="container mt-5">
    <h3 class="text-center mb-4">Featured Properties</h3>
    <div class="row">
        <?php while ($prop = $properties->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <img src="<?php echo $prop['image_url']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5><?php echo $prop['name']; ?></h5>
                        <p><?php echo $prop['location']; ?></p>
                        <p class="text-primary">KES <?php echo number_format($prop['price_per_night'] * 130); ?>/night</p>
                        <a href="property-details.php?id=<?php echo $prop['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5>Easy Booking</h5>
                    <p>Book your desired room in seconds.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5>Secure System</h5>
                    <p>Your data is safe and protected.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h5>Responsive Design</h5>
                    <p>Works perfectly on mobile & desktop.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>