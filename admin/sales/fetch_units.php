<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the unit ID from the AJAX request
$unit_id = $_GET['unit_id'];

// Fetch the unit name from the database
$query = $conn->query("SELECT name FROM units WHERE id = $unit_id");
$unit_name = $query->fetch_assoc()['name'];

// Output the unit name
echo $unit_name;
?>
