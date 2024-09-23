<?php
// Assuming you have a database connection established
$units_query = $conn->query("SELECT * FROM units WHERE status = 1"); // Assuming "status" indicates active units
$units = []; // Initialize an empty array to store the units

// Check if there are any units fetched from the database
if ($units_query->num_rows > 0) {
    // Loop through the fetched units and store them in the $units array
    while ($row = $units_query->fetch_assoc()) {
        $units[] = $row;
    }
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


.table-container {
    overflow-x: auto;
    max-width: 1500px;
}

    .table {
    white-space: nowrap; /* Prevent text wrapping */
    border-collapse: collapse;
    width: 100%;
}

.table th,
.table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    font-size: 12px; /* Set font size for all cells */
}

.table th {
    background-color: #565656;
    font-weight: bolder; /* Make column labels bold */
    color: #fff;
}

/* Blue color for the header */
.table th:nth-child(2) {
    background-color: #007bff;
    color: #fff;
    padding: 15px;
    font-weight: bold;
    border-radius: 10px;
}

/* Black and white colors for the second column in the table body */
.table td:nth-child(2) {
    background-color: #000;
    color: #fff;
    padding: 15px;
    font-weight: bold;
    border-radius: 10px;
}

/* Adjust the width for the second column header */
.table th.first-column-header {
    width: 300px;
}

/* Adjust the width for the first column */
.table td.first-column {
    width: 300px;
}

/* Set width for the second column */
.table th:nth-child(2),
.table td:nth-child(2) {
    width: 100px;
}

/* Set width for all columns except the first one */
.table th:not(:nth-child(2)),
.table td:not(:nth-child(2)) {
    width: 150px;
}
.enlarge-image img {
        transition: transform 0.3s ease-in-out;
    }

.enlarge-image img:hover {
        transform: scale(1.5); /* Enlarge image by 50% */
    }

</style>




<div class="card card-outline card-primary">
    <div class="card-header">
        <h3><i class="fas fa-home icon"></i> Integrated Inventory Management Hub</h3>
        <div class="card-tools">
        <a href="javascript:void(0)" id="create_item" class="btn btn-flat btn-primary"><span class="fas fa-star"></span> Add New Product</a>
