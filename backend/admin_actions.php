<?php
require_once "auth.php";
require_role("Admin");

$action = $_POST["action"] ?? "";

if ($action === "add_plan") {
    $name = trim($_POST["plan_name"] ?? "");
    $duration = $_POST["duration"] ?? "";
    $price = (float)($_POST["price"] ?? 0);
    $description = trim($_POST["description"] ?? "");

    $stmt = $conn->prepare(
        "INSERT INTO membership_plan (plan_name,duration,price,description)
         VALUES (?,?,?,?)"
    );
    $stmt->bind_param("ssds", $name, $duration, $price, $description);
    $stmt->execute();
    header("Location: ../admin_dashboard.php?plan_added=1");
    exit;
}

if ($action === "delete_plan") {
    $planId = (int)($_POST["plan_id"] ?? 0);
    $stmt = $conn->prepare(
        "UPDATE membership_plan SET status='Inactive' WHERE plan_id=?"
    );
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    header("Location: ../admin_dashboard.php");
    exit;
}

if ($action === "send_notification") {
    $uId = (int)($_POST["u_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $message = trim($_POST["message"] ?? "");

    $stmt = $conn->prepare(
        "INSERT INTO notification (u_id,title,message) VALUES (?,?,?)"
    );
    $stmt->bind_param("iss", $uId, $title, $message);
    $stmt->execute();
    header("Location: ../admin_dashboard.php?notification_sent=1");
    exit;
}
?>
