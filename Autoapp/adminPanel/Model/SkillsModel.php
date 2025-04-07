<?php
namespace fateh\skills;

class skills
{

		private $_UPermission;
	
	public function __construct($UserGUID){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    	require("$rootDir/config/config_DB.php");		
		$qur ="SELECT * FROM `Admin_Permission` WHERE `AdminGUID` ='".$UserGUID."'";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		$count = 0;
		$Permission = null;
        if (is_array($data)) {
            $count = count($data);
			if($count > 0){
				$Permission = $data[0]['AccessValue'];
			}else{
				$qur ="SELECT * FROM `Member_Permission` WHERE `MemberGUID` ='".$UserGUID."'";
				$stmt = $pdo -> prepare ($qur);
				$stmt -> execute();
				$data = $stmt ->fetchAll();
				$Permission = $data[0]['AccessValue'];
		    }
		}
		if(!empty($Permission)){
			$this->_UPermission = $Permission;
		}else{
			$url = "/";
	        header("Location: $url");
		}
	}

//################################################
//######################################################  log
//################################################

	public function Logskills ($date,$username,$action,$Entity){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   
		  {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		  }
		//whether ip is from proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
		  {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		  }
		//whether ip is from remote address
		else
		  {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		  }
		
		$qur ="INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, 	`Actor_IP`, Comment) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $date);
		$stmt->bindParam(2, $username);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt -> execute();
		
	}
//################################################
//######################################################  Register 
//################################################
		
	public function addskills($name)
    {
		if( $this->_UPermission > 200 ){
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 17;
			$Visible = 1;
			$Action = 1;
			$comment = "No Comment";
			
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
        	$username = $_SESSION["Admin_ID"];
			//Database
			$qur ="INSERT INTO `Service_Type`( `Visible`, `Name`) VALUES ( ?, ?)";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $name);
			$stmt -> execute();
			
			//Log generator
			
			$this-> Logskills ($date, $username , $Action, $ElemanType,$comment);
			// return VALUES
		}
	}
	
//################################################
//######################################################  get
//################################################
		public function getkills($ID)
    {
		if( $this->_UPermission > 200 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			
			$qur ="SELECT * FROM `Service_Type` WHERE `Visible` = 1 and `ID`= ".$ID;
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			return $data[0]["Name"];
		}
	}

//################################################
//######################################################  view
//################################################
	
	public function V_SkillsAll()
    {
		if( $this->_UPermission > 200 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			
			$qur ="SELECT * FROM `Service_Type` WHERE `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			return $data;
		}
	}
//################################################
//######################################################  Update
//################################################
	
	public function updateskils($ID, $name)
    {
		if( $this->_UPermission > 200 ){
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 17;
			//$Visible = 1;
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
        	$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$comment = "No Comment";
			//Database
			$qur ="UPDATE `Service_Type` SET `Name`= :name WHERE `ID` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':id', $ID);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			
			//Log generator
			
			$this-> Logskills ($date, $username , $Action, $ElemanType, $comment);
		}
	}
	
//################################################
//######################################################  Remove 
//################################################
		public function removeskils($ID)
    {
		if( $this->_UPermission > 200 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			
					//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 17;
			$Visible = 0;
			$Action = 2;
			$comment = "No Comment";
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
        	$username = $_SESSION["Admin_ID"];
					//Database
			$qur ="UPDATE `Service_Type` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt -> execute();
			$data = $stmt ->fetchAll();		
			
					//Log generator
			
			$this-> Logskills ($date, $username , $Action, $ElemanType, $comment);
		}
		
	}

}