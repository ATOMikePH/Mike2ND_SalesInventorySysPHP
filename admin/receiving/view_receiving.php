<?php 
$qry = $conn->query("SELECT * FROM receiving_list where id = '{$_GET['id']}'");
if($qry->num_rows >0){
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v;
    }
    if($from_order == 1){
        $po_qry = $conn->query("SELECT p.*,s.name as supplier FROM `purchase_order_list` p inner join `supplier_list` s on p.supplier_id = s.id where p.id= '{$form_id}' ");
        if($po_qry->num_rows >0){
            foreach($po_qry->fetch_array() as $k => $v){
                if(!isset($$k))
                $$k = $v;
            }
        }

    }else{
        $qry = $conn->query("SELECT b.*,s.name as supplier,p.po_code FROM back_order_list b inner join supplier_list s on b.supplier_id = s.id inner join purchase_order_list p on b.po_id = p.id  where b.id = '{$form_id}'");
            if($qry->num_rows >0){
                foreach($qry->fetch_array() as $k => $v){
                    if($k == 'id')
                    $k = 'bo_id';
                    if(!isset($$k))
                    $$k = $v;
                }
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
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
    <h4><i class="fas fa-check icon"></i> Received Order Details - <?php echo $po_code ?></h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">        
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_created" class="control-label text-info">Received Date and Time</label>
                        <div><?php echo isset($date_created) ? $date_created : '' ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="control-label text-info">From P.O. Code</label>
                    <div><?php echo isset($po_code) ? $po_code : '' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id" class="control-label text-info">Supplier</label>
                        <div><?php echo isset($supplier) ? $supplier : '' ?></div>
                    </div>
                </div>
                <?php if(isset($bo_id)): ?>
                <div class="col-md-6">
                    <label class="control-label text-info">FROM B.O. Code</label>
                    <div><?php echo isset($bo_code) ? $bo_code : '' ?></div>
                </div>    
                <?php endif; ?>
            </div>
  
            <h4 class="text-info">Item Received</h4>
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
                    $qry = $conn->query("SELECT s.*,i.name,i.sku, b.name AS brand FROM `stock_list` s inner join item_list i on s.item_id = i.id inner join brands b on i.brand_id = b.id where s.id in ({$stock_ids})");
                    while($row = $qry->fetch_assoc()):
                        $total += $row['total']
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
                        <td class="py-1 px-2 item">
    <strong><?php echo $row['brand']; ?> <?php echo $row['name']; ?></strong> <br>
    <?php echo ($row['unit']) ?>
</td>
                        <td class="py-1 px-2 text-right"><?php echo "₱ " . number_format($row['price'],2) ?></td>
                        <td class="py-1 px-2 text-right"><?php echo "₱ " . number_format($row['total'],2) ?></td>
                    </tr>

                    <?php endwhile; ?>
                    
                </tbody>
                <tfoot>
                <tr style="height: 20px;"></tr>
                <tr>
        <th class="text-right py-1 px-2" colspan="5"><strong>Total No. of Items</strong></th>
        <th class="text-right py-1 px-2 total-items">0</th>
    </tr>
                <tr>
                        <th class="text-right py-1 px-2" colspan="5">Sub Total</th>
                        <th class="text-right py-1 px-2 sub-total"><?php echo "₱ " . number_format($total,2)  ?></th>
                    </tr>
                    <tr>
                    <th class="text-right py-1 px-2" colspan="5">Discount (<?php echo isset($discount_perc) ? $discount_perc : 0 ?>%)</th>
                        <th class="text-right py-1 px-2 discount "><?php echo isset($discount) ? number_format($discount, 2) : 0 ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="5">Tax Inclusive (<?php echo isset($tax_perc) ? $tax_perc : 0 ?>%)</th>
                        <th class="text-right py-1 px-2 tax "><?php echo  isset($tax) ? number_format($tax,2) : 0 ?></th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="5">Grand Total</th>
                        <th class="text-right py-1 px-2 grand-total"><?php  echo  isset($amount) ? "₱ " . number_format($amount,2) : 0 ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="remarks" class="text-info control-label">Remarks</label>
            <p>
                <?php 
                if ($status > 0) {
                    echo ($status == 2) ? '✔️ RECEIVED ' : '✔️ PARTIALLY RECEIVED ';
                }
                echo isset($remarks) ? $remarks : '';
                ?>
            </p>
        </div>
    </div>
</div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Print</button>
        <a class="btn btn-flat btn-primary" href="<?php echo base_url.'/admin?page=receiving/manage_receiving&id='.(isset($id) ? $id : '') ?>">Edit</a>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=receiving' ?>">Back To List</a>
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
    $(function () {
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

    $('#print').click(function () {
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Received Order - Print View");
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
            '<h4 class="text-center">Received Order Receipt</h4>' +
            '</div><hr/>');

            var transactionDateTimeDiv = p.find('.col-md-6:contains("Received Date and Time")');
        transactionDateTimeDiv.find('label').remove();
        var transactionDateTimeData = transactionDateTimeDiv.html().trim();
        transactionDateTimeDiv.remove();
        _el.append('<div class="col-md-6 text-right">' +
            '<label class="control-label text-info">Received Date and Time</label>' +
            '<div>' + transactionDateTimeData + '</div>' +
            '</div>');

            p.find('.discount, .tax').removeClass('text-success text-warning').css('color', 'black');

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
    '<p class="text-center">( Encoder )</p>' +
    '</div>' +
    '</div>' +
    '<div style="width: 40%; float: right;">' +
    '<p class="text-center"><strong>' + <?php echo json_encode(strtoupper($_settings->info('owner_name'))); ?> + '</strong></p>' +
    '<div class="signature-line">' +
    '<p class="text-center">Signature over Printed Name</p>' +
    '<p class="text-center">( Managerr )</p>' +
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