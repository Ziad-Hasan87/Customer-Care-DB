<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
        if (isset($_POST['submit'])) {
            $employeeid = $_POST['employeeid'];
            $name = $_POST['name'];
            $role = $_POST['role'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];

            // Connect to database
            $conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
            if (!$conn) {
                echo "<script>alert('Connection Failed.');</script>";
            } else {
                if (empty($employeeid) || empty($name) || empty($role) || empty($email) || empty($phone)) {
                    echo "<script>alert('Please fill in all fields');</script>";
                    die('Please fill in all fields');
                }

                $check = mysqli_query($conn, "SELECT * FROM employee WHERE employeeid='$employeeid' OR email='$email'");
                if (mysqli_num_rows($check) > 0) {
                    echo "<script>alert('Employee with this ID or Email already exists');</script>";
                } else {
                    $stmt = "INSERT INTO employee (employeeid, name, role, email, phone) 
                             VALUES ('$employeeid', '$name', '$role', '$email', '$phone')";
                    $execute = mysqli_query($conn, $stmt);
                    if (!$execute) {
                        echo "<script>alert('Failed to add employee');</script>";
                        die('Error: ' . mysqli_error($conn));
                    } else {
                        echo "<script>alert('Employee added successfully');</script>";
                    }
                }

                mysqli_close($conn);
            }
        }
    ?>

    <div class="containers">
        <h2 class="headings">Add New Employee</h2>
        <form action="employee.php" method="POST" class="forms">
            <div>
                <label class="labels" for="employeeid">Employee ID:</label>
                <input type="number" id="employeeid" name="employeeid" required>
            </div>
            <div>
                <label class="labels" for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label class="labels" for="role">Role:</label>
                <input type="text" id="role" name="role" required>
            </div>
            <div>
                <label class="labels" for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label class="labels" for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <button type="submit" class="buttons" name="submit">Add Employee</button>
        </form>
    </div>
</body>
</html>
