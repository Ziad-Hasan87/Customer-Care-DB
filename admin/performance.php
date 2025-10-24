<?php
// performance.php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) { die("Connection Failed: " . mysqli_connect_error()); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Performance</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="table-container">
    <h1 class="headings">Employee Performance</h1>
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Fetch data directly from the view
        $query = "SELECT * FROM employee_performance ORDER BY avg_rating DESC";
        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['employeeid'])."</td>";
                echo "<td>".htmlspecialchars($row['name'])."</td>";
                echo "<td>".htmlspecialchars($row['email'])."</td>";
                echo "<td>".htmlspecialchars($row['phone'])."</td>";
                echo "<td>".htmlspecialchars($row['role'])."</td>";

                // Display average rating as stars if available
                if($row['avg_rating'] !== null){
                    $avg = round($row['avg_rating']); // round to nearest integer
                    $stars = str_repeat('★', $avg) . str_repeat('☆', 5 - $avg);
                    echo "<td>".$stars." (".number_format($row['avg_rating'],1).")</td>";
                } else {
                    echo "<td>N/A</td>";
                }

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No employees found.</td></tr>";
        }

        mysqli_close($conn);
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
