<?php

if(!empty($_POST["auto_ID"])){ 
require('config/config_DB.php');
$data = $pdo->query('SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `AutomobileID` ='.$_POST["auto_ID"])->fetchAll();
	foreach ($data as $row ){
		echo "<tr>";
		echo '<td>'.$row['Version'].'</td>';	
		echo '<td><form method="post" action ="GenrateTariffPDF.php"><button class="btn btn-info btn-anim"><i class="icon-cloud-download"></i><span class="btn-text"> ذخیره فایل </span></button>';
		echo '<input type="hidden" id="auto_ID" name="auto_ID" value="'.$_POST["auto_ID"].'">';	
		echo '<input type="hidden" id="SecID" name="SecID" value="'.$row["SecID"].'">';	
		echo '<input type="hidden" id="version" name="version" value="'.$row['Version'].'"></form></td>';	
		//download icon + form to generate pdf
		
		echo "</tr>";
	}
}



?>