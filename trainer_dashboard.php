<?php
require_once "backend/auth.php";
require_role("Trainer");

$loginId = $_SESSION["login_id"];
$stmt = $conn->prepare("SELECT * FROM trainer WHERE login_id=?");
$stmt->bind_param("i", $loginId);
$stmt->execute();
$trainer = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare(
    "SELECT u.u_name,u.u_phone,u.u_email,um.start_date,um.expiry_date,mp.plan_name
     FROM user_membership um
     JOIN gym_user u ON um.u_id=u.u_id
     JOIN membership_plan mp ON um.plan_id=mp.plan_id
     WHERE um.t_id=? AND um.status='Active'"
);
$stmt->bind_param("i", $trainer["t_id"]);
$stmt->execute();
$members = $stmt->get_result();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Trainer Dashboard</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<nav><div class="logo">TRAINER DASHBOARD</div><div><a href="../backend/logout.php">Logout</a></div></nav>
<div class="container">
<h1>Welcome, <?= htmlspecialchars($trainer["t_name"]) ?></h1>
<p>Expertise: <?= htmlspecialchars($trainer["t_expertise"]) ?></p>
<h2>Assigned Members</h2>
<table><tr><th>Name</th><th>Phone</th><th>Email</th><th>Plan</th><th>Expiry</th></tr>
<?php while($m=$members->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($m["u_name"]) ?></td><td><?= htmlspecialchars($m["u_phone"] ?? "") ?></td><td><?= htmlspecialchars($m["u_email"] ?? "") ?></td><td><?= htmlspecialchars($m["plan_name"]) ?></td><td><?= $m["expiry_date"] ?></td></tr>
<?php endwhile; ?>
</table>
</div>
</body></html>
