<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
$userdata = $this->session->userdata('logged_in');
?>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="<?php echo base_url(); ?>assets/plugins/DataTables/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content"  >
    <!-- begin breadcrumb -->

    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <a href="<?php echo site_url("Account/getContactsPersonCsv"); ?>" class="btn btn-success">Export Data</a>
    <div  class="pull-right">


        <!--<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#adminModal"><i class="fa fa-shield"></i> Delete All</button>-->

    </div>

    <h1 class="page-header">Mobile List <small></small></h1>

    <!-- end page-header -->

    <div class="row">

        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table" id="tableData">
                    <thead>
                        <tr>
                            <th>Sn. No.</th>
                            <th>Name/Contact No.</th>

                            <th>Brand / Model No.</th>

                            <th>Device ID</th>
                            <th>Update Date/Time</th>
                            <th>Contacts</th>
                            <th>Call Log</th>
                            <th>Location</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($contact as $key => $value) {
                            ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo $value['name']; ?>
                                    <br/><?php echo $value['contact_no']; ?></td>
                                <td><?php echo ucwords($value['brand']); ?> - <?php echo $value['model_no']; ?></td>

                                <td><?php echo $value['device_id']; ?></td>
                                <td><?php echo $value['date'] . " " . $value['time']; ?></td>
                                <td><a href="<?php echo site_url("Account/getContacts/" . $value['device_id']); ?>" class="btn btn-danger btn-sm"> Contacts</a></td>
                                <td><a href="<?php echo site_url("Account/getCallLog/" . $value['device_id']); ?>" class="btn btn-danger btn-sm"> Call Log</a></td>
                                <td><a href="<?php echo site_url("Account/getLocation/" . $value['device_id']); ?>" class="btn btn-danger btn-sm"> Location</a></td>
                                <td>
                                    <?php
                                    if ($userdata['user_type'] == 'Admin') {
                                        ?>
                                        <form action="#" method="post" class="myform">
                                            <input type="hidden" name="device_id" value="<?php echo $value['device_id']; ?>">
                                            <button class="btn btn-danger  btn-sm" type="submit" value="deletedata" name="deletedata" value="<?php echo $value['device_id']; ?>">Delete</button>
                                        </form>
                                        <?php
                                    }
                                    ?>
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
</div>
<?php
$this->load->view('layout/footer');
?>

<script>
    $(document).ready(function () {
        $(".myform").submit(function (event) {
            if (!confirm('Are you sure that you want delete record.'))
                event.preventDefault();
        });
    });

<?php
if ($message != "") {
    ?>
        alert("<?php echo $message; ?>");
    <?php
}
?>
</script>
<script src="<?php echo base_url(); ?>assets/plugins/DataTables/js/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/table-manage-default.demo.min.js"></script>

<script>
    $(document).ready(function () {
        $("#myform").submit(function (event) {
            if (!confirm('Are you sure that you want delete record.'))
                event.preventDefault();
        });
    });

<?php
if ($message != "") {
    ?>
        alert("<?php echo $message; ?>");

    <?php
}
?>
</script>

<?php
$this->load->view('layout/footer');
?> 
<script>
    $(function () {

        $('#tableData').DataTable()

    });

</script>