<?php
// Database settings. Change the password to the one you set for lampuser in MySQL.
$db_host = "localhost";
$db_user = "lampuser";
$db_pass = "YOUR_PASSWORD_HERE";
$db_name = "CAT2";

// Make mysqli throw exceptions so errors can be caught below.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed. Check config.php.");
}
