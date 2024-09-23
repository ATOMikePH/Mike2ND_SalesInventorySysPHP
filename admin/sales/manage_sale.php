<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM sales_list where id = '{$_GET['id']}'");
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

    .custom-checkbox {
    display: inline-block;
    vertical-align: middle;
    margin-right: 15px; /* Adjust spacing between checkboxes */
}

.custom-checkbox input[type="checkbox"] {
    display: none;
}

.custom-checkbox label {
    position: relative;
    cursor: pointer;
    padding-left: 30px;
    margin-bottom: 0;
    display: inline-block;
    vertical-align: middle;
    line-height: 1.5;
    font-size: 14px;
}

.custom-checkbox label::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 20px; /* Size of the checkbox */
    height: 20px; /* Size of the checkbox */
    border: 2px solid #ccc; /* Border color of the checkbox */
    border-radius: 3px;
    background-color: #fff; /* Background color when checkbox is not checked */
}

.custom-checkbox input[type="checkbox"]:checked + label::before {
    border-color: #007bff; /* Border color when checkbox is checked */
    background-color: #007bff; /* Background color when checkbox is checked */
}

.custom-checkbox label::after {
    content: '\2713'; /* Checkmark symbol */
    position: absolute;
    top: 50%;
    left: 3px;
    transform: translateY(-50%);
    color: #fff; /* Color of the checkmark */
    font-size: 12px; /* Size of the checkmark */
    display: none;
}

.custom-checkbox input[type="checkbox"]:checked + label::after {
    display: block;
}
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4><i class="fas fa-money-bill-wave icon"></i><?php echo isset($id) ? " POS Customer Sales - ".$sales_code : ' POS Customer Sales' ?></h4>
    </div>
    <div class="card-body">
        <form action="" id="sale-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">Sales Transaction No. :</label>
                        <input type="text" class="form-control form-control-sm rounded-0" value="<?php echo isset($sales_code) ? $sales_code : '' ?>" readonly>
                    </div>
                    <div class="form-group">
    <label for="transaction_datetime" class="control-label text-info">Transaction Date and Time</label>
    <input type="datetime-local" class="form-control" id="transaction_datetime" name="transaction_datetime" value="<?php echo isset($transaction_datetime) ? $transaction_datetime : date('Y-m-d\TH:i'); ?>" readonly required>
</div>
                    <div class="col-md-6">
                    <label for="input_by" class="control-label text-info">Prepared By :</label>
    <input type="text" name="input_by" class="form-control form-control-sm rounded-0" value="<?php echo isset($input_by) ? $input_by : ($_settings->userdata('firstname').' '.$_settings->userdata('lastname')); ?>" readonly>
    <input type="hidden" name="role" class="form-control form-control-sm rounded-0" value="<?php echo isset($role) ? $role : getRole($_settings->userdata('type')); ?>" readonly>

<?php
// Function to determine role based on type
function getRole($type) {
    switch ($type) {
        case 1:
            return "Admin";
            break;
        case 2:
            return "Accounting";
            break;
        case 3:
            return "Staff";
            break;
        default:
            return ""; // Default value if type is not recognized
    }
}
?>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="client" class="control-label text-info">Customer Name :</label>
        <input type="text" name="client" class="form-control form-control-sm rounded-0" value="<?php echo isset($client) ? $client : 'Guest' ?>" >
        <small class="form-text text-muted">*Optional</small>
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
                                "SELECT po.*, il.name, il.sku, il.brand_id 
                                FROM `price_unit_order` po 
                                INNER JOIN `item_list` il 
                                ON po.item_id = il.id 
                                WHERE po.`status` = 1 AND il.status = 1
                                ORDER BY po.`item_id` ASC");
                                while($row = $item->fetch_assoc()):
                                    $item_arr[$row['item_id']] = $row; // Kunin ang `item_id` mula sa `price_unit_order`
                                endwhile;
                            ?>
