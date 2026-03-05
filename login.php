<?php
require_once 'includes/config.php';
session_start();

// If user is already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check for remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    // Clean up expired tokens
    $db->query("DELETE FROM remember_tokens WHERE expires_at < NOW()");

    // Check if token exists and is valid
    $stmt = $db->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];

        // Get user details
        $user_stmt = $db->prepare("SELECT id, full_name FROM users WHERE id = ?");
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user = $user_result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid token, clear cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Use prepared statement
        $stmt = $db->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];

                // Handle Remember Me
                if ($remember) {
                    // Generate token
                    $token = bin2hex(random_bytes(32));

                    // Set expiration (30 days)
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

                    // Delete any existing tokens for this user
                    $deleteStmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                    $deleteStmt->bind_param("i", $user['id']);
                    $deleteStmt->execute();

                    // Store new token
                    $insertStmt = $db->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                    $insertStmt->bind_param("iss", $user['id'], $token, $expires);
                    $insertStmt->execute();

                    // Set cookie for 30 days
                    setcookie('remember_token', $token, time() + (86400 * 30), '/');
                }

                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Wrong password";
            }
        } else {
            $error = "Email not found";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login - Roomora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center">Login</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3">
                        No account? <a href="register.php">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>