<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Curd_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function query($query) {
        $query = $this->db->query($query);
        $data = $query->result_array();
        return $data; //format the array into json data
    }

    public function insert($table, $data) {
        $this->db->insert($table, $data);
        return $insert_id = $this->db->insert_id();
    }

    public function get($table, $order_by = 'asc') {
        $this->db->order_by('id', $order_by);
        $query = $this->db->get($table);
        $data = $query->result_array();
        return $data;
    }
    
    public function get_single($table, $id) {
        $this->db->where('id', $id);
        $query = $this->db->get($table);
        $data = $query->row();
        return $data;
    }

}

?>