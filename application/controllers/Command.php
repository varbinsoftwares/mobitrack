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

        $passwordq = $query->row();
        $this->gblpassword = $passwordq->password;
        $this->userdata = $this->session->userdata('logged_in');
    }

    public function deviceDashboard($device_id) {
        $data = array();


        $this->db->select("name, contact_no, brand, model_no, device_id");
        $this->db->where("device_id", $device_id);
        $query = $this->db->get('get_conects_person');
        $contactperson = $query->row_array();

        $command_list = $this->Command_model->currentCommand($device_id);

        $data['command_list'] = $command_list;
        $data['contactperson'] = $contactperson;

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
          $uploadfile =  base_url()."assets/userfiles/".$value["upload_file_name"];  
          $filepath = $value["file_path"];
          echo "<tr><td>$device_id</td><td>$filepath</td><td><a target='_blank' href='$uploadfile'>$uploadfile</a></td></tr>";  
        }
        
        echo "<table>";
    }
    
    function test(){
        $query = $this->db->get("temp_file");
        $allfiles = $query->result_array();
        foreach ($allfiles as $key => $value) {
            echo "<br/><br/><br/>";
            $decodedata =  json_decode($value["filedata"]);
            print_r($decodedata);
        }
    }

}

?>
