<?php
$userdata = $this->session->userdata('logged_in');
if ($userdata) {
    
} else {
    redirect("Authentication/index", "refresh");
}
$menu_control = array();




$mainContact_menu = array(
    "title" => "List of Users",
    "icon" => "fa fa-user",
    "active" => "",
    "link"=> site_url("Account/getContact"),
    "sub_menu" => array(),
);
array_push($menu_control, $mainContact_menu);
//
$contacall_menu = array(
    "title" => "Contact List All",
    "icon" => "fa fa-group",
    "active" => "",
    "link"=> site_url("Account/getContacts"),
    "sub_menu" => array(),
);
array_push($menu_control, $contacall_menu);


$calllog = array(
    "title" => "Call Log All",
    "icon" => "fa fa-phone",
    "active" => "",
    "link"=> site_url("Account/getCallLog"),
    "sub_menu" => array(),
);
array_push($menu_control, $calllog);


$user_menu = array(
    "title" => "Agent Management",
    "icon" => "fa fa-user",
    "active" => "",
    "sub_menu" => array(
        "Add Agent" => site_url("UserManager/addManager"),
        "Agent Reports" => site_url("UserManager/usersReportManager"),
    ),
);
if ($userdata['user_type'] == 'Admin') {
    array_push($menu_control, $user_menu);
}


foreach ($menu_control as $key => $value) {
    $submenu = $value['sub_menu'];
    foreach ($submenu as $ukey => $uvalue) {
        if ($uvalue == current_url()) {
            $menu_control[$key]['active'] = 'active';
            break;
        }
    }
}
?>

<!-- begin #sidebar -->
<div id="sidebar" class="sidebar whitebackground">
    <!-- begin sidebar scrollbar -->
    <div data-scrollbar="true" data-height="100%">
        <!-- begin sidebar user -->
        <ul class="nav">
            <li class="nav-profile">
                <div class="image">
                    <a href="javascript:;"><img src='<?php echo base_url(); ?>assets/emoji/user.png' alt="" class="media-object rounded-corner" style="    width: 35px;background: url(<?php echo base_url(); ?>assets/emoji/user.png);    height: 35px;background-size: cover;" /></a>
                </div>
                <div class="info textoverflow" >

                    <?php echo $userdata['name']; ?>
                    <small class="textoverflow" title="<?php echo $userdata['username']; ?>"><?php echo $userdata['username']; ?></small>
                </div>
            </li>
        </ul>
        <!-- end sidebar user -->
        <!-- begin sidebar nav -->
        <ul class="nav">
            <li class="nav-header">Navigation</li>

            <?php
            foreach ($menu_control as $mkey => $mvalue) {
                if ($mvalue['sub_menu']) {
                    ?>

                    <li class="has-sub active">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>  
                            <i class="<?php echo $mvalue['icon']; ?>"></i> 
                            <span><?php echo $mvalue['title']; ?></span>
                        </a>
                        <ul class="sub-menu">
                            <?php
                            $submenu = $mvalue['sub_menu'];
                            foreach ($submenu as $key => $value) {
                                ?>
                                <li><a href="<?php echo $value; ?>"><?php echo $key; ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="<?php echo $mvalue['active']; ?>">
              
                        <a href="<?php echo $mvalue['link']; ?>">
                            
                              <b class="fa fa-long-arrow-right pull-right" style="line-height: 22px;"></b>  
                            <i class="<?php echo $mvalue['icon']; ?>"></i> 
                            <span><?php echo $mvalue['title']; ?></span>
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
            <li class="nav-header"> Admin V <?php echo PANELVERSION; ?></li>
   
        </ul>
        <!-- end sidebar nav -->
    </div>
    <!-- end sidebar scrollbar -->
</div>
<div class="sidebar-bg"></div>
<!-- end #sidebar -->