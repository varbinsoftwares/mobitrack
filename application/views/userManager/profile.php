<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
?>
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet"  />

<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />

<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="<?php echo site_url(); ?>">Home</a></li>
        <li class="active">Profile Page</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><?php echo $userdata->name; ?> <?php echo $userdata->last_name; ?> <small><?php echo $userdata->email; ?> </small></h1>
    <!-- end page-header -->
    <!-- begin profile-container -->
    <div class="profile-container">
        <!-- begin profile-section -->
        <div class="profile-section">
            <!-- begin profile-left -->
           
            <!-- end profile-left -->
            <!-- begin profile-right -->
            <div class="t">
                <!-- begin profile-info -->
                <div class="profile-info">
                    <!-- begin table -->
                    <div class="table-responsive">

                        <table class="table table-profile">
                            <thead>
                                <tr>
                                    <th colspan="2">

                                        <div class="media">
                                            <a class="media-left" href="javascript:;">
                                                <img src='<?php echo base_url(); ?>assets/emoji/user.png' alt="" class="media-object rounded-corner" style="    width: 45px;background: url(<?php echo base_url(); ?>assets/emoji/user.png);    height: 45px;background-size: cover;">
                                            </a>
                                            <div class="media-body">
                                                <h4 class="media-heading"><?php echo $userdata->name; ?> <?php echo $userdata->last_name; ?></h4>
                                                <p>
                                                    <?php echo $userdata->email; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!--                                        <h4>
                                                                                    <i class="ion-android-person"></i>
                                        <?php echo $userdata->name; ?> <?php echo $userdata->last_name; ?>
                                                                                    <small>
                                        <?php echo $userdata->email; ?>
                                                                                    </small>
                                                                                </h4>-->
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr >
                                    <td class="field">First Name</td>
                                    <td>
                                        <span id="name" data-type="text" data-pk="<?php echo $userdata->id; ?>" data-name="name" data-value="<?php echo $userdata->name; ?>" data-url="<?php echo site_url("LocalApi/updateUserSession"); ?>" data-original-title="Enter First Name" class="m-l-5 editable editable-click" tabindex="-1" data-toggle="#edit_fname" > <?php echo $userdata->name; ?></span><button class="btn btn-xs btn-link edit_detail" ><i class="fa fa-pencil"></i>Edit</button>
                                    </td>
                                </tr>
                                <tr >
                                    <td class="field">Last Name</td>
                                    <td>
                                        <span id="last_name" data-type="text" data-pk="<?php echo $userdata->id; ?>" data-name="last_name" data-value="<?php echo $userdata->last_name; ?>" data-url="<?php echo site_url("LocalApi/updateUserSession"); ?>" data-original-title="Enter Last Name" class="m-l-5 editable editable-click" tabindex="-1" data-toggle="#edit_lname" > <?php echo $userdata->last_name; ?></span><button  class="btn btn-xs btn-link edit_detail" ><i class="fa fa-pencil"></i>Edit</button>
                                    </td>
                                </tr>


                                <tr class="highlight">
                                    <td class="field">Contact No.</td>
                                    <td>
                                        <i class="fa fa-mobile fa-lg m-r-5"></i> 
                                        <span id="contact_no" data-type="text" data-pk="<?php echo $userdata->id; ?>" data-name="contact_no" data-value="<?php echo $userdata->contact_no; ?>" data-url="<?php echo site_url("LocalApi/updateUserSession"); ?>" data-original-title="Enter Contact No." class="m-l-5 editable editable-click" tabindex="-1" data-toggle="#edit_contact" > <?php echo $userdata->contact_no; ?></span><button class="btn btn-xs btn-link edit_detail" ><i class="fa fa-pencil"></i>Edit</button>
                                    </td>
                                </tr>


                              

                                <tr class="divider">
                                    <td colspan="2"></td>
                                </tr>


                                <tr class="highlight">
                                    <td class="field" colspan="2">
                                        <div class="checkbox m-b-0 pull-left">
                                            <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#changePassword"><i class="fa fa-lock"></i> Change Your Password</button>
                                        </div>
                                    </td>

                                </tr>


                            </tbody>
                        </table>
                    </div>
                     <h4><i class="fa fa-cog"></i> Settings</h4>
                    <div class="checkbox m-b-5 m-t-0" >
                        <label><input type="checkbox" id="edit_toggle" /> Edit Profile Information</label>
                    </div>
                    <!-- end table -->
                </div>
                <!-- end profile-info -->
            </div>
            <!-- end profile-right -->
        </div>
        <!-- end profile-section -->
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="changePassword">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Your Password</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Current Password</label>
                        <input type="" name="c_password" class="form-control" value="<?php echo $userdata->password2; ?>"  required="" placeholder="Enter Your Current Password" readonly="">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">New Password</label>
                        <input type="password" class="form-control" name="n_password"  required=""  placeholder="Enter Your Current Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Confirm Password</label>
                        <input type="password" class="form-control"name="r_password" required=""  placeholder="Enter Your Current Password">
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="submit" name="changePassword" class="btn btn-primary">Save changes</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div style="clear:both"></div>
<?php
$this->load->view('layout/footer');
?>

<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>


<script>
    $(function () {
<?php
$checklogin = $this->session->flashdata('checklogin');
if ($checklogin['show']) {
    ?>
            $.gritter.add({
                title: '<?php echo $checklogin['title']; ?>',
                text: '<?php echo $checklogin['text']; ?>',
                image: '<?php echo base_url(); ?>assets/emoji/<?php echo $checklogin['icon']; ?>',
                            sticky: true,
                            time: '',
                            class_name: 'my-sticky-class'
                        });
    <?php
}
?>
                })
</script>

<script>
    $(function () {
        $('.edit_detail').hide();
        $("#edit_toggle").click(function () {
            $('.edit_detail').hide();
            if (this.checked) {
                $('.edit_detail').show();
            }
        })

        $('.edit_detail').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $($(this).prev()).editable('toggle');
        });

        $('#gender').editable({
            source: {
                'Male': 'Male',
                'Female': 'Female'
            }
        });


        $('#profession').editable({
            source: {
                'Academic': 'Academic',
                'Medicine': 'Medicine',
                'Law': 'Law',
                'Banking': 'Banking',
                'IT': 'IT',
                'Entrepreneur': 'Entrepreneur',
                'Sales/Marketing': 'Sales/Marketing',
                'Other': 'Other',
            }
        });


        $('#country').editable({
            source: {
<?php
foreach ($country as $key => $value) {
    $cont = $value['country_name'];
    echo "'$cont':'$cont',";
}
?>

            }
        });




    })
</script>