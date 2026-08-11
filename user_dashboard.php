<?php
require_once "backend/auth.php";
require_role("User");

$loginId = $_SESSION["login_id"];

$stmt = $conn->prepare("SELECT * FROM gym_user WHERE login_id=?");
$stmt->bind_param("i", $loginId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$plans = $conn->query("SELECT * FROM membership_plan WHERE status='Active' ORDER BY price");

$stmt = $conn->prepare(
    "SELECT um.*, mp.plan_name, mp.duration, mp.price, t.t_name
     FROM user_membership um
     JOIN membership_plan mp ON um.plan_id=mp.plan_id
     LEFT JOIN trainer t ON um.t_id=t.t_id
     WHERE um.u_id=? ORDER BY um.membership_id DESC"
);
$stmt->bind_param("i", $user["u_id"]);
$stmt->execute();
$memberships = $stmt->get_result();

$stmt = $conn->prepare(
    "SELECT * FROM notification WHERE u_id=? ORDER BY notification_date DESC"
);
$stmt->bind_param("i", $user["u_id"]);
$stmt->execute();
$notifications = $stmt->get_result();

$trainers = $conn->query("SELECT t_id,t_name,t_expertise FROM trainer");
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>User Dashboard</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<nav><div class="logo">USER DASHBOARD</div><div><a href="../backend/logout.php">Logout</a></div></nav>
<div class="container">
<h1>Welcome, <?= htmlspecialchars($user["u_name"]) ?></h1>

<h2>Profile</h2>
<form action="backend/user_actions.php" method="post">
<input type="hidden" name="action" value="update_profile">
<label>Phone</label><input name="phone" value="<?= htmlspecialchars($user["u_phone"] ?? "") ?>">
<label>Email</label><input name="email" value="<?= htmlspecialchars($user["u_email"] ?? "") ?>">
<label>Address</label><input name="address" value="<?= htmlspecialchars($user["u_address"] ?? "") ?>">
<button>Update Profile</button>
</form>

<h2>Purchase Membership</h2>
<form action="backend/user_actions.php" method="post">
<input type="hidden" name="action" value="purchase">
<label>Plan</label>
<select name="plan_id" required>
<?php while($p = $plans->fetch_assoc()): ?>
<option value="<?= $p["plan_id"] ?>"><?= htmlspecialchars($p["plan_name"]) ?> - <?= $p["duration"] ?> - Rs. <?= $p["price"] ?></option>
<?php endwhile; ?>
</select>
<label>Trainer (optional)</label>
<select name="trainer_id">
<option value="">No trainer</option>
<?php while($t = $trainers->fetch_assoc()): ?>
<option value="<?= $t["t_id"] ?>"><?= htmlspecialchars($t["t_name"]) ?> - <?= htmlspecialchars($t["t_expertise"]) ?></option>
<?php endwhile; ?>
</select>
<label>Payment Via</label>
<select name="payment_via"><option>Cash</option><option>eSewa</option><option>Khalti</option><option>Mobile Banking</option></select>
<button>Purchase</button>
</form>

<h2>My Memberships</h2>
<table><tr><th>Plan</th><th>Duration</th><th>Trainer</th><th>Start</th><th>Expiry</th><th>Status</th></tr>
<?php while($m=$memberships->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($m["plan_name"]) ?></td><td><?= $m["duration"] ?></td>
<td><?= htmlspecialchars($m["t_name"] ?? "Not assigned") ?></td>
<td><?= $m["start_date"] ?></td><td><?= $m["expiry_date"] ?></td><td><?= $m["status"] ?></td>
</tr>
<?php endwhile; ?>
</table>

<h2>Notifications</h2>
<?php while($n=$notifications->fetch_assoc()): ?>
<div class="card"><b><?= htmlspecialchars($n["title"]) ?></b><p><?= htmlspecialchars($n["message"]) ?></p><small><?= $n["notification_date"] ?></small></div>
<?php endwhile; ?>
</div>
</body></html>
