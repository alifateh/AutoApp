<?php

namespace fateh\Automobile;

class Automobile
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

	public function LogAutomobile($date, $username, $action, $Entity, $comment)
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


	//################################################
	//###################################################### Create
	//################################################

	public function C_AutoTariffType($AutoID, $Tariff)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 63;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "Add Tariff Type to Auto";
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Automobile_TariffType` (`Visible`, `TariffTypeGUID`, `AutoID`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $Tariff);
			$stmt->bindParam(3, $AutoID);
			$stmt->execute();

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function C_ManufactureLogo_ByID($filekey, $FileMove)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 8;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "No Comment";
			//Database
			$qur = "INSERT INTO `Autoapp_Files`( `Visible`, `ElemanType`, `Filekey`, `location`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $ElemanType);
			$stmt->bindParam(3, $filekey);
			$stmt->bindParam(4, $FileMove);
			$stmt->execute();

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
		}
	}
	public function C_Manufacture($name, $filekey, $FileMove)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 5;
			$Visible = 1;
			//$filekey = mt_rand(100000, 999999);
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "INSERT INTO `Automobile_Manufacturer`( `Visible`, `Name`, `Filekey`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $name);
			$stmt->bindParam(3, $filekey);
			$stmt->execute();

			//add file
			if ($FileMove !== "") {
				$this->C_ManufactureLogo_ByID($filekey, $FileMove);
			} else {

				$this->C_ManufactureLogo_ByID($filekey, ""); //means no file add
			}

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			// return VALUES
		}
	}


	public function C_AutoModel_ByID($name, $manid)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 7;
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "No Comment";
			//Database
			$qur = "INSERT INTO `Automobile_Tip` ( `Visible`, `ManufacturerID`, `ModelName`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $manid);
			$stmt->bindParam(3, $name);
			$stmt->execute();

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}
	public function C_AutoFile_ByID($fileadd, $filekey)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 9;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "INSERT INTO `Autoapp_Files`( `Visible`, `ElemanType`, `Filekey`, `location`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $ElemanType);
			$stmt->bindParam(3, $filekey);
			$stmt->bindParam(4, $fileadd);
			$stmt->execute();

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
		}
	}


	public function C_Automobile($name, $ManuID, $modelID, $inetrnal, $fileadd, $filekey)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 2;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "INSERT INTO `Automobile_Name`( `Visible`, `ManufacturerID`, `ModelID`,`Name`, `internalproduct`,`filekey`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $ManuID);
			$stmt->bindParam(3, $modelID);
			$stmt->bindParam(4, $name);
			$stmt->bindParam(5, $inetrnal);
			$stmt->bindParam(6, $filekey);
			$stmt->execute();


			//add file
			if ($fileadd !== "0") {
				$this->C_AutoFile_ByID($fileadd, $filekey);
			} else {

				$this->C_AutoFile_ByID($fileadd, $filekey); //means no file add
			}

			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
		}
	}

	//################################################
	//###################################################### get
	//################################################

	public function Get_ManufactureLogo_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Files` WHERE Visible =1 and `ElemanType` = 8 and `Filekey` ='" . $ID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function Get_Manufactuer_ByID($manID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1 and ID=" . $manID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_ManufactuerAll()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function Get_Automobile_ByID($autoID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID= $autoID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoCount_ByUserID($User_GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_Auto` WHERE `GUID` ='" . $User_GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$counter = 0;
			foreach ($data as $row) {
				$counter++;
			}
			return $counter;
		}
	}
	public function Get_AutoCount_ByGarageID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Garage_Auto` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function Get_GarageAuto_ByGUID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Auto` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_GarageNOTAuto_ByGUID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM (SELECT `AutoID`,`GUID` FROM `Garage_Auto` WHERE `GUID` = "'.$GUID.'") AS GaragCars RIGHT JOIN `Automobile_Name` ON `GaragCars`.`AutoID` = `Automobile_Name`.`ID` WHERE `GaragCars`.`AutoID` IS NUll';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicNOTAuto_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM (SELECT `AutoID`,`GUID` FROM `Member_Auto` WHERE `GUID` = "'.$GUID.'") AS GaragCars RIGHT JOIN `Automobile_Name` ON `GaragCars`.`AutoID` = `Automobile_Name`.`ID` WHERE `GaragCars`.`AutoID` IS NUll';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicAuto_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Auto` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoCountAll()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$counter = 0;
			foreach ($data as $row) {
				$counter++;
			}
			return $counter;
		}
	}

	public function Get_Tip_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ID`='" . $ID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Tip_ByManufactuererID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ManufacturerID`='" . $ID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_TipAll()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoInternal_ByID($autoID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID=" . $autoID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["internalproduct"];
		}
	}

	public function Get_AutoDetial_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID=" . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoPic($fileID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Files` WHERE Visible =1 and `ElemanType` = 9 and `Filekey` ='" . $fileID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoTariffType_ByAutoID ($AutoID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_TariffType` WHERE Visible =1 and `AutoID` = $AutoID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoTariffType_ByID ($TariffGUID, $AutoID )
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_TariffType` WHERE Visible =1 and `TariffTypeGUID` = '$TariffGUID' and `AutoID` = $AutoID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_HideAutoTariffType_ByID ($AutoID, $TariffGUID )
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Automobile_TariffType` WHERE `TariffTypeGUID` = '$TariffGUID' and `AutoID` = $AutoID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//###################################################### Update
	//################################################
	public function U_Manufacture($ID, $name)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 5;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Manufacturer` SET `Name`= :name WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_Automobile($ID, $autoname, $automan, $autotip, $autointernal)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 2;
			$Action = 12;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Name` SET `ManufacturerID`= :manID, `ModelID`= :tipID, `Name`= :name,`internalproduct`= :internal WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':manID', $automan);
			$stmt->bindParam(':tipID', $autotip);
			$stmt->bindParam(':name', $autoname);
			$stmt->bindParam(':internal', $autointernal);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_AutoTariff($ID, $autoname, $automan, $autotip, $autointernal, $Tariff)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 63;
			$Action = 12;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$data = $this ->U_Automobile($ID, $autoname, $automan, $autotip, $autointernal);
			if(!empty($Tariff)){
				$is_Exist = $this -> Get_HideAutoTariffType_ByID ($ID, $Tariff);
				if(!empty($is_Exist)){
					$this -> U_UnhideAUtoTariff_ByAutoID($ID, $Tariff);
				}else{
					$this -> C_AutoTariffType($ID, $Tariff);
				}
			}
		
			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_Tip($ID, $tipname, $tipman)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 7;
			$Action = 12;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Tip` SET `ManufacturerID`= :manID, `ModelName`= :name WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':manID', $tipman);
			$stmt->bindParam(':name', $tipname);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}

	//################################################
	//###################################################### view
	//################################################

	public function V_AutoManufactures()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_AutomobileTips()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_Automobiles()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_N_Automobiles()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT t.`NameFa`, a.`ID`, a.AutoName, a.ManufacturerName, a.`ModelName`, a.`location`, a.`TariffTypeGUID` FROM `N_Tariff_Type` t INNER JOIN (SELECT `Automobile_Name`.`ID`, `Automobile_Name`.`Name` AS AutoName,`Automobile_Manufacturer`.`Name` AS ManufacturerName, `Automobile_Tip`.`ModelName`, `Autoapp_Files`.`location`, `Automobile_TariffType`.`TariffTypeGUID` FROM `Automobile_Name` INNER JOIN `Automobile_Manufacturer` ON `Automobile_Name`.`ManufacturerID` = `Automobile_Manufacturer`.`ID` INNER JOIN `Automobile_Tip` ON `Automobile_Name`.`ModelID` = `Automobile_Tip`.`ID` INNER JOIN `Autoapp_Files` ON `Automobile_Name`.`filekey` = `Autoapp_Files`.`Filekey` INNER JOIN `Automobile_TariffType` ON `Automobile_Name`.`ID` = `Automobile_TariffType`.`AutoID` WHERE `Automobile_Name`.`Visible` = 1 AND `Automobile_Manufacturer`.`Visible` =1 AND `Autoapp_Files`.`Visible`=1) a ON t.`GUID` = a.`TariffTypeGUID`";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

