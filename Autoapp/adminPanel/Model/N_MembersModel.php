<?php

namespace fateh\Member;

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
			$stmt->bindParam(2, $Address[0]);
			$stmt->bindParam(3, $Address[1]);
			$stmt->bindParam(4, $Address[2]);
			$data = $stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function C_MechanicsDetail($MemberGUID, array $info)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Member_info`( `GUID`, `Birthday`, `NationalID`, `NationalSerial`, `BirthdayLocation`, `Educationlevel`, `Religion`, `DutySystem`, `FamilyNum` ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $info[3]);
			$stmt->bindParam(3, $info[4]);
			$stmt->bindParam(4, $info[6]);
			$stmt->bindParam(5, $info[9]);
			$stmt->bindParam(6, $info[7]);
			$stmt->bindParam(7, $info[5]);
			$stmt->bindParam(8, $info[8]);
			$stmt->bindParam(9, $info[10]);
			$stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
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
			$stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function C_MechanicsPermission_ByID($MemberGUID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO Member_Permission (`MemberGUID`, `AccessValue`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, 501);
			$stmt->bindParam(3, $Profile_Pic);
			$stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
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
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
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
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function C_MechanicsLicense_ByID($MemberGUID, array $License)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$License_status = 0;
			//
			$qur = "INSERT INTO `Member_License`( `GUID`, `LicenseCategoryType`, `LicenseNum`, `LicenseExpDate`, `LicenseStatus`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $MemberGUID);
			$stmt->bindParam(2, $License[0]);
			$stmt->bindParam(3, $License[1]);
			$stmt->bindParam(4, $License[2]);
			$stmt->bindParam(5, $License_status);
			$data = $stmt->execute();
			return $data;
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
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
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
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
			$stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				return 1;
			} else {
				return 0;
			}
		}
	}



	public function C_Mechanics(array $info, array $Detail, array $Address, array $license, array $SDevices, array $Automobile, $File)
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
			$stmt->bindParam(3, $info[0]);
			$stmt->bindParam(4, $info[1]);
			$stmt->bindParam(5, $info[2]);
			$stmt->bindParam(6, $Status);
			$stmt->bindParam(7, $info[3]);
			$stmt->bindParam(8, $Pass); // member Defualt pass is mobile number
			$data = $stmt->execute();
			unset($pdo);
			if (!empty($data) && $data == 1) {
				$C_MechanicsDetail = $this->C_MechanicsDetail($MemberGUID, $Detail);
				if ($C_MechanicsDetail == 1) {
					$C_MechanicsAddress = $this->C_MechanicsAddress($MemberGUID, $Address);
					if ($C_MechanicsAddress == 1) {
						$C_MechanicsLicense = $this->C_MechanicsLicense_ByID($MemberGUID, $license);
						if ($C_MechanicsLicense == 1) {
							$this->U_LicenseValidate();
							$C_MechanicsSpecialDevices = $this->C_MechanicsSpecialDevices_ByID($MemberGUID, $SDevices);
							if ($C_MechanicsSpecialDevices == 1) {
								$C_MechanicsAutomobile = $this->C_MechanicsAutomobile_ByID($MemberGUID, $Automobile);
								if ($C_MechanicsAutomobile == 1) {
									$C_MechanicsFiles = $this->C_MechanicPhoto_ByID($MemberGUID, $File);
									if ($C_MechanicsFiles == 1) {
										$this->C_MechanicsPhoto_ByID($MemberGUID);
										$this->C_MechanicsPermission_ByID($MemberGUID);
										$this->C_MechanicsTariffType_ByID($MemberGUID);
										$this->C_Adminlog($Action, $comment);
										return 1;

									} else {
										return 7; //mechanic Auto do not insert
									}
								} else {
									return 6; // mechanic Auto do not insert
								}
							} else {
								return 5; // mechanic Special Devices do not insert
							}
						} else {
							return 4; // mechanic License do not insert
						}
					} else {
						return 3; // mechanic address do not insert
					}
				} else {
					return 2; //mechanic detail do not insert
				}
			} else {
				return 0;
			}
		}
	}

	//################################################
	//###################################################### get
	//################################################

	public function Get_Banners_ByDate()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_Banners` WHERE `Visible` = 1 and `Date_End` >= DATE(NOW()) ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Banners_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_Banners` WHERE `Visible` = 1 and `GUID` = '" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Gat_HejriDate($miladi)
	{

		$miladidate =  str_replace("-", "", $miladi);
		$day = substr($miladidate, 6, 2);
		$mon = substr($miladidate, 4, 2);
		$year = substr($miladidate, 0, 4);
		$persian_Date = array($year, $mon, $day);
		return $persian_Date;
	}

	//################################################
	//######################################################  view
	//################################################
	public function V_Banners()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Adv_Banners` WHERE `Visible` = 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	//################################################
	//######################################################  Update 
	//################################################

	public function U_LicenseValidate()
	{
		date_default_timezone_set("Asia/Tehran");
		$DateANDTime = date('Y-m-d H:i:s');
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur = "SELECT `Member_License`.`GUID` as licenseGUID, `Member_License`.`LicenseExpDate`, `Member_License`.`LicenseStatus` FROM `Member_License` INNER JOIN `Member` ON `Member_License`.`GUID` = `Member`.`GUID` WHERE `Member`.`Visible`=1";
		$stmt = $pdo->prepare($qur);
		$stmt->execute();
		$data = $stmt->fetchAll();
		unset($pdo);
		if (!empty($data)) {
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


	public function U_HideBanner_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d', strtotime('-1 days', strtotime('2023-06-05')));
			$Action = 2;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_Banners` SET `Date_End`= :Date_End WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Date_End', $date);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	public function U_BannerEndDate_ByID($ID, $End_Date)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Banner End Date";
			//Database
			$qur = "UPDATE `Adv_Banners` SET `Date_End`= :Date_End WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Date_End', $End_Date);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	public function U_BannerImage_ByID($ID, $address)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Banner File Address";
			//Database
			$qur = "UPDATE `Adv_Banners` SET `File`= :fileaddress WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':fileaddress', $address);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	public function U_BannerDetails_ByID($ID, $Slogn, $Comments, $Owner, $Link)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Banner Detail";
			//Database
			$qur = "UPDATE `Adv_Banners` SET `Slogn`= :slogn, `Comments`= :bancomments, `Owner`= :banOwner, `Link`= :banlink WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':slogn', $Slogn);
			$stmt->bindParam(':bancomments', $Comments);
			$stmt->bindParam(':banOwner', $Owner);
			$stmt->bindParam(':banlink', $Link);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

	//################################################
	//######################################################  Delete 
	//################################################
	//NEVER Remove Banners, So DO NOT turn Visible to 0



}
