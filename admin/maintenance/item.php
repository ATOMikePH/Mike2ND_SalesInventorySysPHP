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
	<h3><i class="fas fa-tag icon"></i> Item Product Management</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category_filter">Sort by:</label>
                    <select class="form-control" id="category_filter">
                        <option value="">All Categories</option>
                        <?php 
                        // Fetch and populate categories from your database
                        $category_qry = $conn->query("SELECT id, name FROM categories where status = 1");
                        while ($category_row = $category_qry->fetch_assoc()) {
                            echo '<option value="' . $category_row['name'] . '">' . $category_row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        <div class="container-fluid">
			<table class="table table-bordered table-striped">
			<colgroup>
					<col width="10%">
					<col width="12%">
					<col width="30%">
					<col width="10%">
                    <col width="15%">
                    <col width="5%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">Image</th>
                        <th class="text-center">SKU</th>
						<th class="text-center">Full Item Name</th>
						<th class="text-center">Categories</th>
                        <th class="text-center">Unit Size</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
                <tbody>
                    <?php 
                    // Query: Get item details with category information
                    $qry = $conn->query("SELECT i.*, c.name as category, b.name as brand 
                                         FROM `item_list` i
                                         INNER JOIN categories c ON i.category_id = c.id
                                         INNER JOIN brands b ON i.brand_id = b.id
                                         WHERE i.status = 1
                                         ");

                    // Fetch result
                    if ($qry->num_rows > 0) {
                        while ($row = $qry->fetch_assoc()) {
                            $sku = $row['sku'];
                            $cID = $row['category_id'];
                            $name = $row['name'];
                            $brand = $row['brand'];
                            $cost = isset($row['cost']) ? $row['cost'] : '';
                            $category = isset($row['category']) ? $row['category'] : '';
                            $status = $row['status'];
                            $image = isset($row['image']) ? $row['image'] : ''; // Added line for item image

                            // Query: Get unit size from price_unit_order entity
                            $unit_qry = $conn->query("SELECT u.name 
                                                      FROM price_unit_order pu
                                                      INNER JOIN units u ON pu.unit_id = u.id
                                                      WHERE pu.item_id = '{$row['id']}' AND pu.status = 1");

                            // Fetch unit size
                            if ($unit_qry->num_rows > 0) {
                                $unit_sizes = array();
                                while ($unit_row = $unit_qry->fetch_assoc()) {
                                    $unit_sizes[] = $unit_row['name'];
                                }
                                $unit_size = implode(", ", $unit_sizes);
                            } else {
                                $unit_size = '';
                            }

                            // Query: Get supplier information for the item
                            $supplier_qry = $conn->query("SELECT s.name as supplier_name, sp.supplier_price
                                                          FROM supplier_product sp
                                                          INNER JOIN supplier_list s ON sp.supplier_id = s.id
                                                          WHERE sp.product_id = '{$row['id']}'");

                            // Fetch supplier information
                            if ($supplier_qry->num_rows > 0) {
                                // Initialize arrays to store supplier information
                                $supplier_names = array();
                                $supplier_prices = array();

                                // Fetch all supplier information
                                while ($supplier_row = $supplier_qry->fetch_assoc()) {
                                    $supplier_names[] = $supplier_row['supplier_name'];
                                    $supplier_prices[] = $supplier_row['supplier_price'];
                                }
                            } else {
                                // No supplier information available
                                $supplier_names = array();
                                $supplier_prices = array();
                            }
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($image)) : ?>
                                        <img src="<?php echo $image; ?>" alt="Item Image" class="img-thumbnail mb-3 mx-auto d-block" draggable="false">
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo isset($sku) ? $sku : '' ?></td>
                                <td class="text-center"><?php echo ($brand != 'No Brand') ? $brand . ' ' : ''; ?><?php echo $name ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($category); ?></td>
                                <td class="text-center">
                                    <?php 
                                    // Explode unit_size string to array
                                    $unit_sizes = explode(",", $unit_size);
                                    foreach ($unit_sizes as $unit) {
                                        echo "<span class='badge badge-primary rounded-pill'>$unit</span> ";
                                    }
                                    ?>
                                </td>
                                <td align="center">
                                    <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        Manage
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item archive_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-warning"></span> Archive</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } // end while
                    } // end if ?>
                </tbody>
			</table>
		</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
        // Fetch category names for debugging
        var categoryNames = [];
        $('tbody tr').each(function(){
            var category = $(this).find('td:eq(3)').text().trim();
            categoryNames.push(category);
        });
        console.log(categoryNames);

        $('#category_filter').change(function(){
            var category_name = $(this).val();
            console.log("Selected category:", category_name); // Debug code to output selected category name
            filterItems(category_name);
        });

        function filterItems(category_name) {
            $('tbody tr').hide(); // Hide all rows
            if (category_name === '') {
                $('tbody tr').show(); // Show all rows if no category selected
            } else {
                $('tbody tr').each(function(){
                    var category = $(this).find('td:eq(3)').text().trim(); // Get category name of each row
                    if (category === category_name) {
                        $(this).show(); // Show row if it matches the selected category
                    }
                });
            }
        }

		$('.archive_data').click(function(){
                _conf("Are you sure to archive this Item?","archive_item",[$(this).attr('data-id')])
            });
		$('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Item","maintenance/manage_item.php","mid-large")
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Item Details","maintenance/manage_item.php?id="+$(this).attr('data-id'),"mid-large")
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function archive_item($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=archive_item",
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