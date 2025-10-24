<?php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) die("Connection Failed: " . mysqli_connect_error());

$requestid = isset($_GET['requestid']) ? intval($_GET['requestid']) : 0;

if ($requestid <= 0) {
    die("Invalid request ID.");
}

// Fetch service request info
$stmt = "SELECT * FROM servicerequest WHERE requestid=$requestid";
$result = mysqli_query($conn, $stmt);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Request not found.");
}

$request = mysqli_fetch_assoc($result);

// Handle feedback submission
if (isset($_POST['submit_feedback'])) {
    $rating = intval($_POST['rating']);
    $feedback_desc = mysqli_real_escape_string($conn, $_POST['description']);

    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Rating must be between 1 and 5.');</script>";
    } else {
        $customerid = $request['customerid'];
        $employeeid = $request['employeeid'];
        $serviceid = $request['serviceid'] ?? "NULL";
        $date = date('Y-m-d');

        $insert = "INSERT INTO feedback (customerid, requestid, employeeid, serviceid, rating, description, feedbackdate)
                   VALUES ($customerid, $requestid, $employeeid, " . ($serviceid ?? "NULL") . ", $rating, '$feedback_desc', '$date')";
        if (mysqli_query($conn, $insert)) {
            echo "<script>alert('Feedback submitted successfully.'); window.location='userfeedback.php?requestid=$requestid';</script>";
        } else {
            echo "<script>alert('Failed to submit feedback: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="containers">
    <h2 class="headings">Submit Feedback for Request ID <?php echo htmlspecialchars($request['requestid']); ?></h2>
    <div>
        <p><strong>Request ID:</strong> <?php echo htmlspecialchars($request['requestid']); ?></p>
        <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($request['employeeid']); ?></p>
        <p><strong>Request Date:</strong> <?php echo htmlspecialchars($request['requestdate']); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($request['description']); ?></p>
    </div>

    <form action="" method="POST" class="forms">
        <div>
            <label class="labels" for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required>
        </div>
        <div>
            <label class="labels" for="description">Feedback:</label>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea>
        </div>
        <button type="submit" class="buttons" name="submit_feedback">Submit Feedback</button>
    </form>
</div>
</body>
</html>
