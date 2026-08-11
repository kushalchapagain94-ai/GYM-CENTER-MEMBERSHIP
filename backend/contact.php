<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../contact.html?sent=1");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$subject || !$message) {
    exit("Please provide valid contact information.");
}

$stmt = $conn->prepare(
    "INSERT INTO contact_message (name, email, subject, message)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $subject, $message);
$stmt->execute();

header("Location: ../frontend/contact.html?sent=1");
exit;
?>
