<?php
$this->load->view('layout/header');
$this->load->view('layout/topmenu');
$userdata = $this->session->userdata('logged_in');
?>
<style>
    .iconblock i{
        font-size: 40px;
        margin: 5px;
    }
    .controlblock{
        border: 3px solid #d9e0e7;
        padding: 5px;
        height: 180px;
    }
    .controlblock.active{
        border: 3px solid green;
    }

    .controlblock button{
        margin-bottom: 10px;
    }
    .activebutton{
        color:red;
        position: absolute;
        left: 25px;
        color:white;
    }
    .blink_me {
        color:green;
        animation: blinker 1s linear infinite;
    }

    .packagename{
        padding: 15px;
        display: block;
        background: #f2f2f2;
        border-radius: 15px;
        margin: 5px;
        height: 150px;
        margin-bottom: 20px;
    }
    .packagename span{
        font-weight: bold;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }

    .packagename img{
        border-radius: 10px;
        background: white;
    }
    .packagename span.badge {
        float: right;
        position: absolute;
        top: 12px;
        right: 25px;
    }
    .packagename-text{
        margin-top: 15px;
        float: left;
        font-size: 12px;
        color: black;
        width: 100%;
        text-overflow: ellipsis;
        margin-bottom: 7px;
    }

    .packagename small{

        float: left;
        font-size: 9px;
        color: black;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
</style>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content" ng-controller="commandControlDashboard" >


    <div class="row">
        <div class="result-container col-md-12">
            <ul class="media-list">
                <li class="media media-sm" >
                    <a class="media-left" href="javascript:;">
                        <img src="<?php echo $app_info["image"] ?>" alt="" class="media-object rounded-corner">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $app_info["title"] ?></h4>
                        <p>
                            <?php echo $app_info["package_name"] ?>    
                        </p>
                    </div>
                    
                    <a href="<?php echo site_url("Command/deviceDashboard/$device_id");?>" class="media-right">
                        <i class="fa fa-arrow-left fa-4x"></i>
                    </a>
                </li>

            </ul>

        </div>
    </div>

    <!-- begin page-header -->
    <h1 class="">
        <?php
        if ($contactperson) {
            echo $contactperson['name'] . ", " . $contactperson['contact_no'];
        }
        ?>
        <br/>
        <small class="page-header" style="font-size: 15px;color:black;">
            <?php
            if ($contactperson) {

                echo "Brand: " . $contactperson['brand'] . "| Model No:" . $contactperson['model_no'] . " | " . $contactperson['device_id'];
            }
            ?>
        </small>
    </h1>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <h2 class="panel-title">Recent Activities</h2>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <table class="table" id="tableData">
                                <thead>
                                    <tr>
                                        <th style="width:20px">Sn. No.</th>
                                        <th style="width:100px">Activity Title</th>
                                        <th style="width:300px">Activity Message</th>
                                        <th style="width:50px"> Date</th>
                                        <th style="width:50px"> Time</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/DataTables/js/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/js/table-manage-default.demo.min.js"></script>
<?php
$this->load->view('layout/footer');
?>
<script>
    var device_id = "<?php echo $device_id; ?>";
    var package_name = "<?php echo $package_name; ?>";
</script>
<script>
    $(function () {




        $('#tableData').DataTable({
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo site_url("Api/getActivityList/" . $device_id . "/" . $package_name) ?>",
                type: 'GET'
            },
            "columns": [
                {"data": "s_n"},
                {"data": "notification_title"},
                {"data": "notification_body"},
                {"data": "date"},
                {"data": "time"},
            ]
        })

    });

</script>



