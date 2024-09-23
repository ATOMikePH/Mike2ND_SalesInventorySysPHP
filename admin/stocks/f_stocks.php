<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve item ID and unit ID from POST data
$itemId = $_POST['itemId'];
$unitId = $_POST['unitId'];

// Query to fetch stock details based on item ID and unit ID
$query = "SELECT * FROM stock_list WHERE item_id = '$itemId' AND unit = '$unitId'";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Start generating HTML content for the stock details table
    $html = '<table class="table-responsive">

               
                <tbody>';

    // Loop through the results and append each row to the HTML content
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td class="text-center">' . $row['date_created'] . '</td>
                    <td class="text-center">' . $row['quantity'] . '</td>
                    <td class="text-center">
                        <span class="badge ' . ($row['type'] == 1 ? 'badge-success' : 'badge-danger') . '">
                            ' . ($row['type'] == 1 ? 'TRANSFERRED IN' : 'TRANSFERRED OUT') . '
                        </span>
                    </td>
                  </tr>';
    }

    // Close the HTML table
    $html .= '</tbody>
          </table>';

    // Echo the HTML content
    echo $html;
} else {
    // If no stock details found, display a message
    echo '<p>No stock details available.</p>';
}
?>

<!-- optional -->
<!-- <td class="text-center">' . $row['unit'] . '</td> -->