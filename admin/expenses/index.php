<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<?php
function fetchVendorData() {
    global $conn;
    $query = "SELECT * FROM vendors"; // Adjust the query based on your database schema
    $result = mysqli_query($conn, $query);

    return $result;
}

?>

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

    
</style>

 <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-5">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3><i class="fas fa-list-alt icon"></i> Vendor List</h3>
                        <div class="card-tools">
                            <a href="javascript:void(0)" id="new_vendor" class="btn btn-flat btn-primary"><span
                                    class="fas fa-plus"></span> New Vendor</a>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <table class="table table-bordered" id="vendor_table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Vendor Name</th>
                                    <th class="text-center">Settings</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php
    $vendorData = fetchVendorData();

    if ($vendorData && mysqli_num_rows($vendorData) > 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($vendorData)):
    ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $row['vendor_name']; ?></td>
               
                <td class="text-center">
                    
                    <a href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" class="btn btn-sm btn-info view_vendor"><i class="fas fa-info-circle"></i></a>
                                        <a href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" class="btn btn-sm btn-warning edit_vendor"><i class="fas fa-edit"></i></a>
                                        <a href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" class="btn btn-sm btn-danger delete_vendor"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
    <?php
        endwhile;
    } else {
        echo '<tr><td class="text-center" colspan="3">No vendor data available.</td></tr>';
    }
    ?>
</tbody>
                        </table>
                    </div>
                </div>
            </div>

 <div class="col-md-7">
                <div class="card card-outline card-primary">
	<div class="card-header">
    <h3><i class="fas fa-money-check-alt icon"></i> Expenses Management</h3>
        <div class="card-tools">
        <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> New Expenses</a>
		<button id="print_expenses" class="btn btn-flat btn-success"><span class="fas fa-print"></span> Print</button>
    </div>
	</div>
	<div class="card-body" id="print_expenses">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">Created At</th>
                            <th class="text-center">Expense Type</th>
                            <th class="text-center">Transaction Name</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">Amount (PHP)</th>
                            <th class="text-center">Due On</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$i = 1;
$totalAmount = 0;
$qry = $conn->query("SELECT * FROM `expenses` order by `date_created` desc");
while($row = $qry->fetch_assoc()):
    $totalAmount += $row['amount']; 
?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-center" ><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td class="text-center"><?php echo $row['type'] ?></td>
                                <td class="text-center"><?php echo $row['name'] ?></td>
                                <td class="text-center"><?php echo $row['remarks'] ?></td>
                                <td class="text-center"><?php echo number_format($row['amount'],2) ?></td>
                                <td class="text-center"><?php echo date_format(new DateTime($row['transaction_date']), 'Y-m-d') ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            Action
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
		</div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function () {
        $('.delete_vendor').click(function () {
            var vendorId = $(this).attr('data-id');
            _conf("Are you sure to delete this vendor permanently?", "delete_vendor", [vendorId]);
        });

        $('#new_vendor').click(function () {
            uni_modal("<i class='fa fa-plus'></i> New Vendor Data", "expenses/manage_vendor.php", "mid-large");
        });

        $('.edit_vendor').click(function () {
            var vendorId = $(this).attr('data-id');
            uni_modal("<i class='fa fa-edit'></i> Edit Vendor Details", "expenses/manage_vendor.php?id=" + vendorId, "mid-large");
        });

        $('.view_vendor').click(function () {
            var vendorId = $(this).attr('data-id');
            uni_modal("<i class='fa fa-building'></i> Vendor Details", "expenses/view_vendor.php?id=" + vendorId, "");
        });

        $('.table td, .table th').addClass('py-1 px-2 align-middle');
        $('#vendor_table').DataTable({
            searching: true,
            paging: true,

        });
        

});


    function delete_vendor(vendorId) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_vendor",
            method: "POST",
            data: {
                id: vendorId
            },
            dataType: "json",
            error: function (err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>

<script>
	$(document).ready(function(){
// Print button click event
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this expense data permanently?","delete_expense",[$(this).attr('data-id')])
		})
		$('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i> New Expense Data","expenses/manage_expense.php","mid-large")
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Expense Details","expenses/manage_expense.php?id="+$(this).attr('data-id'),"mid-large")
		})
        $('.view_data').click(function(){
			uni_modal("<i class='fa fa-truck-loading'></i> Expense Details","expenses/view_expense.php?id="+$(this).attr('data-id'),"")
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_expense($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_expense",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>

<script>
$(document).ready(function () {
    $('#print_expenses').click(function () {
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Expenses - Print View");

        // Clone the table and remove any unnecessary classes
        var p = $('#print_expenses table').clone();
        p.removeClass('dataTable');

         // Remove sorting icons and additional classes
         p.find('th, td').removeClass('sorting sorting_asc sorting_desc');

// Remove the "Action" column header and corresponding cells
p.find('th:contains("Action")').remove();
p.find('td button, td div.dropdown-menu').remove();

        // Calculate the total amount dynamically
        var totalAmount = 0;
        p.find('tbody tr').each(function () {
            var amountText = $(this).find('td:eq(5)').text().trim(); // Adjust the column index if needed
            var amount = parseFloat(amountText.replace(/[^\d.-]/g, '')) || 0;
            totalAmount += amount;
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
            '<h4 class="text-center">List of Expenses Transaction</h4>' +
            '</div><hr/>');

        // Append the cloned table to the print preview
       // Append the cloned table to the print preview
_el.append('<div class="table-print-preview">' + p.prop('outerHTML') + '</div>');

// Adding spacing
_el.append('<div style="margin-top: 20px;"></div>');

        // Display total amount row
        _el.append('<div class="table-print-preview">' +
            '<table class="table table-bordered table-stripped">' +
            '<tr>' +
            '<td colspan="5" class="text-right"><strong>Total Amount:</strong></td>' +
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
            '<p class="text-center"><strong>' + <?php echo json_encode(strtoupper($_settings->info('owner_name'))); ?> + '</strong></p>' +
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