<?php
	function showtip($tipid){
		if($tipid == 0){
			$str =" بدون تیپ ";
			return $str;
		}else{
		require('config/config_DB.php');
		$data = $pdo->query('SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ID`='.$tipid)->fetchAll();
				return ' تیپ '.$data[0]['ModelName'];
		}
	}
if(!empty($_POST["man_id"])){
	if ($_POST["man_id"] !== 0){
		require('config/config_DB.php');
		$manID = $_POST["man_id"];
		$data = $pdo->query('SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ManufacturerID ='.$manID)->fetchAll();
		echo '<option value="0"> انتخاب نمایید </option>';
		foreach ($data as $row ){
					$tip = showtip($row["ModelID"]);
					echo '<option value="'.$row["ID"].'"> ' .$row["Name"].$tip.'</option>';
				}
	}
}

?>