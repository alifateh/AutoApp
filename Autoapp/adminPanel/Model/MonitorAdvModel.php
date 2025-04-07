<?php

namespace fateh\Advertisements;

class Monitor
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
        $Entity = 70; //Monitoring Advertising
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

        $Entity = 70; //Monitoring Advertising

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

    public function C_Monitor($URL, $Adv_GUID, $Adv_Type)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $Counter = 0;
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Banner";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `Adv_CountVisitors` (`GUID`, `Visible`, `URL`, `Adv_GUID` ,`Adv_Type`, `Counter`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $URL);
            $stmt->bindParam(4, $Adv_GUID);
            $stmt->bindParam(5, $Adv_Type);
            $stmt->bindParam(6, $Counter);
            $stmt->execute();
            $this->C_Adminlog($Action, $comment);
        }
    }

    //################################################
    //###################################################### get
    //################################################

    public function Get_AllAdv_ByType($Type)
    {
        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Adv_CountVisitors` WHERE `Visible` = 1 and `Adv_Type` = $Type";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_Adv_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Adv_CountVisitors` WHERE `Visible` = 1 and `GUID` = '$GUID'";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_AdvBarchart_ByType()
    {
        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Adv_CountVisitors` WHERE `Visible` = 1";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_Advtitle_ByType($advGUID, $advType)
    {
        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            switch ($advType) {
                case 1: //banners
                    $qur = "SELECT * FROM `Adv_Banners` WHERE `Visible` = 1 AND `GUID` = '$advGUID'";
                    break;
                case 2: //mainslide
                    $qur = "SELECT * FROM `Adv_MainSlider` WHERE `Visible` = 1 AND `GUID` = '$advGUID' ";
                    break;
                case 3: //production slide
                    $qur = "SELECT * FROM `Adv_ProductSlider` WHERE `Visible` = 1 AND `GUID` = '$advGUID'";
                    break;
                case 4: //link
                    $qur = "SELECT * FROM `Adv_Application` WHERE `Visible` = 1 AND `GUID` = '$advGUID'";
                    break;
                default:
                    break;
            }

            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            switch ($advType) {
                case 1: //banners
                    return $data[0]['Owner'];
                    break;
                case 2: //mainslide
                    return $data[0]['Slogn'];
                    break;
                case 3: //production slide
                    return $data[0]['Slogn'];
                    break;
                case 4: //link
                    return $data[0]['Slogn'];
                    break;
                default:
                return '';
                    break;
            }
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
    public function V_AllAdvMonitor()
    {
        if ($this->_UPermission > 900) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");

            $qur = "SELECT * FROM `Adv_CountVisitors`";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }


    //################################################
    //######################################################  Update 
    //################################################

    public function U_AddCounter_ByID($ID)
    {
        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");

            //pre info
            //Database
            $qur = "UPDATE `Adv_CountVisitors` SET `Counter`= `Counter`+1 WHERE `Adv_GUID` = :id ";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(':id', $ID);
            $stmt->execute();
            $stmt->fetchAll();
        }
    }


    public function U_HideAdv_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 0;
			$Action = 24;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_CountVisitors` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}
    
    public function U_UnHideAdv_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 1;
			$Action = 24;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_CountVisitors` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
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
