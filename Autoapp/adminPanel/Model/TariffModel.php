<?php

namespace fateh\tariff;

class tariff
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

	public function Logtariff($date, $username, $action, $Entity, $comment)
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
		unset($pdo);
	}

	//################################################
	//######################################################  View
	//################################################

	public function V_ItemsAll()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Item` WHERE `Visible` = 1";
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
			require("$rootDir/config/config_DB.php");

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
			require("$rootDir/config/config_DB.php");
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
			require("$rootDir/config/config_DB.php");
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
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_View_Order` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `Tariff_View_Order`.`order` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  Add
	//################################################

	public function additem($name, $inetrnal)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 12;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];

			//Database
			$qur = "INSERT INTO `Tariff_Item`( `Visible`, `Foreign`, `Name`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $inetrnal);
			$stmt->bindParam(3, $name);
			$stmt->execute();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function addversion($name)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 21;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];

			//Database
			$qur = "INSERT INTO `Tariff_Version_ID`( `Visible`, `Name`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $name);
			$stmt->execute();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}


	public function addtariffvalue($name, $inetrnal)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 12;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];

			//Database
			$qur = "INSERT INTO `Tariff_Item`( `Visible`, `Foreign`, `Name`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $inetrnal);
			$stmt->bindParam(3, $name);
			$stmt->execute();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function CleanOrderTBL($SecID, $tbl_name)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 24;
			$Action = 2;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];

			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$stmt = $pdo->prepare("DELETE FROM `Tariff_View_Order` WHERE `SecID` =:id and `Table_Name` =:tblName");
			$stmt->bindParam(':id', $SecID);
			$stmt->bindParam(':tblName', $tbl_name);
			$stmt->execute();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function Get_TariffVersion()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version_ID` WHERE `Visible` = 1 ORDER By `ID`";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function updateitem($ID, $name, $Foreign)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 12;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "No Comment";
			//Database
			$qur = "UPDATE `Tariff_Item` SET `Name`= :name , `Foreign` =:foreign WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':foreign', $Foreign);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function updateTariffverion($tariff_ID, $ver_ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 21;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "No Comment";
			//Database
			$qur = "UPDATE `Tariff_Version` SET `Version`= :ver_id WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':ver_id', $ver_ID);
			$stmt->bindParam(':id', $tariff_ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function removetariffitem($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 12;
			$Visible = 0;
			$Action = 2;
			$comment = "";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Tariff_Item` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function removeversion($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 21;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Tariff_Version_ID` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function removetariff($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 11;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Tariff_Version` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function getitemname($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Item` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Name"];
		}
	}

	public function getForeign($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Item` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Foreign"];
		}
	}

	public function order($table, $service_ID, $SecID, $order)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//DATA SET
			$Visible = 1;

			$qur = "INSERT INTO `Tariff_View_Order`( `Visible`, `Table_Name`, `Service_ID`, `SecID`, `order`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $table);
			$stmt->bindParam(3, $service_ID);
			$stmt->bindParam(4, $SecID);
			$stmt->bindParam(5, $order);
			$stmt->execute();
			unset($pdo);
		}
	}

	public function C_TariffVer($version, $autoname, $validationdate, $SecID)
	{
		if ($this->_UPermission > 400) {
			//DATA SET
			$visible = 1;
			$Validation = 1;
			$insertdate = date('Y-m-d H:i:s');
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "INSERT INTO `Tariff_Version`( `Visible`, `AutomobileID`, `version`, `AddDate`, `ValidateDate`, `Validation`,`SecID`) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $visible);
			$stmt->bindParam(2, $autoname);
			$stmt->bindParam(3, $version);
			$stmt->bindParam(4, $insertdate);
			$stmt->bindParam(5, $validationdate);
			$stmt->bindParam(6, $Validation);
			$stmt->bindParam(7, $SecID);
			$stmt->execute();
			unset($pdo);
		}
	}

	public function maketarifforder($SecID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 24;
			$Action = 16;
			$comment = "";
			$username = $_SESSION["Admin_ID"];

			$qur = "SELECT * FROM `Tariff_Value` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$order = 1;
			foreach ($data as $row) {
				if ($row['Value'] !== 0) {
					$table = 1;
					$service_ID = $row['ID'];
					$this->order($table, $service_ID, $SecID, $order);
				}
				$order++;
			}
			$qur = "SELECT * FROM `Tariff_Added_Item` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				if ($row['ItemValue'] !== 0) {
					$table = 2;
					$service_ID = $row['ID'];
					$this->order($table, $service_ID, $SecID, $order);
				}
				$order++;
			}
			//$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function tariffadditem($add_price, $add_name, $SecID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 27;
			$Action = 1;
			$comment = "add new service and price to tariff";
			$username = $_SESSION["Admin_ID"];

			//Database

			for ($i = 0; $i < count($add_price); $i++) {
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				$Visible = 1;
				$name = $add_name[$i];
				$item_Value = $add_price[$i];
				if (!empty($item_Value)) {
					$Value = str_replace(",", "", $item_Value);
				} else {
					$Value = 0;
				}
				if (!empty($name)) {

					$qur = "INSERT INTO `Tariff_Added_Item`( `Visible`, `ItemName`, `ItemValue`, `SecID`) VALUES ( ?, ?, ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $Visible);
					$stmt->bindParam(2, $name);
					$stmt->bindParam(3, $Value);
					$stmt->bindParam(4, $SecID);
					$stmt->execute();
					unset($pdo);
				}
			}

			//Log generator

			//$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
			//add file
		}
	}

	public function addvalue($version, $autoname, $ItemID, $serviceprice, $pdate, $add_price, $add_name)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 11;
			$Action = 1;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];

			//DATA SET
			$visible = 1;
			$SecID = md5(uniqid(mt_rand(100000, 999999), true));
			//date hejri to garygori

			$persiandate =  str_replace("/", "", $pdate);

			$day = substr($persiandate, 8, 2);
			$mon = substr($persiandate, 5, 2);
			$year = substr($persiandate, 0, 4);

			$validationdate = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
			$validationdate = date('Y-m-d', $validationdate);

			for ($i = 0; $i < count($ItemID); $i++) {
				$item_ID = $ItemID[$i];
				$item_Value = $serviceprice[$i];
				if (!empty($item_Value)) {
					$Value = str_replace(",", "", $item_Value);
				} else {
					$Value = 0;
				}
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				$qur = "INSERT INTO `Tariff_Value`( `Visible`, `ItemID`, `Value`, `VersionID`,`SecID`) VALUES ( ?, ?, ?, ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $visible);
				$stmt->bindParam(2, $item_ID);
				$stmt->bindParam(3, $Value);
				$stmt->bindParam(4, $version);
				$stmt->bindParam(5, $SecID);
				$stmt->execute();
				unset($pdo);
			}

			$this->C_TariffVer($version, $autoname, $validationdate, $SecID);

			if (!empty($add_price) && !empty($add_name)) {
				$this->tariffadditem($add_price, $add_name, $SecID);
			}
			/////////////important/////////////
			/////////////important/////////////
			//// make first order
			//////////////////////////////////
			$this->maketarifforder($SecID);

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
			return 1;
		}
	}

	public function gettariffauto($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID =" . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$x = array($data[0]['ManufacturerID'], $data[0]['ModelID'], $data[0]['Name']);
			} else {
				$x = array('****', '****', '****');
			}
			return $x;
		}
	}

	public function gettariffautotip($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and ID =' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data[0]['ModelName'];
			} else {

				return "مدل خودرو مشکل دارد";
			}
		}
	}


	public function gettariffautoman($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1 and ID =' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data[0]['Name'];
			} else {
				return "نام خودروساز مشکل دارد";
			}
		}
	}


	public function gettariffdateinhejri($miladi)
	{
		if ($this->_UPermission > 400) {
			$miladidate =  str_replace("-", "", $miladi);
			$day = substr($miladidate, 6, 2);
			$mon = substr($miladidate, 4, 2);
			$year = substr($miladidate, 0, 4);
			//require_once('config/jdf.php');
			//$persian_Date = gregorian_to_jalali($year,$mon,$day);
			$persian_Date = array($year, $mon, $day);

			return $persian_Date;
		}
	}


	/*
	
	PWA
	
	*/

	public function View_Tariff_PWA()
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `Validation` = 1";
		$stmt = $pdo->prepare($qur);
		$stmt->execute();
		$data = $stmt->fetchAll();
		foreach ($data as $row) {
			echo "<tr>";
			$auto = $this->gettariffauto($row['AutomobileID']);
			$auto_name = $auto[2];
			$auto_tipID = $auto[1];
			if ($auto_tipID !== 0 && $auto_tipID !== '****') {
				$auto_tipName = $this->gettariffautotip($auto_tipID);
				$tip = $auto_tipName;
			} else {
				$tip = "بدون تیپ";
			}
			$auto_manID = $auto[0];
			if ($auto_manID !== '****') {
				$auto_manName = $this->gettariffautoman($auto_manID);
			}
			echo "<td> [ " . $auto_manName . " ] &nbsp; [ " . $auto_name . " ] &nbsp; [ " . $tip . " ]</td>";
			$tarrif_verion_name = $this->getversion($row['Version']);
			echo "<td>" . $tarrif_verion_name . "</td>";
			if ($row['Validation'] == 1) {
				$str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
				<button class="btn btn-default btn-icon-anim " onclick="return changetonotvalid()"> معتبر </button>
				<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
				<input type="hidden" name="action" value="valid"></form>';
			} else {
				$str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
				<button class="btn btn-pinterest btn-icon-anim " onclick="return changetovalid()"> نامعتبر </button>
				<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
				<input type="hidden" name="action" value="not-valid"></form>';
			}
			echo "<td>" . $str_validation . "</td>";
			echo '<td>';
			echo '<form method="post" enctype="multipart/form-data" action="PDFGenerator.php">
						<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
						<input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
						<input type="hidden" name="pdf" value="pdf">
						<button class="btn btn-primary">
						دریافت PDF
						<svg width="16" height="16" viewBox="0 0 16 16" class="bi bi-arrow-down me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
						</svg>
						</button>
				</form>';
			echo "</td>";
			echo "</tr>";
		}
	}


	public function getMemberTariff($x)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `Validation` = 1 and `Version`='" . $x . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function getMemberTariff_ByAutoID($x, $AutoID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `Validation` = 1 and `Version` ='" . $x . "' and `AutomobileID` = $AutoID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function getitemall($tbl_name, $srv_ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			if ($tbl_name == 1) {
				$data = $pdo->query("SELECT * FROM `Tariff_Value` WHERE `Visible` = 1 and `ID` = " . $srv_ID)->fetchAll();
				$item_valu = $data[0]["Value"];
				$item_name = $this->getitemname($data[0]["ItemID"]);
				$result = array($item_name, $item_valu);
				return $result;
			}
			if ($tbl_name == 2) {
				$data = $pdo->query("SELECT * FROM `Tariff_Added_Item` WHERE `Visible` = 1 and `ID` =" . $srv_ID)->fetchAll();
				$item_valu = $data[0]["ItemValue"];
				$item_name = $data[0]["ItemName"];

				$result = array($item_name, $item_valu);
				return $result;
			}
		}
	}


	public function ViewPDF($ID, $SecID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_View_Order` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `order` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$counter = 1;
			$str = array();
			for ($i = 0; $i < count($data); $i++) {
				$item = $this->getitemall($data[$i]['Table_Name'], $data[$i]['Service_ID']);
				if ($item[0] !== "" and $item[1] !== 0) {
					$str[$i] = "<tr><td>" . $counter . "</td><td>" . $item[0] . "</td><td>" . number_format($item[1]) . "</td></tr>";
					$counter++;
				}
			}
			return $str;
			$date = date('Y-m-d H:i:s');
			$ElemanType = 21;
			$username = $_SESSION["Admin_ID"];
			$Action = 15;
			$comment = "SecID = " . $SecID;
			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function viewtariffall_order($ID, $SecID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_View_Order` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "' ORDER BY `order` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$counter = 1;
				for ($i = 0; $i < count($data); $i++) {
					$item = $this->getitemall($data[$i]['Table_Name'], $data[$i]['Service_ID']);
					if ($item[0] !== "" and $item[1] !== 0) {
						echo '<li class="dd-item dd3-item" data-id="' . $counter . '">';
						echo '<div class="dd-handle dd3-handle"></div>';
						echo '<div class="dd3-content">';
						echo $item[0] . " = " . $item[1] . " (تومان)";
						echo '</div>';
						echo '<input type="hidden" name="item-ID[]" value="' . $data[$i]['ID'] . '">';
						echo "</li>";
						$counter++;
					}
				}
			}
		}
	}

	public function counttariff()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_Version`";
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

	public function getversion($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version_ID` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Name"];
		}
	}

	public function gettariffver($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]["Version"];
		}
	}

	public function gettariffDetial($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `ID`= " . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$tariff = array($data[0]["AutomobileID"], $data[0]["Version"], $data[0]["ValidateDate"], $data[0]["Validation"], $data[0]["SecID"]);
			return $tariff;
		}
	}

	public function updateversion($ID, $name)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 21;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "No Comment";
			//Database
			$qur = "UPDATE `Tariff_Version_ID` SET `Name`= :name WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
			return $data;
		}
	}

	public function maketariffnotvalid($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 11;
			$Validation = 0;
			$Action = 14;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Tariff_Version` SET `Validation`= :Validation WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Validation', $Validation);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}
	public function maketariffvalid($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 11;
			$Validation = 1;
			$Action = 13;
			$comment = "No Comment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Tariff_Version` SET `Validation`= :Validation WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Validation', $Validation);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function updatetariffgroup($ID, $v)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 22;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "Tariff Version ID = [" . $ID . "] set to = [" . $v . "]";
			//Database
			$qur = "UPDATE `Tariff_Version` SET `Validation`= :Validation WHERE `Version` = :Version";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Validation', $v);
			$stmt->bindParam(':Version', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
			$url = "./V_TariffValid.php";
			header("Location: $url");
		}
	}

	public function updateOrdertariff($ID, $order)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 11;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 16;
			$comment = "";
			//Database
			$qur = "UPDATE `Tariff_View_Order` SET `order`= :order WHERE `ID` = :ID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':order', $order);
			$stmt->bindParam(':ID', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}


	public function updateAddSrv($ID, $Name, $Price)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 26;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "Tariff_Added_Item update in [" . $ID . "]";
			//Database
			$qur = "UPDATE `Tariff_Added_Item` SET `ItemName`= :name, `ItemValue`= :ItemValue WHERE `ID` = :ID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':name', $Name);
			$stmt->bindParam(':ItemValue', $Price);
			$stmt->bindParam(':ID', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function updateSrv($ID, $price, $SecID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 26;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "Tariff_Value update in [" . $SecID . "]";
			//Database
			$qur = "UPDATE `Tariff_Value` SET `Value`= :val WHERE `ID` = :ID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':val', $price);
			$stmt->bindParam(':ID', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();
			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function updateTariffDate($ID, $Ndate)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 23;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "tbl Name = Tariff_Version ID = [" . $ID . "]";
			//date hejri to garygori

			$garygori =  str_replace("/", "", $Ndate);

			$day = substr($garygori, 8, 2);
			$mon = substr($garygori, 5, 2);
			$year = substr($garygori, 0, 4);

			$date_format = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
			$newDate = date('Y-m-d', $date_format);

			//Database
			$qur = "UPDATE `Tariff_Version` SET `ValidateDate` = :NewDate WHERE `ID` = :ID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':NewDate', $newDate);
			$stmt->bindParam(':ID', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->Logtariff($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
			$url = "./V_TariffValid.php";
			header("Location: $url");
		}
	}

	public function EditeViewTariff($ID, $SecID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Tariff_Value` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				$item_name = $this->getitemname($row['ItemID']);
				echo '<div class="col-sm-12">
					<div class="col-sm-6">
						<div class="form-group">
							<div class="input-group col-sm-6">
			
			<label class="control-label mb-10" for="serviceprice">' . $item_name . '</label>
		<div class="input-group">
			<div class="input-group-addon"><i class="pe-7s-cash"></i></div>
			<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="serviceprice[]" name="serviceprice[]" placeholder="' . number_format($row['Value']) . '">
			<input type="hidden" id="ItemID[]" name="ItemID[]" value="' . $row['ID'] . '">
		</div></div></div></div></div><br />';
			}

			$qur = "SELECT * FROM `Tariff_Added_Item` WHERE `Visible` = 1 and `SecID` = '" . $SecID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				echo '<div class="col-sm-12">
					<div class="col-sm-6">
						<div class="form-group">
							<div class="input-group col-sm-6">
			
			<label class="control-label mb-10" for="serviceprice">' . $row['ItemName'] . '</label>
		<div class="input-group">
			<div class="input-group-addon"><i class="pe-7s-cash"></i></div>
			<input type="text" class="form-control" id="addedsrvName[]" name="addedsrvName[]" placeholder="' . $row['ItemName'] . '">
			<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="addedsrvPrice[]" name="addedsrvPrice[]" placeholder="' . number_format($row['ItemValue']) . '">
			<input type="hidden" id="AddItemID[]" name="AddItemID[]" value="' . $row['ID'] . '">
			<input type="hidden" name="SecID" value="' . $SecID . '">
		</div></div></div></div></div><br />';
			}
		}
	}
}
