<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date_condition = "AND WEEK(sl.date_created) = WEEK(NOW()) AND YEAR(sl.date_created) = YEAR(NOW())";

if (isset($_POST['option']) && $_POST['option'] == 'month') {
    $date_condition = "AND MONTH(sl.date_created) = MONTH(NOW()) AND YEAR(sl.date_created) = YEAR(NOW())";
}

if (isset($_POST['option']) && $_POST['option'] == 'year') {
    $date_condition = "AND YEAR(sl.date_created) = YEAR(NOW())";
}

// Query for top sales
$sql_sales = 
"SELECT il.name AS product_name, 
b.name AS brand, 
sl.unit, 
SUM(sl.total) AS total_sales, 
SUM(sl.quantity) AS total_out
        FROM stock_list sl
        INNER JOIN item_list il ON sl.item_id = il.id
        INNER JOIN brands b ON il.brand_id = b.id
        WHERE sl.type = 2 {$date_condition} AND il.status = 1
        GROUP BY sl.item_id, sl.unit
        ORDER BY total_sales DESC
        LIMIT 3";

$result_sales = $conn->query($sql_sales);

$sales_table_data = ''; // Variable to store sales table data

if ($result_sales->num_rows > 0) {
    $counter = 0;
    while ($row = $result_sales->fetch_assoc()) {
        $counter++;
        $rank_icon = "";
        if ($counter == 1) {
            $rank_icon = "🥇";
        } elseif ($counter == 2) {
            $rank_icon = "🥈";
        } elseif ($counter == 3) {
            $rank_icon = "🥉";
        }

        $formatted_total_sales = '₱ ' . number_format($row['total_sales'], 2);

        $sales_table_data .= "<tr>";
        $sales_table_data .= "<td>{$rank_icon}</td>";
        $sales_table_data .= "<td>{$row['brand']} {$row['product_name']} {$row['unit']}</td>";
        $sales_table_data .= "<td>{$formatted_total_sales}</td>";
        $sales_table_data .= "<td>{$row['total_out']}</td>";
        $sales_table_data .= "</tr>";
    }
} else {
    $sales_table_data = "<tr><td colspan='4'>No sales data available.</td></tr>";
}

// Query for highest added stocks
$date_condition2 = "AND WEEK(sl.date_created) = WEEK(NOW()) AND YEAR(sl.date_created) = YEAR(NOW())";

if (isset($_POST['option'])) {
    $interval = $_POST['option'];
    if ($interval == 'month') {
        $date_condition2 = "AND MONTH(sl.date_created) = MONTH(NOW()) AND YEAR(sl.date_created) = YEAR(NOW())";
    } elseif ($interval == 'year') {
        $date_condition2 = "AND YEAR(sl.date_created) = YEAR(NOW())";
    }
}

$sql_stocks = "SELECT sl.item_id, il.name AS item_name, b.name AS brand, sl.unit AS units, SUM(sl.quantity) AS total_quantity
               FROM stock_list sl
               INNER JOIN item_list il ON sl.item_id = il.id
               INNER JOIN brands b ON il.brand_id = b.id
               WHERE sl.type = 1 {$date_condition2} AND il.status = 1
               GROUP BY sl.item_id, sl.unit
               ORDER BY total_quantity DESC";

$result_stocks = $conn->query($sql_stocks);

$stocks_table_data = ''; // Variable to store stocks table data

if ($result_stocks->num_rows > 0) {
    $counter = 0;
    while ($row = $result_stocks->fetch_assoc()) {
        $counter++;
        $rank_icon = "";
        if ($counter == 1) {
            $rank_icon = "🥇";
        } elseif ($counter == 2) {
            $rank_icon = "🥈";
        } elseif ($counter == 3) {
            $rank_icon = "🥉";
        }
        $stocks_table_data .= "<tr>";
        $stocks_table_data .= "<td>{$rank_icon}</td>";
        $stocks_table_data .= "<td>{$row['brand']} {$row['item_name']} {$row['units']}</td>";
        $stocks_table_data .= "<td>{$row['total_quantity']}</td>";
        $stocks_table_data .= "</tr>";
    }
} else {
    $stocks_table_data = "<tr><td colspan='3'>No stocks data available.</td></tr>";
}

$conn->close();

// Echo the table data separately
echo json_encode(array('sales_table' => $sales_table_data, 'stocks_table' => $stocks_table_data));
?>