<?php include("includes/header.php"); ?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Sign Up</h3>

        <form method="POST">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>
