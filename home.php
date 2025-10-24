<?php
session_start();

// Handle logout directly
if (isset($_GET['logout'])) {
    // Clear session
    session_unset();
    session_destroy();

    // Clear cookies
    setcookie("customer_email", "", time() - 3600, "/");
    setcookie("customer_id", "", time() - 3600, "/");

    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Check if the login cookies exist
if (!isset($_COOKIE['customer_email']) || !isset($_COOKIE['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Get customer info from cookies
$email = $_COOKIE['customer_email'];
$customerId = $_COOKIE['customer_id'];
$username = '';

// Connect to database
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if ($conn) {
    $stmt = $conn->prepare("SELECT name FROM customer WHERE customerid = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
}
$conn->close();

// Determine the current page
$page = $_GET['page'] ?? 'profile';

// Map pages to files
$pages = [
    'profile' => 'profile.php',
    'requestToken' => 'requestToken.php',
    'feedback' => 'feedback.php',
    'follow_up' => 'followup.php'
];

// Helper function to mark active nav link
function isActive($p) {
    global $page;
    return $p === $page ? 'class="active"' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Care</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        nav { background-color: #333; overflow: hidden; }
        nav a {
            float: left; display: block;
            color: #f2f2f2; text-align: center;
            padding: 14px 20px; text-decoration: none;
        }
        nav a.active { background-color: #04AA6D; color: white; }
        nav a:hover { background-color: #ddd; color: black; }
        .content { padding: 20px; }
    </style>
</head>
<body>

<nav style="display: flex; background-color: #333; overflow: hidden;">
    <div style="display: flex; gap: 10px;">
        <a href="?page=profile" <?= isActive('profile') ?>>Profile</a>
        <a href="?page=requestToken" <?= isActive('requestToken') ?>>Request Token</a>
        <a href="?page=feedback" <?= isActive('feedback') ?>>Feedback</a>
        <a href="?page=follow_up" <?= isActive('follow_up') ?>>Follow Up</a>
    </div>
    <div style="margin-left: auto;">
        <!-- Logout link triggers logout in same page -->
        <a href="?logout=1" style="color: #f2f2f2; text-decoration: none; padding: 14px 20px;">Logout</a>
    </div>
</nav>

<div class="content">
    <?php
    if (array_key_exists($page, $pages) && file_exists($pages[$page])) {
        include $pages[$page];
    } else {
        echo "<h1>Page not found</h1>";
        echo "<p>The page you are looking for does not exist.</p>";
    }
    ?>
</div>

</body>
</html>
