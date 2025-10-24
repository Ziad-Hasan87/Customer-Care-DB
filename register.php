<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User Account</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
        if(isset($_POST['submit'])){
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];

            $conn = mysqli_connect('localhost','root','','customercaredb',3306);
            if(!$conn){
                echo "<script>alert('Connection Failed.');</script>";
                //die('Connection Failed : '.mysqli_connect_error());
            }
            else{
                if(empty($name) || empty($email) || empty($phone) || empty($password)){
                    echo "<script>alert('Please do not provide empty fields');</script>";
                    die('Please do not provide empty fields');
                }
                if(strlen($password) < 6){
                    echo "<script>alert('Password must be at least 6 characters long');</script>";
                    die('Password must be at least 6 characters long');
                }
                $check = "select * from customer where email ='$email'";
                $result = mysqli_query($conn, $check);
                if(mysqli_num_rows($result) > 0){
                    echo "<script>alert('Email already exists. Please choose a different email.');</script>";
                    die('Email already exists. Please choose a different email.');
                }
                else{
                    $stmt = "insert into customer(name, email, phone, password) values('$name','$email', '$phone', '$password')";
                    $execute = mysqli_query($conn, $stmt);
                    if(!$execute){
                        echo "<script>alert('Registration Failed.');</script>";
                        die('Registration Failed : '.mysqli_error($conn));
                    }
                    echo "Registration Successful";
                    mysqli_close($conn);
                    header("Location: welcome.php?email=" . urlencode($email));
                    exit();
                }
            }
        }
    ?>
    <div class="containers">
        <h2 class = "headings">Register User Account</h2>
        <form action="" method="POST" class = "forms">
            <div>
                <label class = "labels" for="username">Name :</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label class = "labels" for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label class = "labels" for="phone">Phone :</label>
                <input id="phone" name="phone" required>
            </div>
            <div>
                <label class = "labels" for="password">Password :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="submit"class = "buttons">Register</button>
            <p class = "paragraphs">Already have an account? <a href="login.php">Log in here</a></p>
        </form>
    </div>
</body>
</html>