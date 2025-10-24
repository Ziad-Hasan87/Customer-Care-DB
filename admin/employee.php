<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission
if (isset($_POST['submit'])) {
    $employeeid = $_POST['employeeid'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO employee (employeeid, name, role, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $employeeid, $name, $role, $email, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('Employee added successfully'); window.location.href='employee.php';</script>";
        exit();
    } else {
        echo "<p style='color:red;'>Error adding employee: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

// Always fetch after insert logic
$result = $conn->query("SELECT * FROM employee");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f9f9f9; }
        .containers { background: #fff; padding: 20px; margin: 20px auto; width: 90%; max-width: 600px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .headings { text-align: center; color: #333; }
        .forms div { margin-bottom: 15px; }
        .labels { display: block; margin-bottom: 5px; font-weight: bold; }
        .forms input, .forms select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .buttons { display: block; width: 100%; background-color: #04AA6D; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        .buttons:hover { background-color: #038c5a; }
        table { border-collapse: collapse; width: 90%; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #04AA6D; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .table-container { margin-top: 40px; }
    </style>
</head>
<body>
    <div class="table-container">
        <h2 class="headings">All Employees</h2>
        <?php
        if (!$result) {
            echo "<p style='color:red; text-align:center;'>Error fetching employees: " . htmlspecialchars($conn->error) . "</p>";
        } elseif ($result->num_rows === 0) {
            echo "<p style='text-align:center;'>No employees found.</p>";
        } else {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['employeeid']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['role']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        }
        ?>
    </div>
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
<?php $conn->close(); ?>
