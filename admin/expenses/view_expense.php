<?php require_once('./../../config.php') ?>

<?php 
    $item_id = isset($_GET['id']) ? $_GET['id'] : null;

    // Query: Get item details with category
    $qry = $conn->query("SELECT * FROM `expenses` WHERE id = '$item_id'");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        foreach ($row as $k => $v) {
            $$k = $v;
        }
    } else {
        // Handle case where item is not found
        echo "Data not found!";
    }
?>

<style>
    #uni_modal .modal-footer {
        display: none;
    }

    #print_out {
        background-color: #fff;
        padding: 20px;
        border: 1px solid #ccc;
    }

    .text-center {
        text-align: center;
    }

    img {
        max-width: 80%; /* Reducing the logo size */
        display: block;
        margin: 0 auto; /* Centering the logo */
    }

    h4 {
        margin-bottom: 10px; /* Adjusting margin for company name */
    }

    .tin-number {
        margin-bottom: 5px; /* Adjusting margin for TIN number */
    }

    .company-address {
        margin-bottom: 20px; /* Adjusting margin for company address */
    }

    dl {
        margin-bottom: 0;
    }

    dt, dd {
        margin-bottom: 5px;
    }

    .form-group {
        margin-top: 20px;
    }

    #printButton {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
    }
</style>

<div class="container-fluid" id="print_out">
    <div id='transaction-printable-details' class='position-relative'>
        <div class="text-center">
        </div>
        <div class="row">
            <fieldset class="w-100">
                <div class="col-12">
                    <dl class="dl-horizontal">
                        <dt class="text-info">Type:</dt>
                        <dd class="pl-3"><?php echo $type; ?></dd>
                        <dt class="text-info">Expense Name:</dt>
                        <dd class="pl-3"><?php echo $name; ?></dd>
                        <dt class="text-info">Vendor Name:</dt>
                        <dd class="pl-3"><?php echo $vendor; ?></dd>
                        <dt class="text-info">Remarks:</dt>
                        <dd class="pl-3"><?php echo isset($remarks) ? $remarks : ''; ?></dd>
                        <dt class="text-info">Amount:</dt>
                        <dd class="pl-3"><?php echo isset($amount) ? $amount : ''; ?></dd>
                        <dt class="text-info">Comment:</dt>
                        <dd class="pl-3"><?php echo $description; ?></dd>
                        <dt class="text-info">Transaction Date:</dt>
                        <dd class="pl-3"><?php echo isset($transaction_date) ? $transaction_date : ''; ?></dd>
                        <dt class="text-info">Input By:</dt>
                        <dd class="pl-3"><?php echo $input_by; ?></dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Print</button>
        <button class="btn btn-dark btn-flat" type="button" id="cancel" data-dismiss="modal">Close</button>
        
        </div>
    </div>
</div>

<script>
$(function(){
    $('#print').click(function(){
        start_loader();
        var _el = $('<div>');
        var _head = $('head').clone();
        _head.find('title').text("Expenses Record - Print View");
        var p = $('#print_out').clone();
        p.find('tr.text-light').removeClass("text-light bg-navy");

        _el.append(_head);

        // Updated HTML structure for print view
        _el.append('<div class="receipt-container">'+
            '<div class="text-center header">'+
                '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" alt="Company Logo">'+
                '<h4><?php echo $_settings->info('name') ?></h4>'+
                '<div class="tin-number">TIN No. <?php echo $_settings->info('tin_num') != false ? $_settings->info('tin_num') : '000-000-000-000'; ?></div>'+
                '<div class="company-address"><?php echo $_settings->info('company_address') != false ? $_settings->info('company_address') : 'Castillejos, Zambales PH 2208'; ?></div>'+
                '<div class="company-address"><?php echo $_settings->info('phone_num') != false ? $_settings->info('phone_num') : '(+63) 912 345 6789'; ?> | <?php echo $_settings->info('company_email') != false ? $_settings->info('company_email') : 'formatNiMichael@gmail.com'; ?> </div>'+
                '<div><hr></div>'+
                '<h4>Expenses Record Receipt</h4>'+
                '</div><hr/>');

        // Incorporate expenses details
        _el.append('<div class="details">'+
        '<p><strong>Transaction Date:</strong> '+<?php echo isset($transaction_date) ? json_encode($transaction_date) : 'N/A'; ?>+'</p>'+
            '<p><strong>Type:</strong> '+<?php echo json_encode($type); ?>+'</p>'+
            '<p><strong>Name:</strong> '+<?php echo json_encode($name); ?>+'</p>'+
            '<p><strong>Remarks:</strong> '+<?php echo isset($remarks) ? json_encode($remarks) : 'N/A'; ?>+'</p>'+
            '<p><strong>Amount:</strong> '+<?php echo isset($amount) ? json_encode($amount) : 'N/A'; ?>+'</p>'+
            '<p><strong>Comment:</strong> '+<?php echo json_encode($description); ?>+'</p>'+
            '<div><hr></div>'+
        '</div>');

        // Adding spacing
        _el.append('<div style="margin-top: 80px;"></div>');

        _el.append('<style>'+
            '@media print {'+
                '.signature-line {'+
                    'border-top: 2px solid #000;'+
                    'margin-top: 10px;'+
                '}'+
            '}'+
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
    '<p class="text-center">( Owner )</p>' +
    '</div>' +
    '</div>' +
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
    });
});
</script>