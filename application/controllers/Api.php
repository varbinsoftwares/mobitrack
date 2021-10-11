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
        $checkcontact = $query->row();


    
        
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


        if ($checkcontact) {
            $this->response($checkcontact->id);
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
        $contact_no_list = explode(",", $contact_no);

        $call_type = $this->post('call_type');
        $call_type_list = explode(",", $call_type);

        $name = $this->post('name');
        $name_list = explode(",", $name);

        $duration = $this->post('duration');
        $duration_list = explode(",", $duration);

        $date = $this->post('date');
        $date_list = explode(",", $date);


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
                'date' => $date_t,
                'duration' => $duration_t,
            );
            $this->db->insert("get_call_details", $insertArray);
            $last_id = $this->db->insert_id();
        }


        $this->response($this->post('contact_no'));
    }

    function crateContactBulk_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $model_no = $this->post('model_no');
        $device_id = $this->post('device_id');
        $brand = $this->post('brand');

        $contact_no = $this->post('contact_no');
        $contact_no_list = explode(",", $contact_no);
        $name = $this->post('name');
        $name_list = explode(",", $name);

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
        $this->response(count($contact_no_list));
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

    function fileupload2_post($file_id) {
        $type = "member";
        $ext1 = explode('.', $_FILES['file']['name']);
        $ext = strtolower(end($ext1));
        $filename = $type . rand(1000, 10000) . '_' . $file_id;

        $actfilname = $filename . ".mp3";

        $myfile = fopen("assets/userfiles/" . $actfilname, "w") or die("Unable to open file!");
        $txt = $_POST["file"];
        fwrite($myfile, $txt);
        fclose($myfile);



        $this->db->insert("temp_file", array("filedata" => json_encode($_POST)));
        $insert_id = $this->db->insert_id();
        $this->db->where("id", $file_id);
        $this->db->set("upload_file_name", $actfilname);
        $this->db->update("track_command_file");
        $this->response(array("status" => "200"));
    }

    function fileupload_post($file_id) {
        $type = "member";
        $ext1 = explode('.', $_FILES['file']['name']);
        $ext = strtolower(end($ext1));
        $filename = $type . rand(1000, 10000) . '_' . $file_id;

        $actfilname = $filename . "." . $ext;

        move_uploaded_file($_FILES["file"]['tmp_name'], 'assets/userfiles/' . $actfilname);


        $this->db->insert("temp_file", array("filedata" => json_encode($_POST)));
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
        $locationarray = array("latitude" => 0.0, "longitude" => 0.0);
        if ($locationdata) {
            $locationarray = array("latitude" => $locationdata["latitude"], "longitude" => $locationdata["longitude"]);
        }
        $this->response($locationarray);
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

}

?>