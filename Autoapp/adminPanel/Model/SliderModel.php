<?php

namespace fateh\Advertisements;

class MainSlideShow
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
        $Entity = 67; //Main Slide Show
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

        $Entity = 67; //Main Slide Show

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

    public function C_Slider($Slogn, $Link, $File, $Position)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $Adv_Type = 2;
            $Counter = 0;
            $slideGUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Slide";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `Adv_MainSlider` (`GUID`, `Visible`, `Slogn`, `LinkAddress` ,`FileAddress`, `Position`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $slideGUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Slogn);
            $stmt->bindParam(4, $Link);
            $stmt->bindParam(5, $File);
            $stmt->bindParam(6, $Position);
            $stmt->execute();
            unset($pdo);
            require("$rootDir/config/config_DB.php");
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $qur = "INSERT INTO `Adv_CountVisitors` (`GUID`, `Visible`, `URL`, `Adv_GUID` ,`Adv_Type`, `Counter`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Link);
            $stmt->bindParam(4, $slideGUID);
            $stmt->bindParam(5, $Adv_Type);
            $stmt->bindParam(6, $Counter);
            $stmt->execute();

            $this->C_Adminlog($Action, $comment);
        }
    }

    //################################################
    //###################################################### get
    //################################################

    public function Get_VisibleSlides()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_MainSlider` WHERE `Visible` = 1 ORDER BY `Adv_MainSlider`.`Position` ASC ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

    public function Get_Slide_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_MainSlider` WHERE `Visible` = 1 and `GUID` = '". $GUID. "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

    //################################################
    //######################################################  view
    //################################################
	public function V_SlideAll()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

            $qur = "SELECT * FROM `Adv_MainSlider`";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


    //################################################
    //######################################################  Update 
    //################################################

	public function U_HideSlide_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 0;
			$Action = 2;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_MainSlider` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}
    
    public function U_UnHideSlide_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 1;
			$Action = 9;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_MainSlider` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

    public function U_SlideImage_ByID($ID, $address)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Banner File Address";
			//Database
			$qur = "UPDATE `Adv_MainSlider` SET `FileAddress`= :fileaddress WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':fileaddress', $address);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

    public function U_SlideDetails_ByID($ID, $Slogn, $Link, $Position)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Slide Detail";
			//Database
			$qur = "UPDATE `Adv_MainSlider` SET `Slogn`= :slogn, `LinkAddress`= :banlink, `Position`= :Position WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':slogn', $Slogn);
			$stmt->bindParam(':banlink', $Link);
			$stmt->bindParam(':Position', $Position);
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


class ProductSlideShow
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
        $Entity = 68; //Production Slide Show
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

        $Entity = 68; //Production Slide Show

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

    public function C_Slider($Slogn, $Link, $File, $Position)
    {
        if ($this->_UPermission > 900) {
            //pre info
            $Visible = 1;
            $Action = 1;
            $Adv_Type = 3;
            $Counter = 0;
            $slideGUID = md5(uniqid(mt_rand(100000, 999999), true));
            $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
            $comment = "add Slide";
            require("$rootDir/config/config_DB.php");

            //Database
            $qur = "INSERT INTO `Adv_ProductSlider` (`GUID`, `Visible`, `Slogn`, `LinkAddress` ,`FileAddress`, `Position`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $slideGUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Slogn);
            $stmt->bindParam(4, $Link);
            $stmt->bindParam(5, $File);
            $stmt->bindParam(6, $Position);
            $stmt->execute();
            unset($pdo);
            require("$rootDir/config/config_DB.php");
            $GUID = md5(uniqid(mt_rand(100000, 999999), true));
            $qur = "INSERT INTO `Adv_CountVisitors` (`GUID`, `Visible`, `URL`, `Adv_GUID` ,`Adv_Type`, `Counter`) VALUES ( ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($qur);
            $stmt->bindParam(1, $GUID);
            $stmt->bindParam(2, $Visible);
            $stmt->bindParam(3, $Link);
            $stmt->bindParam(4, $slideGUID);
            $stmt->bindParam(5, $Adv_Type);
            $stmt->bindParam(6, $Counter);
            $stmt->execute();

            $this->C_Adminlog($Action, $comment);
        }
    }

    //################################################
    //###################################################### get
    //################################################

    public function Get_VisibleSlides()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_ProductSlider` WHERE `Visible` = 1 ORDER BY `Adv_ProductSlider`.`Position` ASC ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

    public function Get_Slide_ByID($GUID)
	{
		if ($this->_UPermission > 400) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Adv_ProductSlider` WHERE `Visible` = 1 and `GUID` = '". $GUID. "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

    //################################################
    //######################################################  view
    //################################################
	public function V_SlideAll()
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

            $qur = "SELECT * FROM `Adv_ProductSlider`";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


    //################################################
    //######################################################  Update 
    //################################################

	public function U_HideSlide_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 0;
			$Action = 2;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_ProductSlider` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}
    
    public function U_UnHideSlide_ByID($ID)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Visible = 1;
			$Action = 8;
			$comment = "Delete Banner";
			//Database
			$qur = "UPDATE `Adv_ProductSlider` SET `Visible`= :Visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':Visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

    public function U_SlideImage_ByID($ID, $address)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Banner File Address";
			//Database
			$qur = "UPDATE `Adv_ProductSlider` SET `FileAddress`= :fileaddress WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':fileaddress', $address);
			$stmt->bindParam(':id', $ID);
			$data = $stmt->execute();
			$stmt->fetchAll();

			$this->C_Adminlog($Action, $comment);
			return $data;
		}
	}

    public function U_SlideDetails_ByID($ID, $Slogn, $Link, $Position)
	{
		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$Action = 12;
			$comment = "Update Slide Detail";
			//Database
			$qur = "UPDATE `Adv_ProductSlider` SET `Slogn`= :slogn, `LinkAddress`= :banlink, `Position`= :Position WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':slogn', $Slogn);
			$stmt->bindParam(':banlink', $Link);
			$stmt->bindParam(':Position', $Position);
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
