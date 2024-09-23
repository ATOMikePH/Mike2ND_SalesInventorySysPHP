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
	<h3><i class="fas fa-sitemap icon"></i> Product Category Management</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-striped">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="50%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Date Created</th>						
						<th class="text-center">Category Name</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `categories` WHERE status = 1");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>							
							<td class="text-center"><?php echo $row['datec'] ?></td>		
							<td class="text-center">
    <!-- <a href="javascript:void(0);" class="category_link" data-category="<?php echo $row['name']; ?>"> -->
        <?php echo $row['name']; ?>
    </a>
</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item archive_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-warning"></span> Archive</a>
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

 <!-- Modal -->
 <div class="modal" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
				<h4 class="modal-title" id="itemModalLabel">Filtered for <span id="chosenCategory"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>


	<script>
        $(document).ready(function(){
            // Function to handle click event on category links
			$('.category_link').click(function(){
        var category = $(this).data('category');
        // Update the chosen category in the modal title
        $('#chosenCategory').text(category);
        fetchItemsByCategory(category);
    });

            // Function to fetch items by category using AJAX
            function fetchItemsByCategory(category) {
                $.ajax({
                    url: 'maintenance/fetch_items.php',
                    method: 'POST',
                    data: { category: category },
                    dataType: 'html',
                    success: function(response) {
                        $('#itemModal .modal-body').html(response);
                        $('#itemModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        // Handle errors if needed
                    }
                });
            }

            // Initialize DataTables
            $('.table').dataTable();

            // Other event handlers
            $('.archive_data').click(function(){
                _conf("Are you sure to archive this Category?","archive_category",[$(this).attr('data-id')])
            });
            $('#create_new').click(function(){
                uni_modal("<i class='fa fa-plus'></i> Add New Category","maintenance/manage_category.php","mid-large")
            });
            $('.edit_data').click(function(){
                uni_modal("<i class='fa fa-edit'></i> Edit Category Detials","maintenance/manage_category.php?id="+$(this).attr('data-id'),"mid-large")
            });
        });

			function archive_category($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=archive_category",
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