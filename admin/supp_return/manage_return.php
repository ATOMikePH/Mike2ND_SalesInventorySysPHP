<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT r.*,s.name as supplier FROM supp_returnlist r inner join supplier_list s on r.supplier_id = s.id  where r.id = '{$_GET['id']}'");
    if($qry->num_rows >0){
        foreach($qry->fetch_array() as $k => $v){
            $$k = $v;
        }
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

        h4 {
            font-weight: 700;
            margin-bottom: 20px;
        }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4><i class="fas fa-undo icon"></i><?php echo isset($id) ? " Supplier Return Details - ".$return_code : ' Supplier Return Order Form Registration' ?></h4>
    </div>
    <div class="card-body">
        <form action="" id="return-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">Supplier Return Code</label>
                        <input type="text" class="form-control form-control-sm rounded-0" value="<?php echo isset($return_code) ? $return_code : '' ?>" readonly>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group">
    <label for="transaction_datetime" class="control-label text-info">Last Purchased Date and Time</label>
    <input type="datetime-local" class="form-control" id="transaction_datetime" name="transaction_datetime" value="<?php echo isset($transaction_datetime) ? $transaction_datetime : date('Y-m-d\TH:i'); ?>" required>
</div> </div>
                    <div class="col-md-6">
                    <label for="input_by" class="control-label text-info">Input By :</label>
    <input type="text" name="input_by" class="form-control form-control-sm rounded-0" value="<?php echo isset($input_by) ? $input_by : ($_settings->userdata('firstname').' '.$_settings->userdata('lastname')); ?>" readonly>
</div>
<div class="col-md-6">
                    <div class="form-group">
    <label for="return_date" class="control-label text-info">Return Date</label>
    <input type="date" class="form-control" id="return_date" name="return_date" value="<?php echo isset($return_date) ? $return_date : date('Y-m-d'); ?>" required>
</div>
</div>
<div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id" class="control-label text-info">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="custom-select select2">
                            <option <?php echo !isset($supplier_id) ? 'selected' : '' ?> disabled></option>
                            <?php 
                            $supplier = $conn->query("SELECT * FROM `supplier_list` where status = 1 order by `name` asc");
                            while($row=$supplier->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($supplier_id) && $supplier_id == $row['id'] ? "selected" : "" ?> ><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <fieldset>
                <legend class="text-info">Item Orders</legend>
                    <div class="row justify-content-center align-items-end">
                    <?php 
$item_arr = array();
$item = $conn->query(
    "SELECT sp.*, il.*, b.name AS brand_name 
    FROM `supplier_product` sp 
    INNER JOIN `item_list` il ON sp.product_id = il.id 
    LEFT JOIN `brands` b ON il.brand_id = b.id
    WHERE il.status = 1 
    AND sp.status IS NOT NULL 
    AND sp.status != 0 
    ORDER BY il.name ASC"
);

while ($row = $item->fetch_assoc()) {
    $item_arr[$row['supplier_id']][$row['product_id']] = $row;
}
                    ?>
<div class="col-md-3">
    <div class="form-group">
        <label for="item_id" class="control-label">Item Description</label>
        <select id="item_id" class="custom-select select2">
            <option disabled selected></option>
        </select>
    </div>
</div>
                        <div class="col-md-3">
    <div class="form-group">
        <label for="unit" class="control-label">Unit Size Type</label>
        <select id="unit" class="custom-select select2" name="unit">
            <option disabled selected>Select Unit Type</option>
        </select>
    </div>
</div>

<?php 
// Fetch units based on supplier and product
$unit_arr = array();
$unit_query = $conn->query("SELECT sp.product_id, sp.unit_id, u.name AS unit_name 
                            FROM supplier_product sp 
                            INNER JOIN units u ON sp.unit_id = u.id 
                            WHERE sp.status = 1 
                            ORDER BY sp.product_id ASC");
while($row = $unit_query->fetch_assoc()) {
    $unit_arr[$row['product_id']][$row['unit_id']] = $row['unit_name'];
}
?>

<script>
$(document).ready(function() {
    // Function to populate the "Unit" dropdown based on the selected item and supplier
    $('#product_id').change(function() {
        var itemId = $(this).val();
        var supplierId = $('#supplier_id').val();
        var unitNames = <?php echo json_encode($unit_arr); ?>;
        
        // Clear existing options
        $('#unit').empty();

        // Populate options based on the unit names array
        $.each(unitNames[itemId], function(unitId, unitName) {
            $('#unit').append($('<option>', {
                value: unitId,
                text: unitName // Display unit name
            }));
        });
    });
});
</script>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="qty" class="control-label">QTY</label>
                                <input type="number" step="any" class="form-control rounded-0" id="qty">
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                            <button type="button" class="btn btn-flat btn-sm btn-primary" id="add_to_list">
        <i class="fas fa-plus"></i> Add to List
    </button>
                            </div>
                        </div>
                </fieldset>
                <hr>
                <table class="table table-striped table-bordered" id="list">
                <colgroup>
                        <col width="5%">
                        <col width="8%">
                        <col width="12%">
                        <col width="24%">
                        <col width="24%">
                        <col width="18%">
                    </colgroup>
                    <thead>
                        <tr class="text-light bg-navy">
                            <th class="text-center py-1 px-2"></th>                            
                            <th class="text-center py-1 px-2">QTY</th>
                            <th class="text-center py-1 px-2">SKU</th>
                            <th class="text-center py-1 px-2">Product Information</th>
                            <th class="text-center py-1 px-2">Unit Price</th>
                            <th class="text-center py-1 px-2">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        if(isset($id)):
                        $qry = $conn->query("SELECT p.*,i.name,i.sku FROM `po_items` p inner join item_list i on p.item_id = i.id where p.po_id = '{$id}'");
                        while($row = $qry->fetch_assoc()):
                            $total += $row['total']
                        ?>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                    <tr style="height: 20px;"></tr>
    <tr>
        <th class="text-center py-1 px-2" colspan="4"><strong>Mode of Payment</strong></th>
        <th class="text-right py-1 px-2" colspan="1"><strong>Total No. of Items</strong></th>
        <th class="text-right py-1 px-2 total-items">0</th>
    </tr>
    <tr> 
        <td class="text-center" colspan="4">
    <div class="custom-checkbox">
       <input type="checkbox" id="cash" name="p_mode" value="Cash" <?php echo isset($p_mode) && $p_mode === 'Cash' ? 'checked' : ''; ?>>
        <label for="cash">Cash</label>
    </div>
    <div class="custom-checkbox">
        <input type="checkbox" id="online_payment" name="p_mode" value="E-Wallet" <?php echo isset($p_mode) && $p_mode === 'E-Wallet' ? 'checked' : ''; ?>>
        <label for="online_payment">E-Wallet</label>
    </div>
        </td>
    <th class="text-right py-1 px-2" colspan="1" style="vertical-align: middle;">Total Sales (VAT Inclusive)
    <input type="hidden" name="sub_tax" value="<?php echo isset($sub_tax) ? number_format($sub_tax, 2) : '0.00'; ?>">
</th>
    <th class="text-right py-1 px-2 sub-tax" style="vertical-align: middle;">0.00</th>
</tr>
<tr>
<th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="2"><strong>Specify: </strong></th>
<td class="text-right py-1 px-4" colspan="2">
    <select class="text-center fixed-width left-aligned" name="p_specify">
        <option value="" selected disabled>Choose Mode of Payment</option>
    </select>
</td>
<style>
    .fixed-width {
        width: 250px; /* I-adjust ang lapad na nais mo */
    }
    .left-aligned {
        float: left;
    }
</style>
             <th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="1">Less: VAT
            <input type="hidden" name="tax" value="<?php echo isset($tax) ? $tax : 0.00 ?>">
        </th>
        <th class="text-right py-1 px-2 tax "><?php echo isset($tax) ? number_format($tax,2) : 0.00 ?></th>
    </tr>
    <tr>
    <th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="3">VAT Amount <input style="width:50px !important" name="tax_perc" class='' type="number" min="0" max="100" value="<?php echo isset($tax_perc) ? $tax_perc : 12 ?>">%
            <input type="hidden" name="tax" value="<?php echo isset($tax) ? $tax : 0.00 ?>">
        </th>
    <th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="2">Gross Amount: Net of VAT</th>
        <th class="text-right py-1 px-2 sub-total">0.00</th>
    </tr>
    <tr>
    <th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="3">Discount Type :</th>
    <th class="text-right py-1 px-2" colspan="1">
    <select name="discount_name" class="form-control">
        <option value="">Choose Discount Type</option>
        <?php
        $discount_query = $conn->query("SELECT * FROM `discounts` WHERE `status` = 1");
        while($discount_row = $discount_query->fetch_assoc()): ?>
            <option value="<?php echo $discount_row['id']; ?>" <?php echo ($discount_row['id'] == 9) ? 'selected' : ''; ?> data-disc-per="<?php echo $discount_row['disc_per']; ?>"><?php echo $discount_row['name']; ?></option>
        <?php endwhile; ?>
    </select>
</th>
    <th class="text-right py-1 px-2" style="vertical-align: middle;" colspan="1">Less: Discount <input style="width:40px !important; border: none; background-color: transparent;" name="discount_perc" class='' type="text" min="0" max="100" value="<?php echo isset($discount_perc) ? $discount_perc : 0?>" readonly>%</th>
            <input type="hidden" name="discount" value="<?php echo isset($discount) ?  $discount : 0.00 ?>">
        </th>
        <th class="text-right py-1 px-2 discount "><?php echo isset($discount) ? number_format($discount,2) : 0.00 ?></th>
    </tr>
    <tr>
        <th class="text-right py-1 px-2" colspan="5"><strong>TOTAL AMOUNT DUE (PHP)</strong>
            <input type="hidden" name="amount" value="<?php echo isset($amount) ? $amount : 0.00 ?>">
        </th>
        <th class="text-right py-1 px-2 grand-total">0.00</th>
    </tr>
    <tr>
</tr>
</tfoot>
                </table>
                <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Reason for Returning Item</label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control rounded-0"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-primary" type="submit" form="return-form">Save</button>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=supp_return' ?>">Cancel</a>
    </div>
</div>
</div>
<table id="clone_list" class="d-none">
    <tr>
        <td class="py-1 px-2 text-center">
            <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="py-1 px-2 text-center qty">
            <span class="visible"></span>
            <input type="hidden" name="item_id[]">
            <input type="hidden" name="unit[]">
            <input type="hidden" name="qty[]">
            <input type="hidden" name="price[]">
            <input type="hidden" name="total[]">
        </td>
        <td class="py-1 px-2 text-center sku">
        </td>
        <td class="py-1 px-2 item">
        </td>
        <td class="py-1 px-2 text-right cost">
        </td>
        <td class="py-1 px-2 text-right total">
        </td>
    </tr>
</table>

<?php
$cost_arr = array();

// Fetch supplier prices for each supplier, item, and unit combination
$supplier_prices_query = $conn->query("SELECT supplier_id, product_id, unit_id, supplier_price FROM supplier_product WHERE status = 1");
while ($row = $supplier_prices_query->fetch_assoc()) {
    $supplier_id = $row['supplier_id'];
    $item_id = $row['product_id'];
    $unit_id = $row['unit_id'];
    $supplier_price = $row['supplier_price'];

    // Populate the cost_arr array
    $cost_arr[$supplier_id][$item_id][$unit_id] = $supplier_price;
}

?>

<script>
    

var items = <?php echo isset($item_arr) ? json_encode($item_arr) : '{}'; ?>;
var units = <?php echo isset($unit_arr) ? json_encode($unit_arr) : '{}'; ?>;
var costs = <?php echo isset($cost_arr) ? json_encode($cost_arr) : '{}'; ?>;
var supplierId = <?php echo isset($supplier_id) ? $supplier_id : 'null'; ?>;

$(function(){
 // Disable the item dropdown initially
 $('#item_id').prop('disabled', true);

// Add an event listener to the supplier dropdown
$('#supplier_id').change(function () {
    var supplier_id = $(this).val();
    $('#item_id').select2('destroy');

    if (!!items[supplier_id]) {
        $('#item_id').html('');
        $.each(items[supplier_id], function (id, row) {
            var itemName = row.brand_name + ' ' + row.name; // Concatenate brand name and item name
    var opt = $('<option>').attr('value', id).text(itemName);
            $('#item_id').append(opt);
        });

        $('#item_id').select2({
            placeholder: "Please select item here",
            width: 'resolve',
        });

        // Enable the select2 dropdown
        $('#item_id').prop('disabled', false);
    } else {
        // Clear the HTML content of the dropdown
        $('#item_id').html('');

        $('#item_id').select2({
            placeholder: "No Items Listed yet",
            width: 'resolve',
        });

        // Disable the select2 dropdown
        $('#item_id').prop('disabled', true);
    }
});


$('.select2').select2({
    placeholder: "Please select here",
    width: 'resolve',
});

$('#unit').select2({
    placeholder: "Please select item first",
    width: 'resolve',
});

$('#item_id').select2({
    placeholder: "Please select supplier first",
    width: 'resolve',
});

if (supplierId) {
    $('#supplier_id').val(supplierId).trigger('change');
}

$('#supplier_id').change(function () {
    var supplier_id = $(this).val();
    $('#item_id').select2('destroy');

    if (!!items[supplier_id]) {
        $('#item_id').html('');
        $.each(items[supplier_id], function (id, row) {
            var itemName = row.brand_name + ' ' + row.name; // Concatenate brand name and item name
    var opt = $('<option>').attr('value', id).text(itemName);
            $('#item_id').append(opt);
        });

        $('#item_id').select2({
            placeholder: "Please select item here",
            width: 'resolve',
        });

        // Enable the select2 dropdown
        $('#item_id').prop('disabled', false);
    } else {
        // Clear the HTML content of the dropdown
        $('#item_id').html('');

        $('#item_id').select2({
            placeholder: "No Items Listed yet",
            width: 'resolve',
        });

        // Disable the select2 dropdown
        $('#item_id').prop('disabled', true);
    }
});

});
</script>

<script>
$(function(){
    // Disable the unit dropdown initially
    $('#unit').prop('disabled', true);

    // Add an event listener to the supplier dropdown
    $('#supplier_id').change(function () {
        updateUnitDropdown();
    });

    // Add an event listener to the item dropdown
    $('#item_id').change(function () {
        updateUnitDropdown();
    });

    // Function to update the unit dropdown based on selected supplier and item
    function updateUnitDropdown() {
        var supplier_id = $('#supplier_id').val();
        var item_id = $('#item_id').val(); // Get the selected item ID

        // Check if both supplier and item are selected and units are available
        if (supplier_id && item_id && units[item_id]) {
            // Clear existing options
            $('#unit').empty();

            // Populate unit options based on the selected item and supplier
            $.each(units[item_id], function (unitId, unitName) {
                $('#unit').append($('<option>', {
                    value: unitId,
                    text: unitName
                }));
            });

            // Enable the unit dropdown
            $('#unit').prop('disabled', false);
            
            // Reinitialize select2
            $('#unit').select2({
                placeholder: "Please select unit here",
                width: 'resolve',
            });
        } else {
            // Clear the HTML content of the dropdown
            $('#unit').html('');

            $('#unit').select2({
                placeholder: "No Units Setup yet",
                width: 'resolve',
            });
            
            // If either supplier or item is not selected or units are not available, disable the unit dropdown
            $('#unit').empty().prop('disabled', true);
        }
    }

    // Trigger the change event to initialize the unit dropdown based on the initial values
    $('#supplier_id').trigger('change');
});
    
</script>

<script>
    // Function para ma-update ang mga halaga ng discount
    function updateDiscountValues() {
        var discountSelect = document.querySelector('select[name="discount_name"]');
        var selectedOption = discountSelect.options[discountSelect.selectedIndex];
        var discPer = parseFloat(selectedOption.getAttribute('data-disc-per'));
        
        // Set discount percentage
        document.querySelector('input[name="discount_perc"]').value = discPer;
        
        // Calculate discount value
        var subTotal = parseFloat($('.sub-total').text().replace('₱', '').replace(',', ''));
        var discount = subTotal * (discPer / 100);
        var grandTotal = subTotal - discount;
        
        // Update discount display
        $('.discount').text('₱ ' + discount.toFixed(2));
        
        // Update grand total
        $('table#list tfoot .grand-total').text('₱ ' + grandTotal.toFixed(2));
        
        // Trigger any additional calculations if needed
        
    }
    
    // Event listener para sa pag-update ng mga halaga kapag may bagong discount na napili
    document.querySelector('select[name="discount_name"]').addEventListener('change', function() {
        updateDiscountValues();
        calc();
    });
    
    // Call the function once to set the initial values
    updateDiscountValues();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var cashRadio = document.getElementById('cash');
        var onlinePaymentRadio = document.getElementById('online_payment');
        var specifySelect = document.getElementsByName('p_specify')[0];

        function updateOptions() {
            specifySelect.innerHTML = ''; // Clear existing options

            if (cashRadio.checked) {
                var option1 = document.createElement("option");
                option1.text = "Delivery";
                specifySelect.add(option1);

                var option2 = document.createElement("option");
                option2.text = "Pickup";
                specifySelect.add(option2);
            } else if (onlinePaymentRadio.checked) {
                var option1 = document.createElement("option");
                option1.text = "GCash";
                specifySelect.add(option1);

                var option2 = document.createElement("option");
                option2.text = "Paymaya";
                specifySelect.add(option2);
            } else {
                var defaultOption = document.createElement("option");
                defaultOption.text = "Choose Mode of Payment";
                specifySelect.add(defaultOption);
            }
        }

        cashRadio.addEventListener('change', updateOptions);
        onlinePaymentRadio.addEventListener('change', updateOptions);

        // Initial call to set the options based on initial radio button states
        updateOptions();
    });
</script>

<script>
    $(document).ready(function() {
    // Function to check if the conditions are met to enable the submit button
    function checkConditions() {
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var pModeChecked = $('input[name="p_mode"]:checked').length > 0;
        var button = $('button[type="submit"]');
        
        // Check if p_mode is checked  is greater than grand total
        if (pModeChecked) {
            // Enable the submit button
            button.prop('disabled', false);
        } else {
            // Disable the submit button
            button.prop('disabled', true);
        }
    }

    // Call the checkConditions function initially
    checkConditions();

    // Add event listeners for change and input events on p_mode checkboxes  field
    $('input[name="p_mode"]').on('change input', function()
     {
        // Call the checkConditions function
        checkConditions();
    });
     // Call the checkConditions function initially
    checkConditions();
});
$(document).ready(function() {
    $('input[type="checkbox"]').click(function() {
        var $checkboxes = $('input[name="p_mode"]');
        $checkboxes.not(this).prop('checked', false);
    });
    // Function to calculate change

    // Call the calculateChange function when changes
    $('[name="discount_perc"], [name="tax_perc"]').on('input', function() {
    calc();
});
});


$(function(){
        // Function to calculate the total number of items
        function calculateTotalItems() {
            var totalItems = $('table#list tbody tr').length;
            $('tfoot .total-items').html('<strong>' + totalItems + '</strong>');
        }

        // Function to recalculate total items count when the table content changes
        function updateTotalItemsCount() {
            // Call calculateTotalItems function
            calculateTotalItems();
        }

        // Call the function initially to set the initial value
        calculateTotalItems();

        // Add event listener for keyup event on input fields inside the table body
        $('table#list tbody').on('keyup', 'input', function() {
            // Update total items count
            updateTotalItemsCount();
        });

        // Add event listener for change event on select fields inside the table body
        $('table#list tbody').on('change', 'select', function() {
            // Update total items count
            updateTotalItemsCount();
        });

        // Add event listener for click event on remove row buttons
        $('table#list tbody').on('click', '.rem_row', function() {
            // Update total items count
            updateTotalItemsCount();
        });


        <?php
// Fetch all brands from the database
$brand_query = $conn->query("SELECT id, name FROM `brands`");
$brand_arr = array();

// Check if there are results
if ($brand_query->num_rows > 0) {
    // Loop through each row and store brand id as key and brand name as value in $brand_arr
    while ($row = $brand_query->fetch_assoc()) {
        $brand_arr[$row['id']] = $row['name'];
    }
}
?>



$('#add_to_list').click(function () {
    var supplier = $('#supplier_id').val();
    var item = $('#item_id').val();
    var qty = $('#qty').val() > 0 ? $('#qty').val() : 0;
    var unitId = $('#unit').val(); // Get the unit ID

    // Fetch the unit name based on the selected unit ID
    var unitName = (units[item] && units[item][unitId]) ? units[item][unitId] : '';

    // Check if supplier, item, and unit are selected
    if (supplier === '' || item === '' || unitId === '' || unitName === '') {
        alert_toast('Please select supplier, item, and unit.', 'warning');
        return false;
    }

    // Fetch the price based on supplier, item, and unit
    var price = (costs[supplier] && costs[supplier][item] && costs[supplier][item][unitId]) ? costs[supplier][item][unitId] : 0;

    // If price is not found, display an alert and stop further processing
    if (price === 0) {
        alert_toast('Price not found for the selected supplier, item, and unit.', 'warning');
        return false;
    }

    var total = parseFloat(qty) * parseFloat(price);
    var item_sku = items[supplier][item].sku || 'N/A';
    var item_name = items[supplier][item].name || 'N/A';
    var brand_name = <?php echo json_encode($brand_arr); ?>[items[supplier][item].brand_id] || 'N/A';

    // Check if the item already exists in the list
    var existingRow = $('table#list tbody').find('tr[data-id="' + item + '"][data-unit="' + unitId + '"]');
    if (existingRow.length > 0) {
        // Update the quantity and total of the existing row
        var existingQty = parseFloat(existingRow.find('[name="qty[]"]').val());
        var newQty = existingQty + parseFloat(qty);
        var newTotal = newQty * parseFloat(price);

        existingRow.find('[name="qty[]"]').val(newQty);
        existingRow.find('[name="total[]"]').val(newTotal);
        existingRow.find('.qty .visible').text(newQty);
        existingRow.find('.total').text("₱ " + newTotal.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    } else {
        // Create a new row for the item
        var tr = $('#clone_list tr').clone();

        tr.find('[name="item_id[]"]').val(item);
        tr.find('[name="unit[]"]').val(unitName);
        tr.find('[name="qty[]"]').val(qty);
        tr.find('[name="price[]"]').val(price);
        tr.find('[name="total[]"]').val(total);
        tr.attr('data-id', item);
        tr.attr('data-unit', unitId); // Add data-unit attribute to track unit
        tr.find('.unit').text(unitName);
        tr.find('.qty .visible').text(qty);
        tr.find('.sku').html(item_sku);
        tr.find('.item').html('<strong>' + brand_name + ' ' + item_name + '</strong><br/>' + unitName);
        tr.find('.cost').text("₱ " + parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        tr.find('.total').text("₱ " + total.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('table#list tbody').append(tr);
    }

    calc();
    checkConditions();
    updateTotalItemsCount();

    $('#item_id').val('').trigger('change');
    $('#qty').val('');
    $('#unit').val('');

    // Attach remove row function to the newly added row
    $('table#list tbody tr').last().find('.rem_row').click(function () {
        rem($(this));
        calc();
        updateTotalItemsCount();
        checkConditions();
    });

    // Attach input event listener for discount and tax inputs
    $('[name="discount_perc"],[name="tax_perc"]').on('input', function () {
        calc();
    });

    $('#supplier_id').attr('readonly', 'readonly');
});

$('#return-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_sreturn",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(resp.status == 'success'){
						location.replace(_base_url_+"admin/?page=supp_return/view_return&id="+resp.id);
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
                    $('html,body').animate({scrollTop:0},'fast')
				}
			})
		})

    if ('<?php echo isset($id) && $id > 0 ?>' == 1) {
        calc();
        $('#supplier_id').trigger('change');
        $('#supplier_id').prop('readonly', true);
        $('table#list tbody tr .rem_row').click(function () {
            rem($(this));
        });
    }
});

