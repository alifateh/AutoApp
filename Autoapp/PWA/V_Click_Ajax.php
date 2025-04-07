<?php
session_start();
$member_GUID = $_SESSION["Mechanic_GUID"];

$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/Model/MonitorAdvModel.php");
use fateh\Advertisements\Monitor as monitor;
$MonitorObj = new monitor($member_GUID);

if( $_SERVER['REQUEST_METHOD']=='POST' && !empty( $_POST['AdvGUID'] ) ){
    $Adv_GUID=$_POST['AdvGUID'];
    $MonitorObj->U_AddCounter_ByID($Adv_GUID);
}