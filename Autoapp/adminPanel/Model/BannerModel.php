<?php

namespace fateh\Advertisements;

class Banners
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
        $Entity = 66; //contarct
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

        $Entity = 66; //contarct

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

    public function C_Banner($Link, $Slogn, $Comments, $Owner, $File, $Date_End, $Position)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $date = date('Y-m-d H:i:s');
            $Visible = 1;
            $Action = 1;
            $Adv_Type = 1;
            $Counter = 0;
            $BannerGUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Banner";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `Adv_Banners`(`GUID`, `Visible`, `Link`, `Slogn` ,`Comments`, `Owner`, `File`, `Date_Start`, `Date_End`, `Position`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $BannerGUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Link);
            $stmt->bindParam(4, $Slogn);
            $stmt->bindParam(5, $Comments);
            $stmt->bindParam(6, $Owner);
            $stmt->bindParam(7, $File);
            $stmt->bindParam(8, $date);
            $stmt->bindParam(9, $Date_End);
            $stmt->bindParam(10, $Position);
            $stmt->execute();
            unset($pdo);
            require("$rootDir/config/config_DB.php");
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $qur = "INSERT INTO `Adv_CountVisitors` (`GUID`, `Visible`, `URL`, `Adv_GUID` ,`Adv_Type`, `Counter`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Link);
            $stmt->bindParam(4, $BannerGUID);
            $stmt->bindParam(5, $Adv_Type);
            $stmt->bindParam(6, $Counter);
            $stmt->execute();
            $this->C_Adminlog($Action, $comment);
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
			$qur = "SELECT * FROM `Adv_Banners` WHERE `Visible` = 1 and `GUID` = '". $GUID. "'";
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
