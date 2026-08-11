<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.html?registered=1");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";
$name = trim($_POST["name"] ?? "");
$dob = $_POST["dob"] ?? "";
$gender = $_POST["gender"] ?? "";
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$address = trim($_POST["address"] ?? "");

if (!$username || !$password || !$name || !$dob || !$gender) {
    exit("Required fields are missing.");
}

$check = $conn->prepare("SELECT login_id FROM login WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    exit("Username already exists.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "INSERT INTO login (username, password, role) VALUES (?, ?, 'User')"
    );
    $stmt->bind_param("ss", $username, $hash);
    $stmt->execute();
    $loginId = $conn->insert_id;

    $stmt = $conn->prepare(
        "INSERT INTO gym_user
        (login_id, u_name, u_dob, u_gender, u_phone, u_email, u_address)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "issssss", $loginId, $name, $dob, $gender, $phone, $email, $address
    );
    $stmt->execute();

    $conn->commit();
    header("Location: ../login.html?registered=1");
} catch (Throwable $e) {
    $conn->rollback();
    exit("Registration failed.");
}
?>