<a href="javascript:void(0)" id="create_supplier" class="btn btn-flat btn-secondary"><span class="fas fa-gem"></span> Add New Supplier</a>
<a href="javascript:void(0)" id="create_unit" class="btn btn-flat btn-primary"><span class="fas fa-cube"></span> Add New Unit Size</a>
<a href="javascript:void(0)" id="create_brand" class="btn btn-flat btn-secondary"><span class="fas fa-fire"></span> Add New Brand</a>
<a href="javascript:void(0)" id="create_category" class="btn btn-flat btn-primary"><span class="fas fa-tag"></span> Add New Category</a>
<a href="javascript:void(0)" id="create_discount" class="btn btn-flat btn-secondary"><span class="fas fa-percent"></span> Add New Discount</a>
        </div>
    </div>
    <div class="card-body">
    <div class="table-container">
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
    <table class="table table-hovered table-striped">
    <colgroup>
    <col width="5%">
        <col width="15%">
        <?php
        $supplier_query = $conn->query("SELECT * FROM `supplier_list` WHERE status = 1");
        while ($supplier = $supplier_query->fetch_assoc()) {
            echo '<col width="auto">';
        }
        ?>
        <col width="5%">
    </colgroup>
                <thead>
                    <tr>
                    <th>Product Image</th>
                        <th>Product List</th>
                        <th class="text-center" style="display: none;">Category</th>
                        <?php
                        $supplier_query = $conn->query("SELECT * FROM `supplier_list` WHERE status = 1");
                        while ($supplier = $supplier_query->fetch_assoc()) {
                            echo '<th class="text-center" data-supplier-id="' . $supplier['id'] . '">' . $supplier['name'] . '</th>';
                        }
                        ?>
                       
                        <th>Active Unit Supplier Price</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Inside the while loop where you output the table rows -->
                    <?php
                    $item_query = $conn->query("SELECT i.*, c.name as category, b.name as brand  
                    FROM `item_list` i 
                    INNER JOIN categories c ON i.category_id = c.id 
                    INNER JOIN brands b ON i.brand_id = b.id 
                    WHERE i.status = 1
                   ");

                    while ($row = $item_query->fetch_assoc()):

                        $brand = $row['brand'];
                        $category = isset($row['category']) ? $row['category'] : '';
                    ?>

                        <tr>
                        <td class="text-center enlarge-image">
                                <?php if (!empty($row['image'])) : ?>
                                    <a href="#" class="show-image show-image-details" data-toggle="modal" data-target="#imageModal" data-src="<?php echo $row['image']; ?>">
                                        <img src="<?php echo $row['image']; ?>" class="img-thumbnail" style="width: 70px; height: auto;">
                                    </a>
                                    <?php else: ?>
                                    <img src="../uploads/item/noavail.png" alt="No Image" class="img-thumbnail" style="width: 50px; height: auto;">
                                <?php endif; ?>
                            </td>
                        <td class="product-row first-column" data-id="<?php echo $row['id']; ?>">
            <span class="product-name"><?php echo ($brand != 'No Brand') ? $brand . ' ' : ''; ?> <?php echo $row['name']; ?></span>
            <div class="dropdown-menu" role="menu">
                <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>"><span class="fa fa-eye text-dark"></span> View Item Info</a>
                <div class="dropdown-divider">
                </div>
                <a class="dropdown-item edit_unitp" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>"><span class="fa fa-edit text-primary"></span> Manage Unit Price & Reorder</a>
                <div class="dropdown-divider">
                </div>
                <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>"><span class="fa fa-edit text-primary"></span> Edit Item Info</a>
                <div class="dropdown-divider">
                </div>
                <a class="dropdown-item archive_data" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>"><span class="fa fa-trash text-warning"></span> Archive</a>
            </div>
        </td>
        <td class="text-center" style="display: none;"><?php echo htmlspecialchars($category); ?></td>
                            <?php
                            $supplier_query = $conn->query("SELECT * FROM `supplier_list` WHERE status = 1");
                            while ($supplier = $supplier_query->fetch_assoc()) {
                                $product_query = $conn->query("SELECT * FROM `supplier_product` WHERE supplier_id = {$supplier['id']} AND product_id = {$row['id']}");
                                $product = $product_query->fetch_assoc();
                                ?>
<td class="text-center">
    <!-- Button to open the supplier price modal -->
    <button type="button" class="btn btn-link gear-icon-btn" onclick="openSupplierPriceModal(<?php echo $supplier['id']; ?>, <?php echo $row['id']; ?>)">
        <i class="fas fa-cog"></i>
    </button>
</td>
                            <?php } ?>
                            
                            <td class='text-center'>
                <?php
                $totalActiveQuery = $conn->query("SELECT COUNT(*) as total_active FROM `supplier_product` WHERE product_id = {$row['id']} AND status = 1");
                $totalActive = $totalActiveQuery->fetch_assoc()['total_active'];
                echo $totalActive;
                ?>
            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .custom-modal-width {
        max-width: 1000px; /* Adjust the width as needed */
    }
</style>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
			<h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" class="img-fluid" id="imageModalImage" alt="Item Image">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="setSupplierPriceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered custom-modal-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Set Supplier Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <!-- Table layout for setting up the supplier price -->
    <table class="table" id="supplierPriceTable">
        <thead>
            <tr>
                <th class="text-center">Unit Size</th>
                <th class="text-center">Supplier Price</th>
                <th class="text-center">State</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <!-- Select input for choosing unit size -->
                    <select class="form-control" name="unit_id[]" id="unitSelect" required>
                        <option value="">Select Unit</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <!-- Input field for supplier price -->
                    <input type="number" class="form-control" name="supplierPrice[]" id="supplierPriceInput" step="0.01" required>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- Add Row button -->
    <div class="text-left mb-3">
        <button type="button" class="btn btn-success btn-sm" onclick="addRow()">Add Row</button>
    </div>
    <!-- Save button -->
    <div class="text-right">
        <button type="button" class="btn btn-primary" onclick="saveSupplierPrice()">Save</button>
    </div>
</div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
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
                    var category = $(this).find('td:nth-child(3)').text().trim(); // Get category name of each row
                    if (category === category_name) {
                        $(this).show(); // Show row if it matches the selected category
                    }
                });
            }
        }
    });
</script>

<script>

