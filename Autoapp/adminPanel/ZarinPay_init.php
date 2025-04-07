<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
require_once('config/public_conf.php');
//require_once('Model/SmsChanel.php');
//require_once('Model/TellModel.php');
require_once('Model/MemberModel.php');
require_once('Model/InvoiceModel.php');
require_once('Model/ContractModel.php');

use fateh\Finance\Invoice as invoice;
use fateh\Member\Member as member;
//use fateh\phonebook\phonebook as Tell;
//use fateh\smschanel\SMS as sms;
use fateh\Contarct\contarct as contarct;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Error_STR = 0;

    if (!isset($_POST['action'])) {
        $url = "./index.php";
        header("Location: $url");
    } elseif ($_POST['action'] == 'pay' && $_POST['Billing-Methods'] == 1) {

        //start fetching Invoice Data        
        $Invoice_Obj = new invoice($_SESSION["Admin_GUID"]);
        $ContarctObj = new contarct($_SESSION["Admin_GUID"]);
        $mem_GUID = $_POST["Member_GUID"];
        $Inv_GUID = $_POST["Invoice_GUID"];
        $Admin_GUID = $_SESSION["Admin_ID"]; // admin ID



        ///////////////////////////////////////DO IPG
        $Inv_value = $Invoice_Obj->Get_Invoice_ByGUID($Inv_GUID);
        $Amount = $Inv_value[0]['Amount'];
        $description =  $Inv_value[0]['Title'];
        $data = array(
            "merchant_id" => "8747646d-3825-4eb9-87ca-0f95fd5963c7",
            "amount" => $Amount,
            "callback_url" => "https://adminpanel.autoapp.ir/Invoice_Landing.php",
            "description" => $description,
            "metadata" => ["email" => "info@email.com", "mobile" => "09121234567"],
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);

        /////////////////////////Handel
        //////////////////////////////////////////////////////
        $Order_GUID = $result['data']["authority"];
        //////////////////////////////////////////////////////

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            if (empty($result['errors'])) {
                if ($result['data']['code'] == 100) {


                    //////////////////////////////////////////////////////////////////////////////////
                    ////////////////////////////////Set Session//////////////////////////////////////

                    $Session = array();
                    $Session[0] = $Admin_GUID; // admin ID
                    $Session[1] = $mem_GUID; // Member ID
                    $Session[2] = $Order_GUID; //Order GUID
                    $Session[3] = $Inv_GUID; //Invoice ID
                    $Invoice_Obj->SetSeesion($Session);
                    //////////////////////////////////////////////////////////////////////////////////
                    ////////////////////////////////Set Contract//////////////////////////////////////

                    
                    $ContractID = $ContarctObj->Get_Contract_ByMechanic($Inv_value[0]['Title'], $mem_GUID);
                    $route = $ContarctObj->U_ContractAdmin_ByID($ContractID[0]['ID']);

                    header('Location: https://www.zarinpal.com/pg/StartPay/' . $result['data']["authority"]);
                }
            } else {
                $Error_STR = 1;
                $ErrorCode = 'Error Code: ' . $result['errors']['code'];
                $message = 'message: ' .  $result['errors']['message'];
            }
        }
    } elseif ($_POST['action'] == 'pay' && $_POST['Billing-Methods'] != 1) {


        /////////////////////////////////////////////////////////DO NOIPG
        $mem_GUID = $_POST["Member_GUID"];
        $Inv_GUID = $_POST["Invoice_GUID"];
        $ContarctObj = new contarct($_SESSION["Admin_GUID"]);

        //set session

        $_SESSION["GUID"] = $mem_GUID;
        $_SESSION["Inv_GUID"] = $Inv_GUID;

        //start 
        $View_Inv = new invoice($_SESSION["Admin_GUID"]);
        $Inv_value = $View_Inv->Get_Invoice_ByGUID($Inv_GUID);

        $ContractID = $ContarctObj->Get_Contract_ByMechanic($Inv_value[0]['Title'], $mem_GUID);
        $route = $ContarctObj->U_ContractAdmin_ByID($ContractID[0]['ID']);

        foreach ($Inv_value as $value) {
            $price = $value['Amount'];
            $Title = $value['Title'];
        }
    } elseif ($_POST['action'] == 'RegisterPayment') {
        // $en_SMS = new sms();
        // $en_Tell = new Tell($_SESSION["GUID"]);
        $member = new member($_SESSION["GUID"]);
        $Reg_Inv = new invoice($_SESSION["Admin_GUID"]);
        $Ord_Random = uniqid(mt_rand(100000, 999999), true);

        $invoice = array();
        $invoice[0] = $_POST["mem_GUID"]; // Member-GUID
        $invoice[1] = $_POST["Inv_GUID"]; //invoice ID
        $invoice[2] = $_POST["amount"]; //amount
        $invoice[3] = "NoIPG-_-" . $Ord_Random; //OrderID
        $invoice[4] = $_POST["trace"]; //trace
        $invoice[5] = $_POST["refnum"]; //ref
        $invoice[6] = $_POST["Comment"]; //comment
        $invoice[7] = 33; //ElemanType

        $result = $Reg_Inv->Register_Payment_NoIPG($invoice);


        if ($result == 1) {
            $route = $member->U_MechanicStatus_ByID($_POST["mem_GUID"], 1);

            //ارسال پیامک به مکانیک بعد از پرداخت در برنامه
            //$cell_arr = array();
            //$cell_arr[0] = $en_Tell->GetMemberMobile($_POST["guid"]);
            // $Name = $member->GetMemberName($_POST["guid"]);

            //$str2 ="عضو محترم اتحادیه مکانیک های تهران پرداخت شما در اتواپ ثبت گردید";
            //$str2 = "با تشکر از پرداخت غیر حضوری شما در اتواپ";
            //$str1 = $Name[0] . " " . $Name[1];

            //$txt = $str1 . $str2;
            //$topic = 2;
            //$responce = $en_SMS->send_sms($cell_arr, $str2, $topic);

            //remove record from All_Member_Invoice
        }
        $url = "./V_MechanicProfile.php";
        header("Location: $url");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>

    <!-- Jasny-bootstrap CSS -->
    <link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-touchspin CSS -->
    <link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <!-- Data table CSS -->
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>

    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />


    <script type="text/javascript">
        function onlyNumberKey(evt) {

            // Only ASCII charactar in that range allowed 
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
                return false;
            } else {
                return true;
            }
        }

        function validateForm() {
            var x = document.forms["myForm"]["noipg-amount"].value;
            if (x == "") {
                alert("مقدار [مبلغ] خالی می باشد");
                return false;
            }

            var x = document.forms["myForm"]["noipg-ref"].value;
            if (x == "") {
                alert("مقدار [شماره پیگیری] خالی می باشد");
                return false;
            }

            var x = document.forms["myForm"]["noipg-trace"].value;
            if (x == "") {
                alert("مقدار [شماره ارجاع] خالی می باشد");
                return false;
            }

        }
    </script>

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
                                    <a class="pull-left inline-block" data-toggle="collapse" href="#billing-2" aria-expanded="true" aria-controls="billing-2">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <i class="zmdi zmdi-chevron-up"></i>
                                    </a>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> ایجاد صورت حساب </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="billing-2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="example-basic">
                                        <div class="col-sm-6">
                                            <form name="myForm" action="#" onsubmit="return validateForm()" method="post">
                                                <input type="hidden" id="mem_GUID" name="mem_GUID" value="<?php echo $mem_GUID; ?>">
                                                <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="<?php echo $Inv_GUID; ?>">
                                                <input type="hidden" id="Inv_Topic" name="Inv_Topic" value="<?php echo $Title; ?>">
                                                <input type="hidden" id="action" name="action" value="RegisterPayment">
                                                <label class="control-label mb-10" for="exampleInputuname_1"><i class="text-info mb-10">*</i> مبلغ مصوب صورت حساب :
                                                    <?php echo $price; ?> (به ریال) </label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                    <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" name="amount" id="amount" placeholder="<?php echo $price; ?>">
                                                </div>
                                                <br />
                                                <label class="control-label mb-10" for="exampleInputuname_1"><i class="text-info mb-10">*</i> شماره پیگیری : </label>
                                                <div class="input-group ">
                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                    <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" name="trace" id="trace" placeholder="  شماره پیگیری ">
                                                </div>
                                                <br />
                                                <label class="control-label mb-10" for="exampleInputuname_1"><i class="text-info mb-10">*</i> شماره ارجاع : </label>
                                                <div class="input-group ">
                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                    <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" name="refnum" id="refnum" placeholder=" شماره ارجاع ">
                                                </div>
                                                <br />
                                                <label class="control-label mb-10" for="exampleInputuname_1"> یادداشت :
                                                </label>
                                                <div class="input-group ">
                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                    <input type="text" class="form-control" name="Comment" id="Comment" placeholder="  یادداشت ">
                                                </div>
                                                <br />
                                                <div class="form-group">
                                                    <button class="btn btn-success btn-anim" type="submit" name="submit" id="submit"><i class="icon-check"></i><span class="btn-text">
                                                            ثبت </span></button>
                                                </div>
                                            </form>
                                            <br />
                                            <br />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row -->
            </div>

            <!-- Main Content -->

            <div class="container-fluid">
                <!-- Footer -->
                <!-- Footer -->
                <?php footer(); ?>
                <!-- /Footer -->
                <!-- /Footer -->
            </div>

            <!-- /Main Content -->

        </div>
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

        if ($Error_STR == 1) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
        $.toast({
            heading: 'خطا در زرین پال',
            text: $ErrorCode . 'کد خطا = '. $message ,
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