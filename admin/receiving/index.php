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
    <h3><i class="fas fa-check icon"></i> Received Orders</h3>
        <!-- <div class="card-tools">
			<a href="<?php echo base_url ?>admin/?page=purchase_order/manage_po" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
        <table class="table table-bordered table-stripped">
                <colgroup>
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="5%">
                    <col width="5%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Purchased Date</th>
                        <th class="text-center">Received Date</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">From Purchase Order Tag</th>
                        <th class="text-center">Item Name</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">Unit Type</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
$i = 1;
$qry = $conn->query("SELECT * FROM `receiving_list` ORDER BY `date_created` DESC");
while($row = $qry->fetch_assoc()):
    $row['items'] = explode(',',$row['stock_ids']);
    foreach ($row['items'] as $item_id) {
        $item_info = $conn->query("SELECT stock_list.id, stock_list.quantity, stock_list.cogs, stock_list.unit, stock_list.price, item_list.name, brands.name AS brand FROM `stock_list` INNER JOIN `item_list` ON stock_list.item_id = item_list.id INNER JOIN `brands` ON item_list.brand_id = brands.id WHERE stock_list.id='{$item_id}' AND stock_list.type = 1")->fetch_assoc();

                // Set the variables before using them
                if($row['from_order'] == 1){
                    $purchaseOrderData = $conn->query("SELECT * FROM `purchase_order_list` where id='{$row['form_id']}' ")->fetch_assoc();
                    $transaction_datetime2 = $purchaseOrderData['transaction_datetime'];
                    $code = $purchaseOrderData['po_code'];
                } else {
                    $backOrderData = $conn->query("SELECT * FROM `back_order_list` where id='{$row['form_id']}' ")->fetch_assoc();
                    $transaction_datetime2 = $backOrderData['transaction_datetime'];
                    $code = $backOrderData['bo_code'];
                }
        
                if($row['from_order'] == 1){
                    $order_info = $conn->query("SELECT * FROM `purchase_order_list` where id='{$row['form_id']}'")->fetch_assoc();
                    $code = $order_info['po_code'];
                    $supplier_info = $conn->query("SELECT * FROM `supplier_list` where id='{$order_info['supplier_id']}'")->fetch_assoc();
                    $supplier_name = $supplier_info['name'];
                } else {
                    $code = $conn->query("SELECT bo_code from `back_order_list` where id='{$row['form_id']}' ")->fetch_assoc()['bo_code'];
                    $supplier_name = "Supplier N/A";
                }
                ?>
        <tr>
            <td class="text-center"><?php echo $i++; ?></td>
            <td class="text-center"><?php echo date("Y-m-d H:i", strtotime($transaction_datetime2)) ?></td>
            <td class="text-center"><?php echo date("Y-m-d H:i",strtotime($row['transaction_datetime'])) ?></td>
            <td class="text-center"><?php echo $supplier_name ?></td>
            <td class="text-center">
                <a href="<?php echo base_url.'admin?page=receiving/view_receiving&id='.$row['id'] ?>">
                    <?php echo $code ?>
                </a>
            </td>
            <td class="text-center"><?php echo $item_info['brand']; ?> <?php echo $item_info['name']; ?></td>
            <td class="text-center"><?php echo $item_info['quantity']; ?></td>
            <td class="text-center"><?php echo $item_info['unit']; ?></td>
            <td align="center">
                <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    Manage
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="<?php echo base_url.'admin?page=receiving/view_receiving&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url.'admin?page=receiving/manage_receiving&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                </div>
            </td>
        </tr>
<?php 
    }
endwhile; 
?>
                </tbody>
            </table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Received Orders permanently?","delete_receiving",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("Receiving Details","receiving/view_receiving.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_receiving($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_receiving",
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