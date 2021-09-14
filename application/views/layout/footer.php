<div id="adminModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Admin Permission Required</h4>
            </div>
            <div class="modal-body">
                <p>Admin password required to perform this operation</p>
                <div class="row">
                <div class="col-md-12">
                    <form action="#" method="post" id="myform">
                        <input type="password" name="password" required="" class="form-control">
                        <br/>
                        <button class="btn btn-danger" type="submit" value="deletedata" name="deletedata">Delete All</button>
                    </form>
                </div>
                </div>
            </div>
          
        </div>

    </div>
</div>


<!-- begin scroll to top btn -->
<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
<!-- end scroll to top btn -->
</div>
<script src="<?php echo base_url(); ?>assets/js/howler.min.js"></script>
<script src="<?php echo base_url(); ?>assets/angular/rootController.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="<?php echo base_url(); ?>assets/js/apps.min.js"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script>
    $(document).ready(function () {
        App.init();

    });
</script>
</body>
</html>