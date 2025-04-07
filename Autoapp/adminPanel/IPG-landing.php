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

$get_Session = new inv();
$en_Tell = new Tell($_SESSION["Admin_GUID"]);
$en_SMS = new sms();
$member = new member($_SESSION["Admin_GUID"]);


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

$key = "5k2bLXUWsrU4d+7jqP1+2KfoWxPmAGUm"; //fava

$Token = $_POST["token"]; //get from IPG
$ResCode = $_POST["ResCode"]; //get from IPG
$OrderId = $_POST["OrderId"];  //get from IPG


$info_session = $get_Session->AdminGetSessioninfo($OrderId);
// $ExDate = $get_Session -> GetExDate ($info_session[3]);

$_SESSION["Admin_ID"] = $info_session[0];  // admin ID
$_SESSION["GUID"] = $info_session[2];  //member ID

$InvoiceID = $info_session[3];
$SessionGUID = $info_session[1];

$paymentinfo = $get_Session->GetPaymentInfo($SessionGUID, $OrderId);
$ExDate = $paymentinfo[7];
$Amount = $paymentinfo[1];

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
		$invoice = array();
		$Message_color = "panel-success";
		$invoice[0] = $info_session[2]; //GUID
		$invoice[1] = $InvoiceID; //OrderID - 
		$invoice[2] = $Amount; //amount
		$invoice[3] = $OrderId; //OrderID 
		$invoice[4] = $arrres->SystemTraceNo; //Trace
		$invoice[5] = $arrres->RetrivalRefNo; //refNum
		$invoice[6] = 32; //ElemanType
		$invoice[7] = $SessionGUID; //invoicID
		$invoice[8] = $ExDate; //expire date

		$get_Session->updateinvoice($invoice);

		$cell_arr = array();
		$cell_arr[0] = $en_Tell->GetMemberMobile($info_session[2]);
		$Name = $member->GetMemberName($info_session[2]);

		$str2 = " عضو محترم اتحادیه تعمیرکاران خودرو تهران حساب شما در اتواپ شارژ گردید ";
		$str1 = $Name[0] . " " . $Name[1] . " ";

		$txt = $str1 . $str2;
		$topic = 2;


		$x = $en_SMS->send_sms($cell_arr, $txt, $topic);

		$date = date('Y-m-d H:i:s');
		$ElemanType = 28;

		if ($x !== 1) {

			$comment = " ارسال پیامک خوش آمدگویی انجام نشد";
			$Action = 18;
			$en_SMS->Logsms($date, $info_session[0], $Action, $ElemanType, $comment);
		} else {
			$comment = " پیامک خوش آمدگویی ارسال شد";
			$Action = 19;
			$en_SMS->Logsms($date, $info_session[0], $Action, $ElemanType, $comment);
		}
	} else {
		$Message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
		$Message_color = "panel-info";
	}
} else {
	$Message = " تراکنش لغو گردیده و یا پاسخی از درگاه پرداخت دریافت نشد لطفا مجددا اقدام نمایید ";
	$Message_color = "panel-info";
}

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