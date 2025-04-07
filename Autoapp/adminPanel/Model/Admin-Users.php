<?php

namespace fateh\login;

class Admin
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
	//######################################################  log
	//################################################

	public function C_AdminLog ($action, $comment)
	{
		if ($this->_UPermission > 900) {
			$date = date('Y-m-d H:i:s');
			$Entity = 59;
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

			$qur = "INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, 	`Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $date);
			$stmt->bindParam(2, $username);
			$stmt->bindParam(3, $action);
			$stmt->bindParam(4, $Entity);
			$stmt->bindParam(5, $ip_address);
			$stmt->bindParam(6, $comment);
			$stmt->execute();
		}
	}
	
	//################################################
	//######################################################  Create 
	//################################################

	public function C_AdminUser($fileadd, $filekey)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$Visible = 1;
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

			$this->C_AdminLog($Action, $comment);
		}
	}


	//################################################
	//######################################################  view
	//################################################

	public function V_Admin_Users()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Admin_Users`';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function V_PermissionAll()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_PermissionList` WHERE `Visible`=1';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  get
	//################################################
	public function Get_AdminPermission($GUID){
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Admin_Permission` WHERE `AdminGUID` ='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Admin($GUID){
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Admin_Users` WHERE `GUID` ='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function Get_PermissionByValue($value){
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");			
			$qur = "SELECT * FROM `Autoapp_PermissionList` WHERE $value BETWEEN `Start_Value` AND `End_Value`";			
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Permission(){
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");			
			$qur = "SELECT * FROM `Autoapp_PermissionList` WHERE `Visible` =1";			
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	//################################################
	//######################################################  Update
	//################################################
	public function U_AdminPermission($GUID, $Value)
	{
		if ($this->_UPermission > 10100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			
			$Action = 23;
			$comment = "Change Admin Permission";
			
			//Database
			$qur = "UPDATE `Admin_Permission` SET `AccessValue`= :AccessValue WHERE `AdminGUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':AccessValue', $Value);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->C_AdminLog($Action, $comment);
			return $data;
		}
	}
	public function U_ActiveAdmin($ID)
	{
		if ($this->_UPermission > 10100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 1;
			$Action = 8;
			$comment = "Active Admin";
			
			//Database
			$qur = "UPDATE `Admin_Users` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->C_AdminLog($Action, $comment);
			return $data;
		}
	}

	public function U_AdminProfile($ID, $Fname, $Lname, $Aname, $email, $Pass)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Admin Profile";
			
			//Database
			$qur = "UPDATE `Admin_Users` SET `Fname`= :fname, `Lname`= :lname, `Aname`= :aname, `Pass`= :Pass,`Email`= :email WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':fname', $Fname);
			$stmt->bindParam(':lname', $Lname);
			$stmt->bindParam(':aname', $Aname);
			$stmt->bindParam(':Pass', $Pass);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->C_AdminLog($Action, $comment);
			return $data;
		}
	}

	//################################################
	//######################################################  Delete 
	//################################################

	//public function D_Admin($ID)
	//{
	//	if ($this->_UPermission > 10100) {
	//		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
	//		require("$rootDir/config/config_DB.php");
//
	//		//pre info
	//		$Visible = 0;
	//		$Action = 2;
	//		$comment = "Remove Admin";
	//		
	//		//Database
	//		$qur = "UPDATE `Admin_Users` SET `Visible`= :visible WHERE `GUID` = :id";
	//		$stmt = $pdo->prepare($qur);
	//		$stmt->bindParam(':visible', $Visible);
	//		$stmt->bindParam(':id', $ID);
	//		$stmt->execute();
	//		$data = $stmt->fetchAll();
//
//
	//		//Log generator
//
	//		$this->C_AdminLog($Action, $comment);
	//		return $data;
	//	}
	//}
	

}