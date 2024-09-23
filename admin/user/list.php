<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<?php
function format_time_ago($timeDifference)
{
    $seconds = $timeDifference;
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 86400);
    $weeks   = round($seconds / 604800);
    $months  = round($seconds / 2629440);
    $years   = round($seconds / 31553280);

    if ($seconds <= 60) {
        return "Just Now";
    } elseif ($minutes <= 60) {
        return "$minutes minute(s) ago";
    } elseif ($hours <= 24) {
        return "$hours hour(s) ago";
    } elseif ($days <= 7) {
        return "$days day(s) ago";
    } elseif ($weeks <= 4.3) {  // 4.3 == 30/7
        return "$weeks week(s) ago";
    } elseif ($months <= 12) {
        return "$months month(s) ago";
    } else {
        return "$years year(s) ago";
    }
}

?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
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

    h3 {
        font-weight: 700;
        margin-bottom: 20px;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3><i class="fas fa-users-cog icon"></i> User Accounts</h3>
        <div class="card-tools">
            <a href="?page=user/manage_user" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="text-center">Avatar</th>
                        <th>Full Name</th>
                        <th>User Name</th>
                        <th>User Role</th>
                        <th class="text-center">Last Login</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT *, CONCAT(salutation, ' ', firstname, ' ', lastname) AS name FROM `users` WHERE id NOT IN ('1', '2') ORDER BY CONCAT(salutation, ' ', firstname, ' ', lastname) ASC");
                    while ($row = $qry->fetch_assoc()) :
                        $lastLogin = strtotime($row['last_login']);
                        $currentTime = time();
                        $timeDifference = $currentTime - $lastLogin;
                        $formattedTime = format_time_ago($timeDifference);

                        // Check if the current row corresponds to the logged-in user
                        $logged_in_user_id = $_settings->userdata('id');
                        $edit_button_display = $row['id'] == $logged_in_user_id ? '' : 'd-none'; // Hide the button if not the logged-in user
                        ?>
                        <tr>
                            <td class="text-center"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
                            <td><?php echo ucwords($row['name']) ?></td>
                            <td><p class="m-0 truncate-1"><?php echo $row['username'] ?></p></td>
                            <td>
                                <p class="m-0">
                                    <?php
                                    switch ($row['type']) {
                                        case 1:
                                            echo "Administrator";
                                            break;
                                        case 2:
                                            echo "Accounting";
                                            break;
                                        case 3:
                                            echo "Staff";
                                            break;
                                        default:
                                            echo "Unknown Type";
                                            break;
                                    }
                                    ?>
                                </p>
                            </td>
                            <td class="text-center">
                                <?php
                                if (empty($lastLogin)) {
                                    echo "No Login Behaviour";
                                } else {
                                    echo '<span class="last-login" data-last-login="' . $lastLogin . '" data-user-id="' . $row['id'] . '">' . $formattedTime . '</span>';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <span class="session-status" data-session-status="<?php echo $row['session_status']; ?>">
                                <?php
                                if ($row['session_status'] == 1) {
                                    echo '<span class="badge badge-success">Online</span>';
                                } else {
                                    echo '<span class="badge badge-danger">Offline</span>';
                                }
                                ?>
                                </span>
                            </td>
                            <td align="center">
                                <div class="dropdown">
                                    <button class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" type="button" data-toggle="dropdown">
                                        Action
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item <?php echo $edit_button_display; ?>" href="?page=user/manage_user&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                        <div class="dropdown-divider <?php echo $edit_button_display; ?>"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
   $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this User permanently?","delete_user",[$(this).attr('data-id')]);
        });

        $('.table td,.table th').addClass('py-1 px-2 align-middle');
        $('.table').dataTable();


        function updateLastLoginTime() {
            $('.last-login').each(function () {
                var lastLoginTimestamp = parseInt($(this).data('last-login'));
                var userId = $(this).data('user-id');
                var currentTime = Math.floor(new Date().getTime() / 1000);
                var timeDifference = currentTime - lastLoginTimestamp;
                $(this).text(format_time_ago(timeDifference));
            });
        }

        // Initial call to set the initial values
        updateLastLoginTime();

        // Update every minute
        setInterval(updateLastLoginTime, 60000);

    });

    function delete_user($id){
    var sessionStatus = $('.delete_data[data-id="' + $id + '"]').closest('tr').find('.session-status').data('session-status');
    if(sessionStatus == 1) {
        alert_toast('Cannot delete user. User is currently online.', 'warning');
    } else {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Users.php?f=delete",
            method: "POST",
            data: {id: $id},
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
}
</script>