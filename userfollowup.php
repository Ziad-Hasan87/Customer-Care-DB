<?php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) die("Connection Failed: " . mysqli_connect_error());

// Get request ID from URL
$requestid = isset($_GET['requestid']) ? intval($_GET['requestid']) : 0;
if ($requestid <= 0) die("Invalid request ID.");

// Fetch service request info
$stmt = "SELECT * FROM servicerequest WHERE requestid=$requestid";
$result = mysqli_query($conn, $stmt);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Request not found.");
}
$request = mysqli_fetch_assoc($result);

// Handle follow-up submission
if (isset($_POST['submit_followup'])) {
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $followupdate = date('Y-m-d');

    if (!empty($notes)) {
        // Insert follow-up
        $insert = "INSERT INTO followup (requestid, followupdate, notes)
                   VALUES ($requestid, '$followupdate', '$notes')";
        if (mysqli_query($conn, $insert)) {
            // Update request status to 'followingup'
            mysqli_query($conn, "UPDATE servicerequest SET requeststatus='followingup' WHERE requestid=$requestid");

            echo "<script>alert('Follow-up submitted successfully.'); window.location='userfollowup.php?requestid=$requestid';</script>";
        } else {
            echo "<script>alert('Failed to submit follow-up: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Notes cannot be empty.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Follow-up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="containers">
    <h2 class="headings">Request Follow-up for Request ID <?php echo htmlspecialchars($request['requestid']); ?></h2>
    <div>
        <p><strong>Request ID:</strong> <?php echo htmlspecialchars($request['requestid']); ?></p>
        <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($request['employeeid']); ?></p>
        <p><strong>Request Date:</strong> <?php echo htmlspecialchars($request['requestdate']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($request['description']); ?></p>
    </div>

    <form action="" method="POST" class="forms">
        <div>
            <label class="labels" for="notes">Follow-up Notes:</label>
            <textarea id="notes" name="notes" rows="4" cols="50" required></textarea>
        </div>
        <button type="submit" class="buttons" name="submit_followup">Submit Follow-up</button>
    </form>
</div>
</body>
</html>
