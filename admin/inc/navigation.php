<?php
$userType = $_settings->userdata('type');
$isAdmin = ($userType == 1);
$isAccounting = ($userType == 2);
$isStaff = ($userType == 3);
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

        h3 {
            font-weight: 700;
            margin-bottom: 20px;
        }
    </style>

<style>
    .user-panel .image {
        position: relative;
        width: 150px; /* Adjust the width of the avatar */
        height: 150px; /* Adjust the height of the avatar */
        overflow: hidden;
        border-radius: 50%; /* Make the avatar circular */
        padding-right: 0.8rem;
      }

    .user-panel .image img {
        width: 100%;
        height: auto;
        border: 2px solid #fff; /* Add a white border */
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); /* Add a shadow effect */
        border-radius: 50%; /* Make sure the image stays circular */
    }
    
    .badge {
        margin-top: 5px; /* Adjust spacing */
    }
</style>


<style>
    /* Add your custom styles for the collapsed sidebar navigation here */
    .nav-sidebar .nav-item {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Add a border between navigation items */
    }

    .nav-sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8); /* Change the color of the navigation links */
        transition: color 0.3s ease; /* Add a smooth color transition effect */
    }

    .nav-sidebar .nav-link:hover {
        color: #ffffff; /* Change the color of the navigation links on hover */
    }

    .nav-sidebar .nav-item.active .nav-link {
        background-color: rgba(255, 255, 255, 0.1); /* Change the background color of the active navigation item */
        color: #ffffff; /* Change the color of the active navigation item */
    }

    .nav-sidebar .nav-item.active .nav-link i {
        color: #ffffff; /* Change the color of the icon of the active navigation item */
    }
    .nav-link{
      font-family: 'Roboto', sans-serif;
    }
</style>

<style>
    .great-vibes-text {
        font-family: 'Great Vibes';
        color: #ffffff; /* White color */
    }
    .ibm-plex-mono-bold-italic {
        font-family: "IBM Plex Mono", monospace;
        font-weight: 700;
        font-style: italic;
        font-size: 1.2rem;
      }
    .badge {
        font-family: 'Roboto', sans-serif;
     }

</style>

<!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
        <!-- Brand Logo -->
        <a href="<?php echo base_url ?>admin" class="brand-link bg-primary text-sm">
        <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Store Logo" class="brand-image img-circle elevation-3 bg-black" style="width: 1.8rem;height: 1.8rem;max-height: unset" draggable="false">
        <span class="brand-text ibm-plex-mono-bold-italic"><?php echo $_settings->info('short_name') ?></span>
        </a>
        <!-- Sidebar -->
<!-- Sidebar -->
<div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
    <div class="os-resize-observer-host observed">
        <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
    </div>
    <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
        <div class="os-resize-observer"></div>
    </div>
    <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
    <div class="os-padding">
        <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
            <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex flex-column align-items-center">
                    <div class="image">
                        <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2" alt="User Image" draggable="false">
                    </div>
                    <div class="info text-center">
                    <h5 class="d-block text-white great-vibes-text">
    <?php echo ucwords($_settings->userdata('salutation').' '.$_settings->userdata('firstname').' '.$_settings->userdata('lastname')) ?>
