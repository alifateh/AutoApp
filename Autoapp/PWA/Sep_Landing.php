<?php
session_start();
require('config/public_conf.php');
require('Model/MemberModel.php');
require('Model/InvoiceModel.php');
require('Model/Admin-Users.php');

use fateh\Finance\Mechanic_Payment as invoice;
use fateh\Finance\payment_retrive as payment_retrive;
use fateh\Member\Mechanic_Member as Mechanic;


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
    
    //set session
    $_SESSION["Mechanic_GUID"] = $info_session[1];  //member ID

    //set Invoice
    $Order_GUID = $info_session[2]; // Order_GUID 
    $Invoice_GUID = $info_session[3]; //Invoice_GUID


    if ($ResNum == $info_session[2]) {

        //set IPG Response

        $IPGRes = array();
        $IPGRes[0] = $info_session[1]; //member ID
        $IPGRes[1] = $info_session[3]; //Invoice_GUID
        $IPGRes[2] = $ResNum;

        $Session_Obj = new invoice($info_session[1]);
        //fetch invoice data
        $Inv_Data = $Session_Obj->Get_Invoice_ByGUID($Invoice_GUID);

        $Inv_Amount = $Inv_Data[0]['Amount'];
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
                $Message_color = "panel-info";
                $Message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
            }
            curl_close($curl);
            if (!empty($json_response)) {
                $response = json_decode($json_response, true);
                if ($response['Success'] == true && $response['ResultCode'] == 0) {
                    $Mechanic = new Mechanic($info_session[1]);

                    $invoice = array();
                    $invoice[0] = $info_session[1]; //Member_GUID
                    $invoice[1] = $Invoice_GUID; //Invoice_GUID
                    $invoice[2] = $Inv_Amount; //amount
                    $invoice[3] = $Order_GUID; //OrderID 
                    $invoice[4] = $TraceNo; //Trace
                    $invoice[5] = $RefNum; //refNum
                    $invoice[6] = "پرداخت $Inv_Title "; //Comment
                      
                    $result = $Session_Obj->Register_Payment($invoice);
                    $Mechanic->U_MechanicStatus_ByID($info_session[1], 1);
                } else {
                    $Message_color = "panel-info";
                    $Message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
                }
            }
        }
        $Session_Obj->C_IPGResponce($IPGRes);

        if (!isset($_SESSION["Mechanic_GUID"])) {
            session_start();
            session_unset();
            session_write_close();
            $url = "/";
            header("Location: $url");
        } else {

           header( "refresh:7;url=V_PayedBill.php" );
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
<html lang="en" view-mode="rtl">

<head>
    <?php Metatag(); ?>
    <!-- CSS Libraries-->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/ion.rangeSlider.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/apexcharts.css">
    <!-- Core Stylesheet-->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Preloader-->
    <div class="preloader d-flex align-items-center justify-content-center" id="preloader">
        <div class="spinner-grow text-primary" role="status">
            <div class="sr-only"> لطفا منتظر بمانید ... </div>
        </div>
    </div>
    <!-- Internet Connection Status-->
    <div class="internet-connection-status" id="internetStatus"></div>
    <!-- Header Area-->
    <div class="header-area" id="headerArea">
        <div class="container">
            <!-- Paste your Header Content from here-->
            <!-- # Header Five Layout-->
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                <!-- Page Title-->
                <div class="page-heading">
                    <h6 class="mb-0"> وضعیت تراکنش </h6>

                </div>
                <!-- Navbar Toggler-->
            </div>
        </div>
    </div>

    <div class="page-content-wrapper py-3">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <img src="img\charge.png" alt="">
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-body" dir="rtl">
                    <!-- Card 04 -->
                    <div class="card card-gradient-bg">
                        <div class="card-body p-5">
                            <h4 class="display-2 mb-4 font-weight-bold"> وضعیت تراکنش </h4>
                            <?php

                            if ($Message_color == "btn-warning" || $Message_color == "btn-danger") {
                                echo '<a class="btn btn-lg ' . $Message_color . ' btn-round" href="auth.php"><span dir ="rtl">';
                            } else {
                                echo '<a class="btn btn-lg ' . $Message_color . ' btn-round" href="Dashboard.php"><span dir ="rtl">';
                            }

                            ?>
                            <?php
                            echo $Message;
                            echo "</br >";

                            ?>
                            </span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="container px-0">
            <!-- Paste your Footer Content from here-->
            <!-- Footer Content-->
            <div class="footer-nav position-relative">
            </div>
        </div>
    </div>
    <!-- All JavaScript Files-->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/default/internet-status.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/default/dark-mode-switch.js"></script>
    <script src="js/ion.rangeSlider.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/default/active.js"></script>
    <script src="js/default/clipboard.js"></script>
</body>

</html>