<div class="col-md-3">
    <div class="form-group">
        <label for="item_id" class="control-label">Item Description</label>
        <select id="item_id" class="custom-select select2">
            <option disabled selected></option>
            <?php foreach($item_arr as $k => $v): ?>
                <?php
                    // Fetch brand name using brand_id
                    $brand_query = $conn->query("SELECT name FROM `brands` WHERE id = '{$v['brand_id']}'");
                    $brand_name = $brand_query->fetch_assoc()['name'];

                     // Set brand name to blank if it's "No Brand"
                     if ($brand_name == "No Brand") {
                        $brand_name = "";
                    }

                ?>
                <option value="<?php echo $k ?>">
                    <?php echo $brand_name ?> <?php echo $v['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        <label for="unit" class="control-label">Unit Size Type</label>
        <select id="unit" class="custom-select select2" name="unit">
            <option disabled selected>Select Unit</option>
        </select>
    </div>
</div>
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
                        $qry = $conn->query("SELECT s.*,i.name,i.description,i.sku FROM `stock_list` s inner join item_list i on s.item_id = i.id where s.id in ({$stock_ids})");
                        while($row = $qry->fetch_assoc()):
                            $total += $row['total']
                        ?>
                        <tr>
                            <td class="py-1 px-2 text-center">
                                <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
                            </td>
                            <td class="py-1 px-2 text-center qty">
                                <span class="visible"><?php echo number_format($row['quantity']); ?></span>
                                <input type="hidden" name="sku[]" value="<?php echo $row['sku']; ?>">
                                <input type="hidden" name="item_id[]" value="<?php echo $row['item_id']; ?>">
                                <input type="hidden" name="unit[]" value="<?php echo $row['unit']; ?>">
                                <input type="hidden" name="qty[]" value="<?php echo $row['quantity']; ?>">
                                <input type="hidden" name="price[]" value="<?php echo number_format($row['price'],2); ?>">
                                <input type="hidden" name="total[]" value="<?php echo number_format($row['total'],2); ?>">
                            </td>
                            <td class="py-1 px-2 text-center sku">
    <?php echo $row['sku']; ?>
</td>
                            <td class="py-1 px-2 text-center unit">
                            <?php echo $row['unit']; ?>
                            </td>
                            <td class="py-1 px-2 item">
    <strong><?php echo $row['name']; ?></strong> <br>
    <?php echo $row['description']; ?>
</td>
        <td class="py-1 px-2 text-right cost">
    <?php echo "₱ " . number_format($row['price'], 2); ?>
</td>
<td class="py-1 px-2 text-right total">
    <?php echo "₱ " . number_format($row['total'], 2); ?>
</td>
                        </tr>
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
    <th class="text-right py-1 px-2" colspan="3"><strong>Amount Tendered (PHP)</strong></th>
        <td class="text-right py-1 px-5">
            <input class="text-center " type="text" name="amount_tendered" placeholder="Enter amount" value="<?php echo isset($amount_tendered) ? $amount_tendered : 0.00 ?>"/>
        </td>
        <th class="text-right py-1 px-2" colspan="1"><strong>TOTAL AMOUNT DUE (PHP)</strong>
            <input type="hidden" name="amount" value="<?php echo isset($amount) ? number_format($amount, 2) : '0.00' ?>">
        </th>
        <th class="text-right py-1 px-2 grand-total">0.00</th>
    </tr>
    <tr>
    <th class="text-right py-1 px-2" colspan="5">Change (PHP)
        <input type="hidden" name="change_amount" value="<?php echo isset($change_amount) ? number_format($change_amount, 2) : '0.00'; ?>">
    </th>
    <th class="text-right py-1 px-2 change_amount"><?php echo isset($change_amount) ? number_format($change_amount, 2) : '0.00'; ?></th>
</tr>
</tfoot>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="remarks" class="text-info control-label">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control rounded-0"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer py-1 text-center">
    <button class="btn btn-flat btn-primary" type="submit" form="sale-form">
        <i class="fas fa-check"></i> Finalize Transaction
    </button>
    <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=sales' ?>">
        <i class="fas fa-times"></i> Cancel
    </a>
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
$item2_arr = array();
$unit_arr = array(); // Array to store unit names corresponding to each item ID
$cost_arr = array();
$balance_arr = array(); // Array to store balance quantities corresponding to each item ID and unit ID
$item2 = $conn->query("SELECT * FROM `price_unit_order` WHERE `status` = 1 ORDER BY `item_id` ASC");
while($row = $item2->fetch_assoc()):
    $item2_arr[$row['item_id']][] = $row; // Store all item data
    if(!isset($unit_arr[$row['item_id']])) {
        $unit_arr[$row['item_id']] = array(); // Initialize array for unit names if not already initialized
    }
    // Fetch unit name from the 'units' table
    $unit_query = $conn->query("SELECT name FROM `units` WHERE id = '{$row['unit_id']}'");
    $unit_name = $unit_query->fetch_assoc()['name'];
    $unit_arr[$row['item_id']][$row['unit_id']] = $unit_name; // Store unit names corresponding to each item ID and unit ID
    $cost_arr[$row['item_id']][$row['unit_id']] = $row['cost']; // Get the `cost` from the `price_unit_order`
    
    // Fetch available quantity from stock_list table by subtracting stock out from stock in and add balance quantity
    $stock_in_query = $conn->query("SELECT SUM(quantity) as total_stock_in FROM `stock_list` WHERE `status` = 1 AND `type` = 1 AND `item_id` = '{$row['item_id']}' AND `unit` = '{$unit_name}'");
    $stock_out_query = $conn->query("SELECT SUM(quantity) as total_stock_out FROM `stock_list` WHERE `status` = 1 AND `type` = 2 AND `item_id` = '{$row['item_id']}' AND `unit` = '{$unit_name}'");
    $balance_query = $conn->query("SELECT SUM(bbalance) as total_balance FROM `price_unit_order` WHERE `status` = 1 AND `item_id` = '{$row['item_id']}' AND `unit_id` = '{$row['unit_id']}'");
    
    $stock_in_data = $stock_in_query->fetch_assoc();
    $stock_out_data = $stock_out_query->fetch_assoc();
    $balance_data = $balance_query->fetch_assoc();
    
    $availableQuantity = $stock_in_data['total_stock_in'] - $stock_out_data['total_stock_out'] + $balance_data['total_balance'];
    $balance_arr[$row['item_id']][$row['unit_id']] = $availableQuantity; // Store available quantity
endwhile;
?>

<script>
$(document).ready(function() {
    // Function to populate the "Unit" dropdown based on the selected item
    $('#item_id').change(function() {
        var itemId = $(this).val();
        var unitNames = <?php echo json_encode($unit_arr); ?>[itemId]; // Get the unit names array
        var balanceQuantities = <?php echo json_encode($balance_arr); ?>[itemId]; // Get the balance quantities array
        
        // Clear existing options
        $('#unit').empty();

        // Populate options based on the unit names array and balance quantities
        $.each(unitNames, function(unitId, unitName) {
            var balance = balanceQuantities[unitId];
            $('#unit').append($('<option>', {
                value: unitId,
                text: unitName + ' | Available: ' + balance + '' // Display unit name and balance quantity
            }));
        });
    });

    // Function to fetch and display the cost based on the selected item and unit
    $('#unit').change(function() {
        var itemId = $('#item_id').val();
        var unitId = $(this).val();
        var cost = <?php echo json_encode($cost_arr); ?>[itemId][unitId]; // Get the cost for the selected item and unit
        
        // Display the cost in the designated element
        $('#item_cost').text("₱ " + parseFloat(cost).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    });
});

$(document).ready(function() {
    // Function to populate the "Unit" dropdown in the "add to list" fieldset
    $('#item_id').change(function() {
        var itemId = $(this).val();
        var unitNames = <?php echo json_encode($unit_arr); ?>[itemId]; // Get the unit names array
        
        // Clear existing options
        $('#unit_list').empty();

        // Populate options based on the unit names array
        $.each(unitNames, function(unitId, unitName) {
            $('#unit_list').append($('<option>', {
                value: unitId,
                text: unitName // Display unit name
            }));
        });
    });
});
var items = $.parseJSON('<?php echo json_encode($item_arr) ?>')
var costs = $.parseJSON('<?php echo json_encode($cost_arr) ?>')
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
        var amountTendered = parseFloat($('[name="amount_tendered"]').val());
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var pModeChecked = $('input[name="p_mode"]:checked').length > 0;
        var button = $('button[type="submit"]');
        
        // Check if p_mode is checked and amount_tendered is greater than grand total
        if (pModeChecked && amountTendered >= grandTotal) {
            // Enable the submit button
            button.prop('disabled', false);
        } else {
            // Disable the submit button
            button.prop('disabled', true);
        }
    }

    // Call the checkConditions function initially
    checkConditions();

    // Add event listeners for change and input events on p_mode checkboxes and amount_tendered field
    $('input[name="p_mode"], [name="amount_tendered"]').on('change input', function()
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
    function calculateChange() {
        var amountTendered = parseFloat($('[name="amount_tendered"]').val());
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var change_amount = amountTendered - grandTotal;
        // Update the change field
        $('.change_amount').text(change_amount.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('[name="change_amount"]').val(change_amount);
    }

    // Call the calculateChange function when amount_tendered changes
    $('[name="amount_tendered"]').on('input', function() {
        calculateChange();
    });
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

        $('.select2').select2({
            placeholder:"Please select here",
            width:'resolve',
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
    var item = $('#item_id').val();
    var balanceQuantities = <?php echo json_encode($balance_arr); ?>[item];
    var qty = $('#qty').val() > 0 ? $('#qty').val() : 0;
    var unitId = $('#unit').val(); // Get the unit id instead of the unit name
    var availableQuantity = balanceQuantities[unitId];
    var price = costs[item][unitId] || 0; // Correct way to get the price based on item and unit
    var total = parseFloat(qty) * parseFloat(price);
    
    // Check if there is enough stock in the inventory
    if (item && qty > 0 && unitId && items[item] && availableQuantity < qty) {
        alert_toast('Insufficient stock in the inventory. Please check first the stocks then try again.', 'error');
        return false;
    }
    
    var item_sku = items[item].sku || 'N/A';
    var item_name = items[item].name || 'N/A';
    var brand_name = <?php echo json_encode($brand_arr); ?>[items[item].brand_id] || 'N/A';
    
    var tr = $('#clone_list tr').clone();
    if (item == '' || qty == '' || unitId == '') {
        alert_toast('Form Item textfields are required.', 'warning');
        return false;
    }
    
    // Check if the selected quantity exceeds the available quantity
    if (qty > availableQuantity) {
        alert_toast('Quantity exceeds available stock.', 'error');
        return false;
    }
    
    // Check if the item already exists in the list with the same unit
    var existingRow = $('table#list tbody').find('tr[data-id="' + item + '"][data-unit="' + unitId + '"]');
    if (existingRow.length > 0) {
        // Alert the user about the duplicate item with the same unit
        alert_toast('This item with the same unit is already in the list.', 'error');
        return false; // Stop further execution
    }
    
    // Subtract the selected quantity from the available quantity
    availableQuantity -= parseInt(qty);
    
    // Update the dropdown options with the new available quantity
    $('#item_id option:selected').data('available', availableQuantity);
    
    // Get the unit name based on the unit id
    var unitName = <?php echo json_encode($unit_arr); ?>[item][unitId];
    
    tr.find('[name="item_id[]"]').val(item);
    tr.find('[name="unit[]"]').val(unitName); // Set the unit name instead of the unit id
    tr.find('[name="qty[]"]').val(qty);
    tr.find('[name="price[]"]').val(price);
    tr.find('[name="total[]"]').val(total);
    tr.attr('data-id', item);
    tr.attr('data-unit', unitId);
    tr.find('.qty .visible').text(qty);
    tr.find('.unit').text(unitName); // Display the unit name
    tr.find('.sku').text(item_sku);
    tr.find('.item').html('<strong>' + brand_name + ' ' + item_name + '</strong><br/>' + unitName);
    tr.find('.cost').text("₱ " + parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
    tr.find('.total').text("₱ " + parseFloat(total).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
    $('table#list tbody').append(tr);
    calc();
    calculateChange();
    checkConditions();
    // Call the updateTotalItemsCount function after adding a new row
    updateTotalItemsCount();
    tr.find('.rem_row').click(function () {
        rem($(this)); });
    $('#item_id').val('').trigger('change');
    $('#qty').val('');
    $('#unit').val('');
    tr.find('.rem_row').click(function () {
        rem($(this));
        calc();
        updateTotalItemsCount();
        calculateChange();
        checkConditions();
    });

    $('[name="discount_perc"],[name="tax_perc"]').on('input', function () {
        calc();
    });
    $('#supplier_id').attr('readonly', 'readonly');

});
        $('#sale-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_sale",
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
						location.replace(_base_url_+"admin/?page=sales/view_sale&id="+resp.id);
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
		});

        if('<?php echo isset($id) && $id > 0 ?>' == 1){
            calc()
            $('#supplier_id').trigger('change')
            $('#supplier_id').attr('readonly','readonly')
            $('table#list tbody tr .rem_row').click(function(){
                rem($(this))
            });
        }

        
    });
    function rem(_this){
        _this.closest('tr').remove()
        calc()
        if($('table#list tbody tr').length <= 0)
            $('#supplier_id').removeAttr('readonly')

    }
    function calculateTotalSales() {
            var subTotal = parseFloat($('tfoot .sub-total').text().replace('₱', '').replace(/,/g, ''));
            var tax = parseFloat($('tfoot .tax').text().replace('₱', '').replace(/,/g, ''));
            var totalSales = subTotal + tax;
            $('tfoot .sub-tax').text(totalSales.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

function checkConditions() {
        var amountTendered = parseFloat($('[name="amount_tendered"]').val());
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var pModeChecked = $('input[name="p_mode"]:checked').length > 0;
        var button = $('button[type="submit"]');
        
        // Check if p_mode is checked and amount_tendered is greater than grand total
        if (pModeChecked && amountTendered >= grandTotal) {
            // Enable the submit button
            button.prop('disabled', false);
        } else {
            // Disable the submit button
            button.prop('disabled', true);
        }
    }

function calculateChange() {
        var amountTendered = parseFloat($('[name="amount_tendered"]').val());
        var grandTotal = parseFloat($('.grand-total').text().replace('₱', '').replace(/,/g, ''));
        var change_amount = amountTendered - grandTotal;
        // Update the change field
        $('.change_amount').text(change_amount.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('[name="change_amount"]').val(change_amount);
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

    // Calculate and update change
    calculateChange();
}

</script>

