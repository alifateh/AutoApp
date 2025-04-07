<?php

namespace fateh\Phonebook;

class MechanicContact
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
        $Entity = 29; //Member Phone Contact
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
        $Entity = 29; //Member Phone Contact
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

    public function C_MechanicContact($MemberGUID, $Mobile, $Numbers)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Mechanic Phone Contact";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `N_PhoneBook_Member`(`GUID`, `Visible`, `Member_GUID`, `Mobile`, `Number`) VALUES ( ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $MemberGUID);
            $stmt->bindParam(4, $Mobile);
            $stmt->bindParam(5, $Numbers);
            $data = $stmt->execute();
            unset($pdo);
            if ($data == true) {
                $this->C_Adminlog($Action, $comment);
                return 1;
            } else {
                return 0;
            }
        }
    }
    //################################################
    //###################################################### get
    //################################################

    public function Get_MechanicContact_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `N_PhoneBook_Member` WHERE `Visible` = 1 and `Member_GUID` = '$GUID'";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_MechanicSerch_ByID($SValue)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT DISTINCT N_PhoneBook_Member.Mobile , N_PhoneBook_Member.Number, Member.FName, Member.LName, Member.UName, Member_Address.Tag 
            FROM `N_PhoneBook_Member` 
            INNER JOIN Member ON N_PhoneBook_Member.Member_GUID = Member.GUID 
            INNER JOIN Member_Address ON Member.GUID = Member_Address.GUID 
            WHERE N_PhoneBook_Member.`Visible`= 1 and Member.Visible =1 and 
            (Member.FName LIKE '%$SValue%' OR Member.LName LIKE '%$SValue%' OR N_PhoneBook_Member.Number LIKE '%$SValue%' OR Member_Address.Tag LIKE '%$SValue%')";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_PhoneOperator(){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Autoapp_TellOperator` WHERE `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt-> rowCount() > 0) {
				$data = $stmt ->fetchAll();
				return $data;
			}else{
                return false;
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
    public function V_MechanicsContacs()
    {
        if ($this->_UPermission > 900) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT DISTINCT N_PhoneBook_Member.Mobile , N_PhoneBook_Member.Number, Member.FName, Member.LName, Member.UName, Member_Address.Tag 
            FROM `N_PhoneBook_Member`
            INNER JOIN Member ON N_PhoneBook_Member.Member_GUID = Member.GUID 
            INNER JOIN Member_Address ON Member.GUID = Member_Address.GUID 
            WHERE N_PhoneBook_Member.`Visible`= 1 and Member.Visible =1";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }

    }


    //################################################
    //######################################################  Update 
    //################################################
    public function U_MechanicPhone_ByID($MechanicGUID, $ContactID ,$NewNumber)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Contact $MechanicGUID";
			//Database
			$qur = "UPDATE `N_PhoneBook_Member` SET `Number`= :Num WHERE `Member_GUID` = :MechanicGUID AND `GUID` = :ContactID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Num', $NewNumber);
			$stmt->bindParam(':MechanicGUID', $MechanicGUID);
			$stmt->bindParam(':ContactID', $ContactID);
			$data = $stmt->execute();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}


    //################################################
    //######################################################  Delete 
    //################################################
    //NEVER Remove Banners, So DO NOT turn Visible to 0
    public function D_MechanicContact_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 2;
			$comment = "Remove Garage Contact $ID";
            $Visible = 0;
			//Database
			$qur = "UPDATE `N_PhoneBook_Member` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}


}

