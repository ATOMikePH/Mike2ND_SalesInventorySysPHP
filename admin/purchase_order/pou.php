<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if item_id is set and not empty
if(isset($_POST['item_id']) && !empty($_POST['item_id'])){
    $item_id = $_POST['item_id'];
    
    // Fetch the unit size from the database based on the selected item description
    $query = "SELECT unit_size FROM item_list WHERE id = $item_id";
    $result = $conn->query($query);
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $unit_size = $row['unit_size'];
        echo '<option value="'.$unit_size.'">'.$unit_size.'</option>';
    }else{
        echo '<option value="">Unit Size not found</option>';
    }
}else{
    echo '<option value="">Invalid Request</option>';
}
?>