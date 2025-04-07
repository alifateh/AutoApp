<?php

namespace fateh\tariff;

class NewTariff
{
	private $_UPermission;

	public function __construct($UserGUID)
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require ("$rootDir/config/config_DB.php");
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

	public function LogAdmin($action, $Entity, $comment)
	{
		$username = $_SESSION["Admin_ID"];
		$date = date('Y-m-d H:i:s');

		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require ("$rootDir/config/config_DB.php");
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
		unset($pdo);
	}

	//################################################
	//######################################################  View
	//################################################

	public function V_TariffTypes()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Type` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function V_AllTariffServices()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Services` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariffSepecialSER()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_ServicesOndemand` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_AllTariffVersion()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_TariffVersion()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version_ID` WHERE `Visible` = 1 ORDER By `ID`";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_TariffValid()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `Validation` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function V_TariffArchive()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `Validation` = 0";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_TariffOver($ID, $SecID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_View_Order` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `Tariff_View_Order`.`order` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariffAll()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff` WHERE `Visible` = 1 ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariff_ByMechanicID($MechGUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff`INNER JOIN Member_TariffType ON N_Tariff.TariffTypeGUID = Member_TariffType.TariffTypeGUID WHERE N_Tariff.`Visible` = 1 and `Validation` = 1 and Member_TariffType.MemberGUID ='$MechGUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariff_ByMechanicAutoID($MechGUID, $AutoID, $TariffVerID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			//$qur = "SELECT * FROM `N_Tariff`INNER JOIN Member_TariffType ON N_Tariff.TariffTypeGUID = Member_TariffType.TariffTypeGUID WHERE N_Tariff.`Visible` = 1 and `Validation` = 1 and Member_TariffType.MemberGUID ='$MechGUID' and  `AutoGUID`= $AutoID";
			$qur = "SELECT N_Tariff.GUID AS TGUID, N_Tariff.Visible, N_Tariff.AutoGUID, N_Tariff.TariffVerGUID,N_Tariff.TariffTypeGUID, N_Tariff.Validation, N_Tariff.ValidateDate, Member_TariffType.MemberGUID, Member_TariffType.TariffTypeGUID, N_Tariff_Version.GUID AS VTGUID, N_Tariff_Version.Visible AS TVVisible, N_Tariff_Version.NameFa, N_Tariff_Version.NameEn FROM `N_Tariff`
			INNER JOIN Member_TariffType ON N_Tariff.TariffTypeGUID = Member_TariffType.TariffTypeGUID 
			INNER JOIN N_Tariff_Version ON N_Tariff.TariffVerGUID = N_Tariff_Version.GUID
			WHERE N_Tariff.`Visible` = 1 and `Validation` = 1 and Member_TariffType.MemberGUID ='$MechGUID' and `AutoGUID`= $AutoID and N_Tariff_Version.ID = $TariffVerID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariff_ByMechanicTariffType($MechGUID, $TariffTypeGUID, $TariffVerID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT N_Tariff.GUID AS TGUID, N_Tariff.Visible, N_Tariff.AutoGUID, N_Tariff.TariffVerGUID,N_Tariff.TariffTypeGUID, N_Tariff.Validation, N_Tariff.ValidateDate, Member_TariffType.MemberGUID, N_Tariff_Version.GUID AS VTGUID, N_Tariff_Version.Visible AS TVVisible, N_Tariff_Version.NameFa, N_Tariff_Version.NameEn, N_Tariff_Version.ID FROM `N_Tariff`
			INNER JOIN Member_TariffType ON N_Tariff.TariffTypeGUID = Member_TariffType.TariffTypeGUID 
			INNER JOIN N_Tariff_Version ON N_Tariff.TariffVerGUID = N_Tariff_Version.GUID
			WHERE N_Tariff.`Visible` = 1 and `Validation` = 1 and Member_TariffType.MemberGUID ='$MechGUID' and N_Tariff.TariffTypeGUID = '$TariffTypeGUID' and N_Tariff_Version.ID= $TariffVerID ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NTariffArchive()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff` WHERE `Visible` = 1 and `Validation` = 0";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}



	//################################################
	//######################################################  Create
	//################################################

	public function C_NTariffVersion($NameFa, $NameEn)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 21;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Tariff Version";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_Version`( `GUID`, `Visible`, `NameFa`, `NameEn` ) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $NameFa);
			$stmt->bindParam(4, $NameEn);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function C_NTariffOrder($TariffGUID, $ServiceGUID, $SortOrder)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 24;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Tariff Version";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_Order`( `GUID`, `Visible`, `TariffGUID`, `ServiceGUID`, `SortOrder` ) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $TariffGUID);
			$stmt->bindParam(4, $ServiceGUID);
			$stmt->bindParam(5, $SortOrder);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function C_NTariffPrice($TariffGUID, $ServicePrice, $ServiceGUID)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 25;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Tariff Price";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_Prices`( `GUID`, `Visible`, `ServicePrice`, `ServiceGUID`, `TariffGUID` ) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $ServicePrice);
			$stmt->bindParam(4, $ServiceGUID);
			$stmt->bindParam(5, $TariffGUID);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function C_NTariffOndemand($TariffGUID, $NameFa, $NameEn)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 27;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Tariff ondeamnd Service";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_ServicesOndemand`( `GUID`, `Visible`, `TariffGUID`, `NameFa`, `NameEn` ) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $TariffGUID);
			$stmt->bindParam(4, $NameFa);
			$stmt->bindParam(5, $NameEn);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			if (!empty($data) && $data == 1) {
				return $GUID;
			} else {
				return 0;
			}
		}
	}

