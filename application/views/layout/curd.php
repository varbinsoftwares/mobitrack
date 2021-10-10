<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
?>
<!-- ================== BEGIN PAGE CSS STYLE ================== -->
<link href="<?php echo base_url(); ?>assets/plugins/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<link href="<?php echo base_url(); ?>assets/plugins/jquery-tag-it/css/jquery.tagit.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/plugins/jquery-tag-it/js/tag-it.min.js"></script>

<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo base_url(); ?>assets/plugins/DataTables/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->

    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><?php echo $title; ?> <small><?php $description; ?></small></h1>
    <!-- end page-header -->

    <!-- begin panel -->
    <div class="panel panel-inverse">

        <div class="panel-body">

            <div class="m-b-15">
                <button type="button" class="btn btn-primary p-l-40 p-r-40" data-toggle="modal" data-target="#add_item"><i class="fa fa-plus"></i> Add New</button>
            </div>
            <div class="table-responsive">
                <table id="tableData" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php
                            foreach ($fields as $fkey => $fvalue) {
                                ?> 
                                <th style='width: <?php echo $fvalue['width']; ?>'><?php echo $fvalue['title']; ?></th>
                                <?php
                            }
                            ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($list_data as $key => $vdata) {
                            ?>  
                            <tr>
                                <?php
                                foreach ($fields as $fkey => $fvalue) {
                                    ?> 

                                    <td>
                                        <?php
                                        if ($fkey == 'id') {
                                            ?>
                                            <?php echo $vdata[$fkey]; ?>

                                            <?php
                                        } else {
                                            if (isset(($fvalue['type']))) {
                                                switch ($fvalue['type']) {
                                                    case "textarea":
                                                        ?>
                                                        <span  id="<?php echo $fkey; ?>" data-type="textarea" data-pk="<?php echo $vdata['id']; ?>" data-name="<?php echo $fkey; ?>" data-value="<?php echo $vdata[$fkey]; ?>" data-params ={'tablename':'<?php echo $table_name; ?>'} data-url="<?php echo site_url("LocalApi/updateCurd"); ?>" data-mode="inline" class="m-l-5 editable editable-click" tabindex="-1" > <?php echo $vdata[$fkey]; ?></span>
                                                        <?php
                                                        break;
                                                    case "select":
                                                        ?>

                                                        <span  id="<?php echo $fkey; ?>" data-type="select" data-pk="<?php echo $vdata['id']; ?>" data-name="<?php echo $fkey; ?>" data-value="<?php echo $vdata[$fkey]; ?>" data-params ={'tablename':'<?php echo $table_name; ?>'} data-url="<?php echo site_url("LocalApi/updateCurd"); ?>" data-mode="inline" class="m-l-5 editable editable-click <?php echo $fvalue['depends']; ?>" tabindex="-1" > <?php echo $depends[$fvalue['depends']][$vdata[$fkey]]; ?></span>
                                                        <?php
                                                        break;
                                                    default:
                                                        ?>
                                                        <span  id="<?php echo $fkey; ?>" data-type="text" data-pk="<?php echo $vdata['id']; ?>" data-name="<?php echo $fkey; ?>" data-value="<?php echo $vdata[$fkey]; ?>" data-params ={'tablename':'<?php echo $table_name; ?>'} data-url="<?php echo site_url("LocalApi/updateCurd"); ?>" data-mode="inline" class="m-l-5 editable editable-click" tabindex="-1" > <?php echo $vdata[$fkey]; ?></span>
                                                    </td>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                    <span  id="<?php echo $fkey; ?>" data-type="text" data-pk="<?php echo $vdata['id']; ?>" data-name="<?php echo $fkey; ?>" data-value="<?php echo $vdata[$fkey]; ?>" data-params ={'tablename':'<?php echo $table_name; ?>'} data-url="<?php echo site_url("LocalApi/updateCurd"); ?>" data-mode="inline" class="m-l-5 editable editable-click" tabindex="-1" > <?php echo $vdata[$fkey]; ?></span>

                                    <?php
                                }
                            }
                        }
                        ?>
                        <td>
                        </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <!-- end panel -->
</div>
<!-- end #content -->

<!-- Modal -->
<div class="modal fade" id="add_item" tabindex="-1" role="dialog" aria-labelledby="changePassword">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $form_title; ?></h4>
                </div>
                <div class="modal-body">
                    <?php
                    foreach ($form_attr as $fkey => $fvalue) {
                        ?>
                        <div class="form-group">
                            <?php
                            switch ($fvalue['type']) {
                                case "hidden":
                                    ?>
                                    <input type="<?php echo $fvalue['type']; ?>" name="<?php echo $fkey; ?>" class="form-control"  required="<?php echo $fvalue['required']; ?>" placeholder="<?php echo $fvalue['place_holder']; ?>">
                                    <?php
                                    break;
                                case "select":
                                    ?>
                                    <label for="<?php echo $fkey; ?>"><?php echo $fvalue['title']; ?></label>

                                    <select name="<?php echo $fkey; ?>" class="form-control"  required="<?php echo $fvalue['required']; ?>" placeholder="<?php echo $fvalue['place_holder']; ?>">
                                        <?php
                                        if ($depends) {
                                            foreach ($depends[$fvalue['depends']] as $selectkey => $selectvalue) {
                                                echo "<option value='$selectkey'>$selectvalue</option>";
                                            }
                                        }
                                        ?>
                                    </select>

                                    <?php
                                    break;
                                case "textarea":
                                    ?>
                                    <label for="<?php echo $fkey; ?>"><?php echo $fvalue['title']; ?></label>
                                    <textarea name="<?php echo $fkey; ?>" class="form-control"  required="<?php echo $fvalue['required']; ?>" placeholder="<?php echo $fvalue['place_holder']; ?>"></textarea>

                                    <?php
                                    break;
                                default:
                                    ?>
                                    <label for="<?php echo $fkey; ?>"><?php echo $fvalue['title']; ?></label>
                                    <input type="<?php echo $fvalue['type']; ?>" name="<?php echo $fkey; ?>" class="form-control"  required="<?php echo $fvalue['required']; ?>" placeholder="<?php echo $fvalue['place_holder']; ?>">

                                <?php
                            }
                            ?>

                        </div>
                        <?php
                    }
                    ?>



                </div>


                <div class="modal-footer">
                    <button type="submit" name="submitData" class="btn btn-primary">Submit</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="<?php echo base_url(); ?>assets/plugins/DataTables/js/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/table-manage-default.demo.min.js"></script>
<?php
$this->load->view('layout/footer');
?>
<script>
    $(function () {






<?php
if ($depends) {
    foreach ($depends as $selectkey => $selectvalue) {
        if ($selectvalue) {
            ?>

                    $('.<?php echo $selectkey; ?>').editable({
                        source: <?php echo json_encode($selectvalue); ?>
                    });
            <?php
        }
    }
}
?>


        $('.edit_detail').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $($(this).prev()).editable('toggle');
        });

        $(".editable").editable();
     $('#tableData').DataTable({'pageLength':10});


<?php
$checklogin = $this->session->flashdata('checklogin');
if ($checklogin['show']) {
    ?>
            $.gritter.add({
                title: "<?php echo $checklogin['title']; ?>",
                text: "<?php echo $checklogin['text']; ?>",
                image: '<?php echo base_url(); ?>assets/emoji/<?php echo $checklogin['icon']; ?>',
                            sticky: true,
                            time: '',
                            class_name: 'my-sticky-class '
                        });
    <?php
}
?>
                });
</script>