//	public function selectautomodel($autoID)
//	{
//		if ($this->_UPermission > 450) {
//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
//			require("$rootDir/config/config_DB.php");
//			$qur = "SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and ID =" . $autoID;
//			$stmt = $pdo->prepare($qur);
//			$stmt->execute();
//			$data = $stmt->fetchAll();
//			return $data[0]["ModelName"];
//		}
//	}
//
//	public function selectautoname($manID)
//	{
//		if ($this->_UPermission > 450) {
//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
//			require("$rootDir/config/config_DB.php");
//			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ManufacturerID =" . $manID;
//			$stmt = $pdo->prepare($qur);
//			$stmt->execute();
//			$data = $stmt->fetchAll();
//			foreach ($data as $row) {
//				if ($row["ModelID"] !== 0) {
//					$model = $this->Get_Tip_ByID($row['ModelID']);
//					echo '<option value="' . $row['ID'] . '">' . $row['Name'] . ' تیپ [' . $model[0]["ModelName"] . ']</option>';
//				} else {
//					echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
//				}
//			}
//		}
//	}

//	public function selectedautoname($manID, array $AutoID)
//	{
//		if ($this->_UPermission > 450) {
//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
//			require("$rootDir/config/config_DB.php");
//			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ManufacturerID =" . $manID;
//			$stmt = $pdo->prepare($qur);
//			$stmt->execute();
//			$data = $stmt->fetchAll();
//			foreach ($data as $row) {
//				$select = $this->Get_AutoSelected($row['ID'], $AutoID);
//				if ($row["ModelID"] !== 0) {
//					$model = $this->Get_Tip_ByID($row['ModelID']);
//					echo '<option value="' . $row['ID'] . '"' . $select . '>' . $row['Name'] . ' تیپ [' . $model[0]["ModelName"] . ']</option>';
//				} else {
//					echo '<option value="' . $row['ID'] . '"' . $select . '>' . $row['Name'] . '</option>';
//				}
//			}
//		}
//	}

