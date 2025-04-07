<?php

namespace fateh\Category;

class category
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

	public function Category_Log($date, $username, $action, $Entity)
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

		$qur = "INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, 	`Actor_IP`, Comment) VALUES ( ?, ?, ?, ?, ?, ?)";
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
	//###################################################### get
	//################################################

	public function Get_Category($ID)
	{
		if ($this->_UPermission > 350) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Name"];
		}
	}

	//################################################
	//###################################################### Create
	//################################################

	public function C_Category($name)
	{
		if ($this->_UPermission > 350) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 19;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "INSERT INTO `Autoapp_Occupation`( `Visible`, `Name`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $name);
			$stmt->execute();

			//Log generator

			$this->Category_Log($date, $username, $Action, $ElemanType, $comment);
		}
	}

	//################################################
	//######################################################  view
	//################################################

	public function V_Category()
	{
		if ($this->_UPermission > 350) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	//################################################
	//######################################################  Update
	//################################################

	public function U_Category($ID, $name)
	{
		if ($this->_UPermission > 350) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 19;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "No Comment";
			//Database
			$qur = "UPDATE `Autoapp_Occupation` SET `Name`= :name WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->Category_Log($date, $username, $Action, $ElemanType, $comment);
		}
	}


	//################################################
	//######################################################  Delete 
	//################################################


	public function D_Category($ID)
	{
		if ($this->_UPermission > 350) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 19;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Autoapp_Occupation` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->Category_Log($date, $username, $Action, $ElemanType, $comment);
		}
	}
}
