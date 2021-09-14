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
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">Home</a></li>
        <li class="active">Contact List</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <?php
         if ($contactperson) {
                echo $contactperson['name'] . ", " . $contactperson['contact_no'] . " - ";
            }
        if ($contact) {
           
            if ($device_id) {
                $contobj = $contact[0];


                echo "Brand: " . $contobj['brand'] . "| Model No:" . $contobj['model_no'] . " | " . $contobj['device_id'];
                echo "<br/><small>Total Contacts:" . $contobj['totalcontact'] . "</small>";
            } else {
                $contobj = $contact[0];
                echo "Total Contacts:" . $contobj['totalcontact'];
            }
        }
        ?>
    </h1>
    <!-- end page-header -->

    <div class="row">

        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                if ($device_id) {
                    ?>
                    <a href="<?php echo site_url("Account/getContact"); ?>" class="btn btn-success">Back</a>
                    <?php
                }
                ?>
                <a href="<?php echo site_url("Account/getContactsCsv/$device_id"); ?>" class="btn btn-success">Export Data</a>

                <div  class="pull-right">
                    <?php
                    if ($userdata['user_type'] == 'Admin') {
                        ?>
                        <form action="#" method="post" id="myform">
                            <button class="btn btn-danger" type="submit" value="deletedata" name="deletedata">Delete All</button>
                        </form>
                        <?php
                    } else {
                        ?>

                        <!--<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#adminModal"><i class="fa fa-shield"></i> Delete All</button>-->
                        <?php
                    }
                    ?>
                </div>
                <h2 style="text-transform: capitalize">
                    Contact List
                </h2>

                <table class="table" id="tableData">
                    <thead>
                        <tr>
                            <th>Sn. No.</th>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Update Date</th>
                            <th>Update Time</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url(); ?>assets/plugins/DataTables/js/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/table-manage-default.demo.min.js"></script>

<?php
$this->load->view('layout/footer');
?> 

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
<script>
    $(function () {




        $('#tableData').DataTable({
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo site_url("Api/getContactApi/" . $device_id) ?>",
                type: 'GET'
            },
            "columns": [
                {"data": "s_n"},
                {"data": "name"},
                {"data": "contact_no"},
                {"data": "date"},
                {"data": "time"},
            ]
        })

    });

</script>