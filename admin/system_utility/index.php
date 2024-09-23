<?php if ($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
</script>
<?php endif; ?>
<style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            transition: box-shadow 0.3s ease-in-out;
            border: none;
            border-radius: 10px;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #565656;
            color: #00ff84;
            border-bottom: none;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .icon {
            margin-right: 10px;
        }

        h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .btn-custom-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-custom-primary:hover {
            background-color: #0056b3;
        }

        .btn-custom-danger {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .btn-custom-danger:hover {
            background-color: #c82333;
        }

        .custom-file-input {
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        
    </style>

<body>
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-lg-6">
            <div class="card card-outline card-custom">
                <div class="card-header">
                    <h3 class="text-center">System Utilities</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <a class="btn btn-lg btn-block btn-custom-primary backup_system" href="javascript:void(0)">
                                <i class="fas fa-hdd"></i> Backup System
                            </a>
                        </div>
                        <div class="col-md-6 mb-4">
                            <a class="btn btn-lg btn-block btn-secondary backup_database" >
                                <i class="fas fa-database"></i> Backup Database
                            </a>
                        </div>
                        <div class="col-md-12 mb-4">
                            <button class="btn btn-lg btn-block btn-warning reset_data" data-id="all" href="javascript:void(0)">
                                <i class="fas fa-sync-alt"></i> Reset All Data
                            </button>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="file-upload">
                                <label for="backup_file" class="custom-file-label">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i> Choose SQL file for Restore
                                </label>
                                <input type="file" id="backup_file" class="input-file" onchange="updateFileName()" accept=".sql" />
                            </div>
                        </div>
                        <div class="col-md-12 mb-4" id="restore_section" style="display: none;">
                            <button class="btn btn-lg btn-block btn-success restore_database" href="javascript:void(0)">
                                <i class="fas fa-download"></i> Restore Database
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- <div class="col-md-12 mb-4">
    <button class="btn btn-lg btn-block btn-custom-danger terminate_all_sessions">
        <i class="fas fa-sign-out-alt"></i> Terminate All Sessions
    </button>
</div> -->      
</body>



<script>
function updateFileName() {
    var input = document.getElementById('backup_file');
    var label = document.querySelector('.custom-file-label');
    var restoreSection = document.getElementById('restore_section');

    if (input.files.length > 0) {
        var file = input.files[0];
        if (!file.name.toLowerCase().endsWith('.sql')) {
            // Reset the file input and show a SweetAlert
            input.value = ''; // Reset the input value
            label.innerHTML = '<i class="fas fa-cloud-upload-alt upload-icon"></i> Choose SQL file for Restore';
        restoreSection.style.display = 'none'; 
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select a .sql file.'
            });
            return;
        }

        label.innerHTML = '<i class="fas fa-cloud-upload-alt upload-icon"></i> ' + file.name;
        restoreSection.style.display = 'block'; // Show the 'Restore' button
    } else {
        label.innerHTML = '<i class="fas fa-cloud-upload-alt upload-icon"></i> Choose SQL file for Restore';
        restoreSection.style.display = 'none'; // Hide the 'Restore' button
    }
}

    $(document).ready(function () {
        $('.restore_database').click(function () {
            var restoreButton = $(this);
                restore_database(restoreButton);
            
        });
    });

    function restore_database(restoreButton) {
        start_loader();

        var formData = new FormData();
        var fileInput = $('#backup_file')[0];
        formData.append('backup_file', fileInput.files[0]);

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=restore_database",
            method: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            error: function (err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    alert_toast(resp.message, 'success');
                    end_loader();
                } else {
                    alert_toast(resp.message, 'success');
                    end_loader();
                }
            }
        });
    }
</script>

<!-- RESET FUNCTION SCRIPT -->
<script>
$(document).ready(function () {
    $('.reset_data').click(function () {
        var resetButton = $(this);
        showLoader(); // Show loader
        Swal.fire({
            title: 'Reset All Data',
            text: "Are you sure you want to reset all data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                reset_data(resetButton.data('id'));
            }
            hideLoader(); // Hide loader after dialog interaction
        });
    });
});

// Function to show loader
function showLoader() {
    $('.loader').show();
}

// Function to hide loader
function hideLoader() {
    $('.loader').hide();
}

function reset_data() {
    start_loader();  // Assuming you have a function to start a loader or show a loading message

    $.ajax({
        url: _base_url_ + "classes/Master.php?f=reset_data",
        method: "POST",
        data: { reset_all_data: true },
        dataType: "json",
        error: function (err) {
            console.log(err);
            alert_toast("An error occurred.", 'error');
            end_loader();  // Assuming you have a function to stop the loader or hide the loading message
        },
        success: function (resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                alert_toast(resp.message, 'success');
                location.reload();
            } else {
                alert_toast(resp.message, 'error');
                end_loader();  // Assuming you have a function to stop the loader or hide the loading message
            }
        }
    });
}


</script>

<!-- BACKUP SYSTEM FUNCTION SCRIPT -->
<script>
$(document).ready(function () {
    $('.backup_system').click(function () {
        var backupButton = $(this);
        showLoader();
        Swal.fire({
            title: 'Backup System',
            text: "Are you sure you want to backup the system?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, backup it!'
        }).then((result) => {
            if (result.isConfirmed) {
                backup_system(backupButton);
            }
            hideLoader();
        });
    });
});

function backup_system(backupButton) {
    start_loader();

    $.ajax({
        url: _base_url_ + "classes/Master.php?f=backup_system",
        method: "POST",
        data: { backup_system: true },
        dataType: "json",
        error: function (err) {
            console.log(err);
            alert_toast("System has been successfully. Check you Download Folder!", 'success');
            end_loader();
        },
        success: function (resp) {
            if (typeof resp == 'object' && resp.status == 'success') {
                alert_toast(resp.message, 'success');
                // Do not reload the page here
                end_loader();
            } else {
                alert_toast(resp.message, 'error');
                end_loader();
            }
        }
    });
}
</script>

<!-- BACKUP DATABASE FUNCTION SCRIPT -->
<script>
$(document).ready(function () {
    $('.backup_database').click(function () {
        var backupButton = $(this);
        showLoader();
        Swal.fire({
            title: 'Backup Database',
            text: "Are you sure you want to backup the database?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, backup it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "backup_restore/backupdatabase.php"; // Corrected the URL assignment
            }
            hideLoader();
        });
    });
});
</script>

<!-- <script>

$(document).ready(function () {
    $('.terminate_all_sessions').click(function () {
        if (confirm("Are you sure you want to terminate all sessions?")) {
            terminate_all_sessions();
        }
    });
});

function terminate_all_sessions() {
    start_loader();

    $.ajax({
        url: _base_url_ + "classes/Master.php?f=terminate_all",
        method: "POST",
        dataType: "json",
        error: function (err) {
            console.log(err);
            alert_toast("An error occurred.", 'error');
            end_loader();
        },
        success: function (resp) {
    if (typeof resp == 'object' && resp.status == 'success') {
        alert_toast(resp.message, 'success');
        end_loader();
        // Redirect users to the login page
        window.location.href = resp.redirect;
    } else {
        alert_toast(resp.message, 'error');
        end_loader();
    }
}
    });
}


</script> -->