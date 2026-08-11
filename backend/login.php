<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.html");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

$stmt = $conn->prepare(
    "SELECT login_id, username, password, role, status
     FROM login WHERE username = ? LIMIT 1"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    exit("DEBUG: Username not found.");
}

if ($row["status"] !== "Active") {
    exit("DEBUG: Account status is: " . $row["status"]);
}

if (!password_verify($password, $row["password"])) {
    exit("DEBUG: Password verification failed.");
}

$_SESSION["login_id"] = $row["login_id"];
$_SESSION["username"] = $row["username"];
$_SESSION["role"] = $row["role"];

if ($row["role"] === "Admin") {
    header("Location: ../admin_dashboard.php");
} elseif ($row["role"] === "Trainer") {
    header("Location: ../trainer_dashboard.php");
} else {
    header("Location: ../user_dashboard.php");
}
exit;
?>
