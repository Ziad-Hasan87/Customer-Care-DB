<?php
// feedbackmonitor.php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) { die("Connection Failed: " . mysqli_connect_error()); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Monitor</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .stars { color: gold; font-size: 1.2em; }
    </style>
</head>
<body>
<div class="table-container">
    <h1 class="headings">Customer Feedback Monitor</h1>
    <table>
        <thead>
            <tr>
                <th>Feedback ID</th>
                <th>Customer Name</th>
                <th>Request ID</th>
                <th>Employee Name</th>
                <th>Service Name</th>
                <th>Rating</th>
                <th>Feedback Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT * FROM feedback_report_view ORDER BY feedbackdate DESC";
        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['feedbackid'])."</td>";
                echo "<td>".htmlspecialchars($row['customer_name'])."</td>";
                echo "<td>".htmlspecialchars($row['requestid'])."</td>";
                echo "<td>".(!empty($row['employee_name']) ? htmlspecialchars($row['employee_name']) : "N/A")."</td>";
                echo "<td>".(!empty($row['service_name']) ? htmlspecialchars($row['service_name']) : "N/A")."</td>";

                // Display rating as stars
                if($row['rating'] !== null){
                    $rating = intval($row['rating']);
                    $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                    echo "<td class='stars'>{$stars} ({$row['rating']})</td>";
                } else {
                    echo "<td>N/A</td>";
                }

                echo "<td>".htmlspecialchars($row['description'])."</td>";
                echo "<td>".htmlspecialchars($row['feedbackdate'])."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No feedbacks found.</td></tr>";
        }

        mysqli_close($conn);
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
