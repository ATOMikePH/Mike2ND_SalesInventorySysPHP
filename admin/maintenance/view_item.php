<?php require_once('./../../config.php') ?>

<?php 
    $item_id = isset($_GET['id']) ? $_GET['id'] : null;

    // Query: Get item details with category information
    $qry = $conn->query("SELECT i.*, c.name as category, b.name as brand 
                         FROM `item_list` i
                         INNER JOIN categories c ON i.category_id = c.id
                         INNER JOIN brands b ON i.brand_id = b.id
                         WHERE i.id = '{$item_id}'");

    // Fetch result
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $sku = $row['sku'];
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
                                  WHERE pu.item_id = '{$item_id}' AND pu.status = 1");

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
        WHERE sp.product_id = '{$item_id}'");

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
    }
?>

<style>
    #uni_modal .modal-footer {
        display: none;
    }

    #transaction-printable-details dl {
        margin: 0;
        padding: 0;
    }

    #transaction-printable-details dl dt {
        width: 150px;
        float: left;
        clear: left;
        text-align: right;
        margin-right: 10px;
        font-weight: bold;
    }

    #transaction-printable-details dl dd {
        margin-left: 0;
        margin-bottom: 10px;
    }

    #transaction-printable-details img {
        max-width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
        margin: 0 auto; /* Center the image horizontally */
        border: 5px solid #343a40; /* Add a thick border for a stylish frame */
        border-radius: 10px; /* Add border-radius for a rounded appearance */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
        transition: transform 0.3s ease-in-out; /* Add a smooth transition effect */
    }

    #transaction-printable-details img:hover {
        transform: scale(1.15); /* Add a scale effect on hover for an aggressive look */
    }
</style>

<style>
    .box {
        display: inline-flex;
        align-items: center;
    }

    .badge {
        margin-right: 5px;
    }
    #supplierModal .modal-body {
        color: white;
    }
    #supplierModal .table thead th {
        color: white;
    }

    #supplierModal .table tbody td:nth-child(2) {
        color: black;
    }

</style>
<div class="container-fluid" id="print_out">
    <div id='transaction-printable-details' class='position-relative'>
        <div class="row">
            <fieldset class="w-100">
                <div class="col-12">
                <?php if (!empty($image)) : ?>
    <img src="<?php echo $image; ?>" alt="Item Image" class="img-thumbnail mb-3 mx-auto d-block" draggable="false">
<?php endif; ?>

                    <dl>
                        <dt class="text-info">SKU:</dt>
                        <dd class="pl-3"><?php echo $sku ?></dd>
                        <dt class="text-info">Full Item Name:</dt>
                        <dd class="pl-3"><?php echo ($brand != 'No Brand') ? $brand . ' ' : ''; ?><?php echo $name ?> </dd>
                        <dt class="text-info">Quantity Unit:</dt>
                        <dd class="pl-3">
                            <?php 
                            // Explode unit_size string to array
                            $unit_sizes = explode(",", $unit_size);
                            foreach ($unit_sizes as $unit) {
                                echo "<span class='badge badge-primary rounded-pill'>$unit</span> ";
                            }
                            ?>
                        </dd>
                        <dt class="text-info">Category:</dt>
                        <dd class="pl-3"><?php echo isset($category) ? $category : '' ?></dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end">
            <button class="btn btn-dark btn-flat" type="button" id="cancel" onclick="window.location.reload();" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#supplierModal">View Supplier Price</button> -->

<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Supplier Information</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-white">Supplier Name</th>
                                <th class="text-info">Supplier Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($supplier_names)) {
                                for ($i = 0; $i < count($supplier_names); $i++) {
                                    echo "<tr>";
                                    echo "<td>{$supplier_names[$i]}</td>";
                                    echo "<td>₱ " . number_format($supplier_prices[$i], 2) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No supplier information available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(function(){
        $('.table td, .table th').addClass('py-1 px-2 align-middle');
    });
</script>