<?php
session_start();
require "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, username, full_name, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // password_verify compares the typed password with the stored hash.
    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"]   = $user["id"];
        $_SESSION["username"]  = $user["username"];
        $_SESSION["full_name"] = $user["full_name"];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Wrong username or password.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Log in</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="box">
    <h2>Log in</h2>
    <?php if ($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>
    <form method="POST" action="login.php">
      <label>Username</label><input type="text" name="username" required>
      <label>Password</label><input type="password" name="password" required>
      <button type="submit">Log in</button>
    </form>
    <p>No account? <a href="signup.php">Sign up</a></p>
  </div>
</body>
</html>
