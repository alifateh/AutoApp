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


////////////////////////////////////////
// set variables
////////////////////////////////////////

//start fetching Invoice Data        
$Invoice_Obj = new invoice($_SESSION["Mechanic_GUID"]);
$ContarctObj = new contarct($_SESSION["Mechanic_GUID"]);

$member_GUID = $_SESSION["Mechanic_GUID"];


if (isset($_SESSION["Inv_GUID"]) && !empty($_SESSION["Inv_GUID"])) {
    $Inv_GUID = $_SESSION["Inv_GUID"];
} else {
    $Inv_GUID = $_POST["Inv_GUID"];
}


if (isset($_SESSION["ContractID"]) && !empty($_SESSION["ContractID"])) {
    $ContractID = $_SESSION["ContractID"];
} else {
    $ContractID = $_POST["ContractID"];
}

$Retrive_ID = md5(uniqid(mt_rand(100000, 999999), true));

if (!empty($ContractID)) {
    //////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////Set Contract//////////////////////////////////////

    $route = $ContarctObj->U_Contract_ByID($ContractID);
} else {

    $route = "Done";
}

/////////////
//call to get token
/////////////



if ($route == "Done") {

    $Inv_value = $Invoice_Obj->Get_Invoice_ByGUID($Inv_GUID);
    $Amount = $Inv_value[0]['Amount'];
    $description =  $Inv_value[0]['Title'];

    $data = array(
        "action"        => "token",
        "TerminalId"    => "13611265",
        "Amount"        => $Amount,  //$Amount
        "ResNum"        => $Retrive_ID,
        "RedirectUrl"    => "https://pwa.autoapp.ir/Sep_Landing.php",
        "CellNumber"    => "9120000000",
        "SettlementIBANInfo" => array(
            //	array (
            //	"IBAN"				=> "IR170180000000000257045226", //taavoni
            //	"Amount"			=> "5",
            //	"PurchaseID"		=> "000000000000000000000000000000"), 
            array(
                "IBAN"              => "IR520180000000000028223730", //etehadiye
                "Amount"            => "$Amount",
                "PurchaseID"        => "000000000000000000000000000000"
            )
        )
    );

    $url = "https://sep.shaparak.ir/onlinepg/onlinepg";
    $content = json_encode($data);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array("Content-type: application/json")
    );
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($status != (200 || 201)) {
        $Error_STR = 2;
    }
    curl_close($curl);

    ////////////////////////////
    ///// check token 
    ////////////////////////////
    if (!empty($json_response)) {
        $response = json_decode($json_response, true);
        if ($response['status'] == 1) {

            $IPG_status = $response['status'];
            $IPG_Token = $response['token'];
            //////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////Set Session//////////////////////////////////////

            $Session = array();
            $Session[0] = ""; // admin ID
            $Session[1] = $member_GUID; // Member ID
            $Session[2] = $Retrive_ID; //Order GUID
            $Session[3] = $Inv_GUID; //Invoice ID
            $Invoice_Obj->SetSeesion($Session);
        }
    } else {
        echo "خطا در برقراری ارتباط با درگاه بانکی";
    }
} else {
    echo "Contract Issue";
}


if (!empty($route) && $route == 'Done') {
    echo "
<form name='sepform' action='https://sep.shaparak.ir/OnlinePG/OnlinePG' method='POST'>
    <input name='token' type='hidden' value='" . $response['token'] . "'>
    <input name='GetMethod' type='hidden' value=''>
    <script language='JavaScript'>document.sepform.submit();</script>
</form>";
}