function rem(_this) {
    _this.closest('tr').remove();
    calc();
    if ($('table#list tbody tr').length <= 0) {
        $('#supplier_id').prop('readonly', false);
    }
}

function calculateTotalSales() {
            var subTotal = parseFloat($('tfoot .sub-total').text().replace('₱', '').replace(/,/g, ''));
            var tax = parseFloat($('tfoot .tax').text().replace('₱', '').replace(/,/g, ''));
            var totalSales = subTotal + tax;
            $('tfoot .sub-tax').text(totalSales.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

function checkConditions() {
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var pModeChecked = $('input[name="p_mode"]:checked').length > 0;
        var button = $('button[type="submit"]');
        
        // Check if p_mode is checked is greater than grand total
        if (pModeChecked >= grandTotal) {
            // Enable the submit button
            button.prop('disabled', false);
        } else {
            // Disable the submit button
            button.prop('disabled', true);
        }
    }

    function calc() {
    var sub_total = 0;
    var grand_total = 0;
    var discount = 0;
    var tax = 0;
    
    // Calculate subtotals for each item
    $('table#list tbody tr').each(function() {
        var qty = $(this).find('[name="qty[]"]').val();
        var price = $(this).find('[name="price[]"]').val();
        var total = parseFloat(price) * parseFloat(qty);
        $(this).find('[name="total[]"]').val(total);
        $(this).find('.total').text("₱ " + total.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    });

    // Calculate total sub-total
    $('table#list tbody input[name="total[]"]').each(function() {
        sub_total += parseFloat($(this).val());
    });

    // Update sub-total display
    $('table#list tfoot .sub-total').text(sub_total.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    // Calculate discount based on discount percentage
    discount = sub_total * (parseFloat($('[name="discount_perc"]').val()) / 100);
    sub_total -= discount; // Subtract discount from sub-total

    // Calculate tax based on tax percentage
    tax = sub_total * (parseFloat($('[name="tax_perc"]').val()) / 100);

    // Calculate grand total
    grand_total = sub_total + tax;

    // Display discount and tax with two decimal places
    $('.discount').text(discount.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('[name="discount"]').val(discount.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('.tax').text(tax.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('[name="tax"]').val(tax.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));

  // Update grand total display
  $('table#list tfoot .grand-total').text(parseFloat(grand_total).toFixed(2));
$('[name="amount"]').val(parseFloat(grand_total).toFixed(2));

    // Calculate total sales (sub-total + tax) and display with two decimal places
    var totalSales = sub_total + tax;
    $('tfoot .sub-tax').text(totalSales.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));


}

</script>

