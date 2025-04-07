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
	if ($_POST['action'] == 'Payment-Methods') {
		if ($_POST['Payment-Methods'] == 1) {

			//start fetching Invoice Data        
			$Invoice_Obj = new invoice($_SESSION["Admin_GUID"]);
			
			$mem_GUID = $_POST["Member_GUID"];
			$Inv_GUID = $_POST["Invoice_GUID"];
			$Admin_GUID = $_SESSION["Admin_GUID"]; // admin ID

			$Retrive_ID = md5(uniqid(mt_rand(100000, 999999), true));


			///////////////////////////////////////DO IPG
			$Inv_value = $Invoice_Obj->Get_Invoice_ByGUID($Inv_GUID);
			$Amount = $Inv_value[0]['Amount'];
			$description =  $Inv_value[0]['Title'];

			$data = array(
				"action"		=> "token",
				"TerminalId"	=> "13611265",
				"Amount"		=> $Amount,  //$Amount
				"ResNum"		=> $Retrive_ID,
				"RedirectUrl"	=> "https://adminpanel.autoapp.ir/sep_landing.php",
				"CellNumber"	=> "9120000000",
				"SettlementIBANInfo" => array (
				//	array (
				//	"IBAN"				=> "IR170180000000000257045226", //taavoni
				//	"Amount"			=> "25000",
				//	"PurchaseID"		=> "000000000000000000000000000000"), 
					array (
					"IBAN"				=> "IR520180000000000028223730", //etehadiye
					"Amount"			=> $Amount,
					"PurchaseID"		=> "000000000000000000000000000000")
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
			if (!empty($json_response)) {
				$response = json_decode($json_response, true);
				if ($response['status'] == 1) {

					$IPG_status = $response['status'];
					$IPG_Token = $response['token'];
					//////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////Set Session//////////////////////////////////////

					$Session = array();
					$Session[0] = $Admin_GUID; // admin ID
					$Session[1] = $mem_GUID; // Member ID
					$Session[2] = $Retrive_ID; //Order GUID
					$Session[3] = $Inv_GUID; //Invoice ID
					$Invoice_Obj->SetSeesion($Session);
					//////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////Set Contract//////////////////////////////////////
					$ContarctObj = new contarct($_SESSION["Admin_GUID"]);

					$ContractID = $ContarctObj->Get_Contract_ByMechanic($Inv_value[0]['Title'], $mem_GUID);
					$route = $ContarctObj->U_ContractAdmin_ByID($ContractID[0]['ID']);

				}
			} else {
				$Error_STR = 1;
			}
		} else {
			/////////////////////////////////////////////////////////DO NOIPG
			$mem_GUID = $_POST["Member_GUID"];
			$Inv_GUID = $_POST["Invoice_GUID"];
			$_SESSION["GUID"] = $mem_GUID;
			$_SESSION["Inv_GUID"] = $Inv_GUID;
			$ContarctObj = new contarct($_SESSION["Admin_GUID"]);
			$View_Inv = new invoice($_SESSION["Admin_GUID"]);
			$Inv_value = $View_Inv->Get_Invoice_ByGUID($Inv_GUID);

			$ContractID = $ContarctObj->Get_Contract_ByMechanic($Inv_value[0]['Title'], $mem_GUID);
			$ContarctObj->U_ContractAdmin_ByID($ContractID[0]['ID']);
//
			////start 
			$price = $Inv_value[0]['Amount'];
			$Title = $Inv_value[0]['Title'];
		}
	} else {
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
} else {
	$url = "./index.php";
	header("Location: $url");
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

	<?php
	if (!empty($route) && $route == 'Done') {
		echo "
	<form name='sepform' action='https://sep.shaparak.ir/OnlinePG/OnlinePG' method='POST'>
		<input name='token' type='hidden' value='" . $response['token'] . "'>
		<input name='GetMethod' type='hidden' value=''>
		<script language='JavaScript'>document.sepform.submit();</script>
	</form>";
	}

	?>
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
            heading: 'خطا در درگاه',
            text: 'بدون پاسخ از سمت درگاه' ,
            position: 'top-center',
            loaderBg:'#ed3236',
            hideAfter: 6500,
            stack: 6
        });
        return false;
        });";
			echo '</script>';
		}

		if ($Error_STR == 2) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
        $.toast({
            heading: 'خطا در درگاه',
            text: 'Error: call to URL $url failed with status $status, response $json_response' ,
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