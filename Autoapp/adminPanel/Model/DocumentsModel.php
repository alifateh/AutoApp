<?php

namespace fateh\Documents;

class Doc
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

	public function C_Adminlog($action, $comment)
	{
		$date = date('Y-m-d H:i:s');
		$Entity = 61; //contarct
		$username = $_SESSION["Admin_ID"];
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

	public function C_MechanicLog($GUID, $action, $comment)
	{
		$date = date('Y-m-d H:i:s');
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

		$Entity = 61; //contarct

		$qur = "INSERT INTO `Member_Log`( `GUID`, `ActionDateTime`, `Activity`, `Entity`, `Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo->prepare($qur);
		$stmt->bindParam(1, $GUID);
		$stmt->bindParam(2, $date);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt->execute();
	}


	//################################################
	//###################################################### Create
	//################################################

	public function C_MechanicDOC($title, $comment, $fileadd)
	{
		if ($this->_UPermission > 900) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$Visible = 1;
			$Action = 1;
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//Database
			$qur = "INSERT INTO `Mechanic_Documents`(`GUID`, `Visible`, `DateTime`, `Title` ,`Comment`, `Address`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $date);
			$stmt->bindParam(4, $title);
			$stmt->bindParam(5, $comment);
			$stmt->bindParam(6, $fileadd);
			$stmt->execute();
			$this->C_Adminlog($Action, $comment);
		}
	}

	//################################################
	//###################################################### get
	//################################################

	public function Get_MechanicDOC_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Mechanic_Documents` WHERE `Visible` = 1 and `GUID` = ' . $GUID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  view
	//################################################


	public function V_Documents()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Mechanic_Documents` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  Delete 
	//################################################

	public function D_DocumentByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$Visible = 0;
			$Action = 2;
			$comment = "Delete Mechanics Documents";
			//Database
			$qur = "UPDATE `Mechanic_Documents` SET `Visible`= :visible, `DateTime`= :ActionDateTime WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':ActionDateTime', $date);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	//################################################
	//######################################################  Update
	//################################################

	//public function U_Document($ID, $name, $tag)
	//{
	//	if ($this->_UPermission > 400) {
	//		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//		require("$rootDir/config/config_DB.php");
//
	//		//pre info
	//		$date = date('Y-m-d H:i:s');
	//		$ElemanType = 18;
	//		$Visible = 1;
	//		$Action = 12;
	//		$comment = "No Comment";
	//		$username = $_SESSION["Admin_ID"];
	//		//Database
	//		$qur = "UPDATE `Autoapp_SpecialDevice` SET `Name`= :name, `Comment`=:tag WHERE `ID` = :id";
	//		$stmt = $pdo->prepare($qur);
	//		$stmt->bindParam(':name', $name);
	//		$stmt->bindParam(':tag', $tag);
	//		$stmt->bindParam(':id', $ID);
	//		$stmt->execute();
	//		$data = $stmt->fetchAll();
	//		//$this->LogDevice($date, $username, $Action, $ElemanType, $comment);
//
	//		// return VALUES
	//		$url = "./V_Devices.php";
	//		header("Location: $url");
	//	}
	//}
}

?>