<?php
session_start();

$host = "your_mysql_host";
$db   = "your_database_name";
$user = "your_database_username";
$pass = "your_database_password";
$charset = "utf8mb4";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset($charset);
?>
