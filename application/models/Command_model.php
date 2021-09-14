<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Command_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    function currentCommand($device_id, $checkcurrent = false) {
        $this->db->where("device_id", $device_id);
        if ($checkcurrent) {
            $this->db->where("status", "200");
        }
        $query = $this->db->get('track_active_command');
        $checkcommand = $query->result_array();
        $finalcommand = array();
        foreach ($checkcommand as $key => $value) {
            $cmdarray = $value;
            unset($cmdarray["attr"]);

            $cmdarray += json_decode($value["attr"], true);
            $finalcommand[$value["command"]] = $cmdarray;
        }
        return $finalcommand;
    }

}
