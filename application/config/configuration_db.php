<?php

$activedb = "mobitracker_app";
$activeusername = "mobitracker_app";
$activepassword = "JE&6gGOcaRR[";

$baselink = 'http://' . $_SERVER['SERVER_NAME'];

if (strpos($baselink, 'varbin')) {

    $activedb = "j2k5e6r5_araskocon";
    $activeusername = "j2k5e6r5_octopus";
    $activepassword = "India$2017";
}

if (strpos($baselink, 'localhost')) {

    $activedb = "j2k5e6r5_araskocon";
    $activeusername = "j2k5e6r5_octopus";
    $activepassword = "India$2017";
}
?>