<?php

$host = 'localhost';
$dbname = 'roomora';
$username = 'root';
$password = '@bongo4life';

$db = new mysqli($host, $username, $password, $dbname);


if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

//session_start();
