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
<?php 
$qry = $conn->query("SELECT r.*,s.name as supplier FROM supp_returnlist r inner join supplier_list s on r.supplier_id = s.id  where r.id = '{$_GET['id']}'");
if($qry->num_rows >0){
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4><i class="fas fa-undo icon"></i> Supplier Return Order Record - <?php echo $return_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Supplier Return Code</label>
                    <div><?php echo isset($return_code) ? $return_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transaction_datetime" class="control-label text-info">Last Purchased Date and Time</label>
                        <div><?php echo isset($transaction_datetime) ? $transaction_datetime : '' ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="input_by" class="control-label text-info">Input By :</label>
    <input type="text" name="input_by" class="form-control form-control-sm rounded-0" value="<?php echo isset($input_by) ? $input_by : '' ?>" readonly>
</div>
<div class="col-md-6">
                    <div class="form-group">
                        <label for="return_date" class="control-label text-info">Return Date</label>
                        <div><?php echo isset($return_date) ? $return_date : '' ?></div>
                    </div>
                    </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="control-label text-info">Supplier</label>
                        <div><?php echo isset($supplier) ? $supplier : '' ?></div>
                    </div>
                </div>
            </div>
            <h4 class="text-info">Item Orders</h4>
            <table class="table table-striped table-bordered" id="list">
            <colgroup>
                        <col width="12%">
                        <col width="5%">
                        <col width="15%">
                        <col width="25%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                <thead>
                <tr class="text-light bg-navy">
                    <th class="text-center py-1 px-2">QTY</th>
                    <th class="text-center py-1 px-2"></th>
                    <th class="text-center py-1 px-2">SKU</th>
                    <th class="text-center py-1 px-2">Item Unit Description</th>
                    <th class="text-center py-1 px-2">Price per Unit</th>
                    <th class="text-center py-1 px-2">Total Price</th>
                        </tr>
                </thead>
                <tbody>

                                    <?php 
$total = 0;
$qry = $conn->query("SELECT s.*,i.name,i.sku, b.name AS brand  FROM `stock_list` s inner join item_list i on s.item_id = i.id inner join brands b on i.brand_id = b.id where s.id in ({$stock_ids})");
while($row = $qry->fetch_assoc()):
    $total += $row['total'];
?>
                    <style>
    .quantity-sku-multiply {
    font-size: 20px; /* Adjust the size as needed */
    font-weight: bold; /* Optional */
    color: #000; /* Optional, adjust the color as needed */
    padding: 0 5px; /* Optional, adjust the spacing as needed */
}
</style>
                    <tr>
                        <td class="py-1 px-2 text-center"><?php echo number_format($row['quantity']) ?></td>
                        <td class="py-1 px-2 text-center quantity-sku-multiply">X</td>
                        <td class="py-1 px-2 text-center"><?php echo ($row['sku']) ?></td>
                        <td class="py-1 px-2">
        <?php if ($row['brand'] !== "No Brand"): ?>
            <strong><?php echo $row['brand'] . ' ' . $row['name']; ?></strong> <br> <?php echo ($row['unit']) ?>
        <?php else: ?>
            <strong><?php echo $row['name']; ?></strong> <br> <?php echo ($row['unit']) ?>
        <?php endif; ?>
    </td>
                        <td class="py-1 px-2 text-right"><?php echo "₱ " . number_format($row['price'],2) ?></td>
                        <td class="py-1 px-2 text-right"><?php echo "₱ " . number_format($row['total'],2) ?></td>
                    </tr>

                    <?php endwhile; ?>
                    
                    </tbody>
                <tfoot>
    <tr style="height: 20px;"></tr>
    <tr>
        <th class="text-center py-1 px-2" colspan="4"><strong>Mode of Payment</strong></th>
        <!-- <th class="text-right py-1 px-2" colspan="1"><strong>Total No. of Items</strong></th>
        <th class="text-right py-1 px-2 total-items">0</th> -->
    </tr>
    <tr> 
    <td class="text-center" colspan="4">
    <?php 
// Baguhin ang format ng $p_specify gamit ang ucwords()
$p_specify_formatted = ucwords($p_specify);
?>

<?php if(isset($p_mode) && strtolower($p_mode) === 'cash' && isset($p_specify) && strtolower($p_specify) === 'delivery'): ?>
    <label for="cash">Cash on Delivery</label>
<?php elseif(isset($p_mode) && strtolower($p_mode) === 'cash' && isset($p_specify) && strtolower($p_specify) === 'pickup'): ?>
    <label for="cash">Cash on Pickup</label>
<?php elseif(isset($p_mode) && strtolower($p_mode) === 'e-wallet' && isset($p_specify) && strtolower($p_specify) === 'gcash'): ?>
    <label for="e-wallet">E-Wallet (Gcash)</label>
<?php elseif(isset($p_mode) && strtolower($p_mode) === 'e-wallet' && isset($p_specify) && strtolower($p_specify) === 'paymaya'): ?>
    <label for="e-wallet">E-Wallet (Paymaya)</label>
<?php endif; ?>
        </td>
        <th class="text-right py-1 px-2" colspan="1" style="vertical-align: middle;">Total Sales (VAT Inclusive)</th>
        <th class="text-right py-1 px-2 sub-tax" style="vertical-align: middle;"><?php echo isset($total) && isset($tax) ? number_format($total + $tax, 2) : '0.00'; ?></th>
    </tr>
    <tr>
        <th class="text-right py-1 px-2" colspan="5">Less: VAT (<?php echo isset($tax_perc) ? $tax_perc : 0 ?>%)</th>
        <th class="text-right py-1 px-2 tax"><?php echo isset($tax) ? number_format($tax,2) : 0.00 ?></th>
    </tr>
    <tr>
        <th class="text-right py-1 px-2" colspan="5">Gross Amount: Net of VAT</th>
        <th class="text-right py-1 px-2 sub-total"><?php echo isset($total) ? number_format($total,2) : 0.00  ?></th>
    </tr>
    <tr>
    <th class="text-right py-1 px-2" colspan="3">VAT Amount (<?php echo isset($tax_perc) ? $tax_perc : 0 ?>%)</th>
        <th class="text-right py-1 px-2 tax"><?php echo  isset($tax) ? number_format($tax,2) : 0.00 ?></th>
        <th class="text-right py-1 px-2" colspan="1">Less: Discount (<?php echo isset($discount_perc) ? $discount_perc : 0 ?>%)</th>
             <th class="text-right py-1 px-2 discount"><?php echo isset($discount) ? number_format($discount, 2) : 0.00 ?></th>
    </tr>
    <tr>
        <th class="text-right py-1 px-2" colspan="5"><strong>TOTAL AMOUNT DUE (PHP)</strong></th>
        <th class="text-right py-1 px-2 amount_tendered"><strong><?php echo  isset($amount) ? number_format($amount,2) : 0.00 ?></strong></th>
    </tr>
    <tr>
</tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="remarks" class="text-info control-label">Reason for Returning Items</label>
                        <p><?php echo isset($remarks) ? $remarks : '' ?></p>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Print</button>
        <a class="btn btn-flat btn-primary" href="<?php echo base_url.'/admin?page=supp_return/manage_return&id='.(isset($id) ? $id : '') ?>">Edit</a>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=supp_return' ?>">Back To List</a>
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
        <td class="py-1 px-2 text-center unit">
        </td>
        <td class="py-1 px-2 item">
        </td>
        <td class="py-1 px-2 text-right cost">
        </td>
        <td class="py-1 px-2 text-right total">
        </td>
    </tr>
</table>

<script>

$(document).ready(function() {
    $('input[type="checkbox"]').click(function() {
        var $checkboxes = $('input[name="p_mode"]');
        $checkboxes.not(this).prop('checked', false);
    });
    $('[name="discount_perc"], [name="tax_perc"]').on('input', function() {
    calc();
    calculateTotalItems();
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

        updateTotalItemsCount()

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
        $('#add_to_list').click(function () {
        var item = $('#item_id').val();
        var availableQuantity = $('#item_id option:selected').data('available');
        var qty = $('#qty').val() > 0 ? $('#qty').val() : 0;
        var unit = $('#unit').val();
        var price = costs[item] || 0;
        var total = parseFloat(qty) * parseFloat(price);
        

        // Check if there is enough stock in the inventory
        if (item && qty > 0 && unit && items[item] && availableQuantity < qty) {
            alert_toast('Insufficient stock in the inventory. Please check first the stocks then try again.', 'error');
            return false;
        }

    var item_name = items[item].name || 'N/A';
    var item_description = items[item].description || 'N/A';
    var tr = $('#clone_list tr').clone();
    if (item == '' || qty == '' || unit == '') {
        alert_toast('Form Item textfields are required.', 'warning');
        return false;
    }
    if ($('table#list tbody').find('tr[data-id="' + item + '"]').length > 0) {
        alert_toast('Item is already exists on the list.', 'error');
        return false;
    }
    tr.find('[name="item_id[]"]').val(item);
    tr.find('[name="unit[]"]').val(unit);
    tr.find('[name="qty[]"]').val(qty);
    tr.find('[name="price[]"]').val(price);
    tr.find('[name="total[]"]').val(total);
    tr.attr('data-id', item);
    tr.find('.qty .visible').text(qty);
    tr.find('.unit').text(unit);
    tr.find('.item').html('<strong>' + item_name + '</strong><br/>' + item_description);
    tr.find('.cost').text("₱ " + parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
    tr.find('.total').text("₱ " + parseFloat(total).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }))
    $('table#list tbody').append(tr);
    calc();
    calculateTotalSales();
    // Call the updateTotalItemsCount function after adding a new row
    updateTotalItemsCount();
    $('#item_id').val('').trigger('change');
    $('#qty').val('');
    $('#unit').val('');
    tr.find('.rem_row').click(function () {
        rem($(this));
        // Call the updateTotalItemsCount function after adding a new row
    updateTotalItemsCount();
    calculateTotalSales();
    });

    $('[name="discount_perc"],[name="tax_perc"]').on('input', function () {
        calc();
        updateTotalItemsCount()
    });
    $('#supplier_id').attr('readonly', 'readonly');
});

    function calculateTotalSales() {
    var subTotal = parseFloat($('tfoot .sub-total').text().replace('₱', '').replace(/,/g, ''));
    var tax = parseFloat($('tfoot .tax').text().replace('₱', '').replace(/,/g, ''));
    var totalSales = subTotal + tax;
    $('tfoot .sub-tax').text( totalSales.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}


    function calc(){
        var sub_total = 0;
        var grand_total = 0;
        var discount = 0;
        var tax = 0;
        $('table#list tbody tr').each(function(){
            qty = $(this).find('[name="qty[]"]').val()
            price = $(this).find('[name="price[]"]').val()
            total = parseFloat(price) * parseFloat(qty)
            $(this).find('[name="total[]"]').val(total)
            $(this).find('.total').text("₱ " + parseFloat(total).toLocaleString('en-US',{style:'decimal',minimumFractionDigits: 2, maximumFractionDigits: 2}))
        })
        $('table#list tbody input[name="total[]"]').each(function(){
            sub_total += parseFloat($(this).val())
        })
        $('table#list tfoot .sub-total').text( parseFloat(sub_total).toLocaleString('en-US', {style:'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2}))
        var discount =   sub_total * (parseFloat($('[name="discount_perc"]').val()) /100)
        sub_total = sub_total - discount;
        var tax =   sub_total * (parseFloat($('[name="tax_perc"]').val()) /100)
        grand_total = sub_total + tax
        $('.discount').text(parseFloat(discount).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="discount"]').val(parseFloat(discount))
        $('.tax').text(parseFloat(tax).toLocaleString('en-US',{style:'decimal',maximumFractionDigit:2}))
        $('[name="tax"]').val(parseFloat(tax))
        $('table#list tfoot .grand-total').text( parseFloat(grand_total).toLocaleString('en-US', {style:'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2}))
        $('[name="amount"]').val(parseFloat(grand_total))
        calculateTotalSales();
        updateTotalItemsCount()
    }

});

<script>    
    $(function(){
        $('#print').click(function () {
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Supplier Return Order - Print View");
        var p = $('#print_out').clone();
        p.find('tr.text-light').removeClass("text-light bg-navy");

        p.find('.col-md-6:contains("Input By :")').remove();

        
        _el.append(_head);
        
        _el.append('<div class="receipt-container">' +
            '<div class="text-center header">' +
            '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" alt="Company Logo">' +
            '<h4><?php echo $_settings->info('name') ?></h4>' +
            '<div class="tin-number">TIN No. <?php echo $_settings->info('tin_num') != false ? $_settings->info('tin_num') : '000-000-000-000'; ?></div>' +
            '<div class="company-address"><?php echo $_settings->info('company_address') != false ? $_settings->info('company_address') : 'Castillejos, Zambales PH 2208'; ?></div>' +
            '<div class="company-address"><?php echo $_settings->info('phone_num') != false ? $_settings->info('phone_num') : '(+63) 912 345 6789'; ?> | <?php echo $_settings->info('company_email') != false ? $_settings->info('company_email') : 'formatNiMichael@gmail.com'; ?> </div>' +
            '<div><hr></div>' +
            '<h4 class="text-center">Supplier Return Order Receipt</h4>'+
            '</div><hr/>');

          var transactionDateTimeDiv = p.find('.col-md-6:contains("Last Purchased Date and Time")');
        transactionDateTimeDiv.find('label').remove();
        var transactionDateTimeData = transactionDateTimeDiv.html().trim();
        transactionDateTimeDiv.remove();
        _el.append('<div class="col-md-6 text-right">' +
            '<label class="control-label text-info">Last Purchased Date and Time</label>' +
            '<div>' + transactionDateTimeData + '</div>' +
            '</div>');

            var transactionDateTimeDiv = p.find('.col-md-6:contains("Return Date")');
        transactionDateTimeDiv.find('label').remove();
        var transactionDateTimeData = transactionDateTimeDiv.html().trim();
        transactionDateTimeDiv.remove();
        _el.append('<div class="col-md-6 text-right">' +
            '<label class="control-label text-info">Return Date</label>' +
            '<div>' + transactionDateTimeData + '</div>' +
            '</div>');
            var salesCodeDiv = p.find('.col-md-6:contains("Supplier Return Code")');
        salesCodeDiv.find('label').remove();
        var salesCodeData = salesCodeDiv.html().trim();
        salesCodeDiv.remove();
        _el.append('<div class="col-md-3 text-left-justi">' +
            '<label class="control-label text-info">Supplier Return Code</label>' +
            '<div>' + salesCodeData + '</div>' +
            '</div>');

            var salesCodeDiv = p.find('.col-md-6:contains("Supplier")');
        salesCodeDiv.find('label').remove();
        var salesCodeData = salesCodeDiv.html().trim();
        salesCodeDiv.remove();
        _el.append('<div class="col-md-6 text-left">' +
            '<label class="control-label text-info">Supplier</label>' +
            '<div>' + salesCodeData + '</div>' +
            '</div>');
  

            _el.append(p.html())

        _el.append('<div style="margin-top: 80px;"></div>');

        _el.append('<style>' +
            '@media print {' +
            '.signature-line {' +
            'border-top: 2px solid #000;' +
            'margin-top: 10px;' +
            '}' +
            '}' +
            '</style>');

        _el.append('<div class="signature-lines">' +
            '<div style="width: 40%; float: left;">' +
            '<p class="text-center"><strong>' + <?php echo json_encode(strtoupper($input_by)); ?> + '</strong></p>' +
            '<div class="signature-line">' +
            '<p class="text-center">Signature over Printed Name</p>' +
            '<p class="text-center">Prepared By</p>' +
            '</div>' +
            '</div>' +
            '<div style="width: 40%; float: right;">' +
            '<p class="text-center"><strong>' + <?php echo json_encode(strtoupper($_settings->info('owner_name'))); ?> + '</strong></p>' +
            '<div class="signature-line">' +
            '<p class="text-center">Signature over Printed Name</p>' +
            '<p class="text-center">Approved By</p>' +
            '</div>' +
            '</div>' +
            '<div style="clear: both;"></div>' +
            '</div>');

        _el.append('<div class="text-center" style="margin-top: 30px;">' +
            '<p class="text-center"><?php echo json_encode($_settings->info('receipt_footer')); ?></p>' +
            '</div>');

        var nw = window.open("","","width=1200,height=900,left=250,location=no,titlebar=yes");
        nw.document.write(_el.html());
        nw.document.close();

        setTimeout(() => {
            nw.print();
            setTimeout(() => {
                nw.close();
                end_loader();
            }, 200);
        }, 500);
    })
});
    
</script>