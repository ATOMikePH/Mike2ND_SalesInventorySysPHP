<?php
$userType = $_settings->userdata('type');
$isAdmin = ($userType == 1);
$isAccounting = ($userType == 2);
$isStaff = ($userType == 3);
// Fetch data for Stocks Notification
$notificationCount = 0; // Initialize count to 0
$stocksNotificationData = ''; // Variable to hold table rows

$qry = $conn->query(
"SELECT 
    pu.item_id,
    i.name AS item_name,
    b.name AS brand_name,
    c.name AS category,
    i.sku,
    i.image,
    i.status AS item_status,
    i.cogs,
    i.date_created,
    i.date_updated,
    pu.unit_id,
    pu.cost,
    pu.bbalance,
    pu.reorder,
    pu.status AS pu_status,
    u.name AS unit_name  
FROM 
    price_unit_order pu
LEFT JOIN 
    item_list i ON pu.item_id = i.id
LEFT JOIN 
    brands b ON i.brand_id = b.id
LEFT JOIN 
    categories c ON i.category_id = c.id
LEFT JOIN
    units u ON pu.unit_id = u.id WHERE i.status = 1;  
");
    while ($row = $qry->fetch_assoc()) :
        $in = $conn->query("SELECT SUM(quantity) as total FROM stock_list WHERE item_id = '{$row['item_id']}' AND type = 1 AND status = 1 AND unit = '{$row['unit_name']}'")->fetch_array()['total'];
        $out = $conn->query("SELECT SUM(quantity) as total, SUM(cogs) as cogs FROM stock_list WHERE item_id = '{$row['item_id']}' AND type = 2 AND status = 1 AND unit = '{$row['unit_name']}'")->fetch_assoc();
    $row['available'] = $in - $out['total'];
    $balance = $row['available'] + $row['bbalance'];

    $reorder = $row['reorder'];
    // Determine the status and color
    $status = '';
    $color = '';

    if ($balance == 0) {
        $status = 'Out of Stock';
        $color = 'badge-danger';
    } elseif ($balance < $row['reorder']) {
        $status = 'Low Stock';
        $color = 'badge-warning';
    }

    ?> <?php
    $unit_size = '';
    if (!empty($row['unit_id'])) {
        // Fetch unit size based on unit_id
        $unit_result = $conn->query("SELECT name FROM units WHERE id = '{$row['unit_id']}'")->fetch_assoc();
        $unit_size = $unit_result['name'];
    }
    ?>
    <?php
    // Display the item only if it's out of stock or low stock
    if ($status == 'Out of Stock' || $status == 'Low Stock') :
        $notificationCount++; // Increment count
        $stocksNotificationData .= '<tr>';
        $stocksNotificationData .= '<td><a>' . $row['brand_name'] . ' ' . $row['item_name'] .'</a></td>';
        $stocksNotificationData .= '<td><a>' . $unit_size . '</a></td>';
        $stocksNotificationData .= '<td><span class="badge ' . $color . '">' . $status . '</span></td>';
        $stocksNotificationData .= '</tr>';
    endif;
endwhile;
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var rowCount = document.querySelectorAll('.dropdown-content table tbody tr').length;
    var badgeElement = document.querySelector('.dropdown-toggle .badge');
    if (badgeElement) {
        badgeElement.textContent = rowCount;
    }
});
</script>

<style>
/* Add this style to hide badge when count is 0 */
.dropdown-toggle .badge:empty {
    display: none;
}
</style>


<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background-color: #f4f4f4;
    }

    .card-header span {
        font-size: 20px; 
        font-weight: bold; 
        color: #333; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
    }

    .info-box-text,
.info-box-number {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* Adjust the styles for responsiveness */
@media (max-width: 768px) {
    .info-box-text,
    .info-box-number {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }
}

    .welcome-container {
        background-size: cover;
        background-position: center;
        text-align: center;
        padding: 50px;
        color: #000000;
    }

    h1 {
        font-size: 3rem;
        color: #3498db;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .info-box-icon {
        font-size: 2rem;
        line-height: 50px;
        margin-right: 10px;
    }

    .info-box-content {
        padding: 15px;
    }

    .info-box-text {
        font-size: 1rem;
        font-weight: bold;
    }

    .info-box-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }


    .flip-card {
        perspective: 1000px;
    }

    .flip-card-inner {
        transition: transform 0.6s;
        transform-style: preserve-3d;
        cursor: pointer;
    }

    .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        width: 100%;
        height: 100%;
        position: absolute;
        backface-visibility: hidden;
    }

    .flip-card-back {
        transform: rotateY(180deg);
    }

    .row {
    display: flex;
    justify-content: space-between; 
}

