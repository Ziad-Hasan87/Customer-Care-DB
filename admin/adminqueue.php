<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="table-container">
<?php
$conn = mysqli_connect('localhost', 'root', '', 'customercaredb', 3306);
if (!$conn) { die("Connection Failed: " . mysqli_connect_error()); }

if (isset($_POST['assign'])) {
    $requestid = $_POST['requestid'];
    $employeeid = $_POST['selected_employeeid'] ?? null;
    if ($employeeid) {
        $stmt = "UPDATE servicerequest SET employeeid='$employeeid' WHERE requestid='$requestid'";
        mysqli_query($conn, $stmt) or die("<script>alert('Failed to assign employee: " . mysqli_error($conn) . "');</script>");
        header("Location:" . $_SERVER['PHP_SELF']); exit;
    }
}

if (isset($_POST['assignservice'])) {
    $requestid = $_POST['requestid'];
    $serviceid = $_POST['selected_serviceid'] ?? null;
    if ($serviceid) {
        $stmt = "UPDATE servicerequest SET serviceid='$serviceid' WHERE requestid='$requestid'";
        mysqli_query($conn, $stmt) or die("<script>alert('Failed to assign service: " . mysqli_error($conn) . "');</script>");
        header("Location:" . $_SERVER['PHP_SELF']); exit;
    }
}

if (isset($_POST['adddescription'])) {
    $requestid = $_POST['requestid'];
    $description = $_POST['description'] ?? '';
    if (!empty($description)) {
        $descEscaped = mysqli_real_escape_string($conn, $description);
        mysqli_query($conn, "UPDATE servicerequest SET description='$descEscaped' WHERE requestid='$requestid'");
        header("Location:" . $_SERVER['PHP_SELF']); exit;
    }
}

$statuses = ['waiting'=>'Waiting','ongoing'=>'Ongoing','complete'=>'Complete','followingup'=>'Following Up'];
if (isset($_POST['changestatus'])) {
    $requestid = $_POST['requestid'];
    $status = $_POST['selected_status'] ?? null;
    if ($status) {
        mysqli_query($conn, "UPDATE servicerequest SET requeststatus='$status' WHERE requestid='$requestid'");
        header("Location:" . $_SERVER['PHP_SELF']); exit;
    }
}

function renderTable($conn, $status, $label, $statuses, $allowServiceAssign=false){
    echo "<h1 class='headings'>$label Requests</h1>
    <table>
        <thead>
            <tr>
                <th>RequestID</th>
                <th>CustomerID</th>
                <th>EmployeeID</th>
                <th>Request Date</th>
                <th>Category</th>";
    if($allowServiceAssign) echo "<th>ServiceID</th>";
    else echo "<th>ServiceID</th>";
    echo "<th>Description</th><th>Status</th></tr></thead><tbody>";

    $result = mysqli_query($conn, "SELECT * FROM servicerequest WHERE requeststatus='$status' ORDER BY requestid ASC");
    if($result && mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)){
            echo "<tr>";
            echo "<td>".htmlspecialchars($row['requestid'])."</td>";
            echo "<td>".htmlspecialchars($row['customerid'])."</td>";

            if(is_null($row['employeeid'])){
                echo "<td><form method='post' style='display:flex;gap:8px;margin:0;'>
                    <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                    <select name='selected_employeeid' required>";
                $empResult = mysqli_query($conn,"SELECT employeeid,name FROM employee");
                if($empResult && mysqli_num_rows($empResult)>0){
                    while($emp=mysqli_fetch_assoc($empResult)){
                        echo "<option value='".htmlspecialchars($emp['employeeid'])."'>".htmlspecialchars($emp['employeeid']).". ".htmlspecialchars($emp['name'])."</option>";
                    }
                } else echo "<option disabled>No employees found</option>";
                echo "</select><button type='submit' name='assign'>Assign</button></form></td>";
            } else echo "<td>".htmlspecialchars($row['employeeid'])."</td>";

            echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
            echo "<td>".htmlspecialchars($row['requesttype'])."</td>";

            if($allowServiceAssign){
                if(is_null($row['serviceid'])){
                    echo "<td><form method='post' style='display:flex;gap:8px;margin:0;'>
                        <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                        <select name='selected_serviceid' required>";
                    $svcResult = mysqli_query($conn,"SELECT serviceid,name FROM service");
                    if($svcResult && mysqli_num_rows($svcResult)>0){
                        while($svc=mysqli_fetch_assoc($svcResult)){
                            echo "<option value='".htmlspecialchars($svc['serviceid'])."'>".htmlspecialchars($svc['serviceid']).". ".htmlspecialchars($svc['name'])."</option>";
                        }
                    } else echo "<option disabled>No services found</option>";
                    echo "</select><button type='submit' name='assignservice'>Assign</button></form></td>";
                } else {
                    $svcRes = mysqli_query($conn,"SELECT name FROM service WHERE serviceid=".intval($row['serviceid']));
                    $svcName = ($svcRes && mysqli_num_rows($svcRes)>0)? mysqli_fetch_assoc($svcRes)['name'] : "N/A";
                    echo "<td>".htmlspecialchars($row['serviceid']).". ".htmlspecialchars($svcName)."</td>";
                }
            } else {
                if(!is_null($row['serviceid'])){
                    $svcRes = mysqli_query($conn,"SELECT name FROM service WHERE serviceid=".intval($row['serviceid']));
                    $svcName = ($svcRes && mysqli_num_rows($svcRes)>0)? mysqli_fetch_assoc($svcRes)['name'] : "N/A";
                    echo "<td>".htmlspecialchars($row['serviceid']).". ".htmlspecialchars($svcName)."</td>";
                } else echo "<td>N/A</td>";
            }

            if(empty($row['description'])){
                echo "<td><form method='post' style='margin:0;'>
                    <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                    <input type='text' name='description' placeholder='Enter description' required style='width:150px;'>
                    <button type='submit' name='adddescription'>Add</button>
                    </form></td>";
            } else echo "<td>".htmlspecialchars($row['description'])."</td>";

            echo "<td>".htmlspecialchars($row['requeststatus']);
            echo "<form method='post' style='margin:0;'>
                <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                <select name='selected_status' required>";
            foreach($statuses as $val=>$lbl){
                $selected = ($row['requeststatus']===$val)?"selected":"";
                echo "<option value='$val' $selected>$lbl</option>";
            }
            echo "</select><button type='submit' name='changestatus'>Change</button></form></td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No $label requests found.</td></tr>";
    }

    echo "</tbody></table><br><br>";
}

