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

    .modal-dialog {
        max-width: 800px;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3><i class="fas fa-sitemap icon"></i> Transaction Bin</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped">
                <colgroup>
                    <col width="8%">
                    <col width="17%">
                    <col width="24%">
                    <col width="24%">
                    <col width="20%">
                    <col width="7%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Transaction Date</th>
                        <th class="text-center">Transaction Number</th>
                        <th class="text-center">Transaction Type</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            <tbody>
    <?php
    $purchase_query = $conn->query("SELECT * FROM purchase_order_list WHERE sstatus = 0");
    $sales_query = $conn->query("SELECT * FROM sales_list WHERE status = 0");
    $counter = 1;

    // Combine purchase orders and sales records into a single array
    $transactions = array_merge($purchase_query->fetch_all(MYSQLI_ASSOC), $sales_query->fetch_all(MYSQLI_ASSOC));

    // Sort transactions by transaction datetime
    usort($transactions, function($a, $b) {
        return strtotime($a['transaction_datetime']) - strtotime($b['transaction_datetime']);
    });

    foreach ($transactions as $transaction):
    ?>
        <tr>
            <td align="center"><?php echo $counter++; ?></td>
            <td align="center"><?php echo $transaction['transaction_datetime']; ?></td>
            <td align="center"><?php echo $transaction['po_code'] ?? $transaction['sales_code']; ?></td>
            <td align="center">
                <?php
                if (isset($transaction['type'])) {
                    if ($transaction['type'] == 2) {
                        echo 'Supplies Purchased';
                    } elseif ($transaction['type'] == 1) {
                        echo 'Customer Sales';
                    } else {
                        echo 'Unknown';
                    }
                } else {
                    echo 'Unknown';
                }
                ?>
            </td>
            <td align="center"><?php echo $transaction['amount']; ?></td>
            <td align="center">
    <div class="dropdown">
        <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
            Action
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu" role="menu">
            <?php if (isset($transaction['po_code'])): // If it's a purchase order ?>
                <a class="dropdown-item" href="<?php echo base_url.'admin?page=purchase_order/view_po&id='.$transaction['id'] ?>" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-eye text-dark"></span> View
                </a>
                <div class="dropdown-divider"></div>
                <!-- Add other purchase order actions here -->
                <a class="dropdown-item delete_p" href="javascript:void(0)" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-trash text-danger"></span> Delete
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item restore_p" href="javascript:void(0)" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-undo text-success"></span> Restore
                </a>
            <?php elseif (isset($transaction['sales_code'])): // If it's a sales record ?>
                <a class="dropdown-item" href="<?php echo base_url.'admin?page=sales/view_sale&id='.$transaction['id'] ?>" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-eye text-dark"></span> View
                </a>
                <div class="dropdown-divider"></div>
                <!-- Add other sales record actions here -->
                <a class="dropdown-item delete_s" href="javascript:void(0)" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-trash text-danger"></span> Delete
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item restore_s" href="javascript:void(0)" data-id="<?php echo $transaction['id'] ?>">
                    <span class="fa fa-undo text-success"></span> Restore
                </a>
            <?php endif; ?>
        </div>
    </div>
</td>
</tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
	$(document).ready(function(){
		$('.delete_s').click(function(){
			_conf("Are you sure, this will permanently delete Sale Order ?","delete_sale",[$(this).attr('data-id')])
		})
        $('.restore_s').click(function(){
			_conf("Are you sure to restore this Sales Order ?","restore_sale",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_sale($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sale",
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
    function restore_sale($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_sale",
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
	$(document).ready(function(){
		$('.delete_p').click(function(){
			_conf("Are you sure, this will permanently delete Purchase Order ?","delete_po",[$(this).attr('data-id')])
		})
        $('.restore_p').click(function(){
			_conf("Are you sure to restore this Purchase Order ?","restore_po",[$(this).attr('data-id')])
		})
	})
	function delete_po($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_po",
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
    function restore_po($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_po",
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