.col-12 {
    flex: 0 0 auto;
}

.rancho {
    font-family: 'rancho', serif;
    font-size: 50px;
    text-shadow: 8px 8px 8px #aaa;
}

</style>


<?php if ($isAdmin || $isAccounting || $isStaff): ?>


    <div class="welcome-container">
    <h1 class="rancho">Welcome to <?php echo $_settings->info('name') ?>!</h1>

   <style>
    /* Dropdown container style */
    .dropdown-container {
        position: relative;
        display: inline-block;
        margin-top: 20px; /* Adjust margin as needed */
    }

    /* Dropdown toggle button style */
    .dropdown-toggle {
        background-color: #f1f1f1;
        color: #333;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
        padding: 5px 5px; /* Adjust padding as needed */
    }

    .dropdown-toggle:hover {
        background-color: #e0e0e0;
    }

    /* Dropdown content style */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 300px; /* Adjust width as needed */
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        z-index: 1;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        border-radius: 5px;
        padding: 10px;
        overflow: auto; /* Add scrollbar if content exceeds height */
        max-height: 300px; /* Adjust max-height as needed */
    }

    /* Show dropdown content on hover */
    .dropdown-container:hover .dropdown-content {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    /* Table style */
    .dropdown-content table {
        width: 100%;
        border-collapse: collapse;
    }

    .dropdown-content th,
    .dropdown-content td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    .dropdown-content th {
        background-color: #f1f1f1;
        color: #333;
        font-weight: bold;
    }

    /* Link style */
    .dropdown-content a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .dropdown-content a:hover {
        color: #007bff;
    }
</style>

<?php
// Fetch the count of rows that meet the criteria for "Stocks Notification"
$countQuery = $conn->query("SELECT COUNT(*) as count 
                            FROM `item_list` i 
                            INNER JOIN categories c ON i.category_id = c.id 
                            INNER JOIN price_unit_order pu ON pu.item_id = i.id 
                            WHERE (SELECT SUM(quantity) FROM stock_list WHERE item_id = i.id AND type = 1) 
                            - (SELECT SUM(quantity) FROM stock_list WHERE item_id = i.id AND type = 2) 
                            <= pu.bbalance AND pu.bbalance > 0");

// Fetch the count from the result
$countRow = $countQuery->fetch_assoc();
$notificationCount = $countRow['count'];
?>

<style>
.dropdown-toggle.has-count {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

</style>

<div class="dropdown-container">
        <!-- Use a div instead of a button for the dropdown toggle -->
        <div class="dropdown-toggle<?php echo ($notificationCount > 0) ? ' has-count' : ''; ?>">
    <h4>
        <span class="badge badge-warning"><?php echo $notificationCount; ?> </span> List of Items need to Restock!
    </h4>
</div>
        <div class="dropdown-content">
                <table>
                    <colgroup>
                        <col width="50%">
                        <col width="30%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th style="color: black;">Item Name</th>
                            <th style="color: black;">Unit Size</th>
                            <th style="color: black;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $stocksNotificationData; ?>
                    </tbody>
                </table>
            </div>
        </div>


</div>



<hr>


<div class="row">

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-th-list"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Products Registered</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `item_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-navy elevation-1"><i class="fas fa-truck-loading"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Suppliers Registered</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `supplier_list` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-lightblue elevation-1"><i class="fas fa-th-list"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Categories Registered</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `categories` where `status` = 1")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bg-light shadow">
            <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-users"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Accounts Registered</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `users` where id != 1 ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

    <?php endif; ?>
                    <?php if ($isAdmin || $isAccounting): ?>

    <?php
// Function to get the sales count based on time interval
function getSalesCount($interval) {
    global $conn;

    // Initialize $query variable
    $query = "SELECT COUNT(*) as count, SUM(amount) as total_amount FROM `sales_list` WHERE status = 1";

    switch ($interval) {
        case 'daily':
            $query .= " AND DATE(transaction_datetime) = CURDATE()";
            break;
        case 'weekly':
            $query .= " AND WEEK(transaction_datetime) = WEEK(NOW())";
            break;
        case 'monthly':
            $query .= " AND MONTH(transaction_datetime) = MONTH(NOW())";
            break;
        // 'total' case does not need additional conditions
    }

    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    return $data;
}

// Function to get the purchases or expenses count based on time interval
function getTransactionCount($interval, $tableName) {
    global $conn;

    // Initialize $query variable for purchases or expenses
    $transactionQuery = "SELECT COUNT(*) as count, SUM(amount) as total_amount FROM `$tableName`";

    switch ($interval) {
        case 'weekly':
            $transactionQuery .= " WHERE WEEK(transaction_datetime) = WEEK(NOW())";
            break;
        case 'monthly':
            $transactionQuery .= " WHERE MONTH(transaction_datetime) = MONTH(NOW())";
            break;
        // 'total' case does not need additional conditions
    }

    $transactionResult = $conn->query($transactionQuery);
    $transactionData = $transactionResult->fetch_assoc();
    return $transactionData;
}

// Function to get the purchases or expenses count based on time interval and specific remarks
// Function to get the purchases or expenses count based on time interval and specific remarks
function getTransactionCount2($interval, $tableName) {
    global $conn;

    // Check if the 'remarks' column exists in the table
    $checkRemarksColumn = $conn->query("SHOW COLUMNS FROM `$tableName`");
    $remarksColumnExists = $checkRemarksColumn->num_rows > 0;

    // Initialize $query variable for purchases or expenses
    $transactionQuery = "SELECT COUNT(*) as count, SUM(amount) as total_amount FROM `$tableName`";

    switch ($interval) {
        case 'weekly':
            $transactionQuery .= " WHERE WEEK(transaction_date) = WEEK(NOW()) ";
            break;
        case 'monthly':
            $transactionQuery .= " WHERE MONTH(transaction_date) = MONTH(NOW()) ";
            break;
        // 'total' case does not need additional conditions
    }

    // Add condition for specific remarks only if the 'remarks' column exists
    if ($remarksColumnExists) {
        $transactionQuery .= " AND remarks IN ('Completed', 'Paid')";
    }

    try {
        $transactionResult = $conn->query($transactionQuery);
        if ($transactionResult === false) {
            throw new Exception($conn->error);
        }
        
        $transactionData = $transactionResult->fetch_assoc();
        return $transactionData;
    } catch (Exception $e) {
         'Error: ' . $e->getMessage();
    }
}



// Usage examples:
$dailySales = getSalesCount('daily');
$weeklySales = getSalesCount('weekly');
$monthlySales = getSalesCount('monthly');
$totalSales = getSalesCount('total');

$weeklyPurchases = getTransactionCount('weekly', 'purchase_order_list');
$monthlyPurchases = getTransactionCount('monthly', 'purchase_order_list');
$totalPurchases = getTransactionCount('total', 'purchase_order_list');

$weeklyExpenses = getTransactionCount2('weekly', 'expenses');
$monthlyExpenses = getTransactionCount2('monthly', 'expenses');
$totalExpenses = getTransactionCount2('total', 'expenses');

// Calculate income
$weeklyIncome = $weeklySales['total_amount'] - ($weeklyPurchases['total_amount'] + $weeklyExpenses['total_amount']);
$monthlyIncome = $monthlySales['total_amount'] - ($monthlyPurchases['total_amount'] + $monthlyExpenses['total_amount']);
?>

<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-lime elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Weekly Total Sales</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($weeklySales['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Weekly Total Purchases</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($weeklyPurchases['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>



<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Weekly Total Expenses</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($weeklyExpenses['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-3 flip-card">
    <div class="flip-card-inner">
        <!-- Front Content -->
        <div class="flip-card-front">
        <?php
                // Calculate Monthly Gross Profit
                $weeklyGrossProfit = $weeklySales['total_amount'] - $weeklyPurchases['total_amount'];

                // Display weekly Gross Profit
                echo '<div class="info-box bg-light shadow">';
                echo '<span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-money-bill"></i></span>';
                echo '<div class="info-box-content">';
                echo '<span class="info-box-text">Weekly Gross Profit</span>';
                echo '<span class="info-box-number text-right">₱ ' . number_format($weeklyGrossProfit, 2) . '</span>';
                echo '</div>';
                echo '</div>';
            ?>
         
        </div>
        <!-- Back Content (Monthly Gross Profit) -->
        <div class="flip-card-back">
        <div class="info-box bg-light shadow">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Weekly Total Income</span>
                    <span class="info-box-number text-right">
                        <?php echo '₱ ' . number_format($weeklyIncome, 2); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-lime elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Monthly Total Sales</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($monthlySales['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Monthly Total Purchases</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($monthlyPurchases['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Monthly Total Expenses</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($monthlyExpenses['total_amount'], 2); ?>
            </span>
        </div>
    </div>
</div>

<div class="col-12 col-sm-6 col-md-3 flip-card">
    <div class="flip-card-inner">
        <!-- Front Content -->
        <div class="flip-card-front">
        <?php
                // Calculate Monthly Gross Profit
                $monthlyGrossProfit = $monthlySales['total_amount'] - $monthlyPurchases['total_amount'];

                // Display Monthly Gross Profit
                echo '<div class="info-box bg-light shadow">';
                echo '<span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-money-bill"></i></span>';
                echo '<div class="info-box-content">';
                echo '<span class="info-box-text">Monthly Gross Profit</span>';
                echo '<span class="info-box-number text-right">₱ ' . number_format($monthlyGrossProfit, 2) . '</span>';
                echo '</div>';
                echo '</div>';
            ?>
         
        </div>
        <!-- Back Content (Monthly Gross Profit) -->
        <div class="flip-card-back">
        <div class="info-box bg-light shadow">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Monthly Total Income</span>
                    <span class="info-box-number text-right">
                        <?php echo '₱ ' . number_format($monthlyIncome, 2); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-5">
        <div class="card">
            <div class="card-header text-center">
                <span class="text-center">Top Bestseller Product</span>
            </div>
            <div class="card-body text-center">
                <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                    <label class="btn btn-outline-primary active">
                        <input type="radio" name="options" id="option1" value="week" autocomplete="off" checked>This Week
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" id="option2" value="month" autocomplete="off">This Month
                    </label>
                    <label class="btn btn-outline-primary">
                        <input type="radio" name="options" id="option3" value="year" autocomplete="off">This Year
                  </label>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="salesTable">
                        <thead>
                            <tr>
                                <th>Rank</th> 
                                <th>Item Unit Description</th>
                                <th>Total Non-VAT Sales</th>
                                <th>Item Sold</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="border-left border-right"></div>
 
    <div class="col-lg-5">
    <div class="card">
        <div class="card-header text-center">
            <span class="text-center">Highest Received Stocks</span>
        </div>
        <div class="card-body text-center">
        <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                <label class="btn btn-outline-primary active">
                    <input type="radio" name="options_stocks" id="option1_stocks" value="week" autocomplete="off" checked>This Week
                </label>
                <label class="btn btn-outline-primary">
                    <input type="radio" name="options_stocks" id="option2_stocks" value="month" autocomplete="off">This Month
                </label>
                <label class="btn btn-outline-primary">
                    <input type="radio" name="options_stocks" id="option3_stocks" value="year" autocomplete="off">This Year
                </label>
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-sm" id="highestStocksTable">
                    <thead>
                        <tr>
                            <th>Rank</th> 
                            <th>Item Unit Description</th>
                            <th>QTY Received</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadSalesAndStocksData();

    $('input[name="options"]').click(function() {
        loadSalesAndStocksData();
    });

    $('input[name="options_stocks"]').click(function() {
        loadSalesAndStocksData();
    });
});

function loadSalesAndStocksData() {
    var selectedOption = $('input[name="options"]:checked').val();
    var selectedOptionStocks = $('input[name="options_stocks"]:checked').val();

    $.ajax({
        url: "f1s.php",
        type: "POST",
        data: { option: selectedOption },
        success: function(response) {
            var data = JSON.parse(response);
            $("#salesTable tbody").html(data.sales_table);
        }
    });

    $.ajax({
        url: "f1s.php",
        type: "POST",
        data: { option: selectedOptionStocks },
        success: function(response) {
            var data = JSON.parse(response);
            $("#highestStocksTable tbody").html(data.stocks_table);
        }
    });
}
</script>
<script type="text/javascript" src="../dist/js/chartloader.js"></script>

<?php
// Assuming you have already connected to your database
// Fetch sales data for each month
$query = "SELECT MONTHNAME(transaction_datetime) AS month, SUM(amount) AS total_sales FROM sales_list WHERE YEAR(transaction_datetime) = YEAR(NOW()) AND status = 1 GROUP BY MONTH(transaction_datetime)";
$result = mysqli_query($conn, $query);

// Initialize an array to store chart data
$chart_data = array();

// Initialize an array containing all months
$months_of_year = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

// Loop through the results and populate the sales data
while ($row = mysqli_fetch_assoc($result)) {
    $month = $row['month'];
    $total_sales = (float)$row['total_sales'];
    
    // Add sales data to the chart data array
    $chart_data[$month] = $total_sales;
}

// Reorder the months of the year to ensure they are in the correct order
$ordered_chart_data = array();
foreach ($months_of_year as $month) {
    if (isset($chart_data[$month])) {
        $ordered_chart_data[$month] = $chart_data[$month];
    } else {
        // If no data for the month, set sales to 0
        $ordered_chart_data[$month] = 0;
    }
}

// Format chart data as required by Google Charts
$formatted_chart_data = array();
foreach ($ordered_chart_data as $month => $sales) {
    $formatted_chart_data[] = array($month, $sales);
}

// Convert chart data array to JSON format
$chart_data_json = json_encode($formatted_chart_data);
?>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Sales');
        data.addRows(<?php echo $chart_data_json; ?>);

        var currentYear = new Date().getFullYear();

        var options = {
            title: 'MONTHLY SALES PERFORMANCE COMPARISON', 
            titleTextStyle: {fontSize: 18},
            isStacked: true,
            hAxis: {
                title: 'Month of the Year ' + currentYear
            },
            vAxis: {
                title: 'Total Amount of Sales'
            },
            legend: 'none',
            animation: {
                duration: 1000, // Set the duration of the animation in milliseconds
                easing: 'out' // Set the easing function for the animation
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
        chart.draw(data, options);

    }
</script>

<style>

@media screen and (max-width: 768px) {
    #chart_div {
        height: 300px; /* Adjust height as needed */
    }
}

@media screen and (max-width: 576px) {
    #chart_div {
        height: 200px; /* Adjust height as needed */
    }
}
</style>
<div id="chart_div2" style="position: relative; width: 150%; height: 400px; max-width: 150%;"></div>

<?php
// Assuming you have already connected to your database
// Fetch sales data for each year
$query = "SELECT YEAR(transaction_datetime) AS year, 
                 SUM(CAST(REPLACE(amount, ',', '') AS DECIMAL(10,2))) AS total_sales 
          FROM sales_list 
          WHERE YEAR(transaction_datetime) >= YEAR(NOW()) - 2 
          AND status = 1 
          GROUP BY YEAR(transaction_datetime)";
$result = mysqli_query($conn, $query);

// Initialize an array to store chart data
$chart_data = array();

// Initialize an array containing all years
$years = array();
$current_year = date('Y');
for ($i = 0; $i < 3; $i++) {
    $years[] = $current_year - $i;
}

// Loop through the results and populate the sales data
while ($row = mysqli_fetch_assoc($result)) {
    $year = $row['year'];
    $total_sales = (float)$row['total_sales'];
    
    // Add sales data to the chart data array
    $chart_data[$year] = $total_sales;
}

// Reorder the years to ensure they are in the correct order
$ordered_chart_data = array();
foreach ($years as $year) {
    if (isset($chart_data[$year])) {
        $ordered_chart_data[$year] = $chart_data[$year];
    } else {
        // If no data for the year, set sales to 0
        $ordered_chart_data[$year] = 0;
    }
}

// Format chart data as required by Google Charts
$formatted_chart_data = array();
foreach ($ordered_chart_data as $year => $sales) {
    $formatted_chart_data[] = array((string)$year, $sales);
}

// Convert chart data array to JSON format
$chart_data_json = json_encode($formatted_chart_data);
?>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Year'); // Change 'Day' to 'Month'
        data.addColumn('number', 'Sales');
        data.addRows(<?php echo $chart_data_json; ?>);

        var options = {
              title: 'YEARLY SALES PERFORMANCE COMPARISON',
              titleTextStyle: {fontSize: 18}, 
              isStacked: true,
            hAxis: {
                title: 'Years' 
            },
            vAxis: {
                title: 'Total Amount of Sales'
            },
            legend: 'none',
            animation: {
                duration: 1000, // Set the duration of the animation in milliseconds
                easing: 'out' // Set the easing function for the animation
            }
            
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div3'));
        chart.draw(data, options);

    }
</script>

<style>

@media screen and (max-width: 768px) {
    #chart_div {
        height: 300px; /* Adjust height as needed */
    }
}

@media screen and (max-width: 576px) {
    #chart_div {
        height: 200px; /* Adjust height as needed */
    }
}
</style>
<div id="chart_div3" style="position: relative; width: 150%; height: 400px; max-width: 150%;"></div>
    <?php endif; ?>
</div>

