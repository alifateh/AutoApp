<?php 
session_start(); 
if(!isset( $_SESSION["Admin_GUID"]) || !isset( $_SESSION["Admin_ID"])){
session_start();
session_unset();
session_write_close();
$url = "./sysAdmin.php";
header("Location: $url");
	
}
	require('config/public_conf.php');
	require('Model/PayModel.php');
	use fateh\Finance\Invoice as invoice;
	
	//Create sign data(Tripledes(ECB,PKCS7))
function encrypt_pkcs7($str, $key)
{
    $key = base64_decode($key);
    $ciphertext = OpenSSL_encrypt($str,"DES-EDE3", $key, OPENSSL_RAW_DATA);
    return base64_encode($ciphertext);
}
//Send Data
function CallAPI($url, $data = false)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");  
    curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}


	if (!isset($_POST["enable"])){
		$url = "./V_MemberAll.php";
		header("Location: $url");		
	}else{
		
	 $mem_GUID = $_POST["guid"];  // member ID
	 $GUID = $_SESSION["Admin_ID"]; // admin ID
	 
	 
	 $payment_type = $_POST["payment_type"];
	 $Amount = $_POST["amount"];
	 $comment = $_POST["comment"]."[ شارژ حساب کاربری از طریق پرتال مدیریت ]";
	 $ElemanType = $_POST["ElemanType"];
	 $ExDate = $_POST["ExDate"];
	 $InvoiceID = $_POST["InvoiceID"];
	 $OrderId = $_POST["OrderId"];
	 
//#############################Set Session
$Session = array();
$Session[0] = $GUID; // admin ID
$Session[1] = $OrderId; //Session GUID
$Session[2] = $mem_GUID; //user ID
$Session[3] = $InvoiceID; //InvoiceID

$Set_Session = new inv();
$Set_Session -> SetSeesion ($Session);

//#############################
/*
//test
		$key="kLheA+FS7MLoLlLVESE3v3/FP07uLaRw"; //5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm
		$MerchantId="000000140212149"; //000000140332312
		$TerminalId="24000615"; //24053850

*/

//fava
		$key="5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm"; //5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm
		$MerchantId="000000140332312"; //000000140332312
		$TerminalId="24053850"; //24053850
		
		
		$LocalDateTime=date("m/d/Y g:i:s a");
		$ReturnUrl="https://adminpanel.autoapp.ir/IPG-landing.php";
		$SignData=encrypt_pkcs7("$TerminalId;$OrderId;$Amount","$key");
		$data = array('TerminalId'=>$TerminalId,
					  'MerchantId'=>$MerchantId,
					  'Amount'=>$Amount,
					  'SignData'=> $SignData,
				  'ReturnUrl'=>$ReturnUrl,
				  'LocalDateTime'=>$LocalDateTime,
				  'OrderId'=>$OrderId);
		$str_data = json_encode($data);
		$res=CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest',$str_data);
		$arrres=json_decode($res);
		if($arrres->ResCode==0)
		{
			$Token= $arrres->Token;
			$url="https://sadad.shaparak.ir/VPG/Purchase?Token=$Token";
			header("Location:$url");
		}
		else
			die($arrres->Description);
	}
?>