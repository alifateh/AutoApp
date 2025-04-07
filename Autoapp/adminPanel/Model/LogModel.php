<?php

namespace fateh\logger;

class Logger
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
	//######################################################  view
	//################################################
	public function V_AdminsLogs()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT Activity_Log.ID, Activity_Log.Actor_IP, Activity_Log.ActionDateTime,Autoapp_Elemans.PerName as ElemansName ,Activity_Log.Actor_IP,Admin_Users.Fname,Admin_Users.Lname,`Action`.PerName as ActionName 
			FROM `Activity_Log` 
			INNER JOIN Admin_Users ON Activity_Log.User_ID_Actor = Admin_Users.ID 
			INNER JOIN `Action` ON Activity_Log.Activity = `Action`.ID 
			INNER JOIN Autoapp_Elemans ON Activity_Log.Entity = Autoapp_Elemans.ID 
			WHERE Admin_Users.Visible =1 ORDER BY `Activity_Log`.`ID` DESC LIMIT 500";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  get
	//################################################
	public function Get_Username($id)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Admin_Users` where `ID`=' . $id;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_ElementName($elemanID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Elemans` WHERE `Visible` = 1 and `ID` =' . $elemanID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_ActionName($ActionID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Action` WHERE `Visible` = 1 and `ID` =' . $ActionID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
}
class Mechanic_Logger
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
	//######################################################  Get
	//################################################

	public function Get_ElementName($elemanID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Elemans` WHERE `Visible` = 1 and `ID` =' . $elemanID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicInfo($id)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member` where `Visible`= 1 and `GUID`="' . $id . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_ActionName($ActionID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Action` WHERE `Visible` = 1 and `ID` =' . $ActionID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  view
	//################################################

	public function V_MechanicLogs()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT `Member_Log`.`ID`,`Member_Log`.`ActionDateTime`,`Member_Log`.`Actor_IP`,`Member`.`FName`,`Member`.`LName`,`Action`.`PerName` as ActionName, `Autoapp_Elemans`.`PerName` as ElemansName FROM `Member_Log` 
			INNER JOIN `Member` ON `Member_Log`.`GUID` =`Member`.`GUID`
			INNER JOIN `Action` ON `Member_Log`.`Activity` =`Action`.`ID`
			INNER JOIN `Autoapp_Elemans` ON `Member_Log`.`Entity` = `Autoapp_Elemans`.`ID`
			WHERE `Member`.`Visible`=1 ORDER BY `ID` DESC LIMIT 500';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
}
