<?php

namespace fateh\smschanel;

class SMS_Admin
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
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
        $date = date('Y-m-d H:i:s');
        $Entity = 28; //SMS
        $username = $_SESSION["Admin_ID"];

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
    //###################################################### Create
    //################################################

    public function C_SendedSMS($to, $topic, $txt, $date, $username)
    {
        if ($this->_UPermission > 450) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $Action = 17;
            $comment = "$username ارسال پیام برای ";

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

            $qur = "INSERT INTO `Sms_Send`( `Number`, `Topic`, `Txt`, `Date`, `User_ID_Actor`, `Actor_IP`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $to);
            $stmt->bindParam(2, $topic);
            $stmt->bindParam(3, $txt);
            $stmt->bindParam(4, $date);
            $stmt->bindParam(5, $username);
            $stmt->bindParam(6, $ip_address);
            $data = $stmt->execute();

            $this->C_Adminlog($Action, $comment);
            return $data;
        }
    }

    public function C_SendedChargingSMS($to, $txt, $date, $username)
    {
        if ($this->_UPermission > 450) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $topic = 2;
            $Action = 17;
            $comment = "$username ارسال پیام برای ";

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

            $qur = "INSERT INTO `Sms_Send_Charge`( `Number`, `Topic`, `Txt`, `Date`, `User_ID_Actor`, `Actor_IP`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $to);
            $stmt->bindParam(2, $topic);
            $stmt->bindParam(3, $txt);
            $stmt->bindParam(4, $date);
            $stmt->bindParam(5, $username);
            $stmt->bindParam(6, $ip_address);
            $data = $stmt->execute();

            $this->C_Adminlog($Action, $comment);
            return $data;
        }
    }

    public function C_SMSCredential()
    {
        if ($this->_UPermission > 450) {
            //pre info
            $date = date('Y-m-d H:i:s');
            $Visible = 1;
            $Action = 12;
            $comment = "بروز رسانی اعتبار sms";

            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_SOAP.php");
            $credit = $client->GetCredit($parameters)->GetCreditResult;
            $num_sms = explode('.', (string)$credit);

            //return
            $credit = $num_sms['0'];
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");
            $qur = "INSERT INTO `Sms_Remain`(`Visible`, `DateTime`, `RemindNum`) VALUES ( ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $Visible);
            $stmt->bindParam(2, $date);
            $stmt->bindParam(3, $credit);
            $data = $stmt->execute();

            $this->C_Adminlog($Action, $comment);
            return $data;
        }
    }

    //################################################
    //###################################################### SEND SMS
    //################################################
    public function send_sms($to, $text, $topic)
    {
        if ($this->_UPermission > 450) {
            //pre info
            $date = date('Y-m-d H:i:s');
            $username = $_SESSION["Admin_ID"];
            $Action = 17;
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_SOAP.php");
            try {
                $parameters['to'] = $to;
                $parameters['text'] = $text;
                $parameters['isflash'] = False;
                $parameters['udh'] = "";
                $parameters['recId'] = array(0);
                $parameters['status'] = 0x0;
                $client->GetCredit(array("username" => "09125086808", "password" => "V@hidabdi6208"))->GetCreditResult;
                $responce = $client->SendSms($parameters)->SendSmsResult;
                if ($responce == 1) {
                    //Log generator
                    $comment = "موفق ارسال شد";
                    $this->C_Adminlog($Action, $comment);
                    foreach ($to as $key) {
                        $this->C_SendedSMS($key, $topic, $text, $date, $username);
                        return $responce;
                    }
                } else {
                    //Log generator
                    $comment = "ناموفق ارسال شد";
                    $this->C_Adminlog($Action, $comment);
                    return $responce;
                }
            } catch (\SoapFault $ex) {

                $comment = $ex->faultstring;
                $this->C_Adminlog($Action, $comment);
            }
        }
    }


    //################################################
    //###################################################### get
    //################################################


    //################################################
    //######################################################  view
    //################################################
    public function V_SendedSMS()
    {
        if ($this->_UPermission > 450) {
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_DB.php");

            $qur = "SELECT * FROM `Sms_Send`";
            $stmt = $pdo->prepare($qur);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
    }

    public function V_SMSCreditional()
    {
		//pre info
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur ="SELECT * FROM `Sms_Remain` WHERE `Visible` = 1 ORDER BY `ID` DESC limit 1";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		//return $data[0]['RemindNum'];
		return $data;

	}

    //################################################
    //######################################################  Delete 
    //################################################



}

class SMS_PWA
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

        $Entity = 28; //SMS
        $GUID = $_SESSION["Mechanic_GUID"];

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

    public function C_SendedSMS($to, $topic, $txt, $date, $username)
    {
        if ($this->_UPermission > 450) {
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

            $Action = 17;
            $comment = "$username ارسال پیام برای ";

            $qur = "INSERT INTO `Sms_Send`( `Number`, `Topic`, `Txt`, `Date`, `User_ID_Actor`, `Actor_IP`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $to);
            $stmt->bindParam(2, $topic);
            $stmt->bindParam(3, $txt);
            $stmt->bindParam(4, $date);
            $stmt->bindParam(5, $username);
            $stmt->bindParam(6, $ip_address);
            $data = $stmt->execute();

            $this->C_MechanicLog($Action, $comment);
            return $data;
        }
    }

    //################################################
    //###################################################### SEND SMS
    //################################################

    public function send_sms($to, $text, $topic)
    {
        if ($this->_UPermission > 450) {
            //pre info
            $date = date('Y-m-d H:i:s');
            $username = $_SESSION["Admin_ID"];
            $Action = 17;
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            require("$rootDir/config/config_SOAP.php");
            try {
                $parameters['to'] = $to;
                $parameters['text'] = $text;
                $parameters['isflash'] = False;
                $parameters['udh'] = "";
                $parameters['recId'] = array(0);
                $parameters['status'] = 0x0;
                $client->GetCredit(array("username" => "09125086808", "password" => "V@hidabdi6208"))->GetCreditResult;
                $responce = $client->SendSms($parameters)->SendSmsResult;
                if ($responce == 1) {
                    //Log generator
                    $comment = "موفق ارسال شد";
                    $this->C_MechanicLog($Action, $comment);
                    foreach ($to as $key) {
                        $this->C_SendedSMS($key, $topic, $text, $date, $username);
                        return $responce;
                    }
                } else {
                    //Log generator
                    $comment = "ناموفق ارسال شد";
                    $this->C_MechanicLog($Action, $comment);
                    return $responce;
                }
            } catch (\SoapFault $ex) {

                $comment = $ex->faultstring;
                $this->C_MechanicLog($Action, $comment);
            }
        }
    }











}
