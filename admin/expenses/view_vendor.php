<?php require_once('./../../config.php') ?>

<?php 
    $vendor_id = isset($_GET['id']) ? $_GET['id'] : null;

    // Query: Get vendor details
    $qry = $conn->query("SELECT * FROM `vendors` WHERE id = '$vendor_id'");
    if ($qry->num_rows > 0) {
        $vendor = $qry->fetch_assoc();
        foreach ($vendor as $k => $v) {
            $$k = $v;
        }
    } else {
        // Handle case where vendor is not found
        echo "Data not found!";
    }
?>

<style>
    #uni_modal .modal-footer {
        display: none;
    }
</style>

<div class="container-fluid" id="print_out">
    <div id='vendor-printable-details' class='position-relative'>
        <div class="row">
            <fieldset class="w-100">
                <div class="col-12">
                    <dl class="dl-horizontal">
                        <dt class="text-info">Vendor Name:</dt>
                        <dd class="pl-3"><?php echo $vendor_name; ?></dd>
                        <dt class="text-info">TIN Number:</dt>
                        <dd class="pl-3"><?php echo isset($TIN_NUM) ? $TIN_NUM : ''; ?></dd>
                        <dt class="text-info">Vendor Address:</dt>
                        <dd class="pl-3"><?php echo isset($vendor_address) ? $vendor_address : ''; ?></dd>
                        <dt class="text-info">Contact Person:</dt>
                        <dd class="pl-3"><?php echo isset($contact_person) ? $contact_person : ''; ?></dd>
                        <dt class="text-info">Email:</dt>
                        <dd class="pl-3"><?php echo isset($email) ? $email : ''; ?></dd>
                        <dt class="text-info">Phone:</dt>
                        <dd class="pl-3"><?php echo isset($phone) ? $phone : ''; ?></dd>
                    </dl>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-12">
        <div class="d-flex justify-content-end align-items-center">
            <button class="btn btn-dark btn-flat" type="button" id="cancel" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
    })
</script>