<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Roomora</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Roomora</a>

            <div class="ms-auto">
                <?php if (isset($_SESSION['user_name'])): ?>

                    <span class="text-white me-3">
                        Hi, <?php echo $_SESSION['user_name']; ?>
                    </span>

                    <a href="logout.php" class="btn btn-danger btn-sm">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="login.php" class="btn btn-outline-light btn-sm me-2">
                        Login
                    </a>
                    <a href="register.php" class="btn btn-primary btn-sm">
                        Sign Up
                    </a>

                <?php endif; ?>
            </div>
        </div>
    </nav>