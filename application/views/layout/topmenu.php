<?php
$userdata = $this->session->userdata('logged_in');
if ($userdata) {
    
}
?>

<!-- begin #header -->
<div id="header" class="header navbar navbar-fixed-top navbar-inverse">
    <!-- begin container-fluid -->
    <div class="container-fluid">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header" >
            <a href="<?php site_url('Order/index'); ?>" class="navbar-brand" style="padding: 0px 15px;">Contacts</a>
            <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <!-- end mobile sidebar expand / collapse button -->

        <!-- begin header navigation right -->
        <ul class="nav navbar-nav navbar-right">


            <li class="dropdown navbar-user">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <img src='<?php echo base_url(); ?>assets/emoji/user.png' alt="" class="media-object rounded-corner" style="    width: 30px;background: url(<?php echo base_url(); ?>assets/emoji/user.png);    height: 30px;background-size: cover;" /> 
                    <span class="hidden-xs"><?php echo $userdata['name']; ?> <?php echo $userdata['last_name']; ?></span> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu animated fadeInLeft">
                    <li class="arrow"></li>
                    <li><a href="<?php echo site_url("profile") ?>">Edit Profile</a></li>

                    <li class="divider"></li>
                    <li><a href="<?php echo site_url("Authentication/logout") ?>">Log Out</a></li>
                </ul>
            </li>
        </ul>
        <!-- end header navigation right -->
    </div>
    <!-- end container-fluid -->
</div>
<!-- end #header -->
<?php
$this->load->view('layout/sidebar');
?>