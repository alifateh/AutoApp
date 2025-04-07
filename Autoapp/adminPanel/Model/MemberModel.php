<?php

namespace fateh\Member;
//#################
//################# admin
//#################
class Member
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


	public function GetMemAddress($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Address` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$address = array();
			$address[0] = $data[0]['HomeAddress'];
			$address[1] = $data[0]['Email'];
			$address[2] = $data[0]['Tag'];
			return $address;
		}
	}

	public function Get_AutoCount_ByUserID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Member_Auto` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function Get_Automobile_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Automobile_Name` WHERE `ID` =' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				return $row['Name'];
			}
		}
	}


	public function GetMemSkilles($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Service` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['ServiceID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetMemDevices($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Special_Device` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['DeviceID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetMemFiles($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_File` WHERE `Visible` = 1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['Path'];
					$i++;
				}
				return $result;
			}
		}
	}





	public function GetNameCategory($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data[0]['Name'];
			} else {

				return "مجوزی یافت نشد";
			}
		}
	}
	public function GetNameDevices($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['Name'];
		}
	}
	public function GetNameSkills($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Service_Type` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['Name'];
		}
	}
	public function GetMemDetials($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_info` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$Detials = array();
			$Detials[0] = $data[0]['Birthday'];
			$Detials[1] = $data[0]['NationalID'];
			$Detials[2] = $data[0]['NationalSerial'];
			$Detials[3] = $data[0]['BirthdayLocation'];
			$Detials[4] = $data[0]['Educationlevel'];
			$Detials[5] = $data[0]['Religion'];
			$Detials[6] = $data[0]['DutySystem'];
			$Detials[7] = $data[0]['FamilyNum'];
			return $Detials;
		}
	}





	public function GetMemService($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Service` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['ServiceID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetMemSpecialDevice($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_Special_Device` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['DeviceID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetMemCountSDevice($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Member_Special_Device` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function GetMemCountService($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Member_Service` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function isMemberExists($username)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Users_Admin` where `u-name`=?";
			$stm = $pdo->prepare($qur);
			$stm->execute([$username]);
			$d = $stm->fetchAll();
			$count = 0;
			if (is_array($d)) {
				$count = count($d);
			}
			if ($count > 0) {
				$result = true;
			} else {
				$result = false;
			}
			return $result;
		}
	}
	public function Get_Category($GID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_License` where `GUID` = '" . $GID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$cat = $data[0]['LicenseCategoryType'];
				unset($pdo);
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				$qur = "SELECT * FROM `Autoapp_Occupation` where `ID` =" . $cat;
				$stmt = $pdo->prepare($qur);
				$stmt->execute();
				$data = $stmt->fetchAll();
				return $data[0]['Name'];
			} else {
				$name = "بدونه رسته";
				return $name;
			}
		}
	}

	public function GetMemberName($GID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member` where `GUID` = '" . $GID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$name = array();
				$name[0] = $data[0]['FName'];
				$name[1] = $data[0]['LName'];
			} else {
				$name = array();
				$name[0] = 'شخص ثبت نشده است';
				$name[1] = 'DB Error!';
			}
			return $name;
		}
	}






	public function ViewSkill()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
			}
		}
	}
	public function ViewSDevices()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();

			foreach ($data as $row) {
				echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
			}
		}
	}

	public function ViewCategory()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();

			foreach ($data as $row) {
				echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
			}
		}
	}


	public function Selected($autoID, array $ID)
	{
		if ($this->_UPermission > 450) {
			for ($i = 0; $i < count($ID); $i++) {
				if ($autoID == $ID[$i]) {
					return " selected ";
				}
			}
		}
	}

	public function ViewSkillSelectable(array $ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				$select = $this->Selected($row['ID'], $ID);
				echo '<option value="' . $row['ID'] . '"' . $select . '>' . $row['Name'] . '</option>';
			}
		}
	}

	public function ViewDeviceSelectable(array $ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			foreach ($data as $row) {
				$select = $this->Selected($row['ID'], $ID);
				echo '<option value="' . $row['ID'] . '"' . $select . '>' . $row['Name'] . '</option>';
			}
		}
	}







	//***********************************************NEW*********************************************************** */
	//***********************************************NEW*********************************************************** */
	//***********************************************NEW*********************************************************** */
	//***********************************************NEW*********************************************************** */
	//***********************************************NEW*********************************************************** */




	//################################################
	//###################################################### log
	//################################################

	public function C_Adminlog($action, $comment)
	{
		$date = date('Y-m-d H:i:s');
		$Entity = 6; //contarct
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
		$Entity = 6; //contarct
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


	public function C_MechanicsAddress($MemberGUID, array $Address)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//
			$qur = "INSERT INTO `Member_Address`( `GUID`, `HomeAddress`, `Email`, `Tag`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $Address[0]); //HomeAddress
			$stmt->bindParam(3, $Address[1]);  //Email
			$stmt->bindParam(4, $Address[2]); //Tag
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}


	public function C_MechanicTariffType($GUID, $Tariff)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Visible = 1;
			$Action = 1;
			$comment = "Add Tariff Type to Auto";
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Member_TariffType` (`Visible`, `TariffTypeGUID`, `MemberGUID`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $Tariff);
			$stmt->bindParam(3, $GUID);
			$stmt->execute();

			//Log generator
			$this->C_Adminlog($Action, $comment);
		}
	}

	public function C_MechanicsDetail($MemberGUID, array $Detail)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Member_info`( `GUID`, `Birthday`, `NationalID`, `NationalSerial`, `BirthdayLocation`, `Educationlevel`, `Religion`, `DutySystem`, `FamilyNum` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $Detail[0]); //Birthday
			$stmt->bindParam(3, $Detail[1]); //NationalID
			$stmt->bindParam(4, $Detail[2]); //NationalSerial
			$stmt->bindParam(5, $Detail[3]); //BirthdayLocation
			$stmt->bindParam(6, $Detail[4]); //Educationlevel
			$stmt->bindParam(7, $Detail[5]); //Religion
			$stmt->bindParam(8, $Detail[6]); //DutySystem
			$stmt->bindParam(9, $Detail[7]); //FamilyNum
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicsPhoto_ByID($MemberGUID)
	{
		if ($this->_UPermission > 900) {
			$Profile_Pic = "images/Profile_Photo/vector.png";
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Member_Profile_Pic`( `GUID`, `Visible`, `Path` ) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $Profile_Pic);
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicsPermission_ByID($MemberGUID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO Member_Permission (MemberGUID, AccessValue) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$data = $stmt->execute([$MemberGUID, 501]);
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicsTariffType_ByID($MemberGUID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$TType_arr = array();
			$TType_arr[0] = "f8ebae71abffe8be025264761dc90d76";
			$TType_arr[1] = "49b983525af5d31711fa6bebb0fd7b86";
			$TType_arr[2] = "d0cc0f11f9e071737346cde7a9ab4eb2";
			foreach ($TType_arr as $key) {
				$qur = "INSERT INTO `Member_TariffType` (`Visible`, `TariffTypeGUID`, `MemberGUID`) VALUES ( ?, ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $Visible);
				$stmt->bindParam(2, $key);
				$stmt->bindParam(3, $MemberGUID);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicsServices_ByID($MemberGUID, array $Services)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			foreach ($Services as $key) {
				$qur = "INSERT INTO `Member_Service`( `GUID`, `ServiceID`) VALUES ( ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $MemberGUID);
				$stmt->bindParam(2, $key);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicsAutomobile_ByID($MemberGUID, array $auto)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			foreach ($auto as $key) {
				$qur = "INSERT INTO `Member_Auto`( `GUID`, `AutoID`) VALUES ( ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $MemberGUID);
				$stmt->bindParam(2, $key);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}


	public function C_MechanicsLicense_ByID($MemberGUID, array $License)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = strtotime("today");
			$date_now = date("Y-m-d", $date);
			if ($date_now > $License[2]) {
				$License_status = 0;
			} else {
				$License_status = 1;
			}
			//
			$qur = "INSERT INTO `Member_License`( `GUID`, `LicenseCategoryType`, `LicenseNum`, `LicenseExpDate`, `LicenseStatus`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $License[0]); //LicenseCategoryType
			$stmt->bindParam(3, $License[1]);  //LicenseNum
			$stmt->bindParam(4, $License[2]);  //LicenseExpDate
			$stmt->bindParam(5, $License_status);
			$data = $stmt->execute();
			return $data;
			unset($pdo);
			return $data;
		}
	}

	public function C_NTariffType($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$TType_arr = array();
			$TType_arr[0] = "f8ebae71abffe8be025264761dc90d76";
			$TType_arr[1] = "49b983525af5d31711fa6bebb0fd7b86";
			$TType_arr[2] = "d0cc0f11f9e071737346cde7a9ab4eb2";
			foreach ($TType_arr as $key) {
				$qur = "INSERT INTO `Member_TariffType` (`Visible`, `TariffTypeGUID`, `MemberGUID`) VALUES ( ?, ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $Visible);
				$stmt->bindParam(2, $key);
				$stmt->bindParam(3, $GUID);
				$stmt->execute();
			}
		}
	}

	public function C_MechanicsSpecialDevices_ByID($MemberGUID, array $SDevices)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			foreach ($SDevices as $key) {
				$qur = "INSERT INTO `Member_Special_Device`( `GUID`, `DeviceID`) VALUES ( ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $MemberGUID);
				$stmt->bindParam(2, $key);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}


	public function C_MechanicPhoto_ByID($MemberGUID, $File)
	{
		if ($this->_UPermission > 900) {
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Member_Profile_Pic`( `GUID`, `Visible`, `Path` ) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $File);
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}

	public function C_MechanicFiles_ByID($GID, $file)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$Visible = 1;
			$Action = 1;
			$comment = "Upload files $GID";

			$qur = "INSERT INTO `Member_File`( `GUID`, `Visible`, `Path`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $file);
			$data = $stmt->execute();
			unset($pdo);

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	public function C_Mechanics(array $info)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$MemberGUID = md5(uniqid(mt_rand(100000, 999999), true));
			$Visible = 1;
			$Status = 0;
			$Action = 1;
			$comment = $MemberGUID;
			$Pass = password_hash($info[4], PASSWORD_DEFAULT);
			//
			$qur = "INSERT INTO `Member`( `GUID`, `Visible`, `FName`, `LName`, `Gender`, `Status`, `UName`, `Pass`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $info[0]); //FName
			$stmt->bindParam(4, $info[1]); //LName
			$stmt->bindParam(5, $info[2]); //Gender
			$stmt->bindParam(6, $Status);
			$stmt->bindParam(7, $info[3]); //UName = National
			$stmt->bindParam(8, $Pass); // member Defualt pass is mobile number
			$data = $stmt->execute();
			unset($pdo);
			if ($data == true) {
				$this->C_MechanicsPhoto_ByID($MemberGUID);
				$this->C_MechanicsPermission_ByID($MemberGUID);
				$this->C_MechanicsTariffType_ByID($MemberGUID);
				//$this->U_LicenseValidate();
				//Log
				$this->C_Adminlog($Action, $comment);
				$arr_return = array();
				$arr_return[0] = 1;
				$arr_return[1] = $MemberGUID;
				return $arr_return;
			} else {
				return 0;
			}
		}
	}

	//################################################
	//###################################################### get
	//################################################

	public function Get_MechanicLicense_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_License` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function GetMemLicense($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_License` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$License = array();
				$License[0] = $data[0]['LicenseCategoryType'];
				$License[1] = $data[0]['LicenseNum'];
				$License[2] = $data[0]['LicenseExpDate'];
				$License[3] = $data[0]['LicenseStatus'];
			} else {
				$License = array();
				$License[0] = 0;
				$License[1] = "مجوزی یافت نشد";
				$License[2] = '';
				$License[3] = 2;
			}
			return $License;
		}
	}



	public function Get_HideTariffType_ByGUID($GUID, $TariffGUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_TariffType` WHERE `TariffTypeGUID` = '$TariffGUID' and `MemberGUID` = '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_AutoTariffType_ByAutoID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_TariffType` WHERE Visible =1 and `MemberGUID` = '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicReligion()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Religion` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicCategory()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicCity()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Provinces` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}



	public function Get_MechanicSpecialDevices()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicSkills()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicDetails_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_info` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicInfo_ByID($GUID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member` WHERE Visible =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicCity_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Provinces` WHERE `Visible` = 1 and `ID` = $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function Get_MechanicReligion_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Religion` WHERE `Visible` = 1 and `ID` = $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicMilitary_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Military` WHERE `Visible` = 1 and `ID` = $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicMilitary()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Military` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicDegree_ByID($ID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_AcademicDegree` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicEducationlevels()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_AcademicDegree` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicPhoto_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_Profile_Pic` WHERE `GUID` = '$GUID' and `Visible` =1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicFiles_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_File` WHERE `Visible` = 1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicAll()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member` WHERE `Visible` = 1 ORDER BY `Member`.`LName` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_MechanicCount()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT COUNT(*) FROM `Member` WHERE `Visible`=1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['COUNT(*)'];
		}
	}

	public function Get_DateHejri($miladi)
	{

		$miladidate =  str_replace("-", "", $miladi);
		$day = substr($miladidate, 6, 2);
		$mon = substr($miladidate, 4, 2);
		$year = substr($miladidate, 0, 4);
		$persian_Date = array($year, $mon, $day);

		return $persian_Date;
	}

	public function Get_MechanicsInfo_ByID($GUID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member` WHERE Visible =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$info = array();
			$info[0] = $data[0]['FName'];
			$info[1] = $data[0]['LName'];
			$info[2] = $data[0]['Gender'];
			$info[3] = $data[0]['Status'];
			return $info;
		}
	}

	//################################################
	//######################################################  view
	//################################################

	public function V_MemberAll()
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT `Member`.`ID`,`Member`.`GUID`,`Member`.`FName`,`Member`.`LName`,`Member`.`Status`,`Member`.`UName` ,Member_License.LicenseStatus FROM `Member` 
			INNER JOIN Member_License ON `Member`.`GUID` = Member_License.GUID WHERE `Visible` = 1 ORDER BY `ID` ASC;";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  Update 
	//################################################

	public function U_MechanicPhoto_ByID($ID, $Path)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "update Mechanic Photo $ID";
			//Database
			$qur = "UPDATE `Member_Profile_Pic` SET `Path`= :path WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':path', $Path);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();

			//Log generator

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}


	public function U_MechanicServices_ByID($ID, array $skill)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Action = 12;
			$comment = "Update Mechanic Skill $ID";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Member_Service` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if (count($skill) != 0) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($skill); $i++) {
					$qur = "INSERT INTO `Member_Service`(`GUID`, `ServiceID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $skill[$i]);
					$stmt->execute();
				}
				//Log generator
			}

			$this->C_Adminlog($Action, $comment);
		}
	}

	public function U_MechanicMisc_ByID($ID, array $info)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Member_Address` SET `HomeAddress`= :HomeAddress, `Email`= :Email, `Tag`= :Tag WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':HomeAddress', $info[10]);
			$stmt->bindParam(':Email', $info[11]);
			$stmt->bindParam(':Tag', $info[12]);
			$stmt->bindParam(':id', $ID);
			$result = $stmt->execute();
			unset($pdo);
			return $result;
		}
	}

	public function U_MechanicDetial_ByID($ID, array $info)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Member_info` SET `Birthday`= :Birthday, `NationalID`= :NationalID, `NationalSerial`= :NationalSerial, `BirthdayLocation`= :BirthdayLocation, `Educationlevel`= :Educationlevel, `Religion`= :Religion, `DutySystem`= :DutySystem, `FamilyNum`= :FamilyNum WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Birthday', $info[13]);
			$stmt->bindParam(':NationalID', $info[3]);
			$stmt->bindParam(':NationalSerial', $info[4]);
			$stmt->bindParam(':BirthdayLocation', $info[5]);
			$stmt->bindParam(':Educationlevel', $info[7]);
			$stmt->bindParam(':Religion', $info[6]);
			$stmt->bindParam(':DutySystem', $info[8]);
			$stmt->bindParam(':FamilyNum', $info[9]);
			$stmt->bindParam(':id', $ID);
			$result = $stmt->execute();
			unset($pdo);
			return $result;
		}
	}

	public function U_MechanicInfo_ByID($ID, array $info)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Mechaninc Information $ID";
			//Database
			$qur = "UPDATE `Member` SET `FName`= :FName, `LName`= :LName, `Gender`= :Gender, `UName`= :UName  WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':FName', $info[0]);
			$stmt->bindParam(':LName', $info[1]);
			$stmt->bindParam(':Gender', $info[2]);
			$stmt->bindParam(':UName', $info[3]);
			$stmt->bindParam(':id', $ID);
			$result = $stmt->execute();
			unset($pdo);

			if ($result == true) {
				$Detial = $this->U_MechanicDetial_ByID($ID, $info);
				$MISC = $this->U_MechanicMisc_ByID($ID, $info);
				if ($Detial == true && $MISC == true) {
					$this->C_Adminlog($Action, $comment);
					return 1;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		}
	}

	public function U_MechanicDevices_ByID($ID, array $skill)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Action = 12;
			$comment = "";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Member_Special_Device` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if (count($skill) != 0) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($skill); $i++) {
					$qur = "INSERT INTO `Member_Special_Device`(`GUID`, `DeviceID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $skill[$i]);
					$stmt->execute();
				}
				//Log generator
			}

			$this->C_Adminlog($Action, $comment);
		}
	}

	public function U_MechanicLicenseStatus_ByID($ID, $stat)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 8;
			$comment = "Update Mechanic License $ID";
			//Database
			$qur = "UPDATE `Member_License` SET `LicenseStatus`= :stat WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':stat', $stat);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			//Log generator

			$this->C_Adminlog($Action, $comment);
			// run update validation
			$this->U_LicenseValidate();

			return $data;
		}
	}

	public function U_MechanicLicense_ByID($ID, array $License)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Mechanic License $ID";
			//Database
			$qur = "UPDATE `Member_License` SET `LicenseCategoryType`= :cat, `LicenseNum`= :SerialNUM, `LicenseExpDate`= :ExDate WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':cat', $License[0]);
			$stmt->bindParam(':SerialNUM', $License[1]);
			$stmt->bindParam(':ExDate', $License[2]);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			unset($pdo);


			//Log generator

			$this->C_Adminlog($Action, $comment);
			// run update 
			$this->U_LicenseValidate();
		}
	}

	public function U_MechanicPassword_ByID($ID, $Pass)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Mechanic Password $ID";
			$hash = password_hash($Pass, PASSWORD_DEFAULT);
			//Database
			$qur = "UPDATE `Member` SET `Pass`= :hash WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':hash', $hash);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			//Log generator

			$this->C_Adminlog($Action, $comment);

			return $data;
		}
	}

	public function U_MechanicStatus_ByID($ID, $stat)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 8;
			$comment = "";
			//Database
			$qur = "UPDATE `Member` SET `Status`= :status WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':status', $stat);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator
			$this->C_Adminlog($Action, $comment);

			return $data;
		}
	}

	public function U_MechanicTTariff($GUID, $Tariff)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "No Comment";

			//Database
			if (!empty($Tariff)) {
				$is_Exist = $this->Get_HideTariffType_ByGUID($GUID, $Tariff);
				if (!empty($is_Exist)) {
					$this->U_UnhideTariff_ByGUID($GUID, $Tariff);
				} else {
					$this->C_MechanicTariffType($GUID, $Tariff);
				}
			}

			//Log generator

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	public function U_UnhideTariff_ByGUID($GUID, $Tariff)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 1;
			$Action = 2;
			$comment = "Remove Tariff Type $GUID";
			//Database
			$qur = "UPDATE `Member_TariffType` SET `Visible`= :visible WHERE `MemberGUID` = :id and `TariffTypeGUID` = :Tariff";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':Tariff', $Tariff);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator
			$this->C_Adminlog($Action, $comment);

			return $data;
		}
	}

	public function U_LicenseValidate()
	{
		date_default_timezone_set("Asia/Tehran");
		$DateANDTime = date('Y-m-d H:i:s');
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur = "SELECT `Member_License`.`GUID` as licenseGUID, `Member_License`.`LicenseExpDate`, `Member_License`.`LicenseStatus` FROM `Member_License` INNER JOIN `Member` ON `Member_License`.`GUID` = `Member`.`GUID` WHERE `Member`.`Visible`=1";
		$stmt = $pdo->prepare($qur);
		$result = $stmt->execute();
		$data = $stmt->fetchAll();
		unset($pdo);
		if (!empty($result) && $result == true) {
			foreach ($data as $row) {
				if (strtotime($row['LicenseExpDate']) < strtotime($DateANDTime)) {
					$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
					require("$rootDir/config/config_DB.php");
					$qur = "UPDATE `Member_License` SET `LicenseStatus`= 0 WHERE `GUID` ='" . $row['licenseGUID'] . "'";
					$stmt = $pdo->prepare($qur);
					$stmt->execute();
					unset($pdo);
				} else {
					$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
					require("$rootDir/config/config_DB.php");
					$qur = "UPDATE `Member_License` SET `LicenseStatus`= 1 WHERE `GUID` ='" . $row['licenseGUID'] . "'";
					$stmt = $pdo->prepare($qur);
					$stmt->execute();
					unset($pdo);
				}
			}
		}
	}


	public function U_MechanicAutomobile_ByID($ID, array $auto)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Action = 12;
			$comment = "Update Mechanic Auto $ID";

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Member_Auto` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if (count($auto) != 0) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($auto); $i++) {
					$qur = "INSERT INTO `Member_Auto`(`GUID`, `AutoID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $auto[$i]);
					$stmt->execute();
				}
				//Log generator
			}
			$this->C_Adminlog($Action, $comment);
		}
	}

	//################################################
	//######################################################  Delete 
	//################################################

	public function D_Mechanic_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Visible = 0;
			$Action = 2;
			$comment = "Remove $GUID";
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Member` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			//Log generator
			$this->C_Adminlog($Action, $comment);
		}
	}

	public function D_MechanicFiles_ByID($GUID, $File_ID)
	{
		if ($this->_UPermission > 450) {
			//pre info
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Mechanic Files $GUID";
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Member_File` SET `Visible`= :visible WHERE `GUID` = :id and `ID`= :fileid";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$stmt->bindParam(':fileid', $File_ID);
			$data = $stmt->execute();

			//Log generator
			$this->C_Adminlog($Action, $comment);

			// return VALUES
			return $data;
		}
	}

	public function D_MechanicNTariffType_ByMID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info

			$Visible = 0;
			$Action = 2;
			$comment = "Remove ALL Tariff Type Mechanic $GUID";
			//Database
			$qur = "UPDATE `Member_TariffType` SET `Visible`= :visible WHERE `MemberGUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator
			$this->C_Adminlog($Action, $comment);

			return $data;
		}
	}

	public function D_TTariffMechanic_ByMechID($MechanicGUID, $TariffType)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Mechanic Tariff Type $MechanicGUID";
			//Database
			$qur = "UPDATE `Member_TariffType` SET `Visible`= :visible WHERE `TariffTypeGUID` = :Tguid and `MemberGUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':Tguid', $TariffType);
			$stmt->bindParam(':id', $MechanicGUID);
			$stmt->execute();
			$data = $stmt->fetchAll();


			//Log generator

			$this->C_Adminlog($Action, $comment);

			return $data;
		}
	}
}


















