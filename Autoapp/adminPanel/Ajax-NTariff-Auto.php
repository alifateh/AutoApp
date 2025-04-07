<?php
function Get_AutoTip_ByID($tipid)
{
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$data = $pdo->query('SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ID`=' . $tipid)->fetchAll();
	return "[ تیپ " .$data[0]['ModelName']." ]";
}
if (!empty($_POST["man_id"])) {
	$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	require("$rootDir/config/config_DB.php");
	$manID = $_POST["man_id"];
	$data = $pdo->query('SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ManufacturerID =' . $manID)->fetchAll();
	echo '<option value=""> انتخاب نمایید </option>';
	foreach ($data as $row) {
		$tip = Get_AutoTip_ByID($row["ModelID"]);
		echo '<option value="' . $row["ID"] . '"> ' . $row["Name"] ." ".$tip . '</option>';
	}
}else{
	echo '<option value=""> ابتدا خودروساز را مشخص نمایید </option>';
}
