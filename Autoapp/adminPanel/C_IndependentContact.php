<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/N_PhoneContactModel.php");

use fateh\Phonebook\IndependentContact as ContactClass;

$Contact_obj = new ContactClass($_SESSION["Admin_GUID"]);
$GetOperator = $Contact_obj->Get_PhoneOperator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$Error_STR = 0;
	if (isset($_POST['ActionID']) == "CreatePhone") {
		if (isset($_POST['fname']) && !empty($_POST['fname'])) {
			$FirstName = $_POST['fname'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['Lname']) && !empty($_POST['Lname'])) {
			$LastName =  $_POST['Lname'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['tags']) && !empty($_POST['tags'])) {
			$tags =  $_POST['tags'];
		} else {
			$tags = "";
		}

		if (isset($_POST['AddOperator']) && !empty($_POST['AddOperator'])) {
			$Operator = $_POST['AddOperator'];
		} else {
			$Operator = 0;
		}

		if (isset($_POST['Telephone_Num']) && !empty($_POST['Telephone_Num'])) {
			$Number = $_POST['Telephone_Num'];
		} else {
			$Error_STR = 2;
		}

		if ($Error_STR == 0) {
			$route = $Contact_obj->C_IndependentContact($FirstName, $LastName, $Operator, $Number, $tags);
		}
		if (isset($route) && $route == 1) {
			$url = "./V_Phonebook.php";
			header("Location: $url");
		} else {
			$Error_STR = 1;
		}
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
										<h6 class="panel-title txt-dark"> تعریف شماره تلفن </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" id="ActionID" name="ActionID" value="CreatePhone">
										<div class="form-body overflow-hide">
											<div class="col-sm-12">
												<div class="col-sm-3">
													<div class="form-group">
														<label class="control-label mb-10" for="fname"><i class="text-info mb-10">*</i> نام : </label>
														<div class="input-group">
															<div class="input-group-addon"><i class="icon-info"></i></div>
															<input type="text" class="form-control" name="fname" id="fname" placeholder="نام" value="<?php echo $_POST['fname']; ?>">
														</div>
													</div>
													<div class="form-group">
														<label class="control-label mb-10"><i class="text-info mb-10">*</i> نوع شماره تماس : </label>
														<select class="form-control " dir="rtl" name="AddOperator" id="AddOperator">
															<?php
															echo '<option value=""> انتخاب نمایید </option>';
															if (!empty($GetOperator)) {
																foreach ($GetOperator as $key) {
																	echo '<option value= "' . $key['ID'] . '">' . $key['PersianName'] . '</option>';
																}
															} else {
																echo '<option value=""> انتخاب نمایید </option>';
															}
															?>
														</select>
													</div>
													<div class="form-group">
														<label class="control-label mb-10 text-left" for="tags"> کلمات کلیدی : </label>
														<input type="text" value="" data-role="tagsinput" id="tags" name="tags" placeholder="کلمات کلیدی" value="<?php echo $_POST['tags']; ?>">
													</div>
												</div>
												<div class="col-sm-3">
													<div class="form-group mb-30">
														<div class="form-group">
															<label class="control-label mb-10" for="Lname"><i class="text-info mb-10">*</i> نام خانوادگی : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="icon-info"></i></div>
																<input type="text" class="form-control" name="Lname" id="Lname" placeholder="نام خانوادگی" value="<?php echo $_POST['Lname']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10"><i class="text-info mb-10">*</i> شماره تلفن : </label>
															<input type="text" class="form-control" Name="Telephone_Num" onkeypress="return onlyNumberKey(event)" minlength="10" maxlength="10" value="<?php echo $_POST['Telephone_Num']; ?>">
														</div>
													</div>
													<div class="form-actions mt-10">
														<button class="btn btn-success mr-10 mb-30" type="submit" name="AddTellphone" id="AddTellphone"> ثبت </button>
													</div>
												</div>
											</div>
										</div>
									</form>
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


	<?php MainJavasc(); ?>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
	<script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>


	<script type="text/javascript">
		function onlyNumberKey(evt) {

			// Only ASCII charactar in that range allowed 
			var ASCIICode = (evt.which) ? evt.which : evt.keyCode
			if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
				return false;
			return true;
		}
	</script>

	<?php
	if (!empty($Error_STR)) {
		if ($Error_STR == 1) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا درسمت سرور',
			text: 'اطلاعات فرم ثبت نشده است لطفا با ادمین تماس بگیرید' ,
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
			heading: 'خطا در ثبت اطلاعات',
			text: 'فیلدهای خالی مانده را پرنمایید.' ,
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
	<!-- JavaScript -->
</body>

</html>