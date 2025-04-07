<?php
session_start();
$sessionid = Session_id();
$time = time();
require('config/config_DB.php');
		
		$qur ="SELECT count(*) FROM `Member` WHERE `Session` = '".$sessionid."'";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$Count = $stmt ->fetchAll();
if ($Count[0]['count(*)'] == 0){
	$qur ="INSERT INTO `Member`( `Session`, `Time`) VALUES ( ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $sessionid);
		$stmt->bindParam(2, $time);
		$stmt -> execute();
}else{
	$qur ="UPDATE `Member` SET `Time`= :time WHERE `Session` = :session";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(':time', $time);
		$stmt->bindParam(':session', $sessionid);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
}

$qur ="SELECT count(*) FROM `Member`";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		echo $data[0]['count(*)'];
		
?>
