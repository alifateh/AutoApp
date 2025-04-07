<?php

namespace fateh\Devices;

class Devices
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

	public function LogDevice($date, $username, $action, $Entity)
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

		$qur = "INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, `Actor_IP`, Comment) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo->prepare($qur);
		$stmt->bindParam(1, $date);
		$stmt->bindParam(2, $username);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt->execute();
	}


	//################################################
	//###################################################### Create
	//################################################

	public function adddevice($name, $com, $fileadd)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 18;
			$Visible = 1;
			$filekey = mt_rand(400000, 999999);
			$Action = 1;
			$comment = "No Comment";

			//add file
			if ($fileadd !== "") {
				$this->adddivcefile($fileadd, $filekey);
			} else {

				$this->adddivcefile($fileadd, $filekey); //means no file add

			}

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "INSERT INTO `Autoapp_SpecialDevice`( `Visible`, `Name`, `Comment` ,`filekey`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $name);
			$stmt->bindParam(3, $com);
			$stmt->bindParam(4, $filekey);
			$stmt->execute();
			$this->LogDevice($date, $username, $Action, $ElemanType, $comment);
		}
	}

	
	public function adddivcefile($fileadd, $filekey)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 20;
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "No Comment";
			//Database
			$qur = "INSERT INTO `Autoapp_Files`( `Visible`, `ElemanType`, `Filekey`, `location`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $ElemanType);
			$stmt->bindParam(3, $filekey);
			$stmt->bindParam(4, $fileadd);
			$stmt->execute();

			//Log generator

			$this->LogDevice($date, $username, $Action, $ElemanType, $comment);
		}
	}

	//################################################
	//###################################################### get
	//################################################

	public function getdevfile($filekey)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Files` WHERE Visible =1 and `ElemanType` = 20 and`Filekey` =' . $filekey;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				if (!empty($row["location"])) {
					echo '<a href="' . $row["location"] . '"><button class="btn btn btn-default btn-icon-anim"><i class="icon-cloud-download"></i><span class="btn-text"> ذخیره فایل </span></button></a><br><br>';
				}
			}
		}
	}

	public function getdevname($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Name"];
		}
	}

	public function getdevtag($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Comment"];
		}
	}

	public function Get_DeviceFiles_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Files` WHERE `Visible` = 1 and `ElemanType` = 20 and `Filekey`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Devices_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  view
	//################################################


	public function V_Devices()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  Delete 
	//################################################

	public function removedevice($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 18;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Autoapp_SpecialDevice` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->LogDevice($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function removedevfile($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 20;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Autoapp_Files` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogDevice($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
			$url = "./V_Devices.php";
			header("Location: $url");
		}
	}

	//################################################
	//######################################################  Update
	//################################################

	public function updatedev($ID, $name, $tag)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 18;
			$Visible = 1;
			$Action = 12;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Autoapp_SpecialDevice` SET `Name`= :name, `Comment`=:tag WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':tag', $tag);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$this->LogDevice($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
			$url = "./V_Devices.php";
			header("Location: $url");
		}
	}
}

?>