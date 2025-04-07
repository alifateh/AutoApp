<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/Model/GarageModel.php");

use fateh\AutoShop\AutoShop as garage;

$Garage_obj = new garage($_SESSION["Admin_GUID"]);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["ProvinceID"]) && !empty($_POST["ProvinceID"])) {
        $data = $Garage_obj->Get_GarageCities($_POST["ProvinceID"]);
        if(!empty($data)){
            foreach($data as $row){
                echo '<option value="' . $row['GUID'] . '">' . $row['FAName'] . '</option>';
            }
        }else{
            echo '<option value=""> استان مشخص نشده </option>';
        }
    }else{
        echo '<option value=""> خطا در برنامه </option>';
    }
}else{
    echo '<option value=""> استان مشخص نشده </option>';
}
