<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_COOKIE['customer_email']) || !isset($_COOKIE['customer_id'])) {
    header("Location: login.php");
    exit();
}

$email = $_COOKIE['customer_email'];
$customerId = $_COOKIE['customer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div style="display: flex; height: 80%; flex-direction: column; align-items: center; justify-content: center;">
        <h1>Welcome, <?= htmlspecialchars($email) ?>.</h1>
        <p>Customer ID: <?= htmlspecialchars($customerId) ?></p>
    </div>
</body>
</html>