</h5>
                        <?php 
                        switch ($_settings->userdata('type')) {
                            case 1:
                                echo "<span class='badge badge-danger'>Administrator</span>";
                                break;
                            case 2:
                                echo "<span class='badge badge-success'>Accounting</span>";
                                break;
                            case 3:
                                echo "<span class='badge badge-primary'>Staff</span>";
                                break;
                            default:
                                echo "<span class='badge badge-secondary'>Unknown Type</span>";
                                break;
                        }
                        ?>
                    </div>
                </div>
                
                <nav class="mt-4">
                   <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item dropdown">
                      <a href="./" class="nav-link nav-home">
                      <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                          Dashboard
                        </p>
                      </a>
                    </li>
                    <?php if ($isAdmin || $isAccounting || $isStaff): ?>
                    <li class="nav-header">Control</li>                    
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=purchase_order/manage_po" class="nav-link nav-purchase_order_manage_po">
                      <i class="nav-icon fas fa-th-list"></i>
                        <p>
                          POS Supplies Purchase
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=sales/manage_sale" class="nav-link nav-sales_manage_sale">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                          POS Customer Sales
                        </p>
                      </a>
                    </li>                  
                                   
                     <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=stocks" class="nav-link nav-stocks">
                      <i class="nav-icon fas fa-archive"></i>
                        <p>
                          Stocks Monitoring
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Utilities</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/supplier" class="nav-link nav-maintenance_supplier">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>
                          Manage Suppliers
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/item" class="nav-link nav-maintenance_item">
                      <i class="nav-icon fas fa-box-open"></i>
                        <p>
                          Manage Products
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/brand" class="nav-link nav-maintenance_brand">
                      <i class="nav-icon fas fa-tags"></i>
                        <p>
                          Manage Brands
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/category" class="nav-link nav-maintenance_category">
                        <i class="nav-icon fa fa-sitemap"></i>
                        <p>
                          Manage Categories
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                    <a href="<?php echo base_url ?>admin/?page=maintenance/discount" class="nav-link nav-maintenance_discount">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Manage Discounts</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo base_url ?>admin/?page=maintenance/unit" class="nav-link nav-maintenance_unit">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>Manage Quantity Units</p>
                    </a>
                </li>
                    <?php endif; ?>
                    <?php if ($isAdmin || $isAccounting): ?>
                    <li class="nav-header">Accounting Management</li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=advance" class="nav-link nav-advance">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                          Inventory Management Hub
                        </p>
                      </a>
                    </li>                        
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=sales" class="nav-link nav-sales">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                          Customer Sales Order
                        </p>
                      </a>
                    </li>    
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=purchase_order" class="nav-link nav-purchase_order">
                      <i class="nav-icon fas fa-th-list"></i>
                        <p>
                         Supplier Purchase Order
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=supp_return" class="nav-link nav-return">
                        <i class="nav-icon fas fa-undo"></i>
                        <p>
                        Supplier Return Order
                        </p>
                      </a>
                    </li>  
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=receiving" class="nav-link nav-receiving">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>
                        Received Order List
                        </p>
                      </a>
                    </li>                               
                   <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=expenses" class="nav-link nav-expenses">
                      <i class="nav-icon fas fa-money-check-alt"></i>
                        <p>
                          Expenses Management
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Transaction Reports</li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=reports/sales" class="nav-link nav-reports_sales">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                          Sales Reports
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=reports/expenses" class="nav-link nav-reports_expenses">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                          Expenses Reports
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=reports/purchases" class="nav-link nav-reports_purchases">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                          Purchases Reports
                        </p>
                      </a>
                    </li>
<?php endif; ?>
                    <?php if ($isAdmin): ?>
                   
                    <li class="nav-header">Activity Logs</li>  
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=logs/system" class="nav-link nav-logs_system">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                          System Logs
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="<?php echo base_url ?>admin/?page=logs/user" class="nav-link nav-logs_user">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                          User Logs
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Archive Bin</li>  
                    <li class="nav-item">
                    <a href="<?php echo base_url ?>admin/?page=archive/transact_bin" class="nav-link nav-archive_transact_bin">
                      <i class="nav-icon fas fa-trash-restore-alt"></i> 
                      <p>
                        Restore Deleted Transactions
                      </p>
                    </a>
                  </li> 
                  <li class="nav-item">
                    <a href="<?php echo base_url ?>admin/?page=archive/data_bin" class="nav-link nav-archive_data_bin">
                      <i class="nav-icon fa fa-recycle"></i> 
                       <p>
                      Restore Deleted Data
                      </p>
                    </a>
                  </li>
                    <li class="nav-header">System and Security</li>
<li class="nav-item">
    <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
        <i class="nav-icon fas fa-user-cog"></i>
        <p>User Accounts</p>
    </a>
</li>
<!-- <li class="nav-item">
    <a href="<?php echo base_url ?>admin/?page=access_control" class="nav-link nav-access_control">
        <i class="nav-icon fas fa-shield-alt"></i>
        <p>Access Control</p>
    </a> -->
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                          System Information
                        </p>
                      </a>
                    </li>  
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=system_utility" class="nav-link nav-system_utility">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>
                          System Utilities
                        </p>
                      </a>
                    </li>           

                    <?php endif; ?>

                  </ul>
                </nav>
                <!-- /.sidebar-menu -->
              </div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar-corner"></div>
        </div>
        <!-- /.sidebar -->
      </aside>
      <script>
        var page;
    $(document).ready(function(){
      page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      page = page.replace(/\//gi,'_');

      if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

      }
      
		// $('#receive-nav').click(function(){
    //   $('#uni_modal').on('shown.bs.modal',function(){
    //     $('#find-transaction [name="tracking_code"]').focus();
    //   })
		// 	uni_modal("Enter Tracking Number","transaction/find_transaction.php");
		// })
    })
  </script>