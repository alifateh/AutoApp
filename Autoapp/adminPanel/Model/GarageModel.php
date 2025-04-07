<?php

namespace fateh\AutoShop;

class AutoShop
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

	public function LogGarage($date, $username, $action, $Entity, $comment)
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


	//################################################
	//######################################################  get
	//################################################

	public function Get_LocationMap_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Address` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_Garage_ByName($Name)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Garage` WHERE `Name` LIKE '%{$Name}%' ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function Get_GarageInfo_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage` WHERE `Visible` =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['ID'];
				$result[1] = $data[0]['Name'];
				$result[2] = $data[0]['Property_Ownership'];
				$result[3] = $data[0]['DeedStat'];
				$result[4] = $data[0]['RegNumber'];
				$result[5] = $data[0]['BlueNumber'];
				$result[6] = $data[0]['Capacity'];
				$result[7] = $data[0]['PersonnelNum'];
				$result[8] = $data[0]['Area'];
				$result[9] = $data[0]['Tags'];
				return $result;
			}
		}
	}

	public function Get_GarageMapAll()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage` WHERE `Visible` = 1';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				for ($i = 0; $i < count($data); $i++) {
					$result[$i] = $data[$i]['GUID'];
				}
				return $result;
			}
		}
	}


	public function GetGarageFacility($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Facility` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i] = $row['FacilityID'];
					$i++;
				}
				return $result;
			} else {
				return 0;
			}
		}
	}

	public function GetMemGUID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Personnel` WHERE Visible =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['RoleTopic'];
					$result[$i][2] = $row['Personnel_GUID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetDeedStatTopic($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_PropertyDeed` WHERE `Visible` = 1 and ID=' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['PersianName'];
				$result[1] = $data[0]['EnglishName'];
				return $result;
			}
		}
	}

	public function GetOwnershipTopicbyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_PropertyStatus` WHERE `Visible` = 1 and ID= $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['PersianName'];
				$result[1] = $data[0]['EnglishName'];
				return $result;
			}
		}
	}

	public function GetRoleTopicbyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_GrageRoles` WHERE `Visible` = 1 and ID=' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['PersianTopic'];
				$result[1] = $data[0]['EnglishTopic'];
				return $result;
			}
		}
	}

	public function GetRoleTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_GrageRoles` WHERE `Visible` = 1';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['PersianTopic'];
					$result[$i][2] = $row['EnglishTopic'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetCertIssuerbyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_CertificationIssuers` WHERE `Visible` = 1 and ID=' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['PersianName'];
				return $result;
			}
		}
	}

	public function GetCertStatusbyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_CertificationStatus` WHERE `Visible` = 1 and ID=' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['PersianName'];
				$result[1] = $data[0]['EnglishName'];
				return $result;
			}
		}
	}

	public function GetTehranCitybyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Province` WHERE `Visible` = 1 and ID=' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['Name'];
				$result[1] = $data[0]['Comment'];
				return $result;
			}
		}
	}

	public function GetCertbyGUID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Certificate` WHERE Visible =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['CertIssuer'];
					$result[$i][2] = $row['CertStatus'];
					$result[$i][3] = $row['CertNumber'];
					$i++;
				}
				return $result;
			}
		}
	}
	public function GetGarageCount()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT COUNT(*) FROM `Garage` WHERE `Visible`=1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['COUNT(*)'];
		}
	}

	public function GetFacilityTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_LocationFacility` WHERE `Visible` = 1 ORDER BY `Autoapp_LocationFacility`.`PersianName` ASC';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['PersianName'];
					$result[$i][2] = $row['EnglishName'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetFacilityTopicbyID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_LocationFacility` WHERE `Visible` = 1 and `ID` =' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['ID'];
				$result[1] = $data[0]['PersianName'];
				$result[2] = $data[0]['EnglishName'];
			}
			return $result;
		}
	}

	public function GetDeedTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_PropertyDeed` WHERE `Visible` = 1 ORDER BY `Autoapp_PropertyDeed`.`PersianName` ASC';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['PersianName'];
					$result[$i][2] = $row['EnglishName'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetIssuerTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_CertificationIssuers` WHERE `Visible` = 1 ORDER BY `Autoapp_CertificationIssuers`.`PersianName` ASC';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['PersianName'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetStatusTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_CertificationStatus` WHERE `Visible` = 1 ORDER BY `Autoapp_CertificationStatus`.`ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function GetTehranCity()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Province` WHERE `Visible` = 1 ORDER BY `Autoapp_Province`.`Name` ASC';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['Name'];
					$i++;
				}
				return $result;
			}
		}
	}
	public function GetTehranFaz()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_CitiesPhase` WHERE `Visible` = 1 ORDER BY `Autoapp_CitiesPhase`.`Faz` ASC';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['Faz'];
					$result[$i][2] = $row['Neighbourhood'];
					$i++;
				}
				return $result;
			}
		}
	}
	public function GetTehranNeighbour($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_CitiesPhase` WHERE `Visible` = 1 and `ID` =' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['Neighbourhood'];
		}
	}

	public function GetGaragebyMemID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Personnel` WHERE Visible =1 and `Personnel_GUID` ="' . $ID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$i = 0;
				foreach ($data as $row) {
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['RoleTopic'];
					$result[$i][2] = $row['GUID'];
					$i++;
				}
				return $result;
			}
		}
	}

	public function GetOwnerGarage($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'select `Garage_Personnel`.`GUID`,`Garage_Personnel`.`Personnel_GUID`, `Garage_Personnel`.`RoleTopic` FROM `Garage_Personnel` INNER JOIN Garage oN  `Garage`.`GUID` =`Garage_Personnel`.`GUID` WHERE `Garage`.`Visible` =1 and `Garage_Personnel`.`Personnel_GUID` ="' . $ID . '" and `Garage_Personnel`.`RoleTopic` = 2';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();

			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['GUID'];
				return $result;
			}
		}
	}
	public function GetOwnershipTopic()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_PropertyStatus` WHERE `Visible` = 1 ORDER BY `Autoapp_PropertyStatus`.`PersianName` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function GetGaragePhoto($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Profile_Pic` WHERE Visible =1 and `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['Path'];
		}
	}

	public function GetMemAddress($GUID)
	{
		if ($this->_UPermission > 400) {
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



	public function Get_Automobile_ByID($ID)
	{
		if ($this->_UPermission > 400) {
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

	public function GetGarageSkilles($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Service` WHERE `GUID` ="' . $GUID . '"';
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

	public function GetGarageDevices($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Special_Device` WHERE `GUID` ="' . $GUID . '"';
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

	public function GetGarageFiles($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Files` WHERE `Visible` = 1 and `GUID` ="' . $GUID . '"';
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
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_Occupation` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['Name'];
		}
	}
	public function GetNameDevices($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Autoapp_SpecialDevice` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$result = array();
			return $data[0]['Name'];
		}
	}
	public function GetNameSkills($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Service_Type` WHERE `Visible` = 1 and `ID` = ' . $ID;
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$result = array();
			return $data[0]['Name'];
		}
	}
	public function GetMemDetials($GUID)
	{
		if ($this->_UPermission > 400) {
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

	public function GetMemLicense($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Member_License` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$License = array();
			$License[0] = $data[0]['LicenseCategoryType'];
			$License[1] = $data[0]['LicenseNum'];
			$License[2] = $data[0]['LicenseExpDate'];
			$License[3] = $data[0]['LicenseStatus'];
			return $License;
		}
	}
	public function GetGarageService($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Service` WHERE `GUID` ="' . $GUID . '"';
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

	public function GetGarageSpecialDevice($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Special_Device` WHERE `GUID` ="' . $GUID . '"';
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
	public function GetGarageCountSDevice($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Garage_Special_Device` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function GetGarageCountService($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Garage_Service` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}

	public function GetGarageCountCert($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT count(*) FROM `Garage_Certificate` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data[0]['count(*)'];
		}
	}
	public function isMemberExists($username)
	{
		if ($this->_UPermission > 400) {
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
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member_License` where `GUID` = '" . $GID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
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
		}
	}

	public function GetMemberName($GID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Member` where `GUID` = '" . $GID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			$name = array();
			$name[0] = $data[0]['FName'];
			$name[1] = $data[0]['LName'];
			return $name;
		}
	}
	public function Get_DateHejri($miladi)
	{
		if ($this->_UPermission > 400) {
			$miladidate =  str_replace("-", "", $miladi);
			$day = substr($miladidate, 6, 2);
			$mon = substr($miladidate, 4, 2);
			$year = substr($miladidate, 0, 4);
			$persian_Date = array($year, $mon, $day);

			return $persian_Date;
		}
	}

	public function Getmap($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Address` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['Longitude'];
				$result[1] = $data[0]['latitude'];
				$result[2] = $data[0]['Address'];
				return $result;
			}
		}
	}


	//################################################
	//######################################################  View
	//################################################

	public function V_Garage()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Garage` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_GarageMapsAll()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT `Garage`.`GUID`, `Garage`.`Name`,`Garage_Address`.`Longitude`,`Garage_Address`.`latitude` FROM `Garage` 
			RIGHT JOIN `Garage_Address` ON `Garage`.`GUID` = `Garage_Address`.`GUID` 
			WHERE `Garage`.`Visible` =1";
			

			#$qur = "SELECT `Garage`.`GUID`, `Garage`.`Name`,`Garage_Address`.`Longitude`,`Garage_Address`.`latitude` FROM `Garage` 
			#RIGHT JOIN `Garage_Address` ON `Garage`.`GUID` = `Garage_Address`.`GUID` 
			#WHERE `Garage`.`Visible` =1 AND `Longitude`=0 AND`latitude`=0";


			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_GarageFile($id)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Garage_Files` WHERE `Visible` = 1 and `GUID`='" . $id . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_GarageCert($id)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Garage_Certificate` WHERE `Visible` = 1 and `GUID`='" . $id . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_GarageContact($id)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `N_PhoneBook_Garage` WHERE `Visible` = 1 and `Garage_GUID`='$id'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_GaragePersonnel($id)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$qur = "SELECT * FROM `Garage_Personnel` WHERE `Visible` = 1 and `GUID`='" . $id . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function ViewDuty()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Military` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function V_GarageSkills()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	public function ViewSDevices()
	{
		if ($this->_UPermission > 400) {
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
		if ($this->_UPermission > 400) {
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
		if ($this->_UPermission > 400) {
			for ($i = 0; $i < count($ID); $i++) {
				if ($autoID == $ID[$i]) {
					return " selected ";
				}
			}
		}
	}

	public function V_GarageSkill_ByID()
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Service_Type` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function ViewDeviceSelectable(array $ID)
	{
		if ($this->_UPermission > 400) {
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

	//################################################
	//######################################################  Create 
	//################################################

	public function C_GarageProfilePic($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$Profile_Pic = "images/Garage_Photo/Grage_Defualt.jpg";
			$qur2 = "INSERT INTO `Garage_Profile_Pic`( `GUID`, `Visible`, `Path` ) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur2);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $Profile_Pic);
			$stmt->execute();
			unset($pdo);
		}
	}

	public function RegGarage_Info(array $info)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 3;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = $info[0];
			//
			$qur = "INSERT INTO `Garage`( `GUID`, `Visible`, `Name`, `Property_Ownership`, `DeedStat`, `RegNumber`, `BlueNumber`, `Capacity`, `PersonnelNum`, `Area`, `Tags`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $info[0]);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $info[1]); //نام
			$stmt->bindParam(4, $info[2]); // نوع ملک
			$stmt->bindParam(5, $info[3]);  //وضعیت سند
			$stmt->bindParam(6, $info[4]); //شماره پلاک ثبتی 
			$stmt->bindParam(7, $info[5]); //  شماره پلاک شهرداری
			$stmt->bindParam(8, $info[6]); //شهر
			$stmt->bindParam(9, $info[7]); //  تعداد پرسنل
			$stmt->bindParam(10, $info[8]); // مساحت 
			$stmt->bindParam(11, $info[9]); // کلمات کلیدی
			$stmt->execute();
			unset($pdo);
			$this->C_GarageProfilePic($info[0]);
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function RegGarage_Address(array $add)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 43;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = $add[0];
			//
			$qur = "INSERT INTO `Garage_Address`( `GUID`, `Address`, `Longitude`, `latitude`, `PostalCode`, `ProvinceID`, `CityGUID`) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $add[0]);
			$stmt->bindParam(2, $add[1]); // نشانی
			$stmt->bindParam(3, $add[2]); // طول جغرافیایی
			$stmt->bindParam(4, $add[3]); // عرض جغرافیایی
			$stmt->bindParam(5, $add[4]); //کد پستی
			$stmt->bindParam(6, $add[5]); // نام شهر
			$stmt->bindParam(7, $add[6]);  // فاز شهر
			$stmt->execute();
			unset($pdo);

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function RegGarage_People($GUID, $pepole, $role)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 44;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = $GUID;
			//
			$qur = "INSERT INTO `Garage_Personnel`( `GUID`, `Visible`, `RoleTopic`, `Personnel_GUID`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $role);
			$stmt->bindParam(4, $pepole);
			$stmt->execute();

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}
	public function RegisterGarage_Auto($GID, $auto)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Garage_Auto`( `GUID`, `AutoID`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $auto);
			$stmt->execute();
		}
	}

	public function RegGarage_Facility($GID, $Facility)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Garage_Facility`( `GUID`, `FacilityID`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Facility);
			$stmt->execute();
		}
	}

	public function RegGarage_Certificates($GID, $CertIssuer, $CertStatus, $CertNum)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$qur = "INSERT INTO `Garage_Certificate`( `GUID`, `Visible` ,`CertIssuer`, `CertStatus`, `CertNumber`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $CertIssuer);
			$stmt->bindParam(4, $CertStatus);
			$stmt->bindParam(5, $CertNum);
			$stmt->execute();
		}
	}

	public function RegGarage_Instagram($GID, $Instagram)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$qur = "INSERT INTO `Garage_Instagram`( `GUID`, `Visible`, `InstagramAddress`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $Instagram);
			$stmt->execute();
		}
	}

	public function RegGarage_Telegram($GID, $Telegram)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$qur = "INSERT INTO `Garage_Telegram`( `GUID`, `Visible`, `TelegramAddress`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $Telegram);
			$stmt->execute();
		}
	}

	public function RegGarage_WhatsApp($GID, $WhatsApp)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$qur = "INSERT INTO `Garage_WhatsApp`(  `GUID`, `Visible`, `WhatsappAddress`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $WhatsApp);
			$stmt->execute();
		}
	}

	public function RegisterGarage_Files($GID, $file)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 42;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "";

			$qur = "INSERT INTO `Garage_Files`( `GUID`, `Visible`, `Path`) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $file);
			$stmt->execute();

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function RegisterGarage_Service($GID, $Service)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Garage_Service`( `GUID`, `ServiceID`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Service);
			$stmt->execute();
		}
	}

	public function RegisterGarage_SDevice($GID, $Device)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Garage_Special_Device`( `GUID`, `DeviceID`) VALUES ( ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GID);
			$stmt->bindParam(2, $Device);
			$stmt->execute();
		}
	}

	//################################################
	//######################################################  Delete
	//################################################	


	public function D_Garage_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 3;
			$Visible = 0;
			$Action = 2;
			$comment = $ID;
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function D_GarageDoc_ByID($GUID, $File_ID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 46;
			$Visible = 0;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Files` SET `Visible`= :visible WHERE `GUID` = :id and `ID`= :fileid";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $GUID);
			$stmt->bindParam(':fileid', $File_ID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function D_GarageCert_ByID($GUID, $CertID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 47;
			$Visible = 0;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Certificate` SET `Visible`= :visible WHERE `GUID` = :guid and `ID`= :certid";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':guid', $GUID);
			$stmt->bindParam(':certid', $CertID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function D_GaragePersonel_ByID($GUID, $ID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 44;
			$Visible = 0;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Personnel` SET `Visible`= :visible WHERE `GUID` = :guid and `ID`= :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':guid', $GUID);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function D_GarageDevices_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 53;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Special_Device` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}
	public function D_GarageSkills_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 52;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Service` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}
	public function D_GarageFacility_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 51;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Facility` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();

			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}
	public function D_GarageAuto_ByID($ID)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 54;
			$Action = 2;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Auto` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	//################################################
	//######################################################  Update
	//################################################

	public function U_GarageLatitude_ByID($GUID, $Latitude)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 48;
			$Visible = 0;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Address` SET `latitude`= :lat WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':lat', $Latitude);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageLongitude_ByID($GUID, $Longitude)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 49;
			$Visible = 0;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Address` SET `Longitude`= :lat WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':lat', $Longitude);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}

	public function U_GarageBlueNum_ByID($GUID, $Blue)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 43;
			$Visible = 0;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage` SET `BlueNumber`= :num WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':num', $Blue);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageAddress_ByID($GUID, array $Add)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 43;
			$Action = 12;
			$comment = "Update Address";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Address` SET `Address`= :addr, `PostalCode`= :zip, `ProvinceID`= :ProvinceID, `CityGUID`= :CityGUID WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':addr', $Add[0]);
			$stmt->bindParam(':zip', $Add[1]);
			$stmt->bindParam(':CityGUID', $Add[2]);
			$stmt->bindParam(':ProvinceID', $Add[3]);
			$stmt->bindParam(':id', $GUID);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageCert_ByID($GUID, array $cert)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 43;
			$Visible = 0;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage_Certificate` SET `CertIssuer`= :CertIssuer, `CertStatus`= :CertStatus, `CertNumber`= :CertNumber WHERE `GUID` = :guid and `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':CertIssuer', $cert[1]);
			$stmt->bindParam(':CertStatus', $cert[2]);
			$stmt->bindParam(':CertNumber', $cert[3]);
			$stmt->bindParam(':guid', $GUID);
			$stmt->bindParam(':id', $cert[0]);
			$stmt->execute();
			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageFacility_ByID($ID, array $facility)
	{
		if ($this->_UPermission > 400) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 51;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Facility` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if (!empty($facility)) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				foreach ($facility as $value) {
					$qur = "INSERT INTO `Garage_Facility`(`GUID`, `FacilityID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $value);
					$stmt->execute();
				}
			}
			//Log generator
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageDoc_ByID($ID, $Path)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 45;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$qur = "UPDATE `Garage_Profile_Pic` SET `Path`= :path WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':path', $Path);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();

			//Log generator

			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageAuto_ByID($ID, array $auto)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 54;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Auto` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if ($auto[0] != 0) {
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($auto); $i++) {
					$qur = "INSERT INTO `Garage_Auto`(`GUID`, `AutoID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $auto[$i]);
					$stmt->execute();
				}
			}
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageSkills_ByID($ID, array $skill)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 52;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Service` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if ($skill[0] != 0) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($skill); $i++) {
					$qur = "INSERT INTO `Garage_Service`(`GUID`, `ServiceID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $skill[$i]);
					$stmt->execute();
				}
			}
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}


	public function U_GarageDevices_ByID($ID, array $skill)
	{
		if ($this->_UPermission > 400) {
			$date = date('Y-m-d H:i:s');
			$ElemanType = 53;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			$Visible = 1;

			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$stmt = $pdo->prepare("DELETE FROM `Garage_Special_Device` WHERE `GUID` =:GUID");
			$stmt->bindParam(':GUID', $ID);
			$stmt->execute();
			unset($pdo);
			if ($skill[0] != 0) {
				//Database
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				for ($i = 0; $i < count($skill); $i++) {
					$qur = "INSERT INTO `Garage_Special_Device`(`GUID`, `DeviceID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $ID);
					$stmt->bindParam(2, $skill[$i]);
					$stmt->execute();
				}
			}
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_GarageInfo_ByID($ID, array $info)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 50;
			$Action = 12;
			$comment = "";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Garage` SET `Name`= :Name, `Property_Ownership`= :Property_Ownership, `DeedStat`= :DeedStat, `RegNumber`= :RegNumber, `Capacity`= :Capacity, `Area`= :Area, `Tags`= :Tags WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Name', $info[0]);
			$stmt->bindParam(':Property_Ownership', $info[1]);
			$stmt->bindParam(':DeedStat', $info[2]);
			$stmt->bindParam(':RegNumber', $info[3]);
			$stmt->bindParam(':Capacity', $info[4]);
			$stmt->bindParam(':Area', $info[5]);
			$stmt->bindParam(':Tags', $info[6]);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			unset($pdo);
			$this->LogGarage($date, $username, $Action, $ElemanType, $comment);
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
		$Entity = 3; //garage
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
		$Entity = 3; //garage
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

	public function C_GarageAddressByID($GUID, array $address)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "INSERT INTO `Garage_Address`( `GUID`, `Address`, `Longitude`, `latitude`, `PostalCode`, `ProvinceID`, `CityGUID` ) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID); //GUID
			$stmt->bindParam(2, $address[0]); //Address
			$stmt->bindParam(3, $address[1]); //Longitude
			$stmt->bindParam(4, $address[2]); //latitude
			$stmt->bindParam(5, $address[3]); //PostalCode
			$stmt->bindParam(6, $address[4]); //ProvinceID
			$stmt->bindParam(7, $address[5]); //CityGUID
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}
	public function C_GaragePhoto_ByID($GUID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$Path = "images/Garage_Photo/Grage_Defualt.jpg";
			$qur = "INSERT INTO `Garage_Profile_Pic`( `GUID`, `Visible`, `Path` ) VALUES ( ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID); //GUID
			$stmt->bindParam(2, $Visible); //Visible
			$stmt->bindParam(3, $Path); //Path
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}

	public function C_GaragePersonel_ByID($GUID, array $Garage_Personel)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			for ($i = 0; $i <= count($Garage_Personel); $i++) {
				if (!empty($Garage_Personel[$i]) && $Garage_Personel[$i] != "") {
					$qur = "INSERT INTO `Garage_Personnel`( `GUID`, `Visible`, `RoleTopic`, `Personnel_GUID` ) VALUES ( ?, ?, ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $GUID);
					$stmt->bindParam(2, $Visible);
					$stmt->bindParam(3, $i);
					$stmt->bindParam(4, $Garage_Personel[$i]);
					$data = $stmt->execute();
				}
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_GarageExperts_ByID($GUID, array $Garage_Experts)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$RoleTopic = 5; //experts
			for ($i = 0; $i <= count($Garage_Experts); $i++) {
				if (!empty($Garage_Experts[$i]) && $Garage_Experts[$i] != "") {
					$qur = "INSERT INTO `Garage_Personnel`( `GUID`, `Visible`, `RoleTopic`, `Personnel_GUID` ) VALUES ( ?, ?, ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $GUID);
					$stmt->bindParam(2, $Visible);
					$stmt->bindParam(3, $RoleTopic);
					$stmt->bindParam(4, $Garage_Experts[$i]);
					$data = $stmt->execute();
				}
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_GarageSocial_ByID($GUID, array $Garage_Social)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			for ($i = 0; $i <= count($Garage_Social); $i++) {
				if(!empty($Garage_Social[$i]) && $Garage_Social[$i] != "" ){
					$qur = "INSERT INTO `Garage_Social`( `GUID`, `Visible`, `Social_Type`, `Address` ) VALUES ( ?, ?, ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $GUID);
					$stmt->bindParam(2, $Visible);
					$stmt->bindParam(3, $i);
					$stmt->bindParam(4, $Garage_Social[$i]);
					$data = $stmt->execute();
				}

			}
			unset($pdo);
			return $data;
		}
	}

	public function C_GarageCertification_ByID($GUID, array $G_Certification)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$Visible = 1;
			$qur = "INSERT INTO `Garage_Certificate`( `GUID`, `Visible`, `CertIssuer`, `CertStatus`, `CertNumber` ) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $G_Certification[0]);
			$stmt->bindParam(4, $G_Certification[1]);
			$stmt->bindParam(5, $G_Certification[2]);
			$data = $stmt->execute();
			unset($pdo);
			return $data;
		}
	}


	public function C_GargeSpecialDevices($GUID, array $SDevices)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			foreach ($SDevices as $key) {
				$qur = "INSERT INTO `Garage_Special_Device`( `GUID`, `DeviceID`) VALUES ( ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $GUID);
				$stmt->bindParam(2, $key);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_GarageAutomobile_ByID($GUID, array $auto)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			foreach ($auto as $key) {
				$qur = "INSERT INTO `Garage_Auto`( `GUID`, `AutoID`) VALUES ( ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $GUID);
				$stmt->bindParam(2, $key);
				$data = $stmt->execute();
			}
			unset($pdo);
			return $data;
		}
	}


	//if (count($Garage_Automobiles) > 0){
	//	$C_GarageAutomobile = $this->C_GarageAutomobile_ByID($Garage_GUID, $Garage_Automobiles);
	//	if($C_GarageAutomobile != true){
	//		return 5;
	//	}
	//}

	public function C_GarageFacility_ByID($GUID, array $Garage_Facility)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			for ($i = 0; $i <= count($Garage_Facility); $i++) {
				if (!empty($Garage_Facility[$i]) && $Garage_Facility[$i] != "") {
					$qur = "INSERT INTO `Garage_Facility`( `GUID`, `FacilityID`) VALUES ( ?, ?)";
					$stmt = $pdo->prepare($qur);
					$stmt->bindParam(1, $GUID);
					$stmt->bindParam(2, $Garage_Facility[$i]);
					$data = $stmt->execute();
				}
			}
			unset($pdo);
			return $data;
		}
	}

	public function C_Garage(array $Garage_Info)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$Visible = 1;
			$Action = 1;
			$comment = "Add Garage";
			$Garage_GUID = md5(uniqid(mt_rand(100000, 999999), true));
			$arr_return = array();

			$qur = "INSERT INTO `Garage`( `GUID`, `Visible`, `Name`, `Property_Ownership`, `DeedStat`, `RegNumber`, `BlueNumber`, `Capacity`, `PersonnelNum`, `Area`, `Tags`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Garage_GUID);
			$stmt->bindParam(2, $Visible);
			$stmt->bindParam(3, $Garage_Info[0]); //نام
			$stmt->bindParam(4, $Garage_Info[1]); // نوع ملک
			$stmt->bindParam(5, $Garage_Info[2]);  //وضعیت سند
			$stmt->bindParam(6, $Garage_Info[3]); //شماره پلاک ثبتی 
			$stmt->bindParam(7, $Garage_Info[4]); //  شماره پلاک شهرداری
			$stmt->bindParam(8, $Garage_Info[5]); //شهر
			$stmt->bindParam(9, $Garage_Info[6]); //  تعداد پرسنل
			$stmt->bindParam(10, $Garage_Info[7]); // مساحت 
			$stmt->bindParam(11, $Garage_Info[8]); // کلمات کلیدی
			$data = $stmt->execute();
			unset($pdo);
			if ($data == true) {
				$this->C_GaragePhoto_ByID($Garage_GUID);
				
				//////Log
				$this->C_Adminlog($Action, $comment);
				$arr_return[0] = 1;
				$arr_return[1] = $Garage_GUID;
				return $arr_return;
			} else {
				$arr_return[0] = 0;
				$arr_return[1] = "";
				return $arr_return;
			}
		}
	}



	//################################################
	//###################################################### get
	//################################################

	public function Get_GarageCities($GUID)
	{
		if ($this->_UPermission > 450) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Cities` WHERE `Visible`= 1 AND `CapitalCityID` = '$GUID' ORDER BY `FAName` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_GarageDevicesAll()
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

	public function Get_GarageProvinces()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Provinces` WHERE `Visible` = 1 ORDER BY `Name` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_GarageCity_ByProvinceID($PID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Cities` WHERE `Visible` = 1 AND `Autoapp_Cities`.`CapitalCityID`=$PID ORDER BY `FAName` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_GarageAddress_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = 'SELECT * FROM `Garage_Address` WHERE `GUID` ="' . $GUID . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['ID'];
				$result[1] = $data[0]['GUID'];
				$result[2] = $data[0]['Address']; 
				$result[3] = $data[0]['Longitude']; //طول جغرافیایی
				$result[4] = $data[0]['latitude'];//عرض جغرافیایی
				$result[5] = $data[0]['PostalCode'];
				$result[6] = $data[0]['ProvinceID'];
				$result[7] = $data[0]['CityGUID'];
				return $result;
			}
		}
	}

	
	public function Get_GarageProvince_ByID($ID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Provinces` WHERE `Visible` = 1 and ID= $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data;
			}
		}
	}

	public function Get_GarageCity_ByGUID($ID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Cities` WHERE `Visible` = 1 and `GUID`='$ID' ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data;
			}
		}
	}

	public function Get_GarageCity_ByID($ID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Cities` WHERE `Visible` = 1 and `ID`=$ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				return $data;
			}else{
				return 0;
			}
		}
	}
	

	//public function Get_DateHejri($miladi)
	//{
	//
	//	$miladidate =  str_replace("-", "", $miladi);
	//	$day = substr($miladidate, 6, 2);
	//	$mon = substr($miladidate, 4, 2);
	//	$year = substr($miladidate, 0, 4);
	//	$persian_Date = array($year, $mon, $day);
	//
	//	return $persian_Date;
	//}

	//################################################
	//######################################################  view
	//################################################


	//################################################
	//######################################################  Update 
	//################################################




	//################################################
	//######################################################  Delete 
	//################################################























}
