<?php
session_start();
require_once('config/public_conf.php');
require('Model/PayModel.php');

use fateh\Payment\invoice as inv;

require('Model/TellModel.php');

use fateh\phonebook\phonebook as Tell;

require('Model/SmsChanel.php');

use fateh\smschanel\SMS as sms;

require('Model/MemberModel.php');

use fateh\Member\Member as member;

$get_Session = new inv($_SESSION["Mechanic_GUID"]);
$en_Tell = new Tell($_SESSION["Mechanic_GUID"]);
$en_SMS = new sms();
$member = new member($_SESSION["Mechanic_GUID"]);


/////////////////////
//IPG values
/////////////////////
/*
//test
	$key="8v8AEee8YfZX+wwc1TzfShRgH3O9WOho"; 
		
	$Token=$_POST["token"]; //get from IPG
	$ResCode=$_POST["ResCode"]; //get from IPG
	$OrderId = $_POST["OrderId"];  //get from IPG
	
*/

//fava
$key = "5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm";
$Token = $_POST["token"]; //get from IPG
$ResCode = $_POST["ResCode"]; //get from IPG
$OrderId = $_POST["OrderId"];  //get from IPG

/////////////////////
//Data collection
/////////////////////

$info_session = $get_Session->GetSessioninfo($OrderId);
$SessionGUID = $info_session[1];
$member_GUID = $info_session[2]; //member_guid
$InvoiceID = $info_session[3];
$_SESSION["Mechanic_GUID"] = $info_session[2]; //member_guid

$paymentinfo = $get_Session->GetPaymentInfo($SessionGUID, $OrderId);
$ExDate = $paymentinfo[7];
$Amount = $paymentinfo[1];

//Create sign data(Tripledes(ECB,PKCS7))
function encrypt_pkcs7($str, $key)
{
	$key = base64_decode($key);
	$ciphertext = OpenSSL_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
	return base64_encode($ciphertext);
}
//Send Data
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
if ($ResCode == 0) {
	$verifyData = array('Token' => $Token, 'SignData' => encrypt_pkcs7($Token, $key));
	$str_data = json_encode($verifyData);
	$res = CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify', $str_data);
	$arrres = json_decode($res);
}

if (!empty($arrres)) {
	if ($arrres->ResCode != -1 && $arrres->ResCode == 0) {
		//Save $arrres->RetrivalRefNo,$arrres->SystemTraceNo,$arrres->OrderId to DataBase
		$Message = "شماره سفارش:" . $OrderId . "<br>" . "شماره پیگیری : " . $arrres->SystemTraceNo . "<br>" . "شماره مرجع:" .
			$arrres->RetrivalRefNo . "<br> اطلاعات بالا را جهت پیگیری های بعدی یادداشت نمایید." . "<br>";
		$Message_color = "btn-success";

		$invoice = array();
		$invoice[0] = $member_GUID; //GUID
		$invoice[1] = $OrderId; //OrderID 
		$invoice[2] = $Amount; //amount
		$invoice[3] = $OrderId; //OrderID  
		$invoice[4] = $arrres->SystemTraceNo; //Trace
		$invoice[5] = $arrres->RetrivalRefNo; //refNum
		$invoice[6] = 32; //ElemanType
		$invoice[7] = $ExDate; //expire date
		$invoice[8] = $InvoiceID; //invoicIDE

		$get_Session->UpdateMechanicInvoice($invoice);

		$cell_arr = array();
		$cell_arr[0] = $en_Tell->GetMemberMobile($info_session[2]);

		$Name = $member->GetMemberName($info_session[2]);
		$str2 = " عضو محترم اتحادیه تعمیرکاران خودرو تهران حساب شما در اتواپ شارژ گردید ";
		$str1 = $Name[0] . " " . $Name[1] . " ";
		$txt = $str1 . $str2;
		$topic = 2;

		//trigger exception in a "try" block
		$x = $en_SMS->Mechanic_send_sms($cell_arr, $txt, $topic, $member_GUID);
		//If the exception is thrown, this text will not be shown

		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;

		if ($x !== 1) {

			$comment = " ارسال پیامک خوش آمدگویی انجام نشد";
			$Action = 18;
			$en_SMS->LogMechanicSMS($date, $info_session[2], $Action, $ElemanType, $comment);
		} else {
			$comment = " پیامک خوش آمدگویی ارسال شد";
			$Action = 19;
			$en_SMS->LogMechanicSMS($date, $info_session[2], $Action, $ElemanType, $comment);
		}
	} else {
		$Message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
		$Message_color = "btn-warning";
	}
} else {
	$Message = " تراکنش لغو گردیده و یا پاسخی از درگاه پرداخت دریافت نشد لطفا مجددا اقدام نمایید ";
	$Message_color = "btn-danger";
}

if (!isset($_SESSION["Mechanic_GUID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "/";
	header("Location: $url");
}
// header( "refresh:5;url=V_MechanicProfile.php" );
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