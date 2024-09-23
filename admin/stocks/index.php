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
        .enlarge-image img {
        transition: transform 0.3s ease-in-out;
    }

    .enlarge-image img:hover {
        transform: scale(2.5); /* Enlarge image by 50% */
    }
    #stockDetailsModal .modal-body {
        max-height: calc(100vh - 200px); /* Adjust height as needed */
        overflow-y: auto;
    }

        @keyframes fade {
  0% {
    opacity: 1;
  }
  25% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  75% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

.fade.badge {
  animation: fade 4s linear infinite;
}
    
    </style>
    <div class="card card-outline card-primary">
        <div class="card-header">
         <h3><i class="fas fa-cubes icon"></i> Inventory Stocks Monitoring</h3>
            <div class="card-tools">
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <table class="maintable table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="8%">
                        <col width="40%">
                        <col width="15%">
                        <col width="20%">
                        <col width="15%">
                    </colgroup>
               
<thead>
    <tr>
        <th class="text-center">#</th>
        <th class="text-center">Image</th>
        <th class="text-center">Item Name</th>
        <th class="text-center">Unit Type</th>
        <th class="text-center" style="display: none;">Category</th>
        <th class="text-center">Available Stocks</th>
        <th class="text-center" style="display: none;">Beginning Balance</th>
        <th class="text-center">Stock Status</th>
        <th class="text-center">Stock Monitor</th>
    </tr>
</thead>
<tbody>
<?php
$i = 1;
$qry = $conn->query(
"SELECT 
    pu.item_id,
    i.name AS item_name,
    b.name AS brand_name,
    c.name AS category,
    i.sku,
    i.image,
    i.status AS item_status,
    i.cogs,
    i.date_created,
    i.date_updated,
    pu.unit_id,
    pu.cost,
    pu.bbalance,
    pu.reorder,
    pu.status AS pu_status,
    u.name AS unit_name  
FROM 
    price_unit_order pu 
LEFT JOIN 
    item_list i ON pu.item_id = i.id 
LEFT JOIN 
    brands b ON i.brand_id = b.id 
LEFT JOIN 
    categories c ON i.category_id = c.id
LEFT JOIN
    units u ON pu.unit_id = u.id WHERE i.status = 1;  -- Join the units table to get the unit name
");


while ($row = $qry->fetch_assoc()) :
    $in = $conn->query("SELECT SUM(quantity) as total FROM stock_list WHERE item_id = '{$row['item_id']}' AND type = 1 AND status = 1 AND unit = '{$row['unit_name']}'")->fetch_array()['total'];
    $out = $conn->query("SELECT SUM(quantity) as total, SUM(cogs) as cogs FROM stock_list WHERE item_id = '{$row['item_id']}' AND type = 2 AND status = 1 AND unit = '{$row['unit_name']}'")->fetch_assoc();
    $row['available'] = $in - $out['total'];
    $balance = $row['available'] + $row['bbalance'];

    $reorder = $row['reorder'];
    // Determine the status and color
    $status = '';
    $color = '';
    if ($balance < 0) {
        $status = 'Invalid';
        $color = 'badge-danger';
    } else {
        if ($balance == 0) {
            $status = 'Out of Stock';
            $color = 'badge-danger fade';
        } elseif ($balance > $reorder) {
            $status = 'High Level Stock';
            $color = 'badge-success';
        } elseif ($balance == $reorder) {
            $status = 'Stocks at Reorder Level';
            $color = 'badge-primary fade';
        } elseif ($balance < $reorder) {
            $status = 'Low Level Stock';
            $color = 'badge-warning fade';
        } else {
            $status = 'N/A';
            $color = 'badge-danger fade';
        }
    }
    ?> 
    <?php
    $unit_size = '';
    if (!empty($row['unit_id'])) {
        // Fetch unit size based on unit_id
        $unit_result = $conn->query("SELECT name FROM units WHERE id = '{$row['unit_id']}'")->fetch_assoc();
        $unit_size = $unit_result['name'];
    }
?>
    <tr>
        <td class="text-center"><?php echo $i++; ?></td>
        <td class="text-center enlarge-image">
                                <?php if (!empty($row['image'])) : ?>
                                    <a href="#" class="show-image show-image-details" data-toggle="modal" data-target="#imageModal" data-src="<?php echo $row['image']; ?>">
                                        <img src="<?php echo $row['image']; ?>" class="img-thumbnail" style="width: 50px; height: auto;">
                                    </a>
                                    <?php else: ?>
                                    <img src="../uploads/item/noavail.png" alt="No Image" class="img-thumbnail" style="width: 50px; height: auto;">
                                <?php endif; ?>
                            </td>
        <td class="text-center"><?php echo ($row['brand_name'] != 'No Brand') ? $row['brand_name'] . ' ' : ''; ?> <?php echo $row['item_name']; ?></td>
        <td class="text-center"><?php echo $unit_size ?></td>
        <td class="text-center" style="display: none;"><?php echo $row['category'] ?></td>
        <td class="text-center"><?php echo number_format($row['available'] + $row['bbalance']); ?></td>
        <td class="text-center" style="display: none;"><?php echo $row['bbalance']; ?></td>
        <td class="text-center">
    <span class="badge rounded-pill <?php echo $color; ?>"><?php echo $status; ?></span>
</td>
        <td class="text-center">
    <button type="button" class="btn btn-sm btn-outline-primary show-stock-details" data-item-id="<?php echo $row['item_id'] . '-' . $row['unit_id']; ?>">
        <i class="far fa-eye"></i>
    </button>
</td>
    </tr>
<?php endwhile; ?>
</tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="stockDetailsModal" tabindex="-1" role="dialog" aria-labelledby="stockDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col">
                        <h3 class="modal-title" id="stockDetailsModalLabel"></h3>
                    </div>
                    <div class="col-auto">
                       
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
            </div>
            <div class="modal-body">
                <strong><span class="text-center" id="stockDetailsNote"></span></strong>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class='text-center'>Date</th>
                                <th class='text-center'>Quantity</th>
                                <th class='text-center'>Stock Type</th>
                            </tr>
                        </thead>
                        <tbody id="stockDetailsTableBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
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
</div> -->


<script>
$(document).ready(function () {
    $('#stockDetailsModal td, #stockDetailsModal th').addClass('py-1 px-2 align-middle');
    
    // Initialize DataTable with specific options
    var table = $('#stockDetailsModal table').DataTable({
        paging: false,
        searching: false,
        lengthChange: true,
        ordering: true,
        info: false
    });

    $('.maintable td,.maintable th').addClass('py-1 px-2 align-middle');
    
    // Initialize Datamaintable with specific options
    var maintable = $('.maintable').DataTable({
        paging: true,
        searching: true,
        lengthChange: true,
        ordering: true,
        info: true
    });

    $('.maintable').on('click', '.show-stock-details', function () {
    var itemId = $(this).data('item-id'); // Get item ID from data attribute
    var unitId = $(this).closest('tr').find('td:eq(3)').text(); // Get unit ID from table row
    var itemName = $(this).closest('tr').find('td:eq(2)').text(); // Get item name from table row
    var unitName = $(this).closest('tr').find('td:eq(3)').text(); // Get unit name from table row

    // Update the modal title with the selected item and unit name
    $('#stockDetailsModalLabel').text(itemName + ' Stocks Overview for ' + unitName);

    // Fetch stock details via AJAX
    $.ajax({
        url: 'stocks/f_stocks.php', // Adjust URL as per your setup
        type: 'POST',
        data: { itemId: itemId, unitId: unitId }, // Send item ID and unit ID in the request
        success: function (response) {
            // Populate stock details in the modal
            $('#stockDetailsTableBody').html(response);
            $('#stockDetailsModal').modal('show');
        },
        error: function () {
            alert('Error fetching stock details');
        }
    });
});

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
});
</script>