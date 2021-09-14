<?php
$this->load->view('layout/header');
?>
<!-- begin #page-container -->

<!-- begin login -->
<div class="login login-with-news-feed">
    <!-- begin news-feed -->
    <div class="news-feed">
        <div class="news-image">
        </div>
        <div class="news-caption">
            <h4 class="caption-title"><i class="fa fa-diamond text-success"></i> <?php echo SEO_TITLE; ?></h4>
            <p>
                <?php echo SEO_DESC; ?>
            </p>
        </div>
    </div>
    <!-- end news-feed -->
    <!-- begin right-content -->
    <div class="right-content">
        <!-- begin login-header -->
        <div class="login-header" >
            <div class="brand">

                <img src="<?php echo SITE_LOGO; ?>" style="height: 70px;">  
                <small><?php echo SITE_NAME; ?> Admin Control Panel </small>
            </div>
            <div class="icon" >
                <i class="fa fa-sign-in"></i>
            </div>
        </div>
        <!-- end login-header -->
        <!-- begin login-content -->
        <div class="login-content">
            
            <form action="#" method="POST" class="margin-bottom-0">
                <div class="form-group m-b-15">
                    <input type="text" name="email" class="form-control input-lg" placeholder="Email Address" />
                </div>
                <div class="form-group m-b-15">
                    <input type="password" name="password" class="form-control input-lg" placeholder="Password" />
                </div>
                <!--                <div class="checkbox m-b-30">
                                    <label>
                                        <input type="checkbox" /> Remember Me
                                    </label>
                                </div>-->
                <div class="login-buttons">
                    <button type="submit" name="signIn" value="signIn" class="btn btn-success btn-block btn-lg">Sign me in</button>
                </div>
                <div class="m-t-20 m-b-40 p-b-40">
                    To reset your password? Click <a href="#" class="text-success">here</a> to register.
                </div>
                <hr />
                <p class="text-center text-inverse">
                    &copy;Admin Panel All Right Reserved <?php echo date("Y"); ?>
                </p>
            </form>
        </div>
        <!-- end login-content -->
    </div>
    <!-- end right-container -->
</div>
<!-- end login -->



<?php
$this->load->view('layout/footer');
?>
<script>
    $(function () {
        <?php
        $checklogin = $this->session->flashdata('checklogin');
        if($checklogin['show']){
        ?>
        $.gritter.add({
            title: '<?php echo $checklogin['title']; ?>',
            text: '<?php echo $checklogin['text']; ?>',
            image: '<?php echo base_url(); ?>assets/emoji/sad.png',
            sticky: true,
            time: '',
            class_name: 'my-sticky-class'
        });
       <?php
        }
       ?>
    })
</script>