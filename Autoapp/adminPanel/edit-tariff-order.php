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
require_once('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;

$change_order = new tariff($_SESSION["Admin_GUID"]);

if (isset($_POST['tariff-ID'])) {
	$ID = $_POST['tariff-ID'];
	$SecID = $_POST['tariff-SecID'];
} else {

	// return VALUES
	$url = "./V_TariffValid.php";
	header("Location: $url");
}


if (isset($_POST['change-order'])) {

	$ItemID = $_POST['item-ID'];

	for ($i = 0; $i < count($ItemID); $i++) {
		$change_order->updateOrdertariff($ItemID[$i], $i);
	}
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
	<!--Nestable CSS -->
	<link href="vendors/bower_components/nestable2/jquery.nestable.css" rel="stylesheet" type="text/css" />


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
										<?php
										if (!isset($_POST['Edit'])) {
											if (!empty($_POST['tariff-ID'])) {
												$edit_tariff = new tariff($_SESSION["Admin_GUID"]);
												$tariff_detail = $edit_tariff->gettariffDetial($ID);
												$Vdate = $edit_tariff->gettariffdateinhejri($tariff_detail[2]);
												$tariff_date = gregorian_to_jalali($Vdate[0], $Vdate[1], $Vdate[2]);
												$ver = $edit_tariff->getversion($tariff_detail[1]);

												$tariff_auto = $edit_tariff->gettariffauto($tariff_detail[0]);
												$man_name = "خودروساز : " . $edit_tariff->gettariffautoman($tariff_auto[0]);
												if ($tariff_auto[1] !== 0) {
													$tip_name = $edit_tariff->gettariffautotip($tariff_auto[1]);
													$tip = " تیپ " . $tip_name;
												} else {
													$tip = "بدون تیپ";
												}
												$auto_name = " نام خودرو: " . $tariff_auto[2];
												$ver = $edit_tariff->getversion($tariff_detail[1]);
												echo '<h6 class="panel-title txt-dark"> مشخصات خودور [ ' . $man_name . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>
														<h6 class="panel-title txt-dark"> تاریخ اعتبار : ' . $tariff_date[0] . "/" . $tariff_date[1] . "/" . $tariff_date[2] . '</h6>
															<h6 class="panel-title txt-dark"> نسخه تعرفه : ' . $ver . '</h6>';
											} else {
												echo '<div class="alert alert-info alert-dismissable">
															<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><a href="V_TariffValid.php"> لطفا به فهرست تعرفه ها برگردید </a> 
														</div>';
											}
										}


										?>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>
							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data" id="idForm">
										<div class="col-md-6">
											<div class="panel panel-default card-view">
												<div class="panel-heading">
													<div class="pull-left">
														<h6 class="panel-title txt-dark">ترتیب اجرت ها</h6>
													</div>
													<div class="clearfix"></div>
												</div>
												<div class="panel-wrapper collapse in">
													<div class="panel-body">
														<div class="dd" id="nestable2">
															<ol class="dd-list">
																<?php
																if (!empty($_POST['tariff-ID'])) {
																	$ID = $_POST['tariff-ID'];
																	$SecID = $_POST['tariff-SecID'];
																	$order = new tariff($_SESSION["Admin_GUID"]);
																	$order->viewtariffall_order($ID, $SecID);
																} else {
																	echo "<div><a href='V_TariffValid.php'> لطفا به فهرست تعرفه ها برگردید </a></div>";
																}
																?>

															</ol>
														</div>
													</div>
												</div>
												<!-- div dd -->
												<button class="btn btn-pinterest btn-icon-anim" type="submit" id="change-order" name="change-order"> ثبت ترتیب </button>
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

	<!-- jQuery -->
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!--Nestable js -->
	<script src="vendors/bower_components/nestable2/jquery.nestable.js"></script>

	<!-- Nestable Init JavaScript -->
	<script src="dist/js/nestable-data.js"></script>

	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>

	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
	<script>
		$(document).ready(function() {

			// activate Nestable for list 2
			$('#nestable2').nestable({
				group: 1
			})

		});
	</script>


</body>

</html>