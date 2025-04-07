<?php
function Get_ManufactureLogo_ByID($ID)
{
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$data = $pdo->query("SELECT * FROM `Autoapp_Files` WHERE Visible =1 and `ElemanType` = 8 and `Filekey` =$ID")->fetchAll();
	return $data;
}
if (!empty($_POST["man_id"])) {
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$manID = $_POST["man_id"];
	$data = $pdo->query("SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1 and ID=$manID")->fetchAll();

	foreach ($data as $row) {
		$files = Get_ManufactureLogo_ByID($row["Filekey"]);
        if(!empty($files)){
            echo '<img class="rounded ms-3"><img src="'.$files[0]['location'].'" alt="" height="250px" width="250px">';
        }else{
           echo "<p> نمادی برای خودروساز ثبت نشده است </p>";
        }
	}
}else{
    echo "<p> نمادی برای خودروساز ثبت نشده است </p>";
}
