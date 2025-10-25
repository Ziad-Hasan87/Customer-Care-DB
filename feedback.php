<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Orders & Feedbacks</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .stars {
            color: gold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <?php
            if(!isset($_COOKIE['customer_id'])){
                echo "<p>Customer not logged in.</p>";
                exit;
            }

            $customer_id = intval($_COOKIE['customer_id']); 

            $conn = mysqli_connect('localhost','root','','customercaredb',3306);
            if(!$conn){
                die("<p>Connection Failed: ".mysqli_connect_error()."</p>");
            }
        ?>

<h1 class="headings">Completed Orders</h1>
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
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $stmt = "SELECT * FROM servicerequest WHERE customerid = $customer_id AND requeststatus='complete' ORDER BY requestid ASC";
            $result = mysqli_query($conn, $stmt);

            if($result && mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $requestid = intval($row['requestid']);

                    echo "<tr>";
                    echo "<td>{$requestid}</td>";
                    echo "<td>".htmlspecialchars($row['customerid'])."</td>";
                    echo "<td>".(is_null($row['employeeid']) ? 'Not Assigned' : htmlspecialchars($row['employeeid']))."</td>";
                    echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
                    echo "<td>".htmlspecialchars($row['requesttype'])."</td>";
                    echo "<td>".htmlspecialchars($row['description'])."</td>";
                    echo "<td>".htmlspecialchars($row['requeststatus'])."</td>";

                    $feedback_exists = false;
                    $fb_check = mysqli_query($conn, "SELECT feedbackid FROM feedback WHERE customerid = $customer_id AND requestid = $requestid LIMIT 1");
                    if($fb_check && mysqli_num_rows($fb_check) > 0){
                        $feedback_exists = true;
                    }

                    echo "<td style='display:flex; gap:5px;'>";

                    echo "<form action='userfollowup.php' method='get' style='margin:0;'>
                            <input type='hidden' name='requestid' value='{$requestid}'>
                            <button type='submit'>Request Followup</button>
                          </form>";

                    if(!$feedback_exists){
                        echo "<form action='userfeedback.php' method='get' style='margin:0;'>
                                <input type='hidden' name='requestid' value='{$requestid}'>
                                <button type='submit'>Feedback</button>
                              </form>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No completed orders found.</td></tr>";
            }
        ?>
    </tbody>
</table>

        <br><br>

        <h1 class="headings">My Feedbacks</h1>
        <table>
            <thead>
                <tr>
                    <th>FeedbackID</th>
                    <th>CustomerID</th>
                    <th>RequestID</th>
                    <th>ServiceID</th>
                    <th>EmployeeID</th>
                    <th>Rating</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $fb_stmt = "SELECT * FROM feedback WHERE customerid = $customer_id ORDER BY feedbackdate DESC";
                    $fb_result = mysqli_query($conn, $fb_stmt);

                    if($fb_result && mysqli_num_rows($fb_result) > 0){
                        while($fb = mysqli_fetch_assoc($fb_result)){
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($fb['feedbackid'])."</td>";
                            echo "<td>".htmlspecialchars($fb['customerid'])."</td>";
                            echo "<td>".htmlspecialchars($fb['requestid'])."</td>";
                            echo "<td>".(!is_null($fb['serviceid']) ? htmlspecialchars($fb['serviceid']) : "N/A")."</td>";
                            echo "<td>".(!is_null($fb['employeeid']) ? htmlspecialchars($fb['employeeid']) : "N/A")."</td>";
                            
                            $rating = intval($fb['rating']);
                            $stars = str_repeat("★", $rating) . str_repeat("☆", 5 - $rating);
                            echo "<td class='stars'>{$stars}</td>";

                            echo "<td>".htmlspecialchars($fb['description'])."</td>";
                            echo "<td>".htmlspecialchars($fb['feedbackdate'])."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No feedbacks submitted yet.</td></tr>";
                    }

                    mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
