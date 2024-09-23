
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
    <style>
select {
  padding: 8px 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid #ccc; 
}

select:focus {
  outline: none;
  border-color: #007bff; 
}
	</style>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>


<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-user">   
                <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
                <div class="row">
                    <div class="form-group col-6"> <h5>User Information *</h5>
                        <label for="name">First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
                    </div>                    
                    <div class="form-group col-6"><h5>Login Information *</h5>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
                    </div>
                </div>
                <div class="row">
                     <div class="form-group col-6">
                        <label for="name">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
                     </div>
                     <div class="form-group col-6">
                        <label for="type">User Type</label>
                        <select name="type" id="type" class="custom-select" required>
                            <option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Administrator</option>
                            <option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>Accounting</option>
                            <option value="3" <?php echo isset($meta['type']) && $meta['type'] == 3 ? 'selected': '' ?>>Staff</option>
                        </select>
                    </div>

                </div>
                <div class="row">
                <div class="form-group col-6">
                        <label for="salutation">Salutation</label>
                        <select name="salutation" id="salutation" class="form-control" required>
                            <option value="Mr." <?php echo isset($meta['salutation']) && $meta['salutation'] == 'Mr.' ? 'selected' : ''; ?>>Mr.</option>
                            <option value="Mrs." <?php echo isset($meta['salutation']) && $meta['salutation'] == 'Mrs.' ? 'selected' : ''; ?>>Mrs.</option>
                            <option value="Ms." <?php echo isset($meta['salutation']) && $meta['salutation'] == 'Ms.' ? 'selected' : ''; ?>>Ms.</option>
                            <option value="Prof." <?php echo isset($meta['salutation']) && $meta['salutation'] == 'Prof.' ? 'selected' : ''; ?>>Prof.</option>
                            <option value="Dr." <?php echo isset($meta['salutation']) && $meta['salutation'] == 'Dr.' ? 'selected' : ''; ?>>Dr.</option>
                            <option value="" <?php echo isset($meta['salutation']) && $meta['salutation'] == '' ? 'selected' : ''; ?>>No Salutation</option>
                        </select>
                    </div>
                    <?php if(isset($_GET['id'])): ?>
    <div class="form-group col-6">
    <label for="password">Change Password</label>
    <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
    <small class="text-info"><i>Note: Leave this blank if you don't want to change the password.</i></small>
    </div>
<?php else: ?>
    <div class="form-group col-6">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
    <small class="text-info"><i>Note: Retype to Confirm Password to verify.</i></small>
    </div>
<?php endif; ?>
                </div>
                <div class="row">
                <div class="form-group col-6">
                        <label for="" class="control-label">Avatar</label>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))" accept="image/*">
                          <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <?php if(isset($_GET['id'])): ?>

<?php else: ?>
    <div class="form-group col-6">
    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
</div>
<?php endif; ?>
                    
                </div>
                <div class="row">
                    <div class="form-group col-6 d-flex justify-content-center align-items-center">
                        <img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                    </div> 
                </div>
                
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row justify-content-end">
                <button class="btn btn-sm btn-primary mr-2" form="manage-user"><i class="fas fa-save"></i> Save</button>
                <a class="btn btn-sm btn-secondary" href="./?page=user/list">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#customFile').on('change', function() {
            var file = $(this)[0].files[0];
            var fileType = file.type;
            var validImageTypes = ['image/jpeg', 'image/png', 'image/gif']; // Listahan ng mga uri ng mga file ng imahe na tinatanggap

            // Suriin kung ang uri ng file ay isa sa mga tinanggap na uri ng imahe
            if ($.inArray(fileType, validImageTypes) === -1) {
                // Kung hindi, ipakita ang error message gamit ang SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Avatar Image File Type!',
                    text: 'Please select an valid image file.',
                });
                // Clear the file input field
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
    $('#manage-user').submit(function(e){
    e.preventDefault();
    var password = $('#password').val();
    var confirm_password = $('#confirm_password').val();

    // Check if the passwords match
    if(password !== confirm_password && password !== '')  {
        $('#msg').html('<div class="alert alert-danger">Passwords do not match. Please try again.</div>');
        $("html, body").animate({ scrollTop: 0 }, "fast");
        return; // Prevent form submission
    }

        var _this = $(this)
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Users.php?f=save',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    location.href = './?page=user/list';
                }else{
                    $('#msg').html('<div class="alert alert-danger">Username already exists</div>')
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                }
                end_loader()
            }
        })
    })
</script>