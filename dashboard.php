<?php
session_start();
// Only logged-in users can see this page.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="box">
    <h2>Welcome, <?= htmlspecialchars($_SESSION["full_name"]) ?></h2>
    <div class="msg ok">Logged in as <b><?= htmlspecialchars($_SESSION["username"]) ?></b></div>
    <p><a href="logout.php">Log out</a></p>
  </div>
</body>
</html>
