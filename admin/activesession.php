<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

$qry = $conn->query("SELECT id, last_login FROM users WHERE id != '1'");
while ($row = $qry->fetch_assoc()) {
    $response[$row['id']] = strtotime($row['last_login']);
}

$conn->close(); // Close the database connection

header('Content-Type: application/json');
echo json_encode($response);
?>