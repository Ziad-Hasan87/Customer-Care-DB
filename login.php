<?php
session_start();

// Process login only if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        echo "<script>alert('Please fill in all fields'); window.location='login.php';</script>";
        exit();
    }

    $conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("SELECT customerid, email, password FROM customer WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if ($password === $user['password']) {
        $expiry = time() + (10 * 24 * 60 * 60); // 10 days
        setcookie("customer_email", $user['email'], $expiry, "/");
        setcookie("customer_id", $user['customerid'], $expiry, "/");
        $_SESSION['customer_id'] = $user['customerid'];
        $_SESSION['customer_email'] = $user['email'];

        // Alert for successful login and redirect using JS
        echo "<script>
                alert('Login successful! Welcome, {$user['email']}');
                window.location='home.php';
              </script>";
        exit();
    } else {
        // Print database values for debugging
        $db_email = $user['email'];
        $db_password = $user['password'];
        echo "<script>alert('Invalid password. Database values:\\nEmail: $db_email\\nPassword: $db_password');</script>";
    }
} else {
    echo "<script>alert('Invalid email or password');</script>";
}



    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log in</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="containers">
    <h2 class="headings">Log in</h2>
    <form action="login.php" method="POST" class="forms">
        <div>
            <label class="labels" for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label class="labels" for="password">Password :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="buttons">Log in</button>
        <p class="paragraphs">Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>
</body>
</html>
