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
    <h3><i class="fas fa-undo icon"></i> Supplier Return Order Management</h3>
        <div class="card-tools">
			<a href="<?php echo base_url ?>admin/?page=supp_return/manage_return" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> New Return Order</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Return Date</th>
                            <th class="text-center">Return Code</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Remarks</th>
                            <th class="text-center">No. of Items</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT r.*, s.name as supplier FROM `supp_returnlist` r inner join supplier_list s on r.supplier_id = s.id order by r.`date_created` desc");
                        while($row = $qry->fetch_assoc()):
                            $row['items'] = count(explode(',',$row['stock_ids']));
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-center"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td class="text-center">
    <a href="<?php echo base_url.'admin?page=supp_return/view_return&id='.$row['id'] ?>">
        <?php echo $row['return_code'] ?>
    </a>
</td>
                                <td class="text-center"><?php echo $row['supplier'] ?></td>
                                <td class="text-center"> 
    <?php
    $remarks = $row['remarks'];
    $wordLimit = 20;

    // Check if remarks exist
    if (!empty($remarks)) {
        // Check if the number of words in remarks is greater than the limit
        $words = explode(' ', $remarks);
        if (count($words) > $wordLimit) {
            $limitedRemarks = implode(' ', array_slice($words, 0, $wordLimit));
            echo $limitedRemarks . '...';
        } else {
            echo $remarks;
        }
    } else {
        echo 'No details given.';
    }
    ?>
</td>
                                <td class="text-center"><?php echo number_format($row['items']) ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            Action
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="<?php echo base_url.'admin?page=supp_return/view_return&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="<?php echo base_url.'admin?page=supp_return/manage_return&id='.$row['id'] ?>" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Supplier Return Record permanently?","delete_return",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_return($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sreturn",
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