function addRow() {
    var newRowId = Date.now(); // Unique ID for each row
    var selectedUnitIds = []; // Array to store selected unit IDs

    // Collect all selected unit IDs from existing rows
    $('#supplierPriceTable tbody tr').each(function(index, row) {
        var unitId = $(row).find('select[name="unit_id[]"]').val();
        selectedUnitIds.push(unitId);
    });

    var newRow = `
        <tr id="row-${newRowId}">
            <td class="text-center">
                <select class="form-control" name="unit_id[]" required>
                    <option value="">Select Unit</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="text-center">
                <input type="number" class="form-control" name="supplierPrice[]" step="0.01" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm text-center" onclick="removeRow(${newRowId})">Remove this row?</button>
            </td>
        </tr>
    `;
    
    // Get the selected unit ID of the new row
    var selectedUnitId = $(newRow).find('select[name="unit_id[]"]').val();

    // Check if the selected unit ID already exists in the table
    if (selectedUnitIds.includes(selectedUnitId)) {
        // Show a warning message for duplicate unit size
        alert('Unit size cannot be duplicated!');
        return; // Exit the function without adding the row
    }

    // Append the new row to the table body
    $('#supplierPriceTable tbody').append(newRow);
}

function saveSupplierPrice() {
    var rows = $('#supplierPriceTable tbody tr');
    var data = [];

    rows.each(function(index, row) {
        var unitId = $(row).find('select[name="unit_id[]"]').val();
        var supplierPrice = $(row).find('input[name="supplierPrice[]"]').val();

        data.push({
            unitId: unitId,
            supplierPrice: supplierPrice
        });
    });

    var productId = $('#setSupplierPriceModal').data('productId');
    var supplierId = $('#setSupplierPriceModal').data('supplierId');

    $.ajax({
        url: _base_url_ + "classes/Master.php?f=update_supplier_product",
        method: "POST",
        data: {
            productId: productId,
            supplierId: supplierId,
            data: JSON.stringify(data) // Send the data array as JSON string
        },
        dataType: "json",
        error: function (xhr, status, error) {
            console.log(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again later.'
            });
            end_loader();
        },
        success: function (resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Supplier Product updated successfully.'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again later.'
                });
            }
            end_loader();
        }
    });

    $('#setSupplierPriceModal').modal('hide');
}

function removeRow(rowId, spId) {
    console.log("spId:", spId); // Log the spId parameter
    $('#row-' + rowId).remove();
    deleteSupplierProduct(spId); // Call the function to delete the supplier product record
}

function deleteSupplierProduct(spId) {
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_supplier_product",
        method: "POST",
        data: { spId: spId },
        dataType: "json",
        error: function (xhr, status, error) {
            console.log(error);
            // Handle errors
        },
        success: function (resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Supplier Product deleted successfully.'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting Supplier Product.'
                });
            }
        }
    });
}

function openSupplierPriceModal(supplierId, productId) {
    // Set the supplier ID and product ID as data attributes in the modal
    $('#setSupplierPriceModal').data('supplierId', supplierId);
    $('#setSupplierPriceModal').data('productId', productId);

    // Fetch supplier product data from the database
    $.ajax({
        url: 'maintenance/fetch_supplier_product.php', // Change the URL to your PHP file that fetches data from the database
        method: 'POST',
        data: { supplierId: supplierId, productId: productId },
        dataType: 'json',
        success: function(response) {
            // Populate the modal form fields with the fetched data
            populateModalForm(response);
        },
        error: function(xhr, status, error) {
            console.error(error);
            // Handle errors
        }
    });

    // Show the modal
    $('#setSupplierPriceModal').modal('show');
}

function populateModalForm(data) {
    // Clear the existing rows in the table body
    $('#supplierPriceTable tbody').empty();

    // Loop through the fetched data and populate the form fields for each row
    data.forEach(function(row) {
        var newRowId = Date.now(); // Generate a unique ID for each row
        var newRow = `
            <tr id="row-${newRowId}" data-spId="${row.sp_id}"> <!-- Set spId as data attribute -->
                <td class="text-center">
                    <select class="form-control" name="unit_id[]" required>
                        <option value="">Select Unit</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="text-center">
                    <input type="number" class="form-control" name="supplierPrice[]" step="0.01" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${newRowId}, ${row.sp_id})">Remove this row?</button>
                </td>
            </tr>
        `;
        
        // Append the new row to the table body
        $('#supplierPriceTable tbody').append(newRow);

        // Find the newly added row and populate its form fields with data
        var newRowElement = $('#row-' + newRowId);
        newRowElement.find('select[name="unit_id[]"]').val(row.unit_id);
        newRowElement.find('input[name="supplierPrice[]"]').val(row.supplier_price);
    });
}

