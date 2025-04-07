<?php

namespace fateh\login;

class Admin
{
    //################################################
    //######################################################  log
    //################################################

    public function C_Adminlog($username, $action, $Entity, $comment)
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
    public function Get_Admin($username)
    {
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
        $qur = "SELECT * FROM `Admin_Users` where `Aname`=? AND Visible = 1 ORDER BY ID DESC LIMIT 1";
        $stm = $pdo->prepare($qur);
        $stm->execute([$username]);
        $data = $stm->fetchAll();
        return $data;
    }

    public function Login_Admin($uname, $password)
    {
        $date = date('Y-m-d H:i:s');

        $memberRecord = $this->Get_Admin($uname);
        if (!empty($memberRecord)) {
            $hashedPassword = $memberRecord[0]["Pass"];
            if (password_verify($password, $hashedPassword)) {
                if ($memberRecord[0]["Visible"] == 1) {
                    session_start();
                    $_SESSION["Admin_ID"] = $memberRecord[0]["ID"];
                    $_SESSION["Admin_GUID"] = $memberRecord[0]["GUID"];
                    $_SESSION["userFLname"] = $memberRecord[0]["Fname"] . " " . $memberRecord[0]["Lname"];
                    //pre
                    $username = $memberRecord[0]["ID"];
                    $ElemanType = 10;
                    $Action = 3;
                    $comment = "";
                    $this->C_Adminlog($username, $Action, $ElemanType, $comment);
                    $ReValue = 10; //login success
                } else {
                    $username = $memberRecord[0]["ID"];
                    $ElemanType = 10;
                    $Action = 6;
                    $comment = "";
                    $this->C_Adminlog($username, $Action, $ElemanType, $comment);
                    $ReValue = 9;  // Admin Not Active

                }
            } else {
                //pre
                $username = 0;
                $ElemanType = 10;
                $Action = 7;
                $comment = $password;
                $this->C_Adminlog($username, $Action, $ElemanType, $comment);
                $ReValue = 8; // password wrong
            }
        } else {
            //pre info
            $username = 0;
            $ElemanType = 10;
            $Action = 5;
            $comment = "$uname ثبت نشده است";
            $this->C_Adminlog($username, $Action, $ElemanType, $comment);
            $ReValue = 7; //User not found
        }
        return $ReValue;
    }

    public function logout_Admin($username)
    {
        //pre info
        $ElemanType = 10;
        $Action = 4;
        $comment = "Exit";
        $this->C_Adminlog($username, $Action, $ElemanType, $comment);
        session_unset();
        session_write_close();
        $url = "/";
        header("Location: $url");
    }
}


class Member
{
    //################################################
    //######################################################  log
    //################################################
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

    public function Get_MemberInfo($username)
    {
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
        $qur = "SELECT * FROM `Member` WHERE `UName` ='$username' AND Visible = 1 ORDER BY ID DESC LIMIT 1";
        $stmt = $pdo->prepare($qur);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetchAll();
            return $data;
        } else {
            return 0;
        }
    }

    public function login_Mechanic($uname, $password)
    {
        $memberRecord = $this->Get_MemberInfo($uname);
        if (!empty($memberRecord)) {
            $hashedPassword = $memberRecord[0]["Pass"];
            if (password_verify($password, $hashedPassword) && $memberRecord[0]["Visible"] == 1) {

                if ($memberRecord[0]["Status"] == 1) { /////////////////check if mechanic have any enable account
                    session_start();
                    $_SESSION["Mechanic_GUID"] = $memberRecord[0]["GUID"];
                    $_SESSION["username"] = $memberRecord[0]["UName"];
                    $_SESSION["User_FName"] = $memberRecord[0]["FName"];
                    $_SESSION["User_LName"] = $memberRecord[0]["LName"];
                    $_SESSION["User_Status"] = $memberRecord[0]["Status"];
                    //pre
                    $username = $memberRecord[0]["ID"];
                    $ElemanType = 58;
                    $Action = 3;
                    $comment = "loged in";
                    $this->C_MechanicLog($memberRecord[0]["GUID"], $Action, $ElemanType, $comment);
                    $ReValue = 10; //login success
                } else {
                    session_start();
                    $_SESSION["Mechanic_GUID"] = $memberRecord[0]["GUID"];
                    $_SESSION["username"] = $memberRecord[0]["UName"];
                    $_SESSION["User_FName"] = $memberRecord[0]["FName"];
                    $_SESSION["User_LName"] = $memberRecord[0]["LName"];
                    $_SESSION["User_Status"] = $memberRecord[0]["Status"];
                    //pre
                    $username = $memberRecord[0]["ID"];
                    $ElemanType = 58;
                    $Action = 3;
                    $comment = "";
                    $this->C_MechanicLog($memberRecord[0]["GUID"], $Action, $ElemanType, $comment);
                    $ReValue = 10; //login success
                }
            } else {
                //pre
                $username = 0;
                $ElemanType = 58;
                $Action = 7;
                $comment = $password;
                $this->C_MechanicLog($username, $Action, $ElemanType, $comment);
                $ReValue = 8; // password wrong
            }
        } else {
            //pre info
            $username = 0;
            $ElemanType = 58;
            $Action = 5;
            $comment = "$uname ثبت نشده است";
            $this->C_MechanicLog($username, $Action, $ElemanType, $comment);
            $ReValue = 7; //User not found
        }
        return $ReValue;
    }

    public function logout_Member($GUID)
    {
        //pre info
        $ElemanType = 58;
        $Action = 4;
        $comment = "خروج" . $GUID;
        $this->C_MechanicLog($GUID, $Action, $ElemanType, $comment);
        return 1;
    }
}
