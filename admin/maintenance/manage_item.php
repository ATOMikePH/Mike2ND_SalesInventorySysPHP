<style>
    body {
        background-color: #f8f9fa; /* Set a light background color */
    }

    .container-fluid {
        background-color: #fff; 
        border-radius: 10px; 
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px; 
        margin-top: 20px; 
    }

    .image {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 5px; 
    }

    .form-group {
        margin-bottom: 20px; 
    }

    .btn-submit {
        background-color: #007bff; 
        color: #fff; 
    }

    .preview-image {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-top: 10px;
    }

    .select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: .25rem;
    min-height: calc(1.5em + .75rem + 2px);
}
</style>

<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `item_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container mt-1">
<div class="row justify-content-center">
        <div class="col-md-12">
            <form action="" id="item-form" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
                <div class="form-group">
    <label for="sku" class="control-label">SKU</label>
    <input type="text" name="sku" id="sku" class="form-control rounded-0" value="<?php echo isset($sku) ? $sku : ''; ?>" placeholder="This is auto-generated." readonly>
    
            </div>
                <div class="form-group">
                    <label for="name" class="control-label">Item Name without Brand and Unit Size</label>
                    <input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>">
                    <small class="form-text text-muted">eg. Gloss Latex White, LED, Gang Outlet.</small>
                </div>


                <div class="form-group">
                    <label for="brand_id" class="control-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="custom-select select2">
                        <option <?php echo !isset($brand_id) ? 'selected' : ''; ?> disabled>Select Brand</option>
                        <?php
                        $brand = $conn->query("SELECT * FROM `brands` where status = 1 order by `name` asc");
                        while ($row = $brand->fetch_assoc()) :
                        ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($brand_id) && $brand_id == $row['id'] ? "selected" : ""; ?>><?php echo $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category_id" class="control-label">Category</label>
                    <select name="category_id" id="category_id" class="custom-select select2">
                        <option <?php echo !isset($category_id) ? 'selected' : ''; ?> disabled>Select Category</option>
                        <?php
                        $category = $conn->query("SELECT * FROM `categories` where status = 1 order by `name` asc");
                        while ($row = $category->fetch_assoc()) :
                        ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? "selected" : ""; ?>><?php echo $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
    <label for="unit_id" class="control-label">Quantity Unit Size</label>
    <select name="unit_id[]" id="unit_id" class="custom-select select2" multiple>
        <option value="" disabled>Select Quantity Unit</option>
        <?php 
        $unit_ids = $conn->query("SELECT * FROM `units` WHERE status = 1 ORDER BY `name` ASC");
        while($row = $unit_ids->fetch_assoc()):
            $selected = "";
            // Check if the current unit ID is associated with the item
            if(isset($id)) {
                $associated_units = $conn->query("SELECT unit_id FROM `price_unit_order` WHERE item_id = '{$id}' AND status = 1");
                $associated_unit_ids = array();
                while($unit_row = $associated_units->fetch_assoc()) {
                    $associated_unit_ids[] = $unit_row['unit_id'];
                }
                if(in_array($row['id'], $associated_unit_ids)) {
                    $selected = "selected";
                }
            }
        ?>
        <option value="<?php echo $row['id'] ?>" <?php echo $selected ?>><?php echo $row['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <small class="form-text text-muted">Add Multiple at Once: Hold CTRL while choosing Unit.</small>
</div>

                <div class="form-group">
                    <label for="image" class="control-label">Item Image</label>
                    <input type="file" name="image" id="image" accept="image/*" onchange="displayImg(this,$(this))" class="form-control-file">
                    <div id="image-preview-container" class="mt-2">
                            <label>Image:</label>
                            <img src="<?php echo isset($image) ? $image :'' ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                    </div>
                    </div>
            </form>
        </div>
</div>
</div>    

<script>
    $(document).ready(function(){
        $('#image').on('change', function() {
            var file = $(this)[0].files[0];
            var fileType = file.type;
            var validImageTypes = ['image/jpeg', 'image/png', 'image/gif']; 


            if ($.inArray(fileType, validImageTypes) === -1) {
             
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image File Type!',
                    text: 'Please select an valid image file.',
                });
               
                $(this).val('');
            }
        });
    });
</script>
<style>
    img#cimg{
        height: 20vh;
        width: 20vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
</style>
<script>
    $(function(){
        $('.select2').select2({
            width:'resolve'
        })
    })
    function displayImg(input,_this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<script>
$(document).ready(function () {
    $('.select2').select2({ placeholder: "Please Select here", width: "relative" })

    $('#item-form').submit(function (e) {
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();


        // Validate Item Name
        var itemName = $('#name').val();
        if (containsInvalidCharacters(itemName)) {
            var errorMsg = $('<div>').addClass("alert alert-danger err-msg").text("Invalid characters in Item Name");
            _this.prepend(errorMsg);
            errorMsg.show('slow');
            end_loader();
            return;
        }

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_item",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err);
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
        });
    });

    // Function to check for invalid characters
    function containsInvalidCharacters(inputString) {
        var invalidCharacters = ['"', "'", '<', '>', '?']; // Add more if needed
        for (var i = 0; i < invalidCharacters.length; i++) {
            if (inputString.includes(invalidCharacters[i])) {
                return true;
            }
        }
        return false;
    }
});
</script>