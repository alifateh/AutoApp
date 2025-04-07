<?php
namespace fateh\phonebook;

class phonebook
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
	public function C_PhoneLog ($username,$action,$Entity){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
		$date = date('Y-m-d H:i:s');
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


	public function AddTell($number , $GUI, $operator, $orgin, $ElemanType)
    {
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 1;	
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
        	$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$logcomment = $number;
			//Database
			$qur ="INSERT INTO `Phone_Book`( `Visible`, `PhoneGUI`, `ElemanType`, `Number`, `Operator`, `Originality`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $GUI);
			$stmt->bindParam(3, $ElemanType);
			$stmt->bindParam(4, $number);
			$stmt->bindParam(5, $operator);
			$stmt->bindParam(6, $orgin);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
		
	}

	public function AddFreeTell($number , $GUID, $ElemanType, $operator, $orgin, $Fname, $Lname, $Tags)
    {
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 1;	
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
        	$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$logcomment = $GUID;
			//Database
			$qur ="INSERT INTO `Free_ContactList`( `Visible`, `PhoneGUI`, `ElemanType`, `Number`, `Operator`, `Originality`, `FirstName`, `LastName`, `Tag`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $GUID);
			$stmt->bindParam(3, $ElemanType);
			$stmt->bindParam(4, $number);
			$stmt->bindParam(5, $operator);
			$stmt->bindParam(6, $orgin);
			$stmt->bindParam(7, $Fname);
			$stmt->bindParam(8, $Lname);
			$stmt->bindParam(9, $Tags);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
		
	}

	
//################################################
//######################################################  view
//################################################

	public function V_Phonebook (){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			
			$qur ="SELECT * FROM `Phone_Book` where `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			return $data;
		}
	}

	public function V_ContactList (){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Free_ContactList` where `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			return $date;
		}
	}
	

