<?php

namespace fateh\Finance;

class Invoice
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



	public function logInvoce($date, $username, $action, $Entity, $comment)
	{
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

		$qur = "INSERT INTO `Activity_Log`( `ActionDateTime`, `User_ID_Actor`, `Activity`, `Entity`, 	`Actor_IP`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
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
	//######################################################  view
	//################################################
	public function View_Invoice_All()
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Invoice` WHERE `Visible` = 1 ORDER BY `ID` ASC";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_MechanicInvoice($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `All_Member_Invoice` WHERE `Member_GUID` ='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_Invoice_Doc($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Autoapp_Files` WHERE `Visible` = 1 and `Filekey` = '" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function View_PayedBill_ByGUID($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Payed_Bills` WHERE `Visible` IN (1, 3) and `Member_GUID` ='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_PaidBills($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT `Invoice`.`Visible`, `Invoice`.`GUID`, `Invoice`.Title, `Payed_Bills`.Invoice_GUID, `Payed_Bills`.Member_GUID, `Payed_Bills`.Visible, `Payed_Bills`.Amount, `Payed_Bills`.Payment_Method, `Payed_Bills`.Payment_Date, `Payed_Bills`.RetrivalRefNum, `Payed_Bills`.SysTraceNum, `Payed_Bills`.Comment from `Invoice` INNER JOIN `Payed_Bills`ON `Payed_Bills`.`Invoice_GUID`= `Invoice`.`GUID` WHERE `Invoice`.`Visible`= 1 and `Payed_Bills`.Visible =1 and `Invoice`.Title='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function V_NotPaidBills($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `All_Member_Invoice` Inner join `Invoice` ON `All_Member_Invoice`.`Inv_GUID` = `Invoice`.`GUID` WHERE `Invoice`.`Visible` =1 and  `Invoice`.Title='" . $GUID . "'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
	//################################################
	//######################################################  get
	//################################################



	public function Get_Invoice_ByGUID($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Invoice` WHERE `Visible` = 1 and `GUID` = '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_InvoiceTtitle_ByID($ID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible` = 1 and `ID` = $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	//################################################
	//######################################################  Update
	//################################################

	public function Update_Invoice_ByGUID(array $INV)
	{

		if ($this->_UPermission > 900) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 56;
			$Visible = 1;
			$Action = 12;
			$comment = "";
			$username = $INV[5];
			$GUID = $INV[0];
			$Inv_Title = $INV[1];
			$Inv_StartDate = $INV[2];
			$Inv_Amount = str_replace(",", "", $INV[3]);
			$Inv_Comment = $INV[4];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$data = [
				'id' => $INV[0],
				'topic' => $INV[1],
				'amount' => $INV[3],
				'sdate' => $INV[2],
				'Comment' => $INV[4],
			];
			$sql = "UPDATE Invoice SET `Comment`=:Comment, `Title`=:topic, `Amount`=:amount, `Start_Date`=:sdate WHERE GUID=:id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute($data);
			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function U_MultiPay($Payed_ID)
	{

		if ($this->_UPermission > 10100) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 62;
			$Visible = 3;
			$Action = 1;
			$comment = "add multi-step Payment";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			$data = [
				'id' => $Payed_ID,
				'Visible' => $Visible
			];
			$sql = "UPDATE Payed_Bills SET `Visible`=:Visible WHERE `ID`=:id";
			$stmt = $pdo->prepare($sql);
			$result = $stmt->execute($data);
			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
			return $result;
		}
	}

	//################################################
	//######################################################  Remove 
	//################################################

	public function Remove_Invoice($ID)
	{

		if ($this->_UPermission > 900) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 56;
			$Visible = 0;
			$Action = 2;
			$comment = "remove invoice = $ID";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Invoice` SET `Visible`= :visible WHERE `GUID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			//Log generator
			$this->Remove_Invoice_Doc($ID);
			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function Remove_Invoice_Doc($ID)
	{

		if ($this->_UPermission > 100) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 57;
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Invoice Doc $ID";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Autoapp_Files` SET `Visible`= :visible WHERE `Filekey` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			//Log generator

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}

	public function Remove_Invoice_DocByID($ID)
	{

		if ($this->_UPermission > 900) {
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 57;
			$Visible = 0;
			$Action = 2;
			$comment = "Remove Invoice Doc $ID";
			$username = $_SESSION["Admin_ID"];
			//Database
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "UPDATE `Autoapp_Files` SET `Visible`= :visible WHERE `ID` = :id";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(':visible', $Visible);
			$stmt->bindParam(':id', $ID);
			$stmt->execute();
			//Log generator

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);

			// return VALUES
		}
	}

	//################################################
	//######################################################  Register 
	//################################################

	public function Register_Invoice(array $invoice)
	{

		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");

			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 56;
			$username = $invoice[5];
			$Action = 1;
			$comment = "Adding Invoice";
			$Visible = 1;

			//trim value
			$Amount =  str_replace(",", "", $invoice[3]);

			// date hejri to garygori

			$persiandate =  str_replace("/", "", $invoice[2]);
			$day = substr($persiandate, 8, 2);
			$mon = substr($persiandate, 5, 2);
			$year = substr($persiandate, 0, 4);
			$Inv_Start_Date = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
			$Inv_Start_Date = date('Y-m-d', $Inv_Start_Date);

			$qur = "INSERT INTO `Invoice` (`GUID`, `Visible`, `Start_Date`, `Title`, `Amount`, `Comment`) VALUES ( ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindValue(1, $invoice[0]);
			$stmt->bindValue(2, $Visible);
			$stmt->bindValue(3, $Inv_Start_Date);
			$stmt->bindValue(4, $invoice[1]);
			$stmt->bindValue(5, $Amount);
			$stmt->bindValue(6, $invoice[4]);
			$stmt->execute();

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function Register_Invoice_File($Inv_GUID, $File_Address)
	{

		if ($this->_UPermission > 900) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 57;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$comment = "Register_Invoice_File";
			//Database
			$qur = "INSERT INTO `Autoapp_Files` ( `Visible`, `ElemanType`, `Filekey`, `location`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindValue(1, $Visible);
			$stmt->bindValue(2, $ElemanType);
			$stmt->bindValue(3, $Inv_GUID);
			$stmt->bindValue(4, $File_Address);
			$stmt->execute();

			//Log generator

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
		}
	}

	public function Register_Payment_NoIPG(array $invoice)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 56;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 1;
			$Payment_Method = 0; //NO IPG 
			$comment = "Register_Payment_NoIPG";

			//Database
			$qur = "INSERT INTO `Payed_Bills` ( `Visible`, `Member_GUID`, `Invoice_GUID`, `Amount`, `OrderID`, `SysTraceNum`, `RetrivalRefNum`, `Comment`, `Payment_Method`, `Payment_Date`) 
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $invoice[0]); //Member_GUID
			$stmt->bindParam(3, $invoice[1]); // Invoice_GUID
			$stmt->bindParam(4, $invoice[2]); //Amount
			$stmt->bindParam(5, $invoice[3]); //OrderID
			$stmt->bindParam(6, $invoice[4]); //SysTraceNum
			$stmt->bindParam(7, $invoice[5]); //RetrivalRefNum
			$stmt->bindParam(8, $invoice[6]); //Comment
			$stmt->bindParam(9, $Payment_Method); //Method
			$stmt->bindParam(10, $date); //Payment_Date
			$stmt->execute();
			// Remove Bill
			$this->Remove_Bill_ByGUID($invoice);

			//Log generator

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
			return 1;
		}
	}

	public function Register_Payment(array $invoice)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$ElemanType = 56;
			$Visible = 1;
			$username = $_SESSION["Admin_ID"];
			$Action = 21; //Register Peyment
			$Payment_Method = 1; // IPG 
			$comment = "Register_Payment";

			//Database
			$qur = "INSERT INTO `Payed_Bills` ( `Visible`, `Member_GUID`, `Invoice_GUID`, `Amount`, `OrderID`, `SysTraceNum`, `RetrivalRefNum`, `Comment`, `Payment_Method`, `Payment_Date`) 
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $invoice[0]); //Member_GUID
			$stmt->bindParam(3, $invoice[1]); // Invoice_GUID
			$stmt->bindParam(4, $invoice[2]); //Amount
			$stmt->bindParam(5, $invoice[3]); //OrderID
			$stmt->bindParam(6, $invoice[4]); //SysTraceNum
			$stmt->bindParam(7, $invoice[5]); //RetrivalRefNum
			$stmt->bindParam(8, $invoice[6]); //Comment
			$stmt->bindParam(9, $Payment_Method); //Method
			$stmt->bindParam(10, $date); //Payment_Date
			$stmt->execute();
			// Remove Bill
			$this->Remove_Bill_ByGUID($invoice);

			//Log generator

			$this->logInvoce($date, $username, $Action, $ElemanType, $comment);
			return 1;
		}
	}

	//################################################
	//######################################################  DELETE 
	//################################################
	public function Remove_Bill_ByGUID(array $Inv)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//Database
			$data = [
				'M_GUID' => $Inv[0],
				'Inv_GUID' => $Inv[1],
			];
			$qur = "DELETE FROM `All_Member_Invoice` where `Member_GUID` = :M_GUID AND `Inv_GUID` = :Inv_GUID ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute($data);
		}
	}

	//################################################
	//######################################################  Payment Sessions 
	//################################################

	public function C_IPGResponce(array $sessionArr)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$qur = "INSERT INTO `IPG_Res` (`MechanicGUID`, `InvoiceGUID`, `Token`, `ResCode`, `OrderId`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $sessionArr[0]);
			$stmt->bindParam(2, $sessionArr[1]);
			$stmt->bindParam(3, $sessionArr[2]);
			$stmt->bindParam(4, $sessionArr[3]);
			$stmt->bindParam(5, $sessionArr[4]);
			$stmt->execute();
		}
	}

	public function SetSeesion(array $sessionArr)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$qur = "INSERT INTO `Payment_Session` (`Admin_GUID`, `Mem_GUID`, `Order_GUID`, `Invoice_GUID`) VALUES ( ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $sessionArr[0]);
			$stmt->bindParam(2, $sessionArr[1]);
			$stmt->bindParam(3, $sessionArr[2]);
			$stmt->bindParam(4, $sessionArr[3]);
			$stmt->execute();
		}
	}

	public function GetSessioninfo($OrderId)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$qur = 'SELECT * FROM `Payment_Session` WHERE `Order_GUID` = "' . $OrderId . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['Admin_GUID'];
				$result[1] = $data[0]['Mem_GUID'];
				$result[2] = $data[0]['Order_GUID'];
				$result[3] = $data[0]['Invoice_GUID'];
				return $result;
			} else {
				return 1;
			}
		}
	}

	public function Fetch_Admin_Session_Byorder($OrderId)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$qur = 'SELECT * FROM `Payment_Session` WHERE `Order_GUID` = "' . $OrderId . '"';
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$data = $stmt->fetchAll();
				$result = array();
				$result[0] = $data[0]['Admin_GUID'];
				$result[1] = $data[0]['Mem_GUID'];
				$result[2] = $data[0]['Order_GUID'];
				$result[3] = $data[0]['Invoice_GUID'];
				return $result;
			} else {
				return 1;
			}
		}
	}

	public function getPayedTariff($guid, $TariffID)
	{
		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT `Invoice`.`Visible`, `Invoice`.`GUID`, `Invoice`.Title, `Payed_Bills`.Invoice_GUID, `Payed_Bills`.Member_GUID, `Payed_Bills`.Visible
    		        from `Invoice`
    		       INNER JOIN `Payed_Bills`ON `Payed_Bills`.`Invoice_GUID`= `Invoice`.`GUID` WHERE `Invoice`.`Visible`= 1 and `Payed_Bills`.Visible =1 and `Payed_Bills`.Member_GUID='" . $guid . "' and `Invoice`.Title = $TariffID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}
}

//#################
//################# PWA
//#################

class Mechanic_Payment
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

		$Entity = 6;
		$GUID = $_SESSION["Mechanic_GUID"];
		$date = date('Y-m-d H:i:s');

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

	public function Register_Payment(array $invoice)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$date = date('Y-m-d H:i:s');
			$Visible = 1;
			$Action = 21; //Register Peyment
			$Payment_Method = 1; // IPG 
			$comment = "Register_Payment";

			//Database
			$qur = "INSERT INTO `Payed_Bills` ( `Visible`, `Member_GUID`, `Invoice_GUID`, `Amount`, `OrderID`, `SysTraceNum`, `RetrivalRefNum`, `Comment`, `Payment_Method`, `Payment_Date`) 
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $Visible);
			$stmt->bindParam(2, $invoice[0]); //Member_GUID
			$stmt->bindParam(3, $invoice[1]); // Invoice_GUID
			$stmt->bindParam(4, $invoice[2]); //Amount
			$stmt->bindParam(5, $invoice[3]); //OrderID
			$stmt->bindParam(6, $invoice[4]); //SysTraceNum
			$stmt->bindParam(7, $invoice[5]); //RetrivalRefNum
			$stmt->bindParam(8, $invoice[6]); //Comment
			$stmt->bindParam(9, $Payment_Method); //Method
			$stmt->bindParam(10, $date); //Payment_Date
			$stmt->execute();
			// Remove Bill
			$this->Remove_Bill_ByGUID($invoice);

			//Log generator

			$this->C_MechanicLog($Action, $comment);
			return 1;
		}
	}

	//################################################
	//######################################################  Payment Sessions 
	//################################################

	public function C_IPGResponce(array $sessionArr)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//pre info
			$qur = "INSERT INTO `IPG_Res` (`MechanicGUID`, `InvoiceGUID`, `Token`, `ResCode`, `OrderId`) VALUES ( ?, ?, ?, ?, ?)";
			$stmt = $pdo->prepare($qur);
			$stmt->bindParam(1, $sessionArr[0]);
			$stmt->bindParam(2, $sessionArr[1]);
			$stmt->bindParam(3, $sessionArr[2]);
			$stmt->bindParam(4, $sessionArr[3]);
			$stmt->bindParam(5, $sessionArr[4]);
			$stmt->execute();
		}
	}

	//################################################
	//######################################################  get
	//################################################



	public function Get_Invoice_ByGUID($GUID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Invoice` WHERE `Visible` = 1 and `GUID` = '$GUID'";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	public function Get_InvoiceTtitle_ByID($ID)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `N_Tariff_Version` WHERE `Visible` = 1 and `ID` = $ID";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}


	public function Get_LastINV()
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			$qur = "SELECT * FROM `Invoice` WHERE `Visible` = 1 ORDER BY `Invoice`.`ID` DESC LIMIT 1";
			$stmt = $pdo->prepare($qur);
			$stmt->execute();
			$data = $stmt->fetchAll();
			return $data;
		}
	}

	//################################################
	//######################################################  DELETE 
	//################################################
	public function Remove_Bill_ByGUID(array $Inv)
	{

		if ($this->_UPermission > 100) {
			$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
			require("$rootDir/config/config_DB.php");
			//Database
			$data = [
				'M_GUID' => $Inv[0],
				'Inv_GUID' => $Inv[1],
			];
			$qur = "DELETE FROM `All_Member_Invoice` where `Member_GUID` = :M_GUID AND `Inv_GUID` = :Inv_GUID ";
			$stmt = $pdo->prepare($qur);
			$stmt->execute($data);
		}
	}
}

//#################
//################# payment_retrive
//#################
class payment_retrive
{
	public function GetSessioninfo($OrderId)
	{
		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		//pre info
		$qur = "SELECT * FROM `Payment_Session` WHERE `Order_GUID` = '$OrderId'";
		$stmt = $pdo->prepare($qur);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			$data = $stmt->fetchAll();
			$result = array();
			$result[0] = $data[0]['Admin_GUID'];
			$result[1] = $data[0]['Mem_GUID'];
			$result[2] = $data[0]['Order_GUID'];
			$result[3] = $data[0]['Invoice_GUID'];
			return $result;
		} else {
			return 1;
		}
	}
}
