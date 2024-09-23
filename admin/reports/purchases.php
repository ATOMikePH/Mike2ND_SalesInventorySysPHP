
<style>
    body {
        font-family: 'Open Sans', sans-serif;
    }

    .card {
        transition: box-shadow 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    h3 {
        font-weight: 700;
        margin-bottom: 20px;
    }

    .no-data-message {
        font-style: italic;
        color: #888;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        font-size: 50px;
        font-weight: bold;
        text-align: center;
        margin-top: 30vh;
    }

    .show-message {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    /* Center align the form */
    .form-container2 {
        text-align: center;
    }

    /* Add some margin to the buttons for better spacing */
    form button {
        margin: 3px;
    }
</style>

<style>
    .form-container2 {
        float: left;
        margin-right: 500px;
    }
</style>
<style>
    .custom-small-button {
        padding: 5px 10px; /* Adjust the padding to make the button smaller */
        font-size: 12px;  /* Adjust the font size if needed */
    }
</style>


<?php
$qry = null;

if (isset($_POST['filter_dates'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $purchase_query = "SELECT p.*, s.name as supplier_name FROM `purchase_order_list` p
                      LEFT JOIN `supplier_list` s ON p.supplier_id = s.id
                      WHERE p.transaction_datetime BETWEEN '$start_date' AND '$end_date'
                      ORDER BY p.transaction_datetime DESC";
    $qry = $conn->query($purchase_query);
} else {
    $purchase_query = "SELECT p.*, s.name as supplier_name FROM `purchase_order_list` p
                      LEFT JOIN `supplier_list` s ON p.supplier_id = s.id
                      ORDER BY p.transaction_datetime DESC";
    $qry = $conn->query($purchase_query);
}

if (!isset($_SESSION['start_date'])) {
    $_SESSION['start_date'] = date("Y-m-d\TH:i", strtotime(date("Y-m-d") . " 00:00"));
}
if (!isset($_SESSION['end_date'])) {
    $_SESSION['end_date'] = date("Y-m-d\TH:i", strtotime(date("Y-m-d", strtotime("+1 day")) . " 00:00"));
}

$isFormSubmitted = isset($_POST['filter_dates']);
$hasResults = $qry && $qry->num_rows > 0;
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave icon"></i> Purchase Order Report</h3>
        <div class="card-tools">
            <div class="form-container2">
                <form method="post" action="" onsubmit="return validateDates()">
                    <label for="start_date">From Date:</label>
                    <input type="datetime-local" name="start_date" id="start_date" value="<?php echo $_SESSION['start_date']; ?>" required>

                    <label for="end_date">To Date:</label>
                    <input type="datetime-local" name="end_date" id="end_date" value="<?php echo $_SESSION['end_date']; ?>" required>

                    <button type="button" onclick="resetDates()" class="btn btn-sm btn-primary">Reset</button>
                    <button type="submit" name="filter_dates" class="btn btn-sm btn-primary">Filter</button>
                </form>
            </div>
        </div>
        <button id="print_purchases" class="btn btn-flat btn-success"><span class="fas fa-print"></span> Print</button>
<hr>
<div class="card-body" id="print_purchases">
        <div class="container-fluid">
            <div class="container-fluid">
                <?php if (!$isFormSubmitted) : ?>
                    <!-- Show message when no search has been initiated -->
                    <p class="no-data-message" id="noDataMessage">Please search the purchases date and time of data first!</p>
                <?php elseif ($hasResults) : ?>
                 
                    <?php
                    $total_amount = 0;
                    $i = 1;

                    if (isset($_POST['filter_dates'])) {
                        $start_date = $_POST['start_date'];
                        $end_date = $_POST['end_date'];
                        $purchase_query = "SELECT * FROM `purchase_order_list` WHERE transaction_datetime BETWEEN '$start_date' AND '$end_date' ORDER BY transaction_datetime DESC";
                    } else {
                        $purchase_query = "SELECT * FROM `purchase_order_list` ORDER BY transaction_datetime DESC";
                    }

                    $qry = $conn->query($purchase_query);

                    while ($row = $qry->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="text-center"><?php  $i++; ?></td>
                            <td class="text-center"><?php date("Y-m-d H:i", strtotime($row['transaction_datetime'])) ?></td>
                            <td class="text-right"><?php  '₱ ' . number_format($row['amount'], 2) ?></td>                        </tr>
                        <?php
                        $total_amount += $row['amount'];
                    }
                    ?>
                 
<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-light shadow">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-coins"></i></span>

        <div class="info-box-content">
            <span class="info-box-text">Total Calculated Amount:</span>
            <span class="info-box-number text-right">
                <?php echo '₱ ' . number_format($total_amount, 2); ?>
            </span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
                    <!-- Show table only if there are results -->
                    <table class="table table-bordered table-stripped">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="20%">
                            <col width="20%">
                            <col width="10%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Transaction Date</th>
                                <th class="text-center">Purchase Order</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">No. of Items</th>
                                <th class="text-center">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
    $i = 1;
    if (isset($_POST['filter_dates'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $purchase_query = "SELECT p.*, s.name as supplier_name FROM `purchase_order_list` p
                          LEFT JOIN `supplier_list` s ON p.supplier_id = s.id
                          WHERE p.transaction_datetime BETWEEN '$start_date' AND '$end_date'
                          ORDER BY p.transaction_datetime DESC";
    } else {
        $purchase_query = "SELECT p.*, s.name as supplier_name FROM `purchase_order_list` p
                          LEFT JOIN `supplier_list` s ON p.supplier_id = s.id
                          ORDER BY p.transaction_datetime DESC";
    }

    $qry = $conn->query($purchase_query);
    while ($row = $qry->fetch_assoc()) :
        $row['stock_ids'] = isset($row['stock_ids']) ? $row['stock_ids'] : '';
        $row['supplier'] = isset($row['supplier_name']) ? $row['supplier_name'] : '';
        $row['items'] = count(explode(',', $row['stock_ids']));
    ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class="text-center"><?php echo date("Y-m-d H:i", strtotime($row['transaction_datetime'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url . 'admin?page=purchase_order/view_po&id=' . $row['id'] ?>">
                                            <?php echo $row['po_code'] ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo $row['supplier'] ?></td>
                                    <td class="text-center"><?php echo number_format($row['items']) ?></td>
                                    <td class="text-right"><?php echo '₱ ' . number_format($row['amount'], 2) ?></td>
                                </tr>  

                            <?php endwhile; ?>
                            <!-- Add this row for displaying calculations -->
                         
                        </tbody>                              
                          <tfoot>
                                 <tr id="calculation-row" style="display: none;">
                                <td colspan="4" class="text-right">Total:</td>
                                <td id="total-items" class="text-center"></td>
                                <td id="total-amount" class="text-right"></td>
                            </tr>
                            </tfoot>
                    </table>
</div> <!-- Close the container for the card body -->
</div> <!-- Close the card body -->
</div> <!-- Close the card -->
                <?php else : ?>
                    <!-- Show message when no results found -->
                    <p class="no-data-message show-message" id="noDataMessage">No Data Found. Please Try Again.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<script>
    // Add this script for message effect
    document.addEventListener("DOMContentLoaded", function () {
        // Wait for the document to load
        var noDataMessage = document.getElementById("noDataMessage");

        // Trigger the transition effect
        setTimeout(function () {
            noDataMessage.classList.add("show-message");
        }, 100); // Adjust the delay as needed
    });
</script>

<script>
    function resetDates() {
        // Set default values for start_date and end_date
        var defaultStartDate = "<?php echo date("Y-m-d\TH:i", strtotime(date("Y-m-d") . " 00:00")); ?>";
        var defaultEndDate = "<?php echo date("Y-m-d\TH:i", strtotime(date("Y-m-d", strtotime("+1 day")) . " 00:00")); ?>";

        // Set the default values to the input fields
        document.getElementById("start_date").value = defaultStartDate;
        document.getElementById("end_date").value = defaultEndDate;
    }
    
    function validateDates() {
        var startDate = new Date(document.getElementById("start_date").value);
        var endDate = new Date(document.getElementById("end_date").value);

        if (startDate > endDate) {
            alert("Error: From Date cannot be exceed than To Date.");
            return false;
        }

        if (endDate < startDate) {
            alert("Error: To Date cannot be earlier than From Date.");
            return false;
        }

        return true;
    }

    $(document).ready(function () {

        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable();
    })


</script>


<script>
$(document).ready(function () {
    $('#print_purchases').click(function () {
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Purchases Order - Print View");

        // Clone the table and remove any unnecessary classes
        var p = $('#print_purchases table').clone();
        p.removeClass('dataTable');

         // Remove sorting icons and additional classes
         p.find('th, td').removeClass('sorting sorting_asc sorting_desc');

// Remove the "Action" column header and corresponding cells
p.find('th:contains("Action")').remove();
p.find('td button, td div.dropdown-menu').remove();

        // Debugging statements
        console.log('Table rows before cloning:', p.find('tbody tr').length);

        _el.append(_head);
        _el.append('<div class="receipt-container">' +
            '<div class="text-center header">' +
            '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" alt="Company Logo">' +
            '<h4><?php echo $_settings->info('name') ?></h4>' +
            '<div class="tin-number">TIN No. <?php echo $_settings->info('tin_num') != false ? $_settings->info('tin_num') : '000-000-000-000'; ?></div>' +
            '<div class="company-address"><?php echo $_settings->info('company_address') != false ? $_settings->info('company_address') : 'Castillejos, Zambales PH 2208'; ?></div>' +
            '<div class="company-address"><?php echo $_settings->info('phone_num') != false ? $_settings->info('phone_num') : '(+63) 912 345 6789'; ?> | <?php echo $_settings->info('company_email') != false ? $_settings->info('company_email') : 'formatNiMichael@gmail.com'; ?> </div>' +
            '<div><hr></div>' +
            '<h4 class="text-center">List of Purchases Order Transaction</h4>' +
            '</div><hr/>');


        // Calculate the total amount dynamically
        var totalAmount = 0;
        p.find('tbody tr').each(function () {
            var amountText = $(this).find('td:eq(5)').text().trim(); // Adjust the column index if needed
            var amount = parseFloat(amountText.replace(/[^\d.-]/g, '')) || 0;
            totalAmount += amount;
        });

        // Debugging statement
        console.log('Total Amount:', totalAmount);

        // Append the cloned table to the print preview
        _el.append('<div class="table-print-preview">' + p.prop('outerHTML') + '</div>');

        // Adding spacing
        _el.append('<div style="margin-top: 20px;"></div>');

        // Display total amount row
        _el.append('<div class="table-print-preview">' +
            '<table class="table table-bordered table-stripped">' +
            '<tr>' +
            '<td colspan="4" class="text-right"><strong>Total Amount :</strong></td>' +
            '<td class="text-center"><strong>₱ ' + totalAmount.toFixed(2) + '</strong></td>' +
            '<td colspan="2"></td>' +
            '</tr>' +
            '</table>' +
            '</div>');

            // Adding spacing
            _el.append('<div style="margin-top: 80px;"></div>');

        _el.append('<style>' +
            '@media print {' +
            '.signature-line {' +
            'border-top: 2px solid #000;' +
            'margin-top: 10px;' +
            '}' +
            '}' +
            '</style>');

        _el.append('<div class="signature-lines">' +
            '</div>' +
            '</div>' +
            '<div style="width: 40%; float: right;">' +
            '<p class="text-center"><strong><?php echo strtoupper($_settings->info('owner_name')); ?></strong></p>' +
            '<div class="signature-line">' +
            '<p class="text-center">Signature over Printed Name</p>' +
            '<p class="text-center">( Manager )</p>' +
            '</div>' +
            '</div>' +
            '<div style="clear: both;"></div>' +
            '</div>');

        var nw = window.open("", "", "width=1200,height=900,left=250,location=no,titlebar=yes");
        nw.document.write(_el.html());
        nw.document.close();
        setTimeout(() => {
            nw.print();
            setTimeout(() => {
                nw.close();
                end_loader();
            }, 200);
        }, 500);
    });
});
</script>