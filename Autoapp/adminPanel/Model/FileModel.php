<?php

namespace fateh\managefile;

class managefile

{
	private $_UPermission;
	public function __construct($UserGUID)
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur = "SELECT * FROM `Admin_Permission` WHERE `AdminGUID` ='" . $UserGUID . "'";
		$stmt = $pdo->prepare($qur);
		$stmt->execute();
		$data = $stmt->fetchAll();
		$count = 0;
		$Permission = null;
		if (is_array($data)) {
			$count = count($data);
			if ($count > 0) {
				$Permission = $data[0]['AccessValue'];
			} else {
				$qur = "SELECT * FROM `Member_Permission` WHERE `MemberGUID` ='" . $UserGUID . "'";
				$stmt = $pdo->prepare($qur);
				$stmt->execute();
				$data = $stmt->fetchAll();
				$Permission = $data[0]['AccessValue'];
			}
		}
		if (!empty($Permission)) {
			$this->_UPermission = $Permission;
		} else {
			$url = "/";
			header("Location: $url");
		}
	}
	//################################################
	//###################################################### log
	//################################################
	public function Logfiles($username, $action, $Entity)
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		//whether ip is from proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		//whether ip is from remote address
		else {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}

		$comment = "File Add";
		$date = date('Y-m-d H:i:s');

		$qur = "INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, `Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo->prepare($qur);
		$stmt->bindParam(1, $date);
		$stmt->bindParam(2, $username);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt->execute();
	}

	//public function getfkey($elementype, $elementID)
	//{
	//	if ($this->_UPermission > 100) {
	//		if ($elementype == 20) { //devices file
	//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//			require("$rootDir/config/config_DB.php");
	//			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID`= " . $elementID;
	//			$stmt = $pdo->prepare($qur);
	//			$stmt->execute();
	//			$data = $stmt->fetchAll();
	//			return $data[0]["filekey"];
	//		} elseif ($elementype == 9) { //automobile photo
	//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//			require("$rootDir/config/config_DB.php");
	//			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and `ID`= " . $elementID;
	//			$stmt = $pdo->prepare($qur);
	//			$stmt->execute();
	//			$data = $stmt->fetchAll();
	//			return $data[0]["filekey"];
	//		} elseif ($elementype == 8) { //manufacturelogo
	//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//			require("$rootDir/config/config_DB.php");
	//			$qur = "SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1 and `ID`= " . $elementID;
	//			$stmt = $pdo->prepare($qur);
	//			$stmt->execute();
	//			$data = $stmt->fetchAll();
	//			return $data[0]["Filekey"];
	//		} else { // others files
	//			$filekey = mt_rand(100000, 999999);
	//			return $filekey;
	//		}
	//	}
	//}
	public function uploadfile($fileadd, $filekey, $ElemanType)
	{
		if ($this->_UPermission > 100) {
			//pre info
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$fileadd;
			//Database
			$qur = "INSERT INTO `Autoapp_Files`( `Visible`, `ElemanType`, `Filekey`, `location`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $ElemanType);
			$stmt->bindParam(3, $filekey);
			$stmt->bindParam(4, $fileadd);
			$stmt->execute();

			$this->Logfiles($username, $Action, $ElemanType);
		}
	}

	//public function viewfile()
	//{
	//	if ($this->_UPermission > 100) {
	//		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//		require("$rootDir/config/config_DB.php");
	//		$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1";
	//		$stmt = $pdo->prepare($qur);
	//		$stmt->execute();
	//		$data = $stmt->fetchAll();
	//		foreach ($data as $row) {
	//			//$logo = $this-> viewmanufacturelogo($row['Filekey']);
	//			echo "<tr>";
	//			echo "<td>" . $row['Name'] . "</td>";
	//			echo '<td style ="width: 5%;">';
	//			echo '<form method="post">
	//			<input type="hidden" id="skillsId" name="skillsId" value="' . $row['ID'] . '">
	//			<input type="hidden" id="action" name="action" value="remove">
	//			<button class="btn btn-info btn-icon-anim"><i class="icon-trash"></i> حذف </button>
	//			</form></td><td style ="width: 5%;">
	//			<form method="post" enctype="multipart/form-data" action ="edit-skills.php">
	//			<input type="hidden" id="skillsId" name="skillsId" value="' . $row['ID'] . '">
	//			<input type="hidden" id="action" name="action" value="edit">
	//			<button class="btn btn-danger btn-icon-anim "><i class="icon-settings"></i> ویرایش </button>
	//			</form>';
	//			echo "</span></td>";
	//			echo "</tr>";
	//		}
	//	}
	//}

	public function removefile($ID)
	{
		if ($this->_UPermission > 100) {
			//pre info
			$ElemanType = 17;
			$Visible = 0;
			$Action = 2;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Service_Type` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$stmt->fetchAll();
			$this->Logfiles($username, $Action, $ElemanType);
		}
	}
}