//################################################
//######################################################  get
//################################################

	public function Get_IsExistNum($num){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ='SELECT * FROM `Phone_Book` WHERE Visible =1 and `Number` ='.$num;
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt->rowCount() > 0) {
				//phone number exist
				return 1;
			}else{
				//phone number exist
				return 0;
			}
		}
		
	}

	public function Get_SearchNUM ($num){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    		 require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Phone_Book` WHERE `Number` LIKE '%".$num."%' and `Operator` = 1 and `Visible` = 1";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			return $data;
		}
	}

	public function Get_Phone_ByElement ($GID , $ElemanType){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			if ($ElemanType == 29 || $ElemanType == 30){
				$qur ="SELECT * FROM `Member` WHERE `Visible` = 1 and `GUID` ='".$GID."'";
				$stmt = $pdo -> prepare ($qur);
				$stmt -> execute();
				if ($stmt-> rowCount() > 0) {
					$data = $stmt ->fetchAll();
					$result = array();
					$result [0] = $data[0]['FName'];
					$result [1] = $data[0]['LName'];
				}else{
					$result = array();
					$result [0] = "بدون نام";
					$result [1] = "";
				}

			}
			elseif ($ElemanType == 41){
				$qur ="SELECT * FROM `Garage` WHERE `Visible` = 1 and `GUID` ='".$GID."'";
				$stmt = $pdo -> prepare ($qur);
				$stmt -> execute();
				if ($stmt-> rowCount() > 0){
					$data = $stmt ->fetchAll();
					$result = array();
					$result [0] = $data[0]['Name'];
					$result [1] = "";
				}else{
					$result = array();
					$result [0] = "بدون نام";
					$result [1] = "";
				}
			}
			return $result;
		}
	}
	
	public function Get_PhoneFree_ByID ($ID){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Free_ContactList` WHERE `Visible` = 1 and `ID` ='".$ID."'";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
				if ($stmt-> rowCount() > 0) {
					$data = $stmt ->fetchAll();
					$result = array();
					$result [0] = $data[0]['FirstName'];
					$result [1] = $data[0]['LastName'];
					$result [2] = $data[0]['Number'];
					$result [3] = $data[0]['Operator'];
					$result [4] = $data[0]['Tag'];
				}
			
			return $result;
		}
		
	}
	
	public function gettag ( $GID, $ElemanType){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			if ($ElemanType == 29 || $ElemanType == 30){
				$qur ="SELECT * FROM `Member_Address` WHERE `GUID` ='".$GID."'";
				$stmt = $pdo -> prepare ($qur);
				$stmt -> execute();
				if ($stmt-> rowCount() > 0){
					$data = $stmt ->fetchAll();
					return $data[0]['Tag'];
				}else{
					$result = "بدون تگ";
					return $result;
				}
			}
			elseif ($ElemanType == 41){
				$qur ="SELECT * FROM `Garage` WHERE `Visible` = 1 and `GUID` ='".$GID."'";
				$stmt = $pdo -> prepare ($qur);
				$stmt -> execute();
				if ($stmt-> rowCount() > 0){
					$data = $stmt ->fetchAll();
					return $data[0]['Tags'];
				}else{
					$result = "بدون تگ";
					return $result;
				}
			}
		}
	}
	
	public function getnumber ($GID){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			
			$qur ="SELECT * FROM `Phone_Book` WHERE `Visible` = 1 and `Operator` = 1 and `PhoneGUI` ='".$GID."'";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			$data = $stmt ->fetchAll();
			$i =0;
			$result = array();
			foreach ($data as $row ){

				$result [$i][0] = $row['Number'];
				$result [$i][1] = $row['Originality'];
				$i++;
			}
			return $result;
		}
	}

	public function Get_MechanicPhone_ByID($GUID){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
		$qur ="SELECT * FROM `Phone_Book` WHERE `Visible`=1 and `PhoneGUI` ='".$GUID."'";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		if ($stmt->rowCount() > 0) {
			$data = $stmt ->fetchAll();
			$result = array();
			$i =0;
			foreach ($data as $row ){
				$result[$i][0] = $row['Operator'];
				$result[$i][1] = $row['Number'];
				$result[$i][2] = $row['Originality'];
				$i++;
			}
			return $result;
		}
	}
	
	public function GetPhoneMAP($GUID){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Phone_Book` WHERE `Visible`=1 and `Originality` = 1 and `Operator`= 0 and `PhoneGUI` ='".$GUID."'";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt ->fetchAll();
				return $data[0]['Number'];
			}
		}
	}
	
	public function GetMemberMobile($GUID){
		if( $this->_UPermission > 250 ){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="SELECT * FROM `Phone_Book` WHERE `Operator`= 1 and `Originality`= 1 and `PhoneGUI` ='".$GUID."'";
			$stmt = $pdo -> prepare ($qur);
			$stmt -> execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt ->fetchAll();
				return $data[0]['Number'];
			}
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
				$result = array();
				$i =0;
				foreach ($data as $row ){
					$result[$i][0] = $row['ID'];
					$result[$i][1] = $row['PersianName'];
					$result[$i][2] = $row['EnglishName'];
					$i++;
				}
				return $result;
			}
		}
	}

//################################################
//######################################################  Update
//################################################

	public function UpdateTell($number , $GUI, $op, $orgin){
		if( $this->_UPermission > 250 ){
			//pre info
			$ElemanType = 30;
			$Visible = 1;
        	$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$logcomment = $number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Phone_Book` SET `Number`= :num, `Originality`= :org WHERE `Visible` = :visible and `Operator` = :operator and `PhoneGUI` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':num', $number);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':org', $orgin);
			$stmt->bindParam(':operator', $op);
			$stmt->bindParam(':id', $GUI);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ( $username , $Action, $ElemanType, $logcomment);
		}
	}
	public function UpdateTellByID($number , $ID, $ElemanType){
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 1;	
    		$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$logcomment = $number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    		require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Phone_Book` SET `Number`= :num WHERE `Visible` = :visible and `ID` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':num', $number);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt -> execute();
			//Log generator
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}
	public function UpdateTellOrgin($number , $GUI, $op) {
		if( $this->_UPermission > 250 ){
			//pre info
			$ElemanType = 30;
			$Visible = 1;
        	$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$orgin = 1;
			$logcomment = $number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Phone_Book` SET `Number`= :num WHERE `Visible` = :visible and `Operator` = :operator and `Originality`= :org and `PhoneGUI` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':num', $number);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':org', $orgin);
			$stmt->bindParam(':operator', $op);
			$stmt->bindParam(':id', $GUI);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}
	
	public function UpdateFreeTellByID($ID, $Fname, $Lname, $Number, $Operator, $tags){
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 1;	
        	$username = $_SESSION["Admin_ID"];
			$Action = 12;
			$ElemanType = 55;
			$logcomment = $Number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Free_ContactList` SET `Number`= :num, `Operator`= :oprt, `FirstName`= :Fname, `LastName`= :Lname, `Tag`= :Tag  WHERE `Visible` = :visible and `ID` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':num', $Number);
			$stmt->bindParam(':oprt', $Operator);
			$stmt->bindParam(':Fname', $Fname);
			$stmt->bindParam(':Lname', $Lname);
			$stmt->bindParam(':Tag', $tags);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}



//################################################
//######################################################  Remove 
//################################################	

	public function RemovePhone($ID, $ElemanType, $Number){
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 0;	
        	$username = $_SESSION["Admin_ID"];
			$Action = 2;
			$logcomment = $Number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Phone_Book` SET `Visible` = :visible WHERE `ID` = :id and `Number` = :num ";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->bindParam(':num', $Number);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}
	public function RemoveFreePhone($ID, $ElemanType, $Number){
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 0;	
    		$username = $_SESSION["Admin_ID"];
			$Action = 2;
			$logcomment = $Number;
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    		require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Free_ContactList` SET `Visible` = :visible WHERE `ID` = :id and `Number` = :num ";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->bindParam(':num', $Number);
			$stmt -> execute();

			//Log generator

			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}
	
	public function RemovePhonebyID($ID, $ElemanType){
		if( $this->_UPermission > 250 ){
			//pre info
			$Visible = 0;	
        	$username = $_SESSION["Admin_ID"];
			$Action = 2;
			$logcomment = "";
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        	require("$rootDir/config/config_DB.php");
			$qur ="UPDATE `Phone_Book` SET `Visible` = :visible WHERE `ID` = :id";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt -> execute();
			
			//Log generator
			
			$this-> C_PhoneLog ($username , $Action, $ElemanType, $logcomment);
		}
	}	
}

class PWA_PhoneBook
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
	public function C_PhoneLog ($username,$action,$Entity){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
		$date = date('Y-m-d H:i:s');
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

}
?>