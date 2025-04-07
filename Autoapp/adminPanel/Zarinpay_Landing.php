<?php
session_start();
require('config/public_conf.php');
//require('Model/SmsChanel.php');
require('Model/MemberModel.php');
require('Model/InvoiceModel.php');
//require('Model/TellModel.php');

//use fateh\phonebook\phonebook as Tell;
//use fateh\smschanel\SMS as sms;
use fateh\Member\Member as member;
use fateh\Finance\Invoice as invoice;

$Session_Obj = new invoice($_SESSION["Admin_GUID"]);
//$en_Tell = new Tell($_SESSION["Admin_GUID"]);
//$en_SMS = new sms();
$member = new member($_SESSION["Admin_GUID"]);

$Authority = $_GET['Authority'];
$OrderId = $_GET['Authority'];
$Trace_Num = mt_rand(1000000000, 9999999999);

////////////////////////////////////////////////////////////////Retrive Session
$info_session = $Session_Obj->GetSessioninfo($OrderId);

//set session
$_SESSION["Admin_ID"] = $info_session[0];  // admin ID
$_SESSION["GUID"] = $info_session[1];  //member ID
//set Invoice
$Order_GUID = $info_session[2]; // Order_GUID 
$Invoice_GUID = $info_session[3]; //Invoice_GUID
//fetch invoice data
$Inv_Data = $Session_Obj->Get_Invoice_ByGUID($Invoice_GUID);

$Inv_Amount = $Inv_Data[0]['Amount'];
$Inv_Title = $Inv_Data[0]['Title'];

$data = array("merchant_id" => "8747646d-3825-4eb9-87ca-0f95fd5963c7", "authority" => $Authority, "amount" => $Inv_Amount);

$jsonData = json_encode($data);
$ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));



$IPGRes = array();
$IPGRes[0] = $info_session[1];
$IPGRes[1] = $info_session[3];
$IPGRes[2] = $Authority;

$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result, true);

if ($err) {
    echo "cURL Error #:" . $err;
    $IPGRes[3] = $err;
} else {
        if ($result['data']['code'] == 100) {
            $Message_color = "panel-success";
            $Message = 'تراکنش با موفقیت انجام شد. شماره رهگیری شما : ' . $result['data']['ref_id'];
            $IPGRes[3] = $result['data']['ref_id'];
            $IPGRes[4] = $Authority;


            $invoice = array();

            $invoice[0] = $info_session[1]; //Member_GUID
            $invoice[1] = $Invoice_GUID; //Invoice_GUID
            $invoice[2] = $Inv_Amount; //amount
            $invoice[3] = $Order_GUID; //OrderID 
            $invoice[4] = $Trace_Num; //Trace
            $invoice[5] = $result['data']['ref_id']; //refNum
            $invoice[6] = "پرداخت $Inv_Title "; //Comment

            $Session_Obj->Register_Payment($invoice);
            $route = $member->U_MechanicStatus_ByID($info_session[1], 1); 
    } else {

        if($result['errors']['code'] == -51 ){
            $Message_color = "panel-info";
            $Message = "پرداخت از سمت کاربر لغو گردید";
            $IPGRes[3] = $result['errors']['message'];
            $IPGRes[4] = $result['errors']['code'];
        }else{
            $Message_color = "panel-info";
            $Message = 'پیام خطا از سمت زرین پال : ' .  $result['errors']['message'] . 'شماره خطا : ' . $result['errors']['code'];
            $IPGRes[3] = $result['errors']['message'];
            $IPGRes[4] = $result['errors']['code'];
        }

    }
}


$Session_Obj->C_IPGResponce($IPGRes);

if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
header("refresh:5;url=V_MechanicProfile.php");


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>
    <!-- Morris Charts CSS -->
    <link href="vendors/bower_components/morris.js/morris.css" rel="stylesheet" type="text/css" />

    <!-- Data table CSS -->
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Preloader -->
    <div class="preloader-it">
        <div class="la-anim-1"></div>
    </div>
    <!-- /Preloader -->
    <div class="wrapper theme-1-active pimary-color-red">
        <!-- mini Menu Items -->
        <?php minimenu(); ?>
        <!-- /mini Menu Items -->

        <!-- Right Sidebar Menu -->
        <?php Mainmenu(); ?>
        <!-- /Right Sidebar Menu -->
        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="container-fluid pt-25">
                <!-- Row -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default card-view">
                            <div class="panel-heading">
                                <div class="pull-right">
                                    <a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <i class="zmdi zmdi-chevron-up"></i>
                                    </a>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> وضعیت تراکنش </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel <?php echo $Message_color; ?> card-view">
                                            <div class="panel-heading">
                                                <div class="pull-left">
                                                    <h6 class="panel-title txt-light"> پیام سیستم </h6>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="panel-wrapper collapse in">
                                                <div class="panel-body">
                                                    <p><?php echo $Message; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->
            </div>
            <div class="container-fluid">
                <!-- Footer -->
                <!-- Footer -->
                <?php footer(); ?>
                <!-- /Footer -->
                <!-- /Footer -->
            </div>

        </div>
        <!-- /Main Content -->

    </div>

    <!-- /#wrapper -->

    <!-- JavaScript -->
    <?php MainJavasc(); ?>

</body>

</html>