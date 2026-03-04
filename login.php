<?php
session_start();
require 'db.php';

?>
<?php include("includes/header.php"); ?>

<div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow p-4">
        <!-- Welcome message -->
        <h3 class="text-center mb-1">Welcome Back!</h3>
        <p class="text-center text-muted mb-4">Please login to your account</p>



        <!--the authentication process-->

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    //checking whether the email is in the database.
    $query = "SELECT *FROM customers WHERE email = '$email' ";

    $result = mysqli_query($db,$query);
    
    if(mysqli_num_rows($result)==1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];

        if(password_verify($password,$user['password'])){
            header("Location: dashboard.php");
            exit();
        }else{
            $error = "Incorrect password, please try again.";
        }


    }else{
        $error = "That email does not exist, sign up if you don't have an account.";
    }
}
?>

        <!--Error Message-->
        <?php if(isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo $error;?>
            </div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" action="login.php">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email"
                    name="email"
                    class="form-control" 
                    placeholder="you@example.com" 
                    required
                >
            </div>

            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password"
                    name="password"
                    class="form-control" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            
            <button type="submit" class="btn btn-primary w-100">Login</button>

            
            <div class="text-center mt-3">
                <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
            </div>
        </form>

        <!-- Sign up link -->
        <div class="text-center mt-4">
            <span>Don't have an account? </span>
            <a href="register.php" class="text-decoration-none">Sign up</a>
        </div>
    </div>
</div>



<?php include("includes/footer.php"); ?>
