<?php
require 'db.php';
include("includes/header.php");

$error = "";
$message = "";

?>

<div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow p-4">
        <!-- Title -->
        <h3 class="text-center mb-1">Reset Your Password</h3>
        <p class="text-center text-muted mb-4">Enter your email to receive a temporary password</p>

        <?php
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $email = htmlspecialchars($_POST['email']);

            // Check if the email exists
            $queryEmailCheck = "SELECT * FROM customers WHERE email = '$email'";
            $resultEmailCheck = mysqli_query($db, $queryEmailCheck);

            if(mysqli_num_rows($resultEmailCheck) == 1){
                // Generate temporary password
                $tempPassword = bin2hex(random_bytes(4)); // 8 characters
                $hashedTempPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

                // Update password in DB
                $queryUpdate = "UPDATE customers SET password = '$hashedTempPassword' WHERE email = '$email'";
                $resultUpdate = mysqli_query($db, $queryUpdate);

                if($resultUpdate){
                    // Display the temporary password on the page
                    $message = "Your temporary password is: <strong>$tempPassword</strong><br/>
                                Please login and change it immediately.";
                } else {
                    $error = "Failed to update password. Try again later: " . mysqli_error($db);
                }
            } else {
                $error = "No account found with that email.";
            }
        }
        ?>

        <!--  errors -->
        <?php if($error != "") : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Display temporary password -->
        <?php if($message != "") : ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="forgot-password.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email"
                    name="email"
                    class="form-control" 
                    placeholder="you@example.com" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">Generate Temporary Password</button>
        </form>

        <!-- Back to login -->
        <div class="text-center mt-4">
            <a href="login.php" class="text-decoration-none">Back to Login</a>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