function updateSupplierProduct(productId, supplierId, unitId, supplierPrice) {
    start_loader();

    // Check if the slider is turned on and supplierPrice is not set
    if (!supplierPrice) {
        // If enabled and supplierPrice is falsy, show the modal to set up supplier price
        console.log("Showing modal");

        // Set data attributes for the modal
        $('#setSupplierPriceModal').data('productId', productId);
        $('#setSupplierPriceModal').data('supplierId', supplierId);

        // Show the modal
        $('#setSupplierPriceModal').modal('show');
        
        // Hide any open modals
        $('.modal').modal('hide');
        
        end_loader();
        return;
    }

}


    $(document).ready(function () {
        $(".product-row").mouseenter(function () {
            // Hide all other dropdowns
            $(".dropdown-menu").hide();

            // Position the dropdown next to the hovered product row
            var position = $(this).position();
            var top = position.top + $(this).outerHeight();
            var left = position.left;
            $(this).find(".dropdown-menu").css({ top: top, left: left }).show();
        });

        $(".product-row").mouseleave(function () {
            // Hide the dropdown when leaving the product row
            $(this).find(".dropdown-menu").hide();
        });

        $(".dropdown-menu").mouseleave(function () {
            // Hide the dropdown when leaving the dropdown itself
            $(this).hide();
        });

        // Hide dropdowns on click outside the product row or dropdown
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".product-row, .dropdown-menu").length) {
                $(".dropdown-menu").hide();
            }
        });

        // Event handler for other actions
        $(".archive_data").click(function () {
            _conf("Are you sure to archive this Item ?", "archive_item", [$(this).attr('data-id')])
        });

        $('#create_item').click(function () {
            uni_modal("<i class='fa fa-plus'></i> Add New Item", "maintenance/manage_item.php", "mid-large")
        });
        $('#create_supplier').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Supplier","maintenance/manage_supplier.php","mid-large")
		});
        $('#create_unit').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Unit","maintenance/manage_unit.php","mid-large")
		});
        $('#create_category').click(function(){
            uni_modal("<i class='fa fa-plus'></i> Add New Category","maintenance/manage_category.php","mid-large")
        });
        $('#create_brand').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Brand","maintenance/manage_brand.php","mid-large")
		});
        $('#create_discount').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Discount","maintenance/manage_discount.php","mid-large")
		});
        $('.edit_unitp').click(function () {
            uni_modal("<i class='fa fa-edit'></i> Manage Unit Selling Price", "maintenance/manage_unitp.php?id=" + $(this).attr('data-id'), "mid-large")
        });

        $('.edit_data').click(function () {
            uni_modal("<i class='fa fa-edit'></i> Edit Item Details", "maintenance/manage_item.php?id=" + $(this).attr('data-id'), "mid-large")
        });

        $('.view_data').click(function () {
            uni_modal("<i class='fa fa-box'></i> Item Details", "maintenance/view_item.php?id=" + $(this).attr('data-id'), "")
        });

        $('.table td,.table th').addClass('py-1 px-2 align-middle');
        $('.table').dataTable();
    });



    function archive_item($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=archive_item",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err)
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
        })
    }
        // Hover event listener for showing image modal
        $('.maintable').on('mouseenter', '.show-image', function() {
        var src = $(this).data('src');
        var name = $(this).closest('tr').find('td:eq(2)').text();
        $('#imageModalImage').attr('src', src);
        $('#imageModalLabel').text(name);
    });

    // Click event listener for showing image modal
    $('.maintable').on('click', '.show-image', function() {
        var name = $(this).find('img').attr('alt');
        $('#imageModalLabel').text(name);
    });



    // Hover event listener for showing image modal
    $('.table').on('mouseenter', '.show-image', function() {
        var src = $(this).data('src');
        var name = $(this).closest('tr').find('td:eq(2)').text();
        $('#imageModalImage').attr('src', src);
        $('#imageModalLabel').text(name);
    });

    // Click event listener for showing image modal
    $('.table').on('click', '.show-image', function() {
        var name = $(this).find('img').attr('alt');
        $('#imageModalLabel').text(name);
    });
</script>