<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 */
defined('BASEPATH') OR exit('No direct script access allowed');
ob_start();

class UserManager extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->user_id = $this->session->userdata('logged_in')['login_id'];
        $this->user_type = $this->session->logged_in['user_type'];
    }

    public function index() {
        $this->db->order_by("id", "desc");
        $this->db->from('admin_users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $data['users'] = $query->result();
        } else {
            $data['users'] = [];
        }
        if ($this->user_type != 'Admin') {
            redirect('UserManager/not_granted');
        }

        $this->load->view('userManager/usersReport', $data);
    }

    public function not_granted() {
        $userdata = array();
        $this->session->unset_userdata($userdata);
        $this->session->sess_destroy();
        $this->load->view('errors/404');
    }

    public function usersReportManager() {



        $this->db->order_by('id', 'desc');

        $this->db->where(array('status!=' => 'Blocked'));


        $query = $this->db->get('admin_users');

        $data['users_manager'] = $query->result();

        if ($this->user_type == 'Manager') {
            redirect('UserManager/not_granted');
        }

        $this->load->view('userManager/usersReport', $data);
    }

    public function addManager() {
        $config['upload_path'] = 'assets_main/profile_image';
        $config['allowed_types'] = '*';
        $data["message"] = "";
        $data['user_type'] = $this->user_type;
        if (isset($_POST['submit'])) {
            $picture = '';
            if (!empty($_FILES['picture']['name'])) {
                $temp1 = rand(100, 1000000);
                $ext1 = explode('.', $_FILES['picture']['name']);
                $ext = strtolower(end($ext1));
                $file_newname = $temp1 . "1." . $ext;
                ;
                $config['file_name'] = $file_newname;
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('picture')) {
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                } else {
                    $picture = '';
                }
            }

            $email = $this->input->post('email');


            $this->db->where('email', $email);
            $query = $this->db->get('admin_users');
            $user_details = $query->row();

            if ($user_details) {
                $data["message"] = "Email already exist.";
            } else {
                $op_date_time = date('Y-m-d H:i:s');
                $user_type = $this->input->post('user_type');
                $password = $this->input->post('password');
                $pwd = md5($password);
                $first_name = $this->input->post('name');
                $last_name = "";

                $contact_no = $this->input->post('contact_no');

                $post_data = array(
                    'name' => $first_name,
                    'last_name' => $last_name,
                    'email ' => $email,
                    'user_type' => $user_type,
                    'password2' => $password,
                    'image' => $picture,
                    'password' => $pwd,
                    'contact_no' => $contact_no,
                    'op_date_time' => $op_date_time
                );
                $this->db->insert('admin_users', $post_data);
                redirect('UserManager/addManager');
            }
        }
        $this->load->view('userManager/addManager', $data);
    }

    public function profile($userid) {
        $data = array();
        // echo password_hash('rasmuslerdorf', PASSWORD_DEFAULT)."\n";

        $query = $this->db->get_where("admin_users", array("id" => $userid));
        $userdata = $query->row();
        $data['userdata'] = $userdata;



        $data['country'] = array();

        $config['upload_path'] = 'assets/profile_image';
        $config['allowed_types'] = '*';
        if (isset($_POST['submit'])) {
            $picture = '';

            if (!empty($_FILES['picture']['name'])) {
                $temp1 = rand(100, 1000000);
                $config['overwrite'] = TRUE;
                $ext1 = explode('.', $_FILES['picture']['name']);
                $ext = strtolower(end($ext1));
                $file_newname = $temp1 . "$userid." . $ext;
                $picture = $file_newname;
                $config['file_name'] = $file_newname;
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('picture')) {
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                } else {
                    $picture = '';
                }
            }
            $this->db->set('image', $picture);
            $this->db->where('id', $userid); //set column_name and value in which row need to update
            $this->db->update('admin_users');
            $this->userdata['image'] = $picture;
            redirect("userManager/profile/" . $userid);
        }

        if (isset($_POST['changePassword'])) {
            $c_password = $this->input->post('c_password');
            $n_password = $this->input->post('n_password');
            $r_password = $this->input->post('r_password');
            $dc_password = $userdata->password;
            if ($dc_password) {
                if ($r_password == $n_password) {
                    $message = array(
                        'title' => 'Password Changed.',
                        'text' => 'Your password has been changed successfully.',
                        'show' => true,
                        'icon' => 'happy.png'
                    );
                    $this->session->set_flashdata("checklogin", $message);

                    $orderlog = array(
                        'log_type' => "Password Changed",
                        'log_datetime' => date('Y-m-d H:i:s'),
                        'user_id' => $userid,
                        'order_id' => "",
                        'log_detail' => 'Your password has been changed successfully.',
                    );
                    $this->db->insert('system_log', $orderlog);


                    $passowrd = array("password" => md5($n_password), "password2" => $n_password);
                    $this->db->set($passowrd);
                    $this->db->where("id", $userid);
                    $this->db->update("admin_users");

                    redirect("userManager/profile/" . $userid);
                } else {
                    $message = array(
                        'title' => 'Password Error.',
                        'text' => 'Entered password does not match.',
                        'show' => true,
                        'icon' => 'warn.png'
                    );
                    $this->session->set_flashdata("checklogin", $message);
                }
            } else {
                $message = array(
                    'title' => 'Password Errors.',
                    'text' => 'Current password does not match.',
                    'show' => true,
                    'icon' => 'warn.png'
                );
                $this->session->set_flashdata("checklogin", $message);
            }
        }


        $this->load->view('userManager/profile', $data);
    }

}
