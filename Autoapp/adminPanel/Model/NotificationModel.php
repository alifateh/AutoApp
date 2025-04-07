<?php

namespace fateh\Notification;

class InAppNotifications
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

    public function C_Adminlog($action, $Entity, $comment)
    {
        $username = $_SESSION["Admin_ID"];
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

    public function C_MechanicLog($GUID, $action, $Entity, $comment)
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
    //######################################################  Create
    //################################################

    public function C_Notification($Title, $Text, $StartDate, $EndDate)
    {
        if ($this->_UPermission > 400) {
            //pre info
            $ElemanType = 65;
            $Visible = 1;
            $Validation = 1;
            $Action = 1;
            $comment = "Add New Notification";
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));

            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");


            //Database
            $qur = "INSERT INTO `Autoapp_NotificationInApp`( `GUID`, `Visible`, `Title`, `Text`, `Start`, `End`, `Validation`) VALUES ( ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Title);
            $stmt->bindParam(4, $Text);
            $stmt->bindParam(5, $StartDate);
            $stmt->bindParam(6, $EndDate);
            $stmt->bindParam(7, $Validation);
            $data = $stmt->execute();

            //Log generator

            $this->C_Adminlog($Action, $ElemanType, $comment);
            return $data;
        }
    }

    //################################################
    //######################################################  Get
    //################################################

    public function GET_Notification_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_NotificationInApp` WHERE `Visible` = 1 and `GUID` = '$GUID' ";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function GET_Notification_ByMechanicID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Member_Notification` WHERE `Visible` = 1 and `Member_GUID` = '$GUID' ";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function Gat_NotificationCount()
    {

        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT COUNT(*) FROM `Autoapp_NotificationInApp`";
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
    //######################################################  View
    //################################################
    public function V_InAPPNotif()
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_NotificationInApp` WHERE `Visible` =1";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function V_NotificationAll()
    {
        if ($this->_UPermission > 100) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_NotificationInApp`";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }
    //################################################
    //###################################################### Update
    //################################################

    public function U_Notif_Status($MemGUID, $NotifGUID){
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
        $qur = "UPDATE `Member_Notification` SET `Visible`=0 WHERE `Member_GUID` = '" . $MemGUID . "' AND `Notif_GUID` = '" .$NotifGUID. "'";
        $stmt = $pdo->prepare($qur);
        $data = $stmt->execute();
        $stmt->fetchAll();
        return $data;
    }
    public function U_NotifValid()
    {

        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_NotificationInApp` WHERE Autoapp_NotificationInApp.End < now() AND `Visible` =1 AND `Validation`=1";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $pdo = null;
        }
        if (!empty($data)) {

            foreach ($data as $row) {
                $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
                require("$rootDir/config/config_DB.php");
                $qur = "UPDATE `Autoapp_NotificationInApp` SET `Visible`=0, `Validation`=0 WHERE `GUID` = '" . $row['GUID'] . "'";
                $stmt = $pdo->prepare($qur);
                $stmt->execute();
                $stmt->fetchAll();

                $sql = "UPDATE `Member_Notification` SET `Visible`=0 WHERE `Notif_GUID` = '" . $row['GUID'] . "'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $stmt->fetchAll();
                $pdo = null;
            }
        }
    }

    public function U_Notification_ByID($GUID, $Title, $Text, $StartDate, $EndDate)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $Action = 12;
            $ElemanType = 65;
            $comment = "Update Notification";
            $Visible =1 ;
            $Validation =1;

            $qur = "UPDATE `Autoapp_NotificationInApp` SET `Visible`= :Visible, `Title`= :Title, `Text`= :notifText, `Start`= :startDate, `End`= :enddate, `Validation`= :Validat WHERE `GUID` = :id";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(':Visible', $Visible);
            $stmt->bindParam(':Title', $Title);
            $stmt->bindParam(':notifText', $Text);
            $stmt->bindParam(':startDate', $StartDate);
            $stmt->bindParam(':enddate', $EndDate);
            $stmt->bindParam(':Validat', $Validation);
            $stmt->bindParam(':id', $GUID);
            $data = $stmt->execute();
            $stmt->fetchAll();

            $this->C_Adminlog($Action, $ElemanType, $comment);
            $this-> U_NotifValid();
            $this-> D_AllMemeberNotValidNotif();
            return $data;
        }
    }

    //################################################
    //###################################################### Delete
    //################################################
    public function D_AllMemeberNotValidNotif(){

        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "SELECT * FROM `Autoapp_NotificationInApp` WHERE `Visible` = 0 OR `Validation`= 0";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();

            if(!empty($data)){
                foreach($data as $row){
                $qur = "UPDATE `Member_Notification` SET `Visible`=0 WHERE `Notif_GUID` = '" . $row['GUID'] . "'";
                $stmt = $pdo->prepare($qur);
                $stmt->execute();
    
                }
    
            }
        }

    }
    
    public function D_Notification_ByID($GUID)
    {
        if ($this->_UPermission > 400) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");

            $Visible = 0;
            $valid = 0;
            $Action = 2;
            $ElemanType = 65;
            $comment = "Remove Notification";

            $qur = "UPDATE `Autoapp_NotificationInApp` SET `Visible`= :visible, `Validation`= :valid WHERE `GUID` = :id";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(':visible', $Visible);
            $stmt->bindParam(':valid', $valid);
            $stmt->bindParam(':id', $GUID);
            $data = $stmt->execute();
            $stmt->fetchAll();

            $this->C_Adminlog($Action, $ElemanType, $comment);
            $this-> U_NotifValid();
            $this-> D_AllMemeberNotValidNotif();
            return $data;
        }
    }
}
