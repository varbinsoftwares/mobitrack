<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');

        $this->db->select("password");
        $this->db->where("user_type", "admin");
        $query = $this->db->get('admin_users');

        $passwordq = $query->row();
        $this->gblpassword = $passwordq->password;
        $this->userdata = $this->session->userdata('logged_in');
    }

    function getAllContactData() {
        $this->db->select("device_id");
        $this->db->group_by("device_id");
        $queryd = $this->db->get("get_conects");
        $checkcontactd = $queryd->result_array();
        $temparray = array();
        foreach ($checkcontactd as $key => $value) {
            $this->db->select("id, date, time, device_id, model_no, brand, name, contact_no");
            $this->db->where("device_id", $value["device_id"]);
            $query = $this->db->get('get_conects');
            $checkcontactp2 = $query->row_array();
            $checkcontactp2['name'] = "-";
            $checkcontactp2['contact_no'] = "-";
            $temparray[$value['device_id']] = $checkcontactp2;
        }

        $tempdata = [];

        $this->db->select("id, date, time, device_id, model_no, brand, name, contact_no");
        $query = $this->db->get('get_conects_person');
        $checkcontactp = $query->result_array();
        foreach ($checkcontactp as $key => $value) {
            unset($temparray[$value['device_id']]);
        }
        foreach ($temparray as $key => $value) {
            array_push($checkcontactp, $value);
        }
        return $checkcontactp;
    }

    function test() {
        $allcontact = $this->getAllContactData();
        $tempdata = [];
        $alldata = $this->getAllContactData();
        $this->db->select("id, date, time, device_id, model_no, brand, name, contact_no");
        $query = $this->db->get('get_conects_person');
        $checkcontactp = $query->result_array();
        foreach ($checkcontactp as $key => $value) {
            unset($allcontact[$value['device_id']]);
        }
        foreach ($allcontact as $key => $value) {
            array_push($checkcontactp, $value);
        }
        print_r($checkcontactp);
    }

    function getContact() {
        $alldata = $this->getAllContactData();

        $returnarray = [];
        $data['contact'] = $alldata;
        $data["message"] = "";
        if (isset($_POST['deletedata'])) {

            $deviceid = $this->input->post("device_id");
            $this->db->where("device_id", $deviceid);
            $this->db->delete('get_conects_person');
            redirect("Account/getContact");
        }


        $this->load->view('contact_person', $data);
    }

    function getContactsPersonCsv() {
        $delimiter = ",";
        $filename = "contactlist_person" . date('Y-m-d') . ".csv";
        $f = fopen('php://memory', 'w');
        $fields = array('SN', 'Name', 'Contact No.', 'Model No.', 'Brand', 'Device ID', 'Date', 'Time');
        fputcsv($f, $fields, $delimiter);
        $this->db->select("*");

        $query = $this->db->get('get_conects_person');
        $checkcontact = $query->result_array();
        foreach ($checkcontact as $key => $value) {
            $lineData = array($key + 1, $value['name'], $value['contact_no'] . " ", $value['model_no'], $value['brand'], $value['device_id'], $value['date'], $value['time']);
            fputcsv($f, $lineData, $delimiter);
        }
        fseek($f, 0);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        //output all remaining data on a file pointer
        fpassthru($f);
        exit;
    }

    function getContacts($device_id = 0) {
        $queryexe = " where 1";
        if ($device_id) {
            $queryexe = " where device_id = '$device_id' ";
            $this->db->where("device_id", $device_id);
        }

        $this->db->select("*, (select count(id) as totalcontact FROM `get_conects` $queryexe) as totalcontact");

        $this->db->limit("1");
        $query = $this->db->get('get_conects');
        $checkcontact = $query->result_array();


        $contactperson = array();
        if ($device_id) {
            $this->db->select("name, contact_no, brand, model_no, device_id");
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $contactperson = $query->row_array();
        }
        $data['contactperson'] = $contactperson;
        $data['contact'] = $checkcontact;
        $data['device_id'] = $device_id;
        $data["message"] = "";
        if (isset($_POST['deletedata'])) {
            if ($device_id) {
                $this->db->where("device_id", $device_id);
                $this->db->delete('get_conects');
                redirect("Account/getContact");
            }
            if ($this->userdata['user_type'] == 'Admin') {
                $password = $this->input->post("password");
                if (md5($password) == $this->gblpassword) {
                    $this->db->delete('get_conects');
                    redirect("Account/getContact");
                } else {
                    $data["message"] = "Invalid Password";
                }
            }
        }
        $data['loginuser'] = $this->userdata;


        $this->load->view('contacts', $data);
    }

    function getContactsCsv($device_id) {
        $delimiter = ",";
        $filename = "contactlist_$device_id" . date('Y-m-d') . ".csv";
        $f = fopen('php://memory', 'w');
        if ($device_id) {
            $this->db->select("name, contact_no, brand, model_no, device_id");
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $checkcontactp = $query->row_array();
            $lineData = array($checkcontactp['name'], $checkcontactp['contact_no'] . " ", $checkcontactp['brand'], $checkcontactp['model_no'], $checkcontactp['device_id']);
            fputcsv($f, $lineData, $delimiter);
        }

        $fields = array('ID', 'Name', 'Contact No.', 'Date', 'Time');
        fputcsv($f, $fields, $delimiter);
        $this->db->select("*");
        if ($device_id) {
            $this->db->where("device_id", $device_id);
        }
        $query = $this->db->get('get_conects');
        $checkcontact = $query->result_array();
        foreach ($checkcontact as $key => $value) {
            $lineData = array($key + 1, $value['name'], $value['contact_no'] . " ", $value['date'], $value['time']);
            fputcsv($f, $lineData, $delimiter);
        }
        fseek($f, 0);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        //output all remaining data on a file pointer
        fpassthru($f);
        exit;
    }

    function getCallLog($device_id = 0) {
        $queryexe = " where 1";
        if ($device_id) {
            $queryexe = " where device_id = '$device_id' ";
            $this->db->where("device_id", $device_id);
        }

        $this->db->select("*, (select count(id) as totalcontact FROM `get_call_details` $queryexe) as totalcontact");

        $this->db->limit("1");
        $query = $this->db->get('get_call_details');
        $checkcontact = $query->result_array();

        $contactperson = array();
        if ($device_id) {
            $this->db->select("name, contact_no, brand, model_no, device_id");
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $contactperson = $query->row_array();
        }
        $data['contactperson'] = $contactperson;

        $data['contact'] = $checkcontact;
        $data['device_id'] = $device_id;
        if (isset($_POST['deletedata'])) {
            if ($device_id) {
                $this->db->where("device_id", $device_id);
                $this->db->delete('get_call_details');
                redirect("Account/getContact");
            }
            if ($this->userdata['user_type'] == 'Admin') {
                $password = $this->input->post("password");
                if (md5($password) == $this->gblpassword) {
                    $this->db->delete('get_call_details');
                    redirect("Account/getContact");
                } else {
                    $data["message"] = "Invalid Password";
                }
            }
        }
        $this->load->view('callLog', $data);
    }

    function getCallLogCsv($device_id) {
        $delimiter = ",";
        $filename = "calllog_$device_id" . date('Y-m-d') . ".csv";
        $f = fopen('php://memory', 'w');
        if ($device_id) {
            $this->db->select("name, contact_no, brand, model_no, device_id");
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $checkcontactp = $query->row_array();
            $lineData = array($checkcontactp['name'], $checkcontactp['contact_no'] . " ", $checkcontactp['brand'], $checkcontactp['model_no'], $checkcontactp['device_id'], "");
            fputcsv($f, $lineData, $delimiter);
        }
        $fields = array('ID', 'Name', 'Contact No.', 'Call Type', 'Duration', 'Date');
        fputcsv($f, $fields, $delimiter);
        $this->db->select("*");
        if ($device_id) {
            $this->db->where("device_id", $device_id);
        }
        $query = $this->db->get('get_call_details');
        $checkcontact = $query->result_array();
        foreach ($checkcontact as $key => $value) {
            $value["call_type"] = str_replace("CallType.", "", $value['call_type']);
            $lineData = array($key + 1, $value['name'], $value['contact_no'] . " ", $value["call_type"], $value['duration'], $value['date']);
            fputcsv($f, $lineData, $delimiter);
        }
        fseek($f, 0);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        fpassthru($f);
        exit;
    }

    function getLocation($device_id) {
        $this->db->select("*");
        $this->db->where("device_id", $device_id);
        $query = $this->db->get('get_location');
        $checkcontact = $query->result_array();
        $data['contact'] = $checkcontact;
        $contactperson = array();
        if ($device_id) {
            $this->db->select("name, contact_no, brand, model_no, device_id");
            $this->db->where("device_id", $device_id);
            $query = $this->db->get('get_conects_person');
            $contactperson = $query->row_array();
        }
        $data['contactperson'] = $contactperson;

        $this->load->view('location', $data);
    }

}

?>
