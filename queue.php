<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ongoing Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="table-container">
        <h1 class="headings">Ongoing Requests</h1>
        <table>
            <thead>
                <tr>
                    <th>RequestID</th>
                    <th>CustomerID</th>
                    <th>EmployeeID</th>
                    <th>Request Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $conn = mysqli_connect('localhost','root','','customercaredb',3306);
                    if(!$conn){
                        die("<tr><td colspan='7'>Connection Failed: ".mysqli_connect_error()."</td></tr>");
                    }

                    $stmt = "SELECT * FROM servicerequest WHERE requeststatus='ongoing' ORDER BY requestid ASC";
                    $result = mysqli_query($conn, $stmt);

                    if(!$result){
                        die("<tr><td colspan='7'>Query Failed: ".mysqli_error($conn)."</td></tr>");
                    }

                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_assoc($result)){
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['requestid'])."</td>";
                            echo "<td>".htmlspecialchars($row['customerid'])."</td>";
                            if(is_null($row['employeeid'])) echo "<td>Not Assigned</td>";
                            else echo "<td>".htmlspecialchars($row['employeeid'])."</td>";
                            echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
                            echo "<td>".htmlspecialchars($row['requesttype'])."</td>";
                            echo "<td>".htmlspecialchars($row['description'])."</td>";
                            echo "<td>".htmlspecialchars($row['requeststatus'])."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No ongoing requests found.</td></tr>";
                    }

                    mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
