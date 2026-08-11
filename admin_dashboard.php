<?php
require_once "backend/auth.php";
require_role("Admin");

$plans = $conn->query("SELECT * FROM membership_plan ORDER BY plan_id DESC");
$users = $conn->query(
    "SELECT u.u_id,u.u_name,u.u_email,u.u_phone
     FROM gym_user u ORDER BY u.u_id DESC"
);
$revenue = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM payment")->fetch_assoc()["total"];
$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM gym_user")->fetch_assoc()["c"];
$activeMemberships = $conn->query("SELECT COUNT(*) AS c FROM user_membership WHERE status='Active'")->fetch_assoc()["c"];
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Admin Dashboard</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<nav><div class="logo">ADMIN DASHBOARD</div><div><a href="../backend/logout.php">Logout</a></div></nav>
<div class="container">
<h1>Administration</h1>

<div class="card-grid">
<div class="card"><h3>Total Users</h3><h2><?= $totalUsers ?></h2></div>
<div class="card"><h3>Active Memberships</h3><h2><?= $activeMemberships ?></h2></div>
<div class="card"><h3>Total Revenue</h3><h2>Rs. <?= number_format($revenue,2) ?></h2></div>
</div>

<h2>Add Membership Plan</h2>
<form action="backend/admin_actions.php" method="post">
<input type="hidden" name="action" value="add_plan">
<input name="plan_name" placeholder="Plan name" required>
<select name="duration"><option>Monthly</option><option>Quarterly</option><option>Yearly</option></select>
<input type="number" name="price" step="0.01" placeholder="Price" required>
<input name="description" placeholder="Description">
<button>Add Plan</button>
</form>

<h2>Membership Plans</h2>
<table><tr><th>ID</th><th>Plan</th><th>Duration</th><th>Price</th><th>Status</th><th>Action</th></tr>
<?php while($p=$plans->fetch_assoc()): ?>
<tr>
<td><?= $p["plan_id"] ?></td><td><?= htmlspecialchars($p["plan_name"]) ?></td>
<td><?= $p["duration"] ?></td><td><?= $p["price"] ?></td><td><?= $p["status"] ?></td>
<td>
<?php if($p["status"]==="Active"): ?>
<form style="padding:0;margin:0" action="backend/admin_actions.php" method="post">
<input type="hidden" name="action" value="delete_plan">
<input type="hidden" name="plan_id" value="<?= $p["plan_id"] ?>">
<button>Deactivate</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>

<h2>Users</h2>
<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
<?php while($u=$users->fetch_assoc()): ?>
<tr><td><?= $u["u_id"] ?></td><td><?= htmlspecialchars($u["u_name"]) ?></td><td><?= htmlspecialchars($u["u_email"] ?? "") ?></td><td><?= htmlspecialchars($u["u_phone"] ?? "") ?></td></tr>
<?php endwhile; ?>
</table>

<h2>Send Notification</h2>
<form action="backend/admin_actions.php" method="post">
<input type="hidden" name="action" value="send_notification">
<label>User ID</label><input type="number" name="u_id" required>
<label>Title</label><input name="title" required>
<label>Message</label><textarea name="message" required></textarea>
<button>Send Notification</button>
</form>
</div>
</body></html>
