<?php
require_once('../../config.php');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `vendors` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
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
    <form action="" id="vendor-form">

        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

        <div class="form-group">
            <label for="vendor_name">Vendor Name</label>
            <input type="text" name="vendor_name" id="vendor_name" class="form-control rounded-0" value="<?php echo isset($vendor_name) ? $vendor_name : ''; ?>">
        </div>

        <div class="form-group">
            <label for="TIN_NUM">TIN Number</label>
            <input type="text" name="TIN_NUM" id="TIN_NUM" class="form-control rounded-0" value="<?php echo isset($TIN_NUM) ? $TIN_NUM : ''; ?>">
        </div>

        <div class="form-group">
            <label for="vendor_address">Vendor Address</label>
            <textarea name="vendor_address" id="vendor_address" class="form-control rounded-0"><?php echo isset($vendor_address) ? $vendor_address : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="contact_person">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control rounded-0" value="<?php echo isset($contact_person) ? $contact_person : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control rounded-0" value="<?php echo isset($email) ? $email : ''; ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control rounded-0" value="<?php echo isset($phone) ? $phone : ''; ?>">
        </div>

    </form>
</div>


<script>
    $(document).ready(function () {
        $('#vendor-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_vendor",
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
                success: function (resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
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
</script>