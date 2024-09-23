<!-- CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


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
    
<div class="card card-outline card-primary">
	<div class="card-header">
    <h3><i class="fas fa-money-bill-wave icon"></i> Sales Order Management</h3>
        <div class="card-tools">
			<a href="<?php echo base_url ?>admin/?page=sales/manage_sale" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> New Sales Order</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="15%">
                        <col width="25%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Transaction Date</th>
                            <th class="text-center">Sale Code</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">No. of Items</th>
                            <th class="text-center">Total Amount</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM `sales_list` WHERE status = 1 order by `date_created` desc");
                        while($row = $qry->fetch_assoc()):
                            $row['items'] = count(explode(',',$row['stock_ids']));
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-center"><?php echo date("Y-m-d H:i",strtotime($row['transaction_datetime'])) ?></td>
                                <td class="text-center">
    <a href="<?php echo base_url.'admin?page=sales/view_sale&id='.$row['id'] ?>">
        <?php echo $row['sales_code'] ?>
    </a>
</td>

                                <td class="text-center"><?php echo $row['client'] ?></td>
                                <td class="text-center"><?php echo number_format($row['items']) ?></td>
                                <td class="text-right"><?php echo '₱ ' . ($row['amount']) ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            Manage
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="<?php echo base_url.'admin?page=sales/view_sale&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item archive_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Archive</a>
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
<!-- JavaScript -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script>
	$(document).ready(function(){
		$('.archive_data').click(function(){
			_conf("Are you sure to archive this Sales Record ?","archive_sale",[$(this).attr('data-id')])
		})
        $('.table td,.table th').addClass('py-1 px-2 align-middle');

// Initialize DataTables with buttons
$('.table').DataTable({
    dom: 'Bfrtip',
    buttons: [
        'copyHtml5',
        'excelHtml5',
        'pdfHtml5',
        'print'
    ]
});
});
	function archive_sale($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=archive_sale",
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