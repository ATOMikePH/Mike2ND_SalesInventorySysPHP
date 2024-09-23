<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: scale-down;
		border-radius: 100% 100%;
	}
	img#cimg2{
		height: 50vh;
		width: 100%;
		object-fit: contain;
		/* border-radius: 100% 100%; */
	}
</style>
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

        h5 {
            font-weight: 700;
            margin-bottom: 20px;
        }
    </style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
		<h5><i class="fas fa-info-circle icon"></i> System Information</h5>
		</div>
		<div class="card-body">
		<form action="" id="system-frm" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
			<div id="msg" class="form-group"></div>
			<div class="form-group">
				<label for="name" class="control-label">System Name</label>
				<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo $_settings->info('name') ?>">
			</div>
			<!-- <div class="form-group">
				<label for="short_name" class="control-label">System Short Name</label>
				<input type="text" class="form-control form-control-sm" name="short_name" id="short_name" value="<?php echo  $_settings->info('short_name') ?>">
		</div> -->
		<div class="form-group">
				<label for="owner_name" class="control-label">Manager Name</label>
				<input type="text" class="form-control form-control-sm" name="owner_name" id="owner_name" value="<?php echo  $_settings->info('owner_name') ?>">
		</div>
		<div class="form-group">
				<label for="tin_num" class="control-label">Company Business TIN No.</label>
				<input type="text" class="form-control form-control-sm" name="tin_num" id="tin_num" value="<?php echo  $_settings->info('tin_num') ?>">
		</div>
		<div class="form-group">
				<label for="company_address" class="control-label">Company Address</label>
				<input type="text" class="form-control form-control-sm" name="company_address" id="company_address" value="<?php echo  $_settings->info('company_address') ?>">
		</div>
		<div class="form-group">
				<label for="phone_num" class="control-label">Phone Number</label>
				<input type="text" class="form-control form-control-sm" name="phone_num" id="phone_num" value="<?php echo  $_settings->info('phone_num') ?>">
		</div>
		<div class="form-group">
				<label for="company_email" class="control-label">Company Email</label>
				<input type="email" class="form-control form-control-sm" name="company_email" id="company_email" value="<?php echo  $_settings->info('company_email') ?>">
		</div>
		<div class="form-group">
    <label for="receipt_footer" class="control-label">Receipt Bottom Message</label>
    <input type="text" class="form-control form-control-sm" name="receipt_footer" id="receipt_footer" oninput="validateInput(this)" value="<?php echo htmlentities($_settings->info('receipt_footer')); ?>">
    <small class="text-danger" id="receiptFooterError" style="display:none;">Please avoid using double quotes (") or single quotes (').</small>
</div>
			<!-- <div class="form-group">
				<label for="content[about_us]" class="control-label">System Message</label>
				<textarea type="text" class="form-control form-control-sm summernote" name="content[about_us]" id="about_us"><?php echo  is_file(base_app.'about_us.html') ? file_get_contents(base_app.'about_us.html') : '' ?></textarea>
			</div> -->
			<div class="form-group">
				<label for="logo" class="control-label">System Logo</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="logo" name="logo" onchange="displayImg(this,$(this))" accept="image/*">
	              <label class="custom-file-label" for="logo">Choose file</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<!-- <div class="form-group">
				<label for="cover" class="control-label">Cover</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="cover" name="cover" onchange="displayImg2(this,$(this))" accept="image/*">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="" id="cimg2" class="img-fluid img-thumbnail">
			</div> -->
			</form>
		</div>	
			<div class="card-footer">
				<div class="col-md-6">
					<div class="row">
						<button class="btn btn-sm btn-primary" form="system-frm">
							<i class="fas fa-save"></i> Save Changes
						</button>
					</div>
				</div>
			</div>

	</div>
</div>

<script>
    $(document).ready(function(){
        $('#logo').on('change', function() {
            var file = $(this)[0].files[0];
            var fileType = file.type;
            var validImageTypes = ['image/jpeg', 'image/png', 'image/gif']; // Listahan ng mga uri ng mga file ng imahe na tinatanggap

            // Suriin kung ang uri ng file ay isa sa mga tinanggap na uri ng imahe
            if ($.inArray(fileType, validImageTypes) === -1) {
                // Kung hindi, ipakita ang error message gamit ang SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Logo Image File Type!',
                    text: 'Please select an valid image file.',
                });
                // Clear the file input field
                $(this).val('');
            }
        });
    });
</script>

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
                    title: 'Invalid Cover Image File Type!',
                    text: 'Please select an valid image file.',
                });
                // Clear the file input field
                $(this).val('');
            }
        });
    });
</script>

<script>
// Adjust the displayImg JavaScript function
function displayImg(input, _this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
            _this.closest('.custom-file').find('.custom-file-label').html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

	function displayImg2(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#cimg2').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displayImg3(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#cimg3').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
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

<script>
$(document).ready(function(){
    $('#phone_num').on('input', function() {
        var phoneNumber = $(this).val().replace(/\D/g,'');
        var formattedNumber = formatPhoneNumber(phoneNumber);
        $(this).val(formattedNumber);
    });

    function formatPhoneNumber(phoneNumber) {
        // Remove any non-digit characters
        phoneNumber = phoneNumber.replace(/\D/g,'');

        // Format the phone number as (+63) xxx xxx xxxx
        var formattedNumber = phoneNumber.replace(/(\d{1,3})(\d{1,3})(\d{1,4})/, "(+$1) $2 $3");
        
        // Trim any excess characters beyond the expected length
        formattedNumber = formattedNumber.slice(0, 18);

        return formattedNumber;
    }
});
</script>

<script>
    function validateInput(inputElement) {
        var inputValue = inputElement.value;
        var errorElement = document.getElementById('receiptFooterError');

        // Check if the inputValue contains double quotes (") or single quotes (')
        if (inputValue.includes('"') || inputValue.includes("'")) {
            // Show the error message and prevent form submission
            errorElement.style.display = 'block';
            inputElement.setCustomValidity('Invalid input');
        } else {
            // Hide the error message and allow form submission
            errorElement.style.display = 'none';
            inputElement.setCustomValidity('');
        }
    }
</script>