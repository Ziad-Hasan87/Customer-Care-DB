<?php
// adminhome.php

// Determine which page to load
$page = $_GET['page'] ?? 'adminqueue'; // default to Queue

// Map page keys to actual files
$pages = [
    'adminqueue' => 'adminqueue.php',
    'services' => 'services.php',
    'addservice' => 'addservice.php',
    'performance' => 'performance.php',
    'employee' => 'employee.php',
    'feedback' => 'feedbackmonitor.php'
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
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        nav { background-color: #333; overflow: hidden; display: flex; }
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

<!-- Navbar -->
<nav>
    <a href="?page=adminqueue" <?= isActive('adminqueue') ?>>Queue</a>
    <a href="?page=services" <?= isActive('services') ?>>Services</a>
    <a href="?page=addservice" <?= isActive('addservice') ?>>Add Services</a>
    <a href="?page=performance" <?= isActive('performance') ?>>Performance</a>
    <a href="?page=employee" <?= isActive('employee') ?>>Add Employees</a>
    <a href="?page=feedback" <?= isActive('feedback') ?>>Feedbacks</a>
</nav>

<div class="content">
    <?php
    if(array_key_exists($page, $pages) && file_exists($pages[$page])){
        include $pages[$page];
    } else {
        echo "<h1>Page not found</h1>";
        echo "<p>The requested page does not exist.</p>";
    }
    ?>
</div>

</body>
</html>
