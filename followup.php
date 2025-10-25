<?php
// followup.php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) { die("Connection Failed: " . mysqli_connect_error()); }

// Get customer ID from cookie
if (!isset($_COOKIE['customer_id'])) {
    die("Customer not logged in.");
}
$customer_id = intval($_COOKIE['customer_id']);

// Handle "Complete" button click for the first table
if(isset($_POST['mark_complete'])){
    $requestid = intval($_POST['requestid']);
    mysqli_query($conn, "UPDATE servicerequest SET requeststatus='complete' WHERE requestid=$requestid");
    header("Location: " . $_SERVER['PHP_SELF']);
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
    <!-- TABLE 1: Customer's Follow-up Requests -->
    <h1 class="headings">My Follow-up Requests</h1>
    <?php
    $query = "
        SELECT sr.*, f.notes 
        FROM servicerequest sr 
        LEFT JOIN followup f ON sr.requestid=f.requestid
        WHERE sr.customerid = $customer_id AND sr.requeststatus='followingup'
        ORDER BY sr.requestid ASC
    ";
    $result = mysqli_query($conn, $query);
    if(!$result){
        echo "<div class='debug'>Error: " . mysqli_error($conn) . "</div>";
        $rows = [];
    } else {
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    ?>
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
        if(!empty($rows)){
            foreach($rows as $row){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['requestid'])."</td>";
                echo "<td>".htmlspecialchars($row['customerid'])."</td>";
                echo "<td>".htmlspecialchars($row['employeeid'])."</td>";
                echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
                echo "<td>".htmlspecialchars($row['requesttype'])."</td>";
                echo "<td>".(!is_null($row['serviceid']) ? intval($row['serviceid']) : "N/A")."</td>";
                echo "<td>".htmlspecialchars($row['description'])."</td>";
                echo "<td>".(!empty($row['notes']) ? htmlspecialchars($row['notes']) : "N/A")."</td>";
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
            echo "<tr><td colspan='9'>No follow-up requests found.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- TABLE 2: All Follow-ups -->
    <h1 class="headings" style="margin-top:40px;">All Follow-ups</h1>
    <?php
    $query_all = "
        SELECT f.followupid, f.requestid, f.followupdate, f.notes
        FROM followup f
        LEFT JOIN servicerequest sr ON f.requestid = sr.requestid
        WHERE sr.customerid = $customer_id
        ORDER BY f.followupdate DESC
    ";
    $res_all = mysqli_query($conn, $query_all);
    if(!$res_all){
        echo "<div class='debug'>Error fetching all follow-ups: " . mysqli_error($conn) . "</div>";
        $followup_rows = [];
    } else {
        $followup_rows = mysqli_fetch_all($res_all, MYSQLI_ASSOC);
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Follow-up ID</th>
                <th>Request ID</th>
                <th>Follow-up Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($followup_rows)){
            foreach($followup_rows as $row){
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['followupid'])."</td>";
                echo "<td>".htmlspecialchars($row['requestid'])."</td>";
                echo "<td>".htmlspecialchars($row['followupdate'])."</td>";
                echo "<td>".(!empty($row['notes']) ? htmlspecialchars($row['notes']) : "N/A")."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No follow-ups found for this customer.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<?php mysqli_close($conn); ?>
</body>
</html>
