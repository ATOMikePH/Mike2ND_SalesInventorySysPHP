<style>
    #submit {
        display: none;
    }
</style>

<style>
    .btn.btn-secondary[data-dismiss="modal"]::before {
        content: "Finish and ";
    }
</style>

<?php
// Include the necessary files
require_once('../../config.php');

// Check if item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo 'Invalid item ID.';
    exit();
}

// Get the item ID from the URL parameter
$item_id = $_GET['id'];

// Query to fetch unit details for the item from the price_unit_order table
$query = "SELECT pu.pu_id, u.name AS unit_name, pu.cost, pu.bbalance, pu.reorder
          FROM price_unit_order pu
          INNER JOIN units u ON pu.unit_id = u.id
          WHERE pu.item_id = '$item_id' AND pu.status = 1";
$result = $conn->query($query);

// Check if there are units associated with the item
if ($result->num_rows > 0) {
    ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Unit Size</th>
                    <th>Selling Price</th>
                    <th>Beginning Balance</th>
                    <th>Reorder Point</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Loop through the results and display each unit's details
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['unit_name']; ?></td>
                    <td><input type="text" class="form-control edit-field" data-pu-id="<?php echo $row['pu_id']; ?>" data-field="cost" value="<?php echo $row['cost']; ?>"></td>
                    <td><input type="text" class="form-control edit-field" data-pu-id="<?php echo $row['pu_id']; ?>" data-field="bbalance" value="<?php echo $row['bbalance']; ?>"></td>
                    <td><input type="text" class="form-control edit-field" data-pu-id="<?php echo $row['pu_id']; ?>" data-field="reorder" value="<?php echo $row['reorder']; ?>"></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <script>
$(document).ready(function() {
    $('.edit-field').on('change', function() {
        var data = {};
        var pu_id = $(this).data('pu-id');
        var field = $(this).data('field');
        var value = $(this).val();
        
        data[pu_id] = {
            field: field,
            value: value
        };

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=update_pu",
            method: 'POST',
            dataType: 'json',
            data: { data: JSON.stringify(data) },
            success: function(resp) {
                var failed = false;
                for (var key in resp) {
                    if (resp[key] === 'failed') {
                        failed = true;
                        break;
                    }
                }
                if (!failed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Unit Price Updated',
                        showConfirmButton: false,
                        timer: 500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to update unit prices',
                        text: 'Please try again.'
                    });
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'An error occurred',
                    text: 'Please try again later.'
                });
            }
        });
    });
});
</script>

    <?php
} else {
    echo 'No units found for the item.';
}
?>