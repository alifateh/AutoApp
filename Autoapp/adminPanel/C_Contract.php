<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
require('config/public_conf.php');
require('Model/ContractModel.php');
require('Model/N_TariffModel.php');

use fateh\tariff\NewTariff as NewTariff;
use fateh\Contarct\contarct as contarct;

$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);

$data = $Tariff_Obj->V_AllTariffVersion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$cat = new contarct($_SESSION["Admin_GUID"]);
	$version = $_POST['tariffver'];
	$txt = $_POST['txtbox1'];
	$checkbox = $_POST['check'];
	$route = $cat->C_Contract($txt, $checkbox, $version);
	print_r($route);
	if (!empty($route) && $route = 1) {
		$Error_STR = 0;
		$url = "./V_Contracts.php";
		header("Location: $url");
	} else {
		$Error_STR = 1;
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
										<h6 class="panel-title txt-dark"> تعریف تعهدنامه </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data">
										<div id="example-basic">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="input-group col-sm-6">
														<label class="control-label mb-10" for="tariffver"> نسخه تعرفه : </label>
														<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="tariffver" id="tariffver">
															<?php
															
															if (!empty($data)) {
																$numItems = count($data);
																$i = 0;
																foreach ($data as $row) {
																	if (++$i === $numItems) {
																		echo '<option value="' . $row['ID'] . '" selected >' . $row['NameFa'] . '</option>';
																	} else {
																		echo '<option value="' . $row['ID'] . '">' . $row['NameFa'] . '</option>';
																	}
																}
															} else {
																echo '<option value=""> تعرفه ایی ثبت نشده </option>';
															}
															?>

														</select>

													</div>
												</div>
												<div class="card">
													<div class="card-body" dir="rtl">
														<h3 style='text-align: center;'> بسمه تعالی </h3>
														<h5 style='text-align: center;'> ((تعهد نامه)) </h4>
															<br />
															<h4> ریاست محترم اتحادیه صنف تعمیرکاران خودرو تهران </h4>
															<p> احتراما اینجانب ........... به شماره ملی ........ صادره از ......... متصدی واحد صنفی واقع در .......<br />
																دارای پرونده به شماره ...........<br />
															</p>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label mb-10" for="txtbox1"> متن تعهدنامه </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<textarea class="form-control" name="txtbox1" id="txtbox1"> ادامه متن ...  </textarea>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label mb-10" for="exampleInputuname_1"> متن رضایت </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="check" id="check" placeholder="متن فوق را مشاهده و قبول دارم" value="<?php echo $_POST['check'] ;?>" >
													</div>
												</div>
												<br />
												<br />
												<div class="card">
													<div class="card-body" dir="rtl">
														<h5 style="text-align: center; color: red;">
															<< تذکر>>
														</h5>
														<p style='text-align: center; color: red;'> با توجه به اینکه بارکد نرخنامه صرفا مختص به واحد صنفی شما می باشد، از ارائه نرخنامه به دیگران خود داریی نمایید زیرا هرگونه دسترسی غیرمجاز قابل پیگرد قانونی می باشد </p>
													</div>
												</div>
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="login-btn" id="login-btn"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
	<!-- JavaScript -->
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

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
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	<?php


	if (!empty($Error_STR)) {
		if ($Error_STR = 1) {
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
	}


	?>
</body>

</html>