<!-- addService.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Service</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Simple toast notification styling */
        #toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 16px;
            position: fixed;
            z-index: 1000;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }
        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    </style>
</head>
<body>
    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <h2 class = "headings">Add Service</h2>
    <form action="addService.php" method="POST" class="forms" 
      style="display: flex; flex-direction: column; align-items: flex-start; gap: 15px; max-width: 400px; margin: auto;">
    
    <div style="display: flex; flex-direction: column; width: 100%;">
        <label class="labels" for="name" style="margin-bottom: 5px;">Service Name:</label>
        <input type="text" id="name" name="name" class="input-field" style="padding: 8px; width: 100%;" required>
    </div>
    
    <div style="display: flex; flex-direction: column; width: 100%;">
        <label class="labels" for="description" style="margin-bottom: 5px;">Description:</label>
        <textarea id="description" name="description" class="input-field" style="padding: 8px; width: 100%;" required></textarea>
    </div>
    
    <div style="display: flex; flex-direction: column; width: 100%;">
        <label class="labels" for="price" style="margin-bottom: 5px;">Price:</label>
        <input type="number" id="price" name="price" class="input-field" style="padding: 8px; width: 100%;" step="0.01" required>
    </div>
    
    <button type="submit" class="buttons" name="submit" style="padding: 10px 20px; align-self: center;">Add Service</button>
</form>

    </div>

    <!-- Toast container -->
    <div id="toast"></div>

    <?php
    if(isset($_POST['submit'])){
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $conn = mysqli_connect('localhost','root','','customercaredb',3306);
        if(!$conn){
            $message = "Connection failed: " . mysqli_connect_error();
            echo "<script>showToast('$message', true);</script>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO service (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $description, $price);

        if($stmt->execute()){
            $message = "Service added successfully!";
            echo "<script>showToast('$message', false);</script>";
        } else {
            $message = "Error: " . $stmt->error;
            echo "<script>showToast('$message', true);</script>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>

    <script>
        function showToast(message, isError=false) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.style.backgroundColor = isError ? "#e74c3c" : "#27ae60"; // red for error, green for success
            toast.className = "show";
            setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
        }
    </script>
</body>
</html>