	public function C_TariffType($NameFa, $NameEn)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 63;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Tariff Type";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_Type`( `GUID`, `Visible`, `NameFa`, `NameEn` ) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $NameFa);
			$stmt->bindParam(4, $NameEn);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function C_Service($TType, $NameFa, $NameEn)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 12;
			$Visible = 1;
			$Action = 1;
			$comment = "Add New Service";
			$GUID = md5(uniqid(mt_rand(100000, 999999), true));

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");


			//Database
			$qur = "INSERT INTO `N_Tariff_Services`( `GUID`, `Visible`, `TariffTypeGUID`, `NameFa`, `NameEn` ) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $TType);
			$stmt->bindParam(4, $NameFa);
			$stmt->bindParam(5, $NameEn);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function C_NTariff($GUID, $AutoGUID, $TariffVerGUID, $TariffTypeGUID, $ValidateDate)
	{
		if ($this->_UPermission > 400) {
			//pre info

			$ElemanType = 12;
			$Visible = 1;
			$Action = 1;
			$Validation = 1;
			$CreateDate = date('Y-m-d H:i:s');
			$comment = "Add New Tariff";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			//Database
			$qur = "INSERT INTO `N_Tariff`( `GUID`, `Visible`, `AutoGUID`, `TariffVerGUID`, `TariffTypeGUID`, `CreateDate`, `Validation`, `ValidateDate` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $AutoGUID);
			$stmt->bindParam(4, $TariffVerGUID);
			$stmt->bindParam(5, $TariffTypeGUID);
			$stmt->bindParam(6, $CreateDate);
			$stmt->bindParam(7, $Validation);
			$stmt->bindParam(8, $ValidateDate);
			$data = $stmt->execute();

			//Log generator

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}


	//################################################
	//###################################################### get
	//################################################

	public function GET_NTariffValue_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT DISTINCT N_Tariff_Services.NameFa AS SRV_NameFA,
			 N_Tariff_Services.NameEn AS SRV_NameEN,
			  N_Tariff_ServicesOndemand.NameFa,
			   N_Tariff_ServicesOndemand.NameEn,
			    N_Tariff_Prices.`ServicePrice`,
				 N_Tariff_Prices.`ServiceGUID`,
				  N_Tariff_Order.SortOrder,
				  N_Tariff_Prices.GUID AS PGUID,
				  N_Tariff_Prices.TariffGUID 
				  FROM `N_Tariff_Prices` 
				  INNER JOIN N_Tariff_Order  ON N_Tariff_Prices.ServiceGUID = N_Tariff_Order.ServiceGUID  AND N_Tariff_Prices.TariffGUID =  N_Tariff_Order.TariffGUID 
				  LEFT JOIN N_Tariff_ServicesOndemand  ON N_Tariff_Prices.ServiceGUID = N_Tariff_ServicesOndemand.GUID AND N_Tariff_Prices.TariffGUID = N_Tariff_ServicesOndemand.TariffGUID 
				  LEFT JOIN `N_Tariff_Services`  ON N_Tariff_Prices.ServiceGUID = N_Tariff_Services.GUID  
				  WHERE N_Tariff_Prices.`Visible` = 1 AND N_Tariff_Order.Visible =1 AND N_Tariff_Prices.TariffGUID = '$GUID' 
				   ORDER BY `N_Tariff_Order`.`SortOrder` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function GET_NTariff_Count()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT COUNT(*) FROM N_Tariff WHERE `Visible` =1 AND `Validation` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function GET_NTariff_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff` WHERE `Visible` = 1 and `GUID` = '$GUID' ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function Get_TariffType_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Type` WHERE `Visible` = 1 and `GUID`= '$ID' ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_ExistTariffOrder_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Order` WHERE `Visible` = 1 and `GUID`= '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			if (!empty($data)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function Get_NTariffLastOne_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Order` WHERE `TariffGUID`='$GUID' ORDER BY `N_Tariff_Order`.`SortOrder` DESC LIMIT 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_TariffOrder_ByID($Tariff_GUID, $SER_GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Order` WHERE `Visible` = 1 and `TariffGUID`= '$Tariff_GUID' AND `ServiceGUID`= '$SER_GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_TariffType_ByAutoID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_TariffType` WHERE `Visible` = 1 and `AutoID`= $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_TariffType_ByMemberID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Member_TariffType` WHERE `Visible` = 1 and `MemberGUID`= '$ID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Auto_ByTariffID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_TariffType` WHERE `Visible` = 1 and `TariffTypeGUID`= $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_TariffID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Type` WHERE `Visible` = 1 and `GUID`= '$ID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_NService_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Services` WHERE `Visible` = 1 and `GUID`= '$ID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_NSpecialSRE_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_ServicesOndemand` WHERE `Visible` = 1 and `GUID`= '$ID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_NTariffVersion_ByID($ID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible` = 1 and `GUID`= '$ID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_NTariffLastVersion()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible`=1 ORDER BY `N_Tariff_Version`.`ID` DESC LIMIT 1 ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Gat_HejriDate($miladi)
	{

		$miladidate = str_replace("-", "", $miladi);
		$day = substr($miladidate, 6, 2);
		$mon = substr($miladidate, 4, 2);
		$year = substr($miladidate, 0, 4);
		$persian_Date = array($year, $mon, $day);
		return $persian_Date;
	}

	//################################################
	//###################################################### Update
	//################################################

	public function U_TariffValue_ByID($SREGUID, $SERValue)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 12;
			$comment = "Update Service Value";

			$qur = "UPDATE `N_Tariff_Prices` SET `ServicePrice`= :price WHERE `GUID` = :id ";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':price', $SERValue);
			$stmt->bindParam(':id', $SREGUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_TariffValue_ByTariffID($TariffGUID, $SERValue)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 12;
			$comment = "Update Service Value";

			$qur = "UPDATE `N_Tariff_Prices` SET `ServicePrice` = `ServicePrice` + `ServicePrice` * ($SERValue/100) WHERE `TariffGUID` = :id ";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':id', $TariffGUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_TariffType_ByID($GUID, $NameFA, $NameEN)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 12;
			$comment = "Update Tariff Type";

			$qur = "UPDATE `N_Tariff_Type` SET `NameFa`= :NameFa, `NameEn`= :NameEn WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':NameFa', $NameFA);
			$stmt->bindParam(':NameEn', $NameEN);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_NTariff_Services($GUID, $NameFA, $NameEN)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 12;
			$comment = "Update Tariff Service";

			$qur = "UPDATE `N_Tariff_Services` SET `NameFa`= :NameFa, `NameEn`= :NameEn WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':NameFa', $NameFA);
			$stmt->bindParam(':NameEn', $NameEN);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}


	public function U_NTariff_SpecialSER($GUID, $NameFA, $NameEN)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 12;
			$comment = "Update Tariff Special Service";

			$qur = "UPDATE `N_Tariff_ServicesOndemand` SET `NameFa`= :NameFa, `NameEn`= :NameEn WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':NameFa', $NameFA);
			$stmt->bindParam(':NameEn', $NameEN);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_NTariff_Version($GUID, $NameFA, $NameEN)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 21;
			$comment = "Update Tariff Type";

			$qur = "UPDATE `N_Tariff_Version` SET `NameFa`= :NameFa, `NameEn`= :NameEn WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':NameFa', $NameFA);
			$stmt->bindParam(':NameEn', $NameEN);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_NTariff_Validation($GUID, $Validation)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 21;
			$comment = "Update Tariff Type";

			$qur = "UPDATE `N_Tariff` SET `Validation`= :val WHERE `TariffVerGUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':val', $Validation);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_TariffOrder_ByID($GUID, $SortOrder)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 24;
			$comment = "Update Tariff Type";

			$qur = "UPDATE `N_Tariff_Order` SET `SortOrder`= :SortOrder WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':SortOrder', $SortOrder);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_TariffOrder_BySREID($GUID, $SortOrder)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 24;
			$comment = "Update Tariff Type";

			$qur = "UPDATE `N_Tariff_Order` SET `SortOrder`= :SortOrder WHERE `ServiceGUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':SortOrder', $SortOrder);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function U_NTariffVer_ByID($GUID, $VerGUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 21;
			$comment = "Update Tariff Version";

			$qur = "UPDATE `N_Tariff` SET `TariffVerGUID`= :Ver WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Ver', $VerGUID);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			if (empty($data)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function U_NTariffDate_ByID($GUID, $Date)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 21;
			$comment = "Update Tariff Version";

			$qur = "UPDATE `N_Tariff` SET `ValidateDate`= :dat WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':dat', $Date);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			if (empty($data)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function U_NTariffValidation_ByID($GUID, $Valid)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");
			$Action = 12;
			$ElemanType = 22;
			$comment = "Update Tariff Validation $GUID";

			$qur = "UPDATE `N_Tariff` SET `Validation`= :valid WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':valid', $Valid);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			//$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			if ($data == true) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	//################################################
	//###################################################### Delete
	//################################################

	public function D_TariffType_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$Visible = 0;
			$Action = 2;
			$ElemanType = 12;
			$comment = "Remove Tariff Type";

			$qur = "UPDATE `N_Tariff_Type` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function D_NTariffService_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$Visible = 0;
			$Action = 2;
			$ElemanType = 64;
			$comment = "Remove Tariff Services";

			$qur = "UPDATE `N_Tariff_Services` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function D_NTariffVersion_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$Visible = 0;
			$Action = 2;
			$ElemanType = 21;
			$comment = "Remove Tariff Version";

			$qur = "UPDATE `N_Tariff_Version` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function D_NTariff_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require ("$rootDir/config/config_DB.php");

			$Visible = 0;
			$Action = 2;
			$ElemanType = 22;
			$comment = "Remove Tariff Version";

			$qur = "UPDATE `N_Tariff` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->LogAdmin($Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function getversion($ID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible` = 1 and `ID`=".$ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
}
