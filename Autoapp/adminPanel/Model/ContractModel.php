<?php

namespace fateh\Contarct;

class contarct
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

	public function C_Adminlog($action, $comment)
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$date = date('Y-m-d H:i:s');
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
		$Entity = 60; //contarct
		$username = $_SESSION["Admin_ID"];

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

		$Entity = 60; //contarct

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

	public function C_Contract($txt, $checkbox, $Tariff)
	{
		if ($this->_UPermission > 900) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$Visible = 1;
			$Action = 1;
			$comment = "Create New Contract";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//Database
			$qur = "INSERT INTO `Autoapp_Contracts`( `GUID`, `Visible`, `DateTime`, `Text` ,`CheckboxText`, `TariffVersionID`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $date);
			$stmt->bindParam(4, $txt);
			$stmt->bindParam(5, $checkbox);
			$stmt->bindParam(6, $Tariff);
			$data = $stmt->execute();
			$this->C_Adminlog( $Action, $comment);
			return $data;
		}
	}

	//################################################
	//######################################################  view
	//################################################

	public function V_Contracts()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Contracts` WHERE `Visible` = 1';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	//################################################
	//######################################################  get
	//################################################

	public function Get_Contract_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Contracts` WHERE `GUID` ='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Contract_ByMechanic($tariff, $mechanic)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Contarct_All` WHERE `TariffVersionID` ='$tariff' and `MechanicGUID` ='$mechanic'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Contract_ByMechanicContract($Contract, $mechanic)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Contarct_All` WHERE `ContractGUID` ='$Contract' and `MechanicGUID` ='$mechanic'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	//################################################
	//######################################################  Update
	//################################################
	public function U_Contract_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info

			$status = 1;
			$Action = 22;
			$username = $_SESSION["Mechanic_GUID"];
			$comment = "Approved";
			$date = date('Y-m-d H:i:s');

			//Database
			$qur = "UPDATE `Contarct_All` SET `Status`= :stat, `DateTime`= :DateT WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':stat', $status);
			$stmt->bindParam(':DateT', $date);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$stmt->fetchAll();


			//Log generator

			$this->C_MechanicLog($username, $Action, $comment);
			return "Done";
		}
	}

	public function U_ContractAdmin_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info

			$status = 1;
			$Action = 22;
			$comment = "Approved";
			$date = date('Y-m-d H:i:s');

			//Database
			$qur = "UPDATE `Contarct_All` SET `Status`= :stat, `DateTime`= :DateT WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':stat', $status);
			$stmt->bindParam(':DateT', $date);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$stmt->fetchAll();


			//Log generator

			$this->C_Adminlog($Action, $comment);
			return "Done";
		}
	}


	//################################################
	//######################################################  Delete 
	//################################################


}
