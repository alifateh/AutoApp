<?php
function Get_AutoPic($ID)
{
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$data = $pdo->query("SELECT * FROM `Autoapp_Files` WHERE Visible =1 and `ElemanType` = 9 and `Filekey` ='$ID'")->fetchAll();
	return $data;
}
if (!empty($_POST["Auto_ID"])) {
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$Auto_ID = $_POST["Auto_ID"];
	$data = $pdo->query("SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID=$Auto_ID")->fetchAll();

	foreach ($data as $row) {
		$files = Get_AutoPic($row["filekey"]);
        if(!empty($files)){
            echo '<img class="rounded ms-3"><img src="'.$files[0]['location'].'" alt="" height="250px" width="250px">';
        }else{
           echo "<p> نمادی برای خودروساز ثبت نشده است </p>";
        }
	}
}else{
    echo "<p> نمادی برای خودروساز ثبت نشده است </p>";
}
