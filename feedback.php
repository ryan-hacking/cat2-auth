<?php
session_start();
require "config.php";

// Only logged-in users can use this page.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$error = "";

// POST: save the submitted form into the feedback table.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? "");
    $rating  = (int) ($_POST["rating"] ?? 0);
    $message = trim($_POST["message"] ?? "");

    if ($subject === "" || $message === "" || $rating < 1 || $rating > 5) {
        $error = "Fill in every field and pick a rating from 1 to 5.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (username, subject, rating, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $_SESSION["username"], $subject, $rating, $message);
        $stmt->execute();
        // Redirect so refreshing the page does not submit the form again.
        header("Location: feedback.php?sent=1");
        exit;
    }
}

// GET: retrieve everything saved so far, newest first.
$rows = $conn->query("SELECT username, subject, rating, message, created_at FROM feedback ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Feedback</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="box" style="max-width:640px">
    <h2>Course feedback</h2>
    <?php if (isset($_GET["sent"])): ?><div class="msg ok">Feedback saved to the database.</div><?php endif; ?>
    <?php if ($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>

    <form method="POST" action="feedback.php">
      <label>Subject</label><input type="text" name="subject" required>
      <label>Rating (1 to 5)</label>
      <select name="rating" required style="width:100%;padding:8px;margin-top:4px">
        <option value="">Choose...</option>
        <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
      </select>
      <label>Message</label><textarea name="message" rows="4" required style="width:100%;padding:8px;margin-top:4px;box-sizing:border-box"></textarea>
      <button type="submit">Submit feedback</button>
    </form>

    <h3>Saved feedback</h3>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px">
      <tr style="background:#eee"><th>User</th><th>Subject</th><th>Rating</th><th>Message</th><th>Time</th></tr>
      <?php while ($r = $rows->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r["username"]) ?></td>
        <td><?= htmlspecialchars($r["subject"]) ?></td>
        <td><?= (int) $r["rating"] ?></td>
        <td><?= htmlspecialchars($r["message"]) ?></td>
        <td><?= htmlspecialchars($r["created_at"]) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to dashboard</a></p>
  </div>
</body>
</html>
