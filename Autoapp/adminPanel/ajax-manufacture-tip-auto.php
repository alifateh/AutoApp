<?php
if(!empty($_POST["man_id"])){ 
require('config/config_DB.php');
$data = $pdo->query('SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ManufacturerID` ='.$_POST["man_id"])->fetchAll(PDO::FETCH_ASSOC);
echo '<option value="0" selected="selected"></option>';
	foreach ($data as $row ){	
	echo '<option value="'.$row['ID'].'">'.$row['ModelName'].'</option>';
	}
}
?>