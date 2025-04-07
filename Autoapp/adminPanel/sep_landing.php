<?php
session_start();
require('config/public_conf.php');
require('Model/MemberModel.php');
require('Model/InvoiceModel.php');
require('Model/Admin-Users.php');
//require('Model/TellModel.php');
//require('Model/SmsChanel.php');

//use fateh\phonebook\phonebook as Tell;
//use fateh\smschanel\SMS as sms;
use fateh\Member\Member as member;
use fateh\Finance\Invoice as invoice;
use fateh\Finance\payment_retrive as payment_retrive;
use fateh\login\Admin as admin;


//$en_Tell = new Tell($_SESSION["Admin_GUID"]);
//$en_SMS = new sms();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Error_STR = 0;
    $State = $_POST['State'];
    $Status = $_POST['Status'];
    $RefNum = $_POST['RefNum'];
    $ResNum = $_POST['ResNum'];
    $TraceNo = $_POST['TraceNo'];

    ////////////////////////////////////////////////////////////////Retrive Session
    $SessionPay_Obj = new payment_retrive();
    $info_session = $SessionPay_Obj->GetSessioninfo($ResNum);
    $Admin_Obj = new admin($info_session[0]);
    $info_admin = $Admin_Obj -> Get_Admin ($info_session[0]);

    //set session
    $_SESSION["Admin_GUID"] = $info_session[0];  // admin GUID
    $_SESSION["Admin_ID"] = $info_admin[0]['ID'];  // admin GUID
    $_SESSION["GUID"] = $info_session[1];  //member 
    //set Invoice
    $Order_GUID = $info_session[2]; // Order_GUID 
    $Invoice_GUID = $info_session[3]; //Invoice_GUID


    if ($ResNum == $info_session[2]) {

        //set IPG Response

        $IPGRes = array();
        $IPGRes[0] = $info_session[1]; //member ID
        $IPGRes[1] = $info_session[3]; //Invoice_GUID
        $IPGRes[2] = $ResNum;

        $Session_Obj = new invoice($info_session[0]);
        //fetch invoice data
        $Inv_Data = $Session_Obj->Get_Invoice_ByGUID($Invoice_GUID);

        echo $Inv_Amount = $Inv_Data[0]['Amount'];
        $Inv_Title = $Inv_Data[0]['Title'];

        if ($State == "CanceledByUser" && $Status == 1) {
            $Message_color = "panel-info";
            $Message = ' تراکنش توسط کاربر لغو شد : ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;

        } elseif ($State == "Failed" && $Status == 3) {
            $Message_color = "panel-info";
            $Message = ' پرداخت به دلایل بانکداری پرداخت نشد و درصورت کسر مبلغ از حساب تا 72 ساعت کاری آتی مبلغ به حساب عودت داده می شود : ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "SessionIsNull" && $Status == 4) {
            $Message_color = "panel-info";
            $Message = 'کاربر در بازه زمانی تعیین شده پاسخی ارسال نکرده است ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "InvalidParameters" && $Status == 5) {
            $Message_color = "panel-info";
            $Message = 'پارامترهای ارسالی نامعتبر است' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "MerchantIpAddressIsInvalid" && $Status == 8) {
            $Message_color = "panel-info";
            $Message = 'آدرس سرور پذیرنده نامعتبر است [ در پرداخت های بر پایه توکن ]' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "TokenNotFound" && $Status == 10) {
            $Message_color = "panel-info";
            $Message = 'توکن ارسال شده یافت نشد ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "TokenRequired" && $Status == 11) {
            $Message_color = "panel-info";
            $Message = ' با این شماره ترمینال فقط تراکنش های توکنی قابل پرداخت هستند  ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "TerminalNotFound" && $Status == 12) {
            $Message_color = "panel-info";
            $Message = ' شماره ترمینال ارسال شده یافت نشد ' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "MultisettlePolicyErrors" && $Status == 21) {
            $Message_color = "panel-info";
            $Message = 'محدودیت های مدل چند حسابی رعایت نشده' .  $State . 'شماره خطا : ' . $Status;
            $IPGRes[3] = $State;
            $IPGRes[4] = $Status;
        } elseif ($State == "OK" && $Status == 2) {
            $Message_color = "panel-success";
            $Message = 'تراکنش با موفقیت انجام شد. شماره رهگیری شما : ' . $TraceNo;
            $IPGRes[3] = $RefNum;
            $IPGRes[4] = $TraceNo;
            $data = array(
                "RefNum"            => $RefNum,
                "TerminalNumber"    => 13611265
            );
            $url = "https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/VerifyTransaction";
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
            if (!empty($json_response)) {
                $response = json_decode($json_response, true);
                if ($response['Success'] == true && $response['ResultCode'] == 0) {
                    $member = new member($info_session[0]);

                    $invoice = array();
                    $invoice[0] = $info_session[1]; //Member_GUID
                    $invoice[1] = $Invoice_GUID; //Invoice_GUID
                    $invoice[2] = $Inv_Amount; //amount
                    $invoice[3] = $Order_GUID; //OrderID 
                    $invoice[4] = $TraceNo; //Trace
                    $invoice[5] = $RefNum; //refNum
                    $invoice[6] = "پرداخت $Inv_Title "; //Comment
        
                    
                    $result = $Session_Obj->Register_Payment($invoice);
                    $member->U_MechanicStatus_ByID($info_session[1], 1);
                } else {
                    $Error_STR = 2;
                }
            }
        }
        $Session_Obj->C_IPGResponce($IPGRes);

        if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["GUID"])) {
            session_start();
            session_unset();
            session_write_close();
            $url = "./sysAdmin.php";
            header("Location: $url");
        } else {

            header("refresh:5;url=V_MechanicProfile.php");
        }
    }
} else {
    $Message_color = "panel-info";
    $Message = "مشکل از call-back  بانک";
    $IPGRes[3] = "مشکل از call-back  بانک";
    $IPGRes[4] = 2;
}

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

    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>



    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <?php

    if (!empty($Error_STR)) {

        if ($Error_STR == 2) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
        $.toast({
            heading: 'خطا در Verify Transaction ',
            text: 'پرداخت از سمت درگاه تایید نشده و مبلغ تا 72 ساعت آتی به حساب بازگردانده می شود.' ,
            position: 'top-center',
            loaderBg:'#ed3236',
            hideAfter: 6500,
            stack: 6
        });
        return false;
        });";
            echo '</script>';
        }
    }

    ?>

</body>

</html>