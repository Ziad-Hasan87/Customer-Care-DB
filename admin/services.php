<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Services</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="table-container">
        <h1 class="headings">All Services</h1>
        <table>
            <thead>
                <tr>
                    <th>ServiceID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Connect to the database
                    $conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
                    if (!$conn) {
                        die("<tr><td colspan='4'>Connection Failed: " . mysqli_connect_error() . "</td></tr>");
                    }

                    // Fetch all services
                    $stmt = "SELECT * FROM service ORDER BY serviceid ASC";
                    $result = mysqli_query($conn, $stmt);

                    if (!$result) {
                        die("<tr><td colspan='4'>Query Failed: " . mysqli_error($conn) . "</td></tr>");
                    }

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['serviceid']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No services found.</td></tr>";
                    }

                    mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
