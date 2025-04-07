<?php
namespace fateh\smschanel;

class SMS
{
//public $username;

	public function Logsms ($date,$username,$action,$Entity,$comment){
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
		
		$qur ="INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, `Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $date);
		$stmt->bindParam(2, $username);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt -> execute();
		
	}
	
	public function savesendsms ($to, $topic, $txt, $date, $username){
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
		
		$qur ="INSERT INTO `Sms_Send`( `Number`, `Topic`, `Txt`, `Date`, `User_ID_Actor`, `Actor_IP`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $to);
		$stmt->bindParam(2, $topic);
		$stmt->bindParam(3, $txt);
		$stmt->bindParam(4, $date);
		$stmt->bindParam(5, $username);
		$stmt->bindParam(6, $ip_address);
		$stmt -> execute();
		
	}
	
		public function SavechargingSMS ($to, $txt, $date, $username){
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
		$topic = 2;
		$qur ="INSERT INTO `Sms_Send_Charge`( `Number`, `Topic`, `Txt`, `Date`, `User_ID_Actor`, `Actor_IP`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $to);
		$stmt->bindParam(2, $topic);
		$stmt->bindParam(3, $txt);
		$stmt->bindParam(4, $date);
		$stmt->bindParam(5, $username);
		$stmt->bindParam(6, $ip_address);
		$stmt -> execute();
		
	}
	
	public function saveinboxsms (){
		require('config/config_SOAP_inbox.php');
		
		$parameters['isRead'] =True;
		$read = $sms_client->GetInboxCount($parameters)->GetInboxCountResult;

		$parameters['isRead'] =false;
		$notread = $sms_client->GetInboxCount($parameters)->GetInboxCountResult;
		$count = $read + $notread;

		$array = array();
		$array2 = array();
		$parameters['location'] =  1;
		$parameters['index'] = 0;
		$parameters['count'] =$count;
		$x = $sms_client->GetMessageStr($parameters)->GetMessageStrResult;
		$array = str_getcsv($x, "|");

		if($array[0] != "Count is OverLoaded"){
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
			$sql = 'TRUNCATE TABLE `Sms_Replay`';
			$statement = $pdo->prepare($sql);
			$statement->execute();
			unset($pdo);
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
			foreach($array as $value){ 
			$array2 = str_getcsv($value, ",");
			
			$qur ="INSERT INTO `Sms_Replay`(`CellNumber`, `DateTime`, `Txt`) VALUES ( ?, ?, ?)";
			$stmt = $pdo -> prepare ($qur);
			$stmt->bindParam(1, $array2[3]);
			$stmt->bindParam(2, $array2[4]);
			$stmt->bindParam(3, $array2[1]);
			$stmt -> execute();
			}
		}
		
		//pre info
		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;
        $username = $_SESSION["Admin_ID"];
		$Action = 12;
		$comment = "بروز رسانی آمار سرویس پیامک";
		//pre info
		
		$this-> Logsms ($date, $username , $Action, $ElemanType, $comment);

	}
	
	public function getinboxsms (){

		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		
		$qur ="SELECT * FROM `Sms_Replay`";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		foreach ($data as $row ){
			
			echo "<tr>";
			echo "<td>".$row['CellNumber']."</td>";
			echo "<td>".$row['DateTime']."</td>";
			echo "<td>".$row['Txt']."</td>";
			echo "</tr>";
		}
		
		//pre info
		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;
        $username = $_SESSION["Admin_ID"];
		$Action = 15;
		$comment = "مشاهده پیامک های دریافتی";
		//pre info
		
		$this-> Logsms ($date, $username , $Action, $ElemanType, $comment);
	}

	
	public function savecreditional()
    {
		//pre info
		$date = date('Y-m-d H:i:s');
		$Visible = 1;
		require('config/config_SOAP.php');

		$credit= $client->GetCredit($parameters)->GetCreditResult;
		$num_sms = explode('.', (string)$credit);

		//return
		$credit = $num_sms['0'];
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur ="INSERT INTO `Sms_Remain`(`Visible`, `DateTime`, `RemindNum`) VALUES ( ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $Visible);
		$stmt->bindParam(2, $date);
		$stmt->bindParam(3, $credit);
		$stmt -> execute();

	}
	
