<?php
session_start();

require_once 'includes/config.php';

if (isset($_COOKIE['remember_token'])) {
    $token = mysqli_real_escape_string($db, $_COOKIE['remember_token']);

    $db->query("DELETE FROM remember_tokens WHERE token = '$token'");

    // deleting the cookie
    setcookie('remember_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $db->query("DELETE FROM remember_tokens WHERE user_id = $user_id");
}


$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

session_destroy();

header("Location: login.php");
exit();