//	public function viewautoselectable()
//	{
//		if ($this->_UPermission > 450) {
//			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
//			require("$rootDir/config/config_DB.php");
//			$qur = "SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1";
//			$stmt = $pdo->prepare($qur);
//			$stmt->execute();
//			$data = $stmt->fetchAll();
//			foreach ($data as $row) {
//				echo '<optgroup label=" اضافه کردن گروه خودرهای [' . $row['Name'] . ']">';
//				$this->selectautoname($row['ID']);
//				echo '</optgroup>';
//			}
//		}
//	}

//	public function Get_AutoSelected($ID, $AutoID)
//	{
//		if ($ID == $AutoID) {
//			return " selected ";
//		}else{
//			return "";
//		}
//	}

	//################################################
	//###################################################### Delete
	//################################################

	public function D_Manufactur_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 5;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Manufacturer` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			return $data;
		}
	}

	public function D_AutoTariffType_ByAutoID($AutoID, $TariffType)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 63;
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Tariff Type";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_TariffType` SET `Visible`= :visible WHERE `TariffTypeGUID` = :Tguid and `AutoID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':Tguid', $TariffType);
			$stmt->bindParam(':id', $AutoID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			return $data;
		}
	}

	public function U_UnhideAUtoTariff_ByAutoID($AutoID, $Tariff)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 63;
			$Visible = 1;
			$Action = 2;
			$comment = "Remove Tariff Type";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_TariffType` SET `Visible`= :visible WHERE `AutoID` = :id and `TariffTypeGUID` = :Tariff";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':Tariff', $Tariff);
			$stmt->bindParam(':id', $AutoID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			return $data;
		}
	}

	public function D_AllAutoTariffType_ByAutoID($AutoID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 63;
			$Visible = 0;
			$Action = 2;
			$comment = "Remove ALL Tariff Type";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_TariffType` SET `Visible`= :visible WHERE `AutoID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $AutoID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			return $data;
		}
	}

	public function D_ManufacturLogo_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 8;
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

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}


	public function D_AutomonbileFile_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 9;
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
			$stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
			$url = "V_Automobiles.php";
			header("Location: $url");
		}
	}

	public function removeAuto($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 2;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Name` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}
	
	//################################################
	//###################################################### Delete
	//################################################

	public function D_AutomobileTip_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 7;
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Automobile Tip $ID";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Automobile_Tip` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			//Log generator
			$this->LogAutomobile($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}
}
