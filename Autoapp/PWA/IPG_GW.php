<?php
session_start();
if (!isset($_SESSION["Mechanic_GUID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "/";
    header("Location: $url");
}

require('config/public_conf.php');
require('Model/InvoiceModel.php');
require('Model/ContractModel.php');

use fateh\Finance\Invoice as invoice;
use fateh\Contarct\contarct as contarct;

$member_GUID = $_SESSION["Mechanic_GUID"];


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
/// Get Data from session
//////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Mechanic_GUID = $_POST["mem-ID"];
    $Inv_GUID = $_POST["Inv_GUID"];
    $ContractID = $_POST["ContractID"];
} else {
    $Inv_GUID = $_SESSION["Inv_GUID"];
    $ContractID = $_SESSION["ContractID"];
    $Mechanic_GUID = $member_GUID;
}
if(!empty($ContractID)){
    $ContarctObj = new contarct($_SESSION["Mechanic_GUID"]);
    $route = $ContarctObj->U_Contract_ByID($ContractID);
    if ($route == "Done") {

        if ($member_GUID !== $Mechanic_GUID) {
            session_start();
            session_unset();
            session_write_close();
            $url = "/";
            header("Location: $url");
        } else {
            $Inv_fetch = new invoice($_SESSION["Mechanic_GUID"]);
            ////////////////////////////
            // create Bill
            ///////////////////////////
            //start fetching Invoice Data        
            $Inv_value = $Inv_fetch->Get_Invoice_ByGUID($Inv_GUID);
            foreach ($Inv_value as $value) {
                $Amount = $value['Amount'];
                $Title = $value['Title'];
            }
            $comment = "[پرداخت صورت حساب $Title از طریق پنل ادمین]";
            $ElemanType = 56;
            $Order_GUID = mt_rand(1000000000, 9999999999);
            //#############################Set Session
            $Session = array();
            $Session[0] = ""; // admin ID
            $Session[1] = $Mechanic_GUID; // Member ID
            $Session[2] = $Order_GUID; //Order GUID
            $Session[3] = $Inv_GUID; //Invoice ID
        
            $Set_Session = new invoice($_SESSION["Mechanic_GUID"]);
            $Set_Session->SetSeesion($Session);
            //////////////////////////
            //start to call IPG API
            /////////////////////////
        
            //#############################
        
            //test
            //	$key="8v8AEee8YfZX+wwc1TzfShRgH3O9WOho"; //5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm
            //	$MerchantId="000000140336964"; //000000140332312
            //	$TerminalId="24095674"; //24053850
        
        
            //#############################fava Token
            $key = "5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm"; //5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm
            $MerchantId = "000000140332312"; //000000140332312
            $TerminalId = "24053850"; //24053850
            //#############################        
            $LocalDateTime = date("m/d/Y g:i:s a");
            $ReturnUrl = "https://pwa.autoapp.ir/Landing-IPG.php";
            $SignData = encrypt_pkcs7("$TerminalId;$Order_GUID;$Amount", "$key");
            $MultiplexingData = array(
                "Type" => "Percentage",
                "MultiplexingRows" => array(
                    array(
                        'IbanNumber' => 1,
                        'Value' => 0
                    ), array(
                        'IbanNumber' => 2,
                        'Value' => 100
                    )
                )
            );
            $data = array(
                'TerminalId' => $TerminalId,
                'MerchantId' => $MerchantId,
                'Amount' => $Amount,
                'MultiplexingData' => $MultiplexingData,
                'SignData' => $SignData,
                'ReturnUrl' => $ReturnUrl,
                'LocalDateTime' => $LocalDateTime,
                'OrderId' => $Order_GUID
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
        }

    } else {
        session_start();
        session_unset();
        session_write_close();
        $url = "/";
        header("Location: $url");
    }
}else{
    session_start();
    session_unset();
    session_write_close();
    $url = "/";
    header("Location: $url");
}

