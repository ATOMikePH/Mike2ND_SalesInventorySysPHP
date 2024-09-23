<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT r.*,s.name as supplier FROM return_list r inner join supplier_list s on r.supplier_id = s.id  where r.id = '{$_GET['id']}'");
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
                    <legend class="text-info">Item Form</legend>
                    <div class="row justify-content-center align-items-end">
                    <?php 
$item_arr = array();
$cost_arr = array();
$item = $conn->query("SELECT sp.*, il.* FROM `supplier_product` sp INNER JOIN `item_list` il ON sp.product_id = il.id WHERE il.status = 1 AND sp.status IS NOT NULL AND sp.status != 0 ORDER BY il.name ASC");
while($row=$item->fetch_assoc()):
    $item_arr[$row['supplier_id']][$row['product_id']] = $row;
    $cost_arr[$row['supplier_id']][$row['product_id']] = $row['supplier_price'];
endwhile;
?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="item_id" class="control-label">Item</label>
                                <select id="item_id" class="custom-select select2">
                                <option disabled selected></option>
                                <?php
                                $items = $conn->query("SELECT * FROM `supplier_product` sp INNER JOIN `item_list` il ON sp.product_id = il.id WHERE il.status = 1 ORDER BY il.name ASC");
                                while ($row = $items->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unit" class="control-label">Unit</label>
                                <input type="text" class="form-control rounded-0" id="unit">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="qty" class="control-label">Qty</label>
                                <input type="number" step="any" class="form-control rounded-0" id="qty">
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="form-group">
                                <button type="button" class="btn btn-flat btn-sm btn-primary" id="add_to_list">Add to List</button>
                            </div>
                        </div>
                </fieldset>
                <hr>
                <table class="table table-striped table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                    </colgroup>
                    <thead>
                        <tr class="text-light bg-navy">
                            <th class="text-center py-1 px-2"></th>
                            <th class="text-center py-1 px-2">Qty</th>
                            <th class="text-center py-1 px-2">Unit</th>
                            <th class="text-center py-1 px-2">Item</th>
                            <th class="text-center py-1 px-2">Sub Cost</th>
                            <th class="text-center py-1 px-2">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        if(isset($id)):
                        $qry = $conn->query("SELECT s.*,i.name,i.description FROM `stock_list` s inner join item_list i on s.item_id = i.id where s.id in ({$stock_ids})");
                        while($row = $qry->fetch_assoc()):
                            $total += $row['total']
                        ?>
                        <tr>
                            <td class="py-1 px-2 text-center">
                                <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
                            </td>
                            <td class="py-1 px-2 text-center qty">
                                <span class="visible"><?php echo number_format($row['quantity']); ?></span>
                                <input type="hidden" name="item_id[]" value="<?php echo $row['item_id']; ?>">
                                <input type="hidden" name="unit[]" value="<?php echo $row['unit']; ?>">
                                <input type="hidden" name="qty[]" value="<?php  echo $row['quantity']; ?>">
                                <input type="hidden" name="price[]" value="<?php echo $row['price']; ?>">
                                <input type="hidden" name="total[]" value="<?php echo $row['total']; ?>">
                            </td>
                            <td class="py-1 px-2 text-center unit">
                            <?php echo $row['unit']; ?>
                            </td>
                            <td class="py-1 px-2 item">
    <strong><?php echo $row['name']; ?></strong> <br>
    <?php echo $row['description']; ?>
</td>
                            <td class="py-1 px-2 text-right cost">
                            <?php echo "₱ " . number_format($row['price'],2); ?>
                            </td>
                            <td class="py-1 px-2 text-right total">
                            <?php echo "₱ " . number_format($row['total'],2); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right py-1 px-2" colspan="5">Grand Total
                                <input type="hidden" name="amount" value="<?php echo isset($amount) ? "₱ " . number_format($amount,2) : 0 ?>">
                            </th>
                            <th class="text-right py-1 px-2 grand-total">0</th>
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
        <button class="btn btn-flat btn-primary" type="submit" form="return-form">Save</button>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url.'/admin?page=return' ?>">Cancel</a>
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
var items = <?php echo json_encode($item_arr); ?>;
var costs = <?php echo json_encode($cost_arr); ?>;
var supplierId = <?php echo isset($supplier_id) ? $supplier_id : 'null'; ?>;

$(function () {
    $('.select2').select2({
        placeholder: "Please select here",
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
                var opt = $('<option>').attr('value', id).text(row.name);
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

    $('#add_to_list').click(function () {
        var supplier = $('#supplier_id').val();
        var item = $('#item_id').val();
        var qty = $('#qty').val() > 0 ? $('#qty').val() : 0;
        var unit = $('#unit').val();
        var price = costs[supplier] && costs[supplier][item] ? costs[supplier][item] : 0;
        var total = parseFloat(qty) * parseFloat(price);

        var item_name = items[supplier][item].name || 'N/A';
        var item_description = items[supplier][item].description || 'N/A';

        if (item === '' || qty === '' || unit === '') {
            alert_toast('Form Item textfields are required.', 'warning');
            return false;
        }

        if ($('table#list tbody').find('tr[data-id="' + item + '"]').length > 0) {
            alert_toast('Item is already exists on the list.', 'error');
            return false;
        }

        var tr = $('#clone_list tr').clone();

        tr.find('[name="item_id[]"]').val(item);
        tr.find('[name="unit[]"]').val(unit);
        tr.find('[name="qty[]"]').val(qty);
        tr.find('[name="price[]"]').val(price);
        tr.find('[name="total[]"]').val(total);
        tr.attr('data-id', item);

        tr.find('.qty .visible').text(qty);
        tr.find('.unit').text(unit);
        tr.find('.item').html('<strong>' + item_name + '</strong><br/>' + item_description);
        tr.find('.cost').text("₱ " + parseFloat(price).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        tr.find('.total').text("₱ " + parseFloat(total).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('table#list tbody').append(tr);
        calc();

        $('#item_id').val('').trigger('change');
        $('#qty').val('');
        $('#unit').val('');

        tr.find('.rem_row').click(function () {
            rem($(this));
        });

        $('[name="discount_perc"],[name="tax_perc"]').on('input', function () {
            calc();
        });

        $('#supplier_id').prop('readonly', true);
    });
        $('#return-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_return",
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
						location.replace(_base_url_+"admin/?page=return/view_return&id="+resp.id);
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

        if('<?php echo isset($id) && $id > 0 ?>' == 1){
            calc()
            $('#supplier_id').trigger('change')
            $('#supplier_id').attr('readonly','readonly')
            $('table#list tbody tr .rem_row').click(function(){
                rem($(this))
            })
        }
    })
    function rem(_this){
        _this.closest('tr').remove()
        calc()
        if($('table#list tbody tr').length <= 0)
            $('#supplier_id').removeAttr('readonly')

    }
    function calc() {
    var grand_total = 0;
    $('table#list tbody input[name="total[]"]').each(function(){
        grand_total += parseFloat($(this).val());
    });

    var formatted_total = "₱ " + grand_total.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 });

    $('table#list tfoot .grand-total').text(formatted_total);
    $('[name="amount"]').val(formatted_total);
}
</script>