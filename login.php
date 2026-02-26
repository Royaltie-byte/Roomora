<?php
include("includes/header.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once 'includes/config.php';

    $token = $_COOKIE['remember_token'];
    $result = $db->query("SELECT u.* FROM users u JOIN remember_tokens rt ON u.id = rt.user_id WHERE rt.token = '$token' AND rt.expires_at > NOW()");

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    require_once 'includes/config.php';

    $result = $db->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                $db->query("DELETE FROM remember_tokens WHERE user_id = " . $user['id']);

                $db->query("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (" . $user['id'] . ", '$token', '$expires')");

                setcookie('remember_token', $token, [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found";
    }
}
?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck">
                <label class="form-check-label" for="rememberCheck">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3">
            No account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php include("includes/footer.php"); ?>