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

    function unseenAppNotification($package_name, $device_id) {
        $this->db->select("count(id) as counter");
        $this->db->where("seen", "no");
        $this->db->where("device_id", $device_id);
        $this->db->where("package_name", $package_name);
        $query = $this->db->get("get_notifications");
        $countnotification = $query->row_array();
        if ($countnotification) {
            return $countnotification["counter"];
        } else {
            return 0;
        }
    }

    function appTitle($package_name) {

        $this->db->where("package_name", $package_name);
        $query = $this->db->get("android_apps");
        $appname = $query->row_array();
        if ($appname) {
            $apptitle = $appname["title"];
            $appimage = $appname["image"];
            if (!$apptitle) {
                $applistdata = explode(".", $package_name);

                $apptitle = end($applistdata);
            }
            if (!$appimage) {
                $appimage = base_url() . "assets/images/" . "defaultapp.png";
            }
            $apparray = array(
                "title" => $apptitle,
                "image" => $appimage,
                "package_name" => $appname["package_name"]
            );
            return $apparray;
        } else {
            $apparray = array(
                "title" => "Unknown App",
                "image" => base_url() . "assets/images/" . "defaultapp.png",
                "package_name" => $package_name
            );
            return $apparray;
        }
    }

    function appNotificationList($device_id) {
        $querydata = "select * from (SELECT aa.package_name, count(aa.id) as counter FROM get_notifications as gn  
                          join android_apps as aa on aa.package_name = gn.package_name
                          where device_id='$device_id' group by package_name) as apptable order by counter desc";
        $querynty = $this->db->query($querydata);
        $notificationdata = $querynty->result_array();
        $applist = [];
        foreach ($notificationdata as $key => $value) {
            $value["counter"] = $this->unseenAppNotification($value["package_name"], $device_id);
            $value["app_info"] = $this->appTitle($value["package_name"]);
            array_push($applist, $value);
        }
        return $applist;
    }

}
