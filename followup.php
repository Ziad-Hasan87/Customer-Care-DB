<?php
// followup.php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) { die("Connection Failed: " . mysqli_connect_error()); }

// Get customer ID from cookie
if (!isset($_COOKIE['customer_id'])) {
    die("Customer not logged in.");
}
$customer_id = intval($_COOKIE['customer_id']);

// Handle "Complete" button click
if(isset($_POST['mark_complete'])){
    $requestid = intval($_POST['requestid']);
    mysqli_query($conn, "UPDATE servicerequest SET requeststatus='complete' WHERE requestid=$requestid");
    header("Location: " . $_SERVER['PHP_SELF']); // refresh page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-up Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="table-container">
    <h1 class="headings">Follow-up Requests</h1>
    <table>
        <thead>
            <tr>
                <th>RequestID</th>
                <th>CustomerID</th>
                <th>EmployeeID</th>
                <th>Request Date</th>
                <th>Category</th>
                <th>ServiceID</th>
                <th>Description</th>
                <th>Follow-up Notes</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "
            SELECT sr.*, f.notes 
            FROM servicerequest sr 
            LEFT JOIN followup f ON sr.requestid=f.requestid
            WHERE sr.customerid = $customer_id AND sr.requeststatus='followingup'
            ORDER BY sr.requestid ASC
        ";
        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['requestid'])."</td>";
                echo "<td>".htmlspecialchars($row['customerid'])."</td>";
                echo "<td>".htmlspecialchars($row['employeeid'])."</td>";
                echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
                echo "<td>".htmlspecialchars($row['requesttype'])."</td>";

                if(!is_null($row['serviceid'])){
                    $svcRes = mysqli_query($conn,"SELECT name FROM service WHERE serviceid=".intval($row['serviceid']));
                    $svcName = ($svcRes && mysqli_num_rows($svcRes) > 0) ? mysqli_fetch_assoc($svcRes)['name'] : "N/A";
                    echo "<td>".htmlspecialchars($row['serviceid']).". ".htmlspecialchars($svcName)."</td>";
                } else {
                    echo "<td>N/A</td>";
                }

                echo "<td>".htmlspecialchars($row['description'])."</td>";
                echo "<td>".(!empty($row['notes']) ? htmlspecialchars($row['notes']) : "N/A")."</td>";

                // Status column with "Complete" button
                echo "<td>";
                if($row['requeststatus'] !== 'complete'){
                    echo "<form method='post' style='margin:0;'>
                            <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                            <button type='submit' name='mark_complete'>Complete</button>
                          </form>";
                } else {
                    echo "Complete";
                }
                echo "</td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No Follow-up requests found.</td></tr>";
        }

        mysqli_close($conn);
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
