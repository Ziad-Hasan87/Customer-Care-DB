<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Token</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
        if(!isset($_COOKIE['customer_id'])) {
            echo "<script>alert('Customer not logged in.');</script>";
            exit('Customer not logged in.');
        }
        $customerID = intval($_COOKIE['customer_id']);

        if(isset($_POST['submit'])){
            $requesttype= $_POST['category'];
            $date = date('Y-m-d H:i:s');
            $conn = mysqli_connect('localhost','root','','customercaredb',3306);
            if(!$conn){
                echo "<script>alert('Connection Failed.');</script>";
            } else {
                if(empty($requesttype)){
                    echo "<script>alert('Please select a category');</script>";
                    die('Please select a category');
                }
                $stmt = "INSERT INTO servicerequest(customerid, requesttype, requestDate, requeststatus) 
                         VALUES('$customerID', '$requesttype', '$date', 'waiting')";
                $execute = mysqli_query($conn, $stmt);
                if(!$execute){
                    echo "<script>alert('Request Failed.');</script>";
                    die('Request Failed : '.mysqli_error($conn));
                }
                mysqli_close($conn);
                header("Location: home.php");
                exit();
            }
        }
    ?>
    <div class="containers">
        <h2 class="headings">Request Customer Care Service Token</h2>
        <form action="requestToken.php" method="POST" class="forms">
            <div>
                <label class="labels" for="customerid">CustomerID : <?= htmlspecialchars($customerID) ?></label>
            </div>
            <div>
                <label class="labels" for="category">Category:</label>
                <select id="category" name="category" class="selection" required>
                    <option value="warranty">Warranty</option>
                    <option value="repair">Repair</option>
                </select>
            </div>
            <button type="submit" class="buttons" name="submit">Request Token</button>
        </form>
    </div>
</body>
</html>