		public function send_sms($to, $text, $topic)
    {
		//pre info
		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;
		$username = $_SESSION["Admin_ID"];
		$Action = 17;
		require('config/config_SOAP.php');
		 try {
			$parameters['to'] = $to;
			$parameters['text'] = $text;
			$parameters['isflash'] = False;
			$parameters['udh'] = "";
			$parameters['recId'] = array(0);
			$parameters['status'] = 0x0;
			$client->GetCredit(array("username"=>"09125086808","password"=>"V@hidabdi6208"))->GetCreditResult;
			$responce = $client->SendSms($parameters)->SendSmsResult;
			if($responce == 1){
				//Log generator
				$comment = "موفق ارسال شد";
				$this-> Logsms ($date, $username , $Action, $ElemanType, $comment);
				foreach($to as $key){
					$this-> savesendsms ($key, $topic, $text, $date, $username);
					return $responce;
				}
			}else{
				//Log generator
				$comment = "ناموفق ارسال شد";
				$this-> Logsms ($date, $username , $Action, $ElemanType, $comment);
				return $responce;
			}
		 } catch (SoapFault $ex) {
			
			$comment = $ex->faultstring;
			$this-> Logsms ($date, $username , $Action, $ElemanType, $comment);
		}


	}
	
	public function getusername($id){
		if ($id != 0){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur ="SELECT * FROM `Users_Admin` where `ID`=?";
		$stm = $pdo -> prepare ($qur);
		$stm -> execute([$id]);
		$d = $stm ->fetchAll();
		unset($pdo);
        return $d[0]["u-name"];
		}else{
			return "بدون کاربر ثبت شده";}

	}
	
	public function gettopic($TID){
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");	
		$qur ="SELECT * FROM `Autoapp_SMSTopics` where `ID`=".$TID;
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		return $data[0]["PersianTopic"];

	}
	
	
	public function viewsended()
    {
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		
		$qur ="SELECT * FROM `Sms_Send` ORDER BY `Sms_Send`.`ID` DESC LIMIT 100";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		return $data;
	}
	
	public function viewcreditional()
    {
		//pre info
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$qur ="SELECT * FROM `Sms_Remain` WHERE `Visible` = 1 ORDER BY `ID` DESC limit 1";
		$stmt = $pdo -> prepare ($qur);
		$stmt -> execute();
		$data = $stmt ->fetchAll();
		return $data[0]['RemindNum'];


	}

	///////////////////////////////////////////
	///////////////////////////////////////////PWA
	///////////////////////////////////////////
	public function Mechanic_send_sms($to, $text, $topic, $guid)
    {
		//pre info
		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;
		$username = $guid;
		$Action = 17;
		require('config/config/config_SOAP.php');
		 try {
			$parameters['to'] = $to;
			$parameters['text'] = $text;
			$parameters['isflash'] = False;
			$parameters['udh'] = "";
			$parameters['recId'] = array(0);
			$parameters['status'] = 0x0;
			$client->GetCredit(array("username"=>"09125086808","password"=>"V@hidabdi6208"))->GetCreditResult;
			$responce = $client->SendSms($parameters)->SendSmsResult;
			if($responce == 1){
				//Log generator
				$comment = "موفق ارسال شد";
				$this-> LogMechanicSMS ($date, $username , $Action, $ElemanType, $comment);
				foreach($to as $key){
					$this-> SavechargingSMS ($key, $text, $date, $username);
					return $responce;
				}
			}else{
				//Log generator
				$comment = "پیامک خوش آمد ارسال نشد";
				$this-> LogMechanicSMS ($date, $username , $Action, $ElemanType, $comment);
				return $comment;
			}
		 } catch (SoapFault $ex) {
			
			$comment = $ex->faultstring;
			$this-> LogMechanicSMS ($date, $username , $Action, $ElemanType, $comment);
		}
	}
	public function LogMechanicSMS ($date,$username,$action,$Entity,$comment){
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
		
		$qur ="INSERT INTO `Member_Log`( `GUID`, `ActionDateTime`, `Activity`, `Entity`, `Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
		$stmt = $pdo -> prepare ($qur);
		$stmt->bindParam(1, $username);
		$stmt->bindParam(2, $date);
		$stmt->bindParam(3, $action);
		$stmt->bindParam(4, $Entity);
		$stmt->bindParam(5, $ip_address);
		$stmt->bindParam(6, $comment);
		$stmt -> execute();
		
	}

	
	
}
	
	?>