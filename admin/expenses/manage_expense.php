<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `expenses` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>

<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: scale-down;
        object-position: center center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
    }

    /* Add your custom styles here */
</style>

<div class="container-fluid">
    <form action="" id="expense-form">

        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

        <div class="form-group">
            <label for="type">Expense Type</label>
            <select name="type" id="type" class="form-control rounded-0">
                  <option value="Miscellaneous" <?php echo (isset($type) && $type === 'Miscellaneous') ? 'selected' : ''; ?>>Miscellaneous</option>
        <option value="Travel Expense" <?php echo (isset($type) && $type === 'Travel Expense') ? 'selected' : ''; ?>>Travel Expenses</option>
        <option value="Utilities" <?php echo (isset($type) && $type === 'Utilities') ? 'selected' : ''; ?>>Utilities</option>
        <option value="Marketing Expenses" <?php echo (isset($type) && $type === 'Marketing Expenses') ? 'selected' : ''; ?>>Marketing Expenses</option>
        <option value="Employee Salary" <?php echo (isset($type) && $type === 'Employee Salary') ? 'selected' : ''; ?>>Employee Salary</option>
        <option value="Office Supplies" <?php echo (isset($type) && $type === 'Office Supplies') ? 'selected' : ''; ?>>Office Supplies</option>
        <option value="Equipment Purchase" <?php echo (isset($type) && $type === 'Equipment Purchase') ? 'selected' : ''; ?>>Equipment Purchase</option>
        <option value="Training and Development" <?php echo (isset($type) && $type === 'Training and Development') ? 'selected' : ''; ?>>Training and Development</option>
        <option value="Consulting Fees" <?php echo (isset($type) && $type === 'Consulting Fees') ? 'selected' : ''; ?>>Consulting Fees</option>
        <option value="Maintenance and Repairs" <?php echo (isset($type) && $type === 'Maintenance and Repairs') ? 'selected' : ''; ?>>Maintenance and Repairs</option>
		<option value="Other" <?php echo (isset($type) && $type === 'Other') ? 'selected' : ''; ?>>Other</option>
    </select>
    </div>

<div class="form-group">
    <label for="name">Expense Name</label>
    <input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>">
</div>

<div class="form-group">
    <label for="vendor">Vendor Name</label>
    <select name="vendor" id="vendor" class="form-control rounded-0">
        <option value="">Select Vendor</option> <!-- Default NULL choice -->
        <?php
// Fetch expense details including vendor
$expense_query = $conn->query("SELECT * FROM expenses WHERE id = '{$_GET['id']}' ");
if ($expense_query->num_rows > 0) {
    $expense = $expense_query->fetch_assoc();
    // Check if vendor is set
    if (isset($expense['vendor'])) {
        $vendor = $expense['vendor'];
    } else {
        $vendor = ''; // Set default value if vendor is not set
    }
}
?>
        <?php
        // Retrieve vendors from the database
        $vendor_query = $conn->query("SELECT * FROM Vendors");
        if ($vendor_query->num_rows > 0) {
            while ($vendor_row = $vendor_query->fetch_assoc()) {
                // Check if $vendor is set and matches the current vendor name
                $selected = (isset($vendor) && $vendor == $vendor_row['vendor_name']) ? 'selected' : '';
                echo "<option value='" . $vendor_row['vendor_name'] . "' $selected>" . $vendor_row['vendor_name'] . "</option>";
            }
        }
        ?>
    </select>
</div>
<div class="form-group">
    <label for="amount">Amount ₱</label>
    <input type="text" name="amount" id="amount" class="form-control rounded-0 text-end" value="<?php echo isset($amount) ? number_format($amount, 2) : ''; ?>">
</div>

<div class="form-group">
    <label for="remarks">Remarks</label>
    <select name="remarks" id="remarks" class="form-control rounded-0 select2">
     <option value="Paid" <?php echo (isset($remarks) && $remarks === 'Paid') ? 'selected' : ''; ?>>Paid</option>
        <option value="Not Paid" <?php echo (isset($remarks) && $remarks === 'Not Paid') ? 'selected' : ''; ?>>Not Paid</option>
        <option value="Pending" <?php echo (isset($remarks) && $remarks === 'Pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="Completed" <?php echo (isset($remarks) && $remarks === 'Completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="Cancelled" <?php echo (isset($remarks) && $remarks === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
        <option value="Refunded" <?php echo (isset($remarks) && $remarks === 'Refunded') ? 'selected' : ''; ?>>Refunded</option>
        <option value="On Hold" <?php echo (isset($remarks) && $remarks === 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
        </select>
        </div>

<div class="form-group">
    <label for="description">Comment</label>
    <input type="text" name="description" id="description" class="form-control form-control-sm" value="<?php echo isset($description) ? ($description) : ''; ?>">
</input>
</div>

        <div class="form-group">
            <label for="transaction_date">Transaction Date</label>
            <input type="date" class="form-control" id="transaction_date" name="transaction_date" value="<?php echo isset($transaction_date) ? $transaction_date : date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
    <label for="input_by">Input By :</label>
    <input type="text" name="input_by" class="form-control" value="<?php echo isset($input_by) ? $input_by : ($_settings->userdata('salutation').' '.$_settings->userdata('firstname').' '.$_settings->userdata('lastname')); ?>" readonly>
</div>

    </form>
</div>

<script>
   $(document).ready(function(){
    // Format amount on page load
    var defaultAmount = '<?php echo isset($amount) ? $amount : 0.00; ?>';
    $('#amount').val(parseFloat(defaultAmount).toFixed(2));

    // Input validation for amount field
    $('#amount').on('input', function () {
        var inputValue = $(this).val();
        // Remove any non-numeric and non-currency characters
        var sanitizedValue = inputValue.replace(/[^0-9₱.]/g, '');
        // Update the input field with the sanitized value
        $(this).val(sanitizedValue);
    });


        $('#expense-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_+"classes/Master.php?f=save_expense",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    if(typeof resp =='object' && resp.status == 'success'){
                        location.reload();
                    } else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        end_loader()
                    } else {
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp)
                    }
                }
            })
        })
    })

    $(document).ready(function(){
		 $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>