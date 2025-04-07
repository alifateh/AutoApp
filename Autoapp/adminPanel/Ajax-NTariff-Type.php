<?php

function Get_TariffType_ByID($ID)
{

    $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    require("$rootDir/config/config_DB.php");

    $qur = "SELECT * FROM `N_Tariff_Type` WHERE `Visible` = 1 and `GUID`= '$ID' ";
    $stmt = $pdo->prepare($qur);
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
}

if (!empty($_POST["Auto_ID"])) {

    $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    require("$rootDir/config/config_DB.php");

    $AutoID = $_POST["Auto_ID"];

    $qur = "SELECT * FROM `Automobile_TariffType` WHERE `Visible` = 1 and `AutoID`= $AutoID";
    $stmt = $pdo->prepare($qur);
    $stmt->execute();
    $data = $stmt->fetchAll();
    if (!empty($data)) {
        echo "<option value=''> انتخاب نمایید </option>";
        foreach ($data as $row) {
            $NameFA = Get_TariffType_ByID($row['TariffTypeGUID']);
            echo "<option value='" . $row['TariffTypeGUID'] . "'>" . $NameFA[0]['NameFa'] . "</option>";
        }
    } else {
        echo '<option value=""> نوع تعرفه برای خودرو تعریف نشده </option>';
    }
} else {
    echo '<option value=""> ابتدا خودرو را مشخص کنید </option>';
}