class GarageContact
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
        $Entity = 41; //Garage Phone Contact
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
        $Entity = 41; //Garage Phone Contact
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

    public function C_GarageContact($GarageGUID, $Mobile, array $Numbers)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Mechanic Phone Contact";
            require("$rootDir/config/config_DB.php");

            //Database
            for ($i = 0; $i <= count($Numbers); $i++) {
                if (!empty($Numbers[$i])) {
                    $qur = "INSERT INTO `N_PhoneBook_Garage`(`GUID`, `Visible`, `Garage_GUID`, `Mobile`, `Number`) VALUES ( ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($qur);
                    $stmt->bindParam(1, $GUID);
                    $stmt->bindParam(2, $Visible);
                    $stmt->bindParam(3, $GarageGUID);
                    $stmt->bindParam(4, $Mobile);
                    $stmt->bindParam(5, $Numbers[$i]);
                    $data = $stmt->execute();
                }else{
                    $data = true;
                }
            }

            unset($pdo);
            if ($data == true) {
                $this->C_Adminlog($Action, $comment);
                return 1;
            } else {
                return 0;
            }
        }
    }
    //################################################
    //###################################################### get
    //################################################

    public function Get_GarageContact_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `N_PhoneBook_Garage` WHERE `Visible` = 1 and `Garage_GUID` = '$GUID'";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_GarageSerch_ByID($SValue)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT DISTINCT N_PhoneBook_Garage.Mobile , N_PhoneBook_Garage.Number, Garage.Name, Garage.Tags, Garage.BlueNumber 
            FROM `N_PhoneBook_Garage` 
            INNER JOIN Garage ON N_PhoneBook_Garage.Garage_GUID = Garage.GUID 
            WHERE N_PhoneBook_Garage.`Visible`= 1 and Garage.Visible =1 and 
            (Garage.Name LIKE '%$SValue%' OR Garage.Tags LIKE '%$SValue%' OR N_PhoneBook_Garage.Number LIKE '%$SValue%')";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_PhoneOperator(){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Autoapp_TellOperator` WHERE `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt-> rowCount() > 0) {
				$data = $stmt ->fetchAll();
				return $data;
			}else{
                return false;
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
    public function V_GarageContacs()
    {
        if ($this->_UPermission > 900) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT DISTINCT N_PhoneBook_Garage.Mobile , N_PhoneBook_Garage.Number, Garage.Name, Garage.Tags, Garage.BlueNumber 
            FROM `N_PhoneBook_Garage` 
            INNER JOIN Garage ON N_PhoneBook_Garage.Garage_GUID = Garage.GUID 
            WHERE N_PhoneBook_Garage.`Visible`= 1 and Garage.Visible =1";
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

    }

   

    //################################################
    //######################################################  Delete 
    //################################################
    //NEVER Remove Banners, So DO NOT turn Visible to 0

    public function D_GarageContact_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 2;
			$comment = "Remove Garage Contact $ID";
            $Visible = 0;
			//Database
			$qur = "UPDATE `N_PhoneBook_Garage` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

}


class IndependentContact
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
        $Entity = 29; //Member Phone Contact
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
        $Entity = 29; //Member Phone Contact
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

    public function C_IndependentContact($FirstName, $LastName, $Mobile, $Number, $Tags)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Independent Phone Contact";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `Autoapp_IndependentContact`(`GUID`, `Visible`, `FirstName`, `LastName`, `Mobile`, `Number`, `Tags`) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $FirstName);
            $stmt->bindParam(4, $LastName);
            $stmt->bindParam(5, $Mobile);
            $stmt->bindParam(6, $Number);
            $stmt->bindParam(7, $Tags);
            $data = $stmt->execute();
            unset($pdo);
            if ($data == true) {
                $this->C_Adminlog($Action, $comment);
                return 1;
            } else {
                return 0;
            }
        }
    }
    //################################################
    //###################################################### get
    //################################################

    public function Get_IndependentContact_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_IndependentContact` WHERE `Visible` = 1 and `GUID` = '$GUID'";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_IndependentSerch_ByID($SValue)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_IndependentContact` WHERE `FirstName` LIKE '%$SValue%' OR `LastName` LIKE '%$SValue%' OR `Number` LIKE '%$SValue%' OR `Tags` LIKE '%$SValue%'";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Get_PhoneOperator(){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Autoapp_TellOperator` WHERE `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt-> rowCount() > 0) {
				$data = $stmt ->fetchAll();
				return $data;
			}else{
                return false;
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
    public function V_IndependentContact()
    {
        if ($this->_UPermission > 900) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_IndependentContact` WHERE `Visible` = 1";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }

    }

    //################################################
    //######################################################  Update 
    //################################################
    public function U_ContactIndependent_ByID($GUID, $FirstName, $LastName, $Mobile, $Number, $Tags)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Contact $MechanicGUID";
			//Database
			$qur = "UPDATE `Autoapp_IndependentContact` SET `FirstName`= :fname, `LastName`= :lnam, `Mobile`= :operator, `Number`= :Num, `Tags`= :tag WHERE `GUID` = :CGUID";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':fname', $FirstName);
			$stmt->bindParam(':lnam', $LastName);
			$stmt->bindParam(':operator', $Mobile);
			$stmt->bindParam(':Num', $Number);
			$stmt->bindParam(':tag', $Tags);
			$stmt->bindParam(':CGUID', $GUID);
			$data = $stmt->execute();
            unset($pdo);
            if ($data == true) {
                $this->C_Adminlog($Action, $comment);
                return 1;
            } else {
                return 0;
            }
		}
	}


    //################################################
    //######################################################  Delete 
    //################################################
    //NEVER Remove Banners, So DO NOT turn Visible to 0
    public function D_IndependentContact_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 2;
			$comment = "Remove Garage Contact $ID";
            $Visible = 0;
			//Database
			$qur = "UPDATE `Autoapp_IndependentContact` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}


}