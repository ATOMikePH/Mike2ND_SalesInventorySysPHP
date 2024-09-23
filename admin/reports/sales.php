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
// Initialize $qry to avoid the "Undefined variable" warning
$qry = null;

// Check if the form is submitted
if (isset($_POST['filter_dates'])) {
    // Modify your SQL query to include a WHERE clause for the date range
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $sales_query = "SELECT * FROM `sales_list` WHERE status = 1 AND transaction_datetime BETWEEN '$start_date' AND '$end_date' ORDER BY transaction_datetime DESC";
    $qry = $conn->query($sales_query);

    // Update default dates if form is submitted
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
} else {
    // Default query without date filter
    $sales_query = "SELECT * FROM `sales_list` WHERE status = 1 ORDER BY transaction_datetime DESC";
    $qry = $conn->query($sales_query);

    // Set default values if session variables are not set
    if (!isset($_SESSION['start_date'])) {
        $_SESSION['start_date'] = date("Y-m-d\TH:i", strtotime(date("Y-m-d") . " 00:00"));
    }
    if (!isset($_SESSION['end_date'])) {
        $_SESSION['end_date'] = date("Y-m-d\TH:i", strtotime(date("Y-m-d", strtotime("+1 day")) . " 00:00"));
    }
}
// Check if there are results to display
$hasResults = $qry && $qry->num_rows > 0;
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave icon"></i> Sales Report</h3>
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
        <button id="print_sales" class="btn btn-flat btn-success"><span class="fas fa-print"></span> Print</button>
       
        <hr>
    </div>
    <div class="card-body" id="print_sales">
        <div class="container-fluid">
            <?php if (!$hasResults) : ?>
                <!-- Show message when no results found -->
                <p class="no-data-message show-message" id="noDataMessage">No Data Found. Please Try Again.</p>
            <?php else : ?>
                <!-- Show table only if there are results -->
                
    
                <table class="table table-bordered table-stripped">
                    <colgroup>
                    <col width="5%">
                    <col width="13%">
                    <col width="10%">
                    <col width="15%">
                    <col width="20%">
                    <col width="7%">
                    <col width="6%">
                    <col width="8%">
                    <col width="8%">
                    <col width="8%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Transaction Date</th>
                            <th class="text-center" style="display: none;">Category</th>
                            <th class="text-center">Sale Code</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Type</th>
                            <th class="text-center">Net Sales</th>
                            <th class="text-center">Gross Sales</th>
                            <th class="text-center">VAT Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_sales = 0;
                        $i = 1;
                        while ($row = $qry->fetch_assoc()) :
                            // Fetch items from stock_ids and explode
                            // Balikan kita maya
                            $tax = $row['tax'];
                            $items = explode(',', $row['stock_ids']);
                            foreach ($items as $item_id) {
                                $item_info = $conn->query(
                                    "SELECT 
                                    item_list.name, 
                                    brands.name AS brand,
                                    categories.name AS category,  
                                    stock_list.total, 
                                    stock_list.unit, 
                                    stock_list.quantity  
                                    FROM `stock_list` 
                                    INNER JOIN `item_list` ON stock_list.item_id = item_list.id 
                                    INNER JOIN `brands` ON item_list.brand_id = brands.id 
                                    INNER JOIN `categories` ON item_list.category_id = categories.id 
                                    WHERE stock_list.id = '$item_id'")->fetch_assoc();
                                $total_sales += $item_info['total'];
                                $net_sales = $item_info['total'] - ($item_info['total'] * $row['discount_perc'] / 100);
                                $vat_sales = $item_info['total'] + (($item_info['total'] * $row['tax_perc'] / 100));
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class="text-center"><?php echo date("Y-m-d H:i", strtotime($row['transaction_datetime'])) ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url . 'admin?page=sales/view_sale&id=' . $row['id'] ?>">
                                            <?php echo $row['sales_code'] ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo $row['client'] ?></td>
                                    <td class="text-center"><?php echo $item_info['brand']; ?> <?php echo $item_info['name']; ?></td>
                                    <td class="text-center" style="display: none;"><?php echo $item_info['category']; ?></td>
                                    <td class="text-center"><?php echo $item_info['quantity']; ?></td>
                                    <td class="text-center"><?php echo $item_info['unit'] ?></td>
                                    <td class="text-right"><?php echo '₱ ' . number_format($net_sales, 2) ?></td> 
                                    <td class="text-right"><?php echo '₱ ' . number_format($item_info['total'], 2) ?></td>
                                    <td class="text-right"><?php echo '₱ ' . number_format($vat_sales, 2) ?></td>
                                </tr>
                        <?php
                            }
                        endwhile;
                        ?>

                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<script>
    $(document).ready(function () {
        $('#export_excel').click(function () {
            var wb = XLSX.utils.table_to_book(document.getElementById('print_sales').getElementsByTagName('table')[0]);
            XLSX.writeFile(wb, 'sales_report.xlsx');
        });
    });
</script>

<script>
    
$(document).ready(function() {
    $('table').on('draw.dt', function () {
        var totalAmount = 0;
        var totalVat = 0;
        var totalNet = 0;

        $('table tbody tr').each(function () {
            var amountText = $(this).find('td:eq(9)').text().trim(); // Adjust the column index if needed
            var amount = parseFloat(amountText.replace(/[^\d.-]/g, '')) || 0;
            totalAmount += amount;

            var vatText = $(this).find('td:eq(10)').text().trim(); // Adjust the column index if needed
            var vat = parseFloat(vatText.replace(/[^\d.-]/g, '')) || 0;
            totalVat += vat;

            var netText = $(this).find('td:eq(8)').text().trim(); // Adjust the column index if needed
            var net = parseFloat(netText.replace(/[^\d.-]/g, '')) || 0;
            totalNet += net;
        });

        // Check if tfoot exists, if not, append it
        if ($('table tfoot').length === 0) {
            $('table').append('<tfoot>' +
                '<tr>' +
                '<td colspan="9" class="text-right"><strong>Total Net Sales:</strong></td>' +
                '<td class="text-center"><strong>₱ ' + totalNet.toFixed(2) + '</strong></td>' +
                '</tr>' +
                '<tr>' +
                '<td colspan="9" class="text-right"><strong>Total VAT Sales:</strong></td>' +
                '<td class="text-center"><strong>₱ ' + totalVat.toFixed(2) + '</strong></td>' +
                '</tr>' +
                '<tr>' +
                '<td colspan="9" class="text-right"><strong>Total Gross Sales:</strong></td>' +
                '<td class="text-center"><strong>₱ ' + totalAmount.toFixed(2) + '</strong></td>' +
                '</tr>' +
                '</tfoot>');
        } else {
            // If tfoot already exists, update its content
            $('table tfoot tr:nth-child(1) td').html('<strong>Total Net Sales:</strong>');
            $('table tfoot tr:nth-child(1) td:nth-child(2)').html('<strong>₱ ' + totalNet.toFixed(2) + '</strong>');

            $('table tfoot tr:nth-child(2) td').html('<strong>Total VAT Sales:</strong>');
            $('table tfoot tr:nth-child(2) td:nth-child(2)').html('<strong>₱ ' + totalVat.toFixed(2) + '</strong>');

            $('table tfoot tr:nth-child(3) td').html('<strong>Total Gross Sales:</strong>');
            $('table tfoot tr:nth-child(3) td:nth-child(2)').html('<strong>₱ ' + totalAmount.toFixed(2) + '</strong>');
        }
    });
});
</script>
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
    // Initialize DataTable
    var table = $('.table').DataTable();

    // Function to calculate total sales dynamically
    function calculateTotalSales() {
        var totals = [0, 0, 0]; // Index 0: Total Net Sales, Index 1: Total Gross Sales, Index 2: Total VAT Sales

        // Iterate over visible rows in the DataTable
        table.rows({ search: 'applied' }).every(function () {
            var data = this.data();
            var netSalesText = data[8].trim(); // Assuming the Net Sales is in the 8th column (index 7)
            var grossSalesText = data[9].trim(); // Assuming the Gross Sales is in the 9th column (index 8)
            var vatSalesText = data[10].trim(); // Assuming the VAT Sales is in the 10th column (index 9)

            var netSales = parseFloat(netSalesText.replace(/[^\d.-]/g, '')) || 0;
            var grossSales = parseFloat(grossSalesText.replace(/[^\d.-]/g, '')) || 0;
            var vatSales = parseFloat(vatSalesText.replace(/[^\d.-]/g, '')) || 0;

            totals[0] += netSales;
            totals[1] += grossSales;
            totals[2] += vatSales;
        });

        return totals;
    }

    // Update total sales when the search is performed or the table is filtered
    table.on('draw', function () {
        var totals = calculateTotalSales();
        $('#total-net-sales').text('₱ ' + totals[0].toFixed(2));
        $('#total-gross-sales').text('₱ ' + totals[1].toFixed(2));
        $('#total-vat-sales').text('₱ ' + totals[2].toFixed(2));
    });

    $('#print_sales').click(function () {
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Sales Order - Print View");

        // Clone the table and remove any unnecessary classes
        var p = $('#print_sales table').clone();
        p.removeClass('dataTable');

         // Remove sorting icons and additional classes
         p.find('th, td').removeClass('sorting sorting_asc sorting_desc');

// Remove the "Action" column header and corresponding cells
p.find('th:contains("Action")').remove();
p.find('td button, td div.dropdown-menu').remove();
p.find('tfoot').remove();

p.find('td.text-center a').each(function () {
        var salesCode = $(this).text().trim();
        $(this).replaceWith('<span style="color: black;">' + salesCode + '</span>');
    });

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
            '<h4 class="text-center">List of Sales Transaction</h4>' +
            '</div><hr/>');


        // Append the cloned table to the print preview
        _el.append('<div class="table-print-preview">' + p.prop('outerHTML') + '</div>');

        // Adding spacing
        _el.append('<div style="margin-top: 20px;"></div>');

               // Calculate total sales dynamically
               var totals = calculateTotalSales();

// Display total sales row
_el.append('<div class="table-print-preview">' +
    '<table class="table table-bordered table-stripped">' +
    '<tr>' +
    '<td colspan="9" class="text-right"><strong>Total Net Sales:</strong></td>' +
    '<td class="text-center"><strong id="total-net-sales">₱ ' + totals[0].toFixed(2) + '</strong></td>' +
    '</tr>' +
    '<tr>' +
    '<td colspan="9" class="text-right"><strong>Total Gross Sales:</strong></td>' +
    '<td class="text-center"><strong id="total-gross-sales">₱ ' + totals[1].toFixed(2) + '</strong></td>' +
    '</tr>' +
    '<tr>' +
    '<td colspan="9" class="text-right"><strong>Total VAT Sales:</strong></td>' +
    '<td class="text-center"><strong id="total-vat-sales">₱ ' + totals[2].toFixed(2) + '</strong></td>' +
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