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

// Check if supplierId and productId are set in the POST data
if (isset($_POST['supplierId'], $_POST['productId'])) {
    $supplierId = $_POST['supplierId'];
    $productId = $_POST['productId'];

    // Prepare and execute the SQL query to fetch supplier product data
    $stmt = $conn->prepare("SELECT * FROM supplier_product WHERE supplier_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $supplierId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the results into an associative array
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Send the fetched data as JSON response
    echo json_encode($data);
} else {
    // If supplierId and productId are not set in the POST data, return an error
    echo json_encode(['error' => 'Supplier ID and Product ID are required.']);
}
?>