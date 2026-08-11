<?php
require_once "backend/config.php";
$result = $conn->query("SELECT * FROM membership_plan WHERE status='Active' ORDER BY price");
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Packages</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<nav><div class="logo">GYM CENTER</div><div><a href="index.html">Home</a><a href="about.html">About</a><a href="contact.html">Contact</a><a href="login.html">Login</a></div></nav>
<div class="container">
<h1>Membership Packages</h1>
<div class="card-grid">
<?php while($p = $result->fetch_assoc()): ?>
<div class="card">
<h2><?= htmlspecialchars($p["plan_name"]) ?></h2>
<p><?= htmlspecialchars($p["duration"]) ?></p>
<h3>Rs. <?= number_format($p["price"], 2) ?></h3>
<p><?= htmlspecialchars($p["description"] ?? "") ?></p>
</div>
<?php endwhile; ?>
</div>
</div>
</body></html>
