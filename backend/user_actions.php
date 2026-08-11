<?php
require_once "auth.php";
require_role("User");

$loginId = $_SESSION["login_id"];

$stmt = $conn->prepare("SELECT u_id, u_name FROM gym_user WHERE login_id = ?");
$stmt->bind_param("i", $loginId);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
$uId = $userRow["u_id"];

if (($_POST["action"] ?? "") === "update_profile") {
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");

    $stmt = $conn->prepare(
        "UPDATE gym_user SET u_phone=?, u_email=?, u_address=? WHERE u_id=?"
    );
    $stmt->bind_param("sssi", $phone, $email, $address, $uId);
    $stmt->execute();
    header("Location: ../user_dashboard.php?updated=1");
    exit;
}

if (($_POST["action"] ?? "") === "purchase") {
    $planId = (int)($_POST["plan_id"] ?? 0);
    $trainerId = !empty($_POST["trainer_id"]) ? (int)$_POST["trainer_id"] : null;
    $paymentVia = $_POST["payment_via"] ?? "Cash";

    $stmt = $conn->prepare("SELECT duration, price FROM membership_plan WHERE plan_id=? AND status='Active'");
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc();

    if (!$plan) exit("Invalid plan.");

    $start = date("Y-m-d");
    $interval = match ($plan["duration"]) {
        "Monthly" => "+1 month",
        "Quarterly" => "+3 months",
        "Yearly" => "+1 year",
        default => "+1 month"
    };
    $expiry = date("Y-m-d", strtotime($interval));

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            "INSERT INTO user_membership
            (u_id, plan_id, t_id, start_date, expiry_date, status)
            VALUES (?, ?, ?, ?, ?, 'Active')"
        );
        $stmt->bind_param("iiiss", $uId, $planId, $trainerId, $start, $expiry);
        $stmt->execute();
        $membershipId = $conn->insert_id;

        $stmt = $conn->prepare(
            "INSERT INTO payment
            (membership_id, amount, payment_via, payment_date, remarks)
            VALUES (?, ?, ?, CURDATE(), 'Membership purchase')"
        );
        $stmt->bind_param("ids", $membershipId, $plan["price"], $paymentVia);
        $stmt->execute();

        $stmt = $conn->prepare(
            "INSERT INTO notification (u_id, title, message)
             VALUES (?, 'Membership Activated', ?)"
        );
        $message = "Your membership is active until " . $expiry . ".";
        $stmt->bind_param("is", $uId, $message);
        $stmt->execute();

        $conn->commit();
        header("Location: ../user_dashboard.php?purchased=1");
    } catch (Throwable $e) {
        $conn->rollback();
        exit("Purchase failed.");
    }
    exit;
}
?>
