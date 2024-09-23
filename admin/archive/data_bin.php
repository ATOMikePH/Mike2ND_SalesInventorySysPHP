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
        <h3><i class="fas fa-sitemap icon"></i> Data Bin</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-striped">
                <colgroup>
                    <col width="8%">
                    <col width="24%">
                    <col width="24%">
					<col width="24%">
                    <col width="7%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
						<th class="text-center">Date Archived</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Data Type</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            <tbody>
    <?php
    $category_query = $conn->query("SELECT * FROM categories WHERE status = 0");
    $item_query = $conn->query("SELECT * FROM item_list WHERE status = 0");
	$discount_query = $conn->query("SELECT * FROM discounts WHERE status = 0");
	$brand_query = $conn->query("SELECT * FROM brands WHERE status = 0");
	$supplier_query = $conn->query("SELECT * FROM supplier_list WHERE status = 0");
	$unit_query = $conn->query("SELECT * FROM units WHERE status = 0");
    $counter = 1;

    // Combine purchase orders and sales records into a single array
    $datas = array_merge($category_query->fetch_all(MYSQLI_ASSOC), $item_query->fetch_all(MYSQLI_ASSOC), $discount_query->fetch_all(MYSQLI_ASSOC), $brand_query->fetch_all(MYSQLI_ASSOC), $supplier_query->fetch_all(MYSQLI_ASSOC), $unit_query->fetch_all(MYSQLI_ASSOC));

    // Sort transactions by transaction datetime
	usort($datas, function($a, $b) {
		return strtotime($a['datetime']) - strtotime($b['datetime']);
	});
?>
<?php foreach ($datas as $data): ?>
<tr>
    <td align="center"><?php echo $counter++; ?></td>
    <td align="center"><?php echo $data['datetime']; ?></td>
    <td align="center"><?php echo $data['name'] ?? 'Unknown'; ?></td>
    <td align="center">
        <?php
        if (isset($data['type'])) {
            if ($data['type'] == 1) {
                echo 'Item Data'; 
            } elseif ($data['type'] == 2) {
                echo 'Category Data';
            } elseif ($data['type'] == 3) {
                echo 'Discount Data';
            } elseif ($data['type'] == 4) {
                echo 'Brand Data';
			} elseif ($data['type'] == 5) {
                echo 'Supplier Data';
			} elseif ($data['type'] == 6) {
                echo 'Unit Data';
            } else {
                echo 'Unknown';
            }
        } else {
            echo 'Unknown';
        }
        ?>
    </td>
    <td align="center">
        <div class="dropdown">
            <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                Action
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu" role="menu">
                <?php if (isset($data['type']) && $data['type'] == 2): // If it's a category ?>
                    <a class="dropdown-item delete_c" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_c" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-undo text-success"></span> Restore
                    </a>
                <?php elseif (isset($data['type']) && $data['type'] == 3): // If it's a discount ?>
                    <a class="dropdown-item delete_d" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_d" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-undo text-success"></span> Restore
                    </a>
                <?php elseif (isset($data['type']) && $data['type'] == 4): // If it's a brand ?>
                    <a class="dropdown-item delete_b" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_b" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-undo text-success"></span> Restore
                    </a>
				<?php elseif (isset($data['type']) && $data['type'] == 5): // If it's a supplier ?>
                    <a class="dropdown-item delete_s" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_s" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-undo text-success"></span> Restore
                    </a>
					<?php elseif (isset($data['type']) && $data['type'] == 6): // If it's a unit ?>
                    <a class="dropdown-item delete_u" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_u" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-undo text-success"></span> Restore
                    </a>
                <?php elseif (isset($data['type']) && $data['type'] == 1): // If it's an item ?>
                    <a class="dropdown-item delete_i" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
                        <span class="fa fa-trash text-danger"></span> Delete
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item restore_i" href="javascript:void(0)" data-id="<?php echo $data['id'] ?>">
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
		$('.delete_b').click(function(){
			_conf("Are you sure, this will permanently delete brand ?","delete_brand",[$(this).attr('data-id')])
		})
        $('.restore_b').click(function(){
			_conf("Are you sure to restore this brand data ?","restore_brand",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_brand($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_brand",
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
    function restore_brand($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_brand",
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

<!-- discount -->
<script>
	$(document).ready(function(){
		$('.delete_d').click(function(){
			_conf("Are you sure, this will permanently delete discount ?","delete_discount",[$(this).attr('data-id')])
		})
        $('.restore_d').click(function(){
			_conf("Are you sure to restore this discount data ?","restore_discount",[$(this).attr('data-id')])
		})
	})
	function delete_discount($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_discount",
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
    function restore_discount($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_discount",
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

<!-- category -->
<script>
	$(document).ready(function(){
		$('.delete_c').click(function(){
			_conf("Are you sure, this will permanently delete category ?","delete_category",[$(this).attr('data-id')])
		})
        $('.restore_c').click(function(){
			_conf("Are you sure to restore this category data ?","restore_category",[$(this).attr('data-id')])
		})
	})
	function delete_category($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_category",
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
    function restore_category($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_category",
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

<!-- supplier -->
<script>
	$(document).ready(function(){
		$('.delete_s').click(function(){
			_conf("Are you sure, this will permanently delete supplier ?","delete_supplier",[$(this).attr('data-id')])
		})
        $('.restore_s').click(function(){
			_conf("Are you sure to restore this supplier data ?","restore_supplier",[$(this).attr('data-id')])
		})
	})
	function delete_supplier($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_supplier",
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
    function restore_supplier($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_supplier",
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

<!-- unit -->
<script>
	$(document).ready(function(){
		$('.delete_u').click(function(){
			_conf("Are you sure, this will permanently delete unit ?","delete_unit",[$(this).attr('data-id')])
		})
        $('.restore_u').click(function(){
			_conf("Are you sure to restore this unit data ?","restore_unit",[$(this).attr('data-id')])
		})
	})
	function delete_unit($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_unit",
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
    function restore_unit($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_unit",
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

<!-- item -->
<script>
	$(document).ready(function(){
		$('.delete_i').click(function(){
			_conf("Are you sure, this will permanently delete item ?","delete_item",[$(this).attr('data-id')])
		})
        $('.restore_i').click(function(){
			_conf("Are you sure to restore this item data ?","restore_item",[$(this).attr('data-id')])
		})
	})
	function delete_item($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_item",
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
    function restore_item($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=restore_item",
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