//#################
//########################################################################################################### PWA
//#################

class Mechanic_Member
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

	public function C_MechanicLog($action, $comment)
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

		$Entity = 6;
		$GUID = $_SESSION["Mechanic_GUID"];
		$date = date('Y-m-d H:i:s');

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

	public function ismember($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Personnel` WHERE (`Personnel_GUID` ="' . $GUID . '"AND `Visible` = 1 AND `RoleTopic` = 2) OR (`Personnel_GUID` ="' . $GUID . '" AND `Visible` = 1 AND `RoleTopic` = 4)';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function listDevices($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT `Member_Special_Device`.`DeviceID`,`Autoapp_SpecialDevice`.`Name` FROM `Member_Special_Device` INNER JOIN Autoapp_SpecialDevice ON Member_Special_Device.DeviceID = Autoapp_SpecialDevice.ID WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['Name'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function Get_TariffTypelist_ByMechanic($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_TariffType` WHERE Visible =1 and `MemberGUID` = '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function listSkilles($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT `Member_Service`.`ServiceID`,`Service_Type`.`Name` FROM `Member_Service` INNER JOIN Service_Type ON Member_Service.ServiceID = Service_Type.ID WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['Name'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function Get_MechanicLicense_ByID($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_License` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function U_MechanicStatus_ByID($ID, $stat)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info

			$Action = 8;
			$comment = "";
			//Database
			$qur = "UPDATE `Member` SET `Status`= :status WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':status', $stat);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			$data = $stmt->fetchAll();

			//Log generator

			$this->C_MechanicLog($Action, $comment);
			return $data;
		}
	}
}
