<?php
require "config.php";

$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST["username"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $phone     = trim($_POST["phone"] ?? "");
    $password  = $_POST["password"] ?? "";

    if ($username === "" || $full_name === "" || $email === "" || strlen($password) < 6) {
        $message = "Fill in all required fields. Password must be at least 6 characters.";
        $type = "error";
    } else {
        // Never store the plain password, store a hash.
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, full_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $username, $full_name, $email, $phone, $hash);
            $stmt->execute();
            $message = "Account created for " . htmlspecialchars($username) . ". You can now log in.";
            $type = "ok";
        } catch (mysqli_sql_exception $e) {
            // Error 1062 = duplicate entry (username already taken).
            $message = ($e->getCode() == 1062) ? "That username is already taken." : "Could not create the account.";
            $type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Sign up</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="box">
    <h2>Sign up</h2>
    <?php if ($message): ?><div class="msg <?= $type ?>"><?= $message ?></div><?php endif; ?>
    <form method="POST" action="signup.php">
      <label>Username</label><input type="text" name="username" required>
      <label>Full name</label><input type="text" name="full_name" required>
      <label>Email</label><input type="email" name="email" required>
      <label>Phone (optional)</label><input type="text" name="phone">
      <label>Password</label><input type="password" name="password" required>
      <button type="submit">Create account</button>
    </form>
    <p>Already registered? <a href="login.php">Log in</a></p>
  </div>
</body>
</html>