function renderFollowupTable($conn, $statuses){
    echo "<h1 class='headings'>Follow-up Requests</h1>
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
        <tbody>";

    $result = mysqli_query($conn, "SELECT sr.*, f.notes FROM servicerequest sr LEFT JOIN followup f ON sr.requestid=f.requestid WHERE sr.requeststatus='followingup' ORDER BY sr.requestid ASC");

    if($result && mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)){
            echo "<tr>";
            echo "<td>".htmlspecialchars($row['requestid'])."</td>";
            echo "<td>".htmlspecialchars($row['customerid'])."</td>";
            echo "<td>".htmlspecialchars($row['employeeid'])."</td>";
            echo "<td>".htmlspecialchars($row['requestdate'])."</td>";
            echo "<td>".htmlspecialchars($row['requesttype'])."</td>";
            
            if(!is_null($row['serviceid'])){
                $svcRes = mysqli_query($conn,"SELECT name FROM service WHERE serviceid=".intval($row['serviceid']));
                $svcName = ($svcRes && mysqli_num_rows($svcRes)>0)? mysqli_fetch_assoc($svcRes)['name'] : "N/A";
                echo "<td>".htmlspecialchars($row['serviceid']).". ".htmlspecialchars($svcName)."</td>";
            } else echo "<td>N/A</td>";

            echo "<td>".htmlspecialchars($row['description'])."</td>";

            echo "<td>".(!empty($row['notes']) ? htmlspecialchars($row['notes']) : "N/A")."</td>";

            echo "<td>".htmlspecialchars($row['requeststatus']);
            echo "<form method='post' style='margin:0;'>
                <input type='hidden' name='requestid' value='".htmlspecialchars($row['requestid'])."'>
                <select name='selected_status' required>";
            foreach($statuses as $val=>$lbl){
                $selected = ($row['requeststatus']===$val)?"selected":"";
                echo "<option value='$val' $selected>$lbl</option>";
            }
            echo "</select><button type='submit' name='changestatus'>Change</button></form></td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No Follow-up requests found.</td></tr>";
    }

    echo "</tbody></table><br><br>";
}

renderTable($conn,'waiting','Waiting',$statuses,false);
renderTable($conn,'ongoing','Ongoing',$statuses,true);
renderFollowupTable($conn, $statuses);
renderTable($conn,'complete','Complete',$statuses,false);


mysqli_close($conn);
?>
    </div>
</body>
</html>
