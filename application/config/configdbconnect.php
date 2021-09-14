<?php
require('configuration_db.php');
$globleConnectDB = array();
$globleConnectTheme = array("style_css"=>"");
try {
    $username = $activeusername;
    $password = $activepassword;
    $dbname = $activedb;

    $conn = new PDO("mysql:host=localhost;dbname=$activedb", $username, $password);


    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare('SELECT * FROM configuration_site');
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $globleConnectDB = $row;
    }
    
    
    $stmt2 = $conn->prepare('SELECT * FROM theme_css');
    $stmt2->execute();
    while ($row = $stmt2->fetch()) {
        $globleConnectTheme = $row;
    }

    $stmt = $conn->prepare('SELECT * FROM configuration_report');
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $globleConnectReport = $row;
    }


    $stmt = $conn->prepare('SELECT * FROM configuration_cartcheckout');
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $globleConnectCheckout = $row;
    }

    
} catch (PDOException $e) {
    
}
