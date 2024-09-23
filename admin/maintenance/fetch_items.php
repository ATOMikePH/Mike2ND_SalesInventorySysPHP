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

$category = $_POST['category'];

$query = "SELECT i.*, c.name as category_name FROM item_list i INNER JOIN categories c ON i.category_id = c.id WHERE c.name = '$category'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo '
        <div class="modal-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Available Stocks</th>
                    </tr>
                </thead>
                <tbody>';
    while ($row = $result->fetch_assoc()) {
        // Ipapakita ang detalye ng bawat item sa isang table row
        $in = $conn->query("SELECT SUM(quantity) as total FROM stock_list WHERE item_id = '{$row['id']}' AND type = 1")->fetch_array()['total'];
        $out = $conn->query("SELECT SUM(quantity) as total, SUM(cogs) as cogs FROM stock_list WHERE item_id = '{$row['id']}' AND type = 2")->fetch_assoc();
        $row['balance'] = $in - $out['total'];
        $available = $row['balance'] + $row['bbalance'];

        echo '<tr>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['cost'] . '</td>';
        echo '<td>' . $available . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
} else {
    echo 'No items found for this category.';
}

// I-close ang connection
$conn->close();
?>