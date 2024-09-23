<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

// Lumikha ng connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Siguruhing walang error sa connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize an empty array to store unit sizes
$units = array();

// Fetch unit sizes from the database
$query = "SELECT * FROM units";
$result = mysqli_query($conn, $query);

// Check if query executed successfully
if ($result) {
    // Loop through the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Add unit size to the array
        $units[] = array(
            'id' => $row['id'],
            'name' => $row['name']
        );
    }
}

// I-close ang connection
$conn->close();

// Convert the array to JSON format
echo json_encode($units);
?>