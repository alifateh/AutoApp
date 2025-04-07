<?php
session_start();
if (!isset($_SESSION["Mechanic_GUID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "/";
	header("Location: $url");
}
//////////////////////////
/// import 
//////////////////////////
require('config/public_conf.php');
require_once('Model/PayModel.php');

use fateh\Payment\invoice as inv;


//////////////////////////
/// Get Data from session
//////////////////////////

$mem_GUID = $_SESSION["Mechanic_GUID"];
$Mechanic_GUID = $_POST["guid"];

if ($mem_GUID !== $Mechanic_GUID) {
	session_start();
	session_unset();
	session_write_close();
	$url = "/";
	header("Location: $url");
}


////////////////////////////
// create Invoice
///////////////////////////


if (!empty($Mechanic_GUID)) {

	$Billing = new inv($_SESSION["Mechanic_GUID"]);

	$InvoiceID = md5(uniqid(mt_rand(100000, 999999), true));
	session_start();
	$_SESSION["SessionGUID"] = $InvoiceID;
	$OrderId = abs(crc32(uniqid()));

	$invoice = array();
	$invoice[0] = $Mechanic_GUID;
	$invoice[1] = 1; //PaymentTopic
	$invoice[2] = 500000; //amount
	$invoice[3] = $OrderId; //OrderID
	$invoice[4] = ""; //Trace
	$invoice[5] = ""; //refNum
	$invoice[6] = "شارژ حساب کاربری از طریق پنل اعضا"; //comment
	$invoice[7] = 32; //ElemanType
	$invoice[8] = date('Y-m-d', strtotime('+1 year')); //Expire Date
	$invoice[9] = $InvoiceID; //invoice ID

	$Billing->Mechanic_Invoice($invoice);

	//#############################Set Session
	$sessionArr = array();
	$sessionArr[0] = $OrderId; //session
	$sessionArr[1] = $InvoiceID; //Session GUID
	$sessionArr[2] = $Mechanic_GUID; //user ID
	$sessionArr[3] = $InvoiceID; //InvoiceID

	$Billing->SetSeesion($sessionArr);
} else {
	session_start();
	session_unset();
	session_write_close();
	$url = "/";
	header("Location: $url");
}


////////////////////////////////////////
//Create sign data(Tripledes(ECB,PKCS7))
////////////////////////////////////////

function encrypt_pkcs7($str, $key)
{
	$key = base64_decode($key);
	$ciphertext = OpenSSL_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
	return base64_encode($ciphertext);
}

/////////////
//Send Data
/////////////
function CallAPI($url, $data = false)
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
	$result = curl_exec($curl);
	curl_close($curl);
	return $result;
}



//////////////////////////
//start to call IPG API
/////////////////////////


/*
//#############################
//test
		$key="8v8AEee8YfZX+wwc1TzfShRgH3O9WOho"; 
		$MerchantId="000000140336964"; 
		$TerminalId="24095674"; 
*/



//fava

$key = "5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm";
$MerchantId = "000000140332312";
$TerminalId = "24053850";
$Amount = $invoice[2];

$LocalDateTime = date("m/d/Y g:i:s a");
$ReturnUrl = "https://pwa.autoapp.ir/Mechanic-IPG-landing.php";
$SignData = encrypt_pkcs7("$TerminalId;$OrderId;$Amount", "$key");
$data = array(
	'TerminalId' => $TerminalId,
	'MerchantId' => $MerchantId,
	'Amount' => $Amount,
	'SignData' => $SignData,
	'ReturnUrl' => $ReturnUrl,
	'LocalDateTime' => $LocalDateTime,
	'OrderId' => $OrderId
);
$str_data = json_encode($data);
$res = CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest', $str_data);
$arrres = json_decode($res);
if ($arrres->ResCode == 0) {
	$Token = $arrres->Token;
	$url = "https://sadad.shaparak.ir/VPG/Purchase?Token=$Token";
	header("Location:$url");
} else {
	die($arrres->Description);
}
