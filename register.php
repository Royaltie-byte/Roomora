<?php 

include("includes/header.php");
require 'db.php';

 ?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Register With Us.</h3>



        <!--Recording the credentials in the database for storage-->
<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = htmlspecialchars($_POST['full-name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    //hashing the password
    $hashedPassword = password_hash($password , PASSWORD_DEFAULT);

    

    //Checking if the email already has an account.

    $queryChecker = "SELECT *FROM customers WHERE email = '$email'";

    $resultChecker = mysqli_query($db,$queryChecker);

    if(mysqli_num_rows($resultChecker)==1){
            $error = "The email has already been used to create an account.";
        }else{
            //proceed to insert the email.
            $queryInsert = "INSERT INTO customers(full_name,email,password )
              VALUES ('$name','$email','$hashedPassword')";

            $resultInsert = mysqli_query($db,$queryInsert);

            if($resultInsert){
                header("Location: login.php");
                exit();
            }else{
                echo "Error:". mysqli_error($db);
                
    } 
            
        }
   
}
?>

        <!--Error message-->
        <?php if(isset($error)) : ?>
            <div class="alert alert-danger">
                <?php echo $error;?>
            </div>
        <?php endif ; ?>


        <!--Modified the form to enable it to send details to php-->
        <form action="register.php"  method="POST">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" class="form-control" required name="full-name">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" required name="email">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" required name="password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>



<?php include("includes/footer.php"); ?>
