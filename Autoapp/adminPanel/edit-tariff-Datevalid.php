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
require('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;

$Tariff_ID = $_POST['editVer-ID'];

if (isset($_POST['Edit'])) {
	$tariff_ID = $_POST['tariff-ID'];
	$new_date = $_POST['validationDate'];
	$update_tariff = new tariff($_SESSION["Admin_GUID"]);

	if (!empty($new_date)) {
		$update_tariff->updateTariffDate($tariff_ID, $new_date);
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


	<!-- hejri -->

	<script src="dist/js/jquery.min.js"></script>
	<link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

	<script type="text/javascript">
		$(function() {
			$("#validationDate").persianDatepicker({
				formatDate: "YYYY-0M-0D"
			});
		});
	</script>
	<!-- hejri -->
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
											if (isset($_POST['editVer-ID'])) {
												$ID = $_POST['editVer-ID'];
												$tariff_Date = $_POST['ValidateDate'];

												//date hejri to garygori

												$garydate =  str_replace("/", "", $tariff_Date);

												$day = substr($garydate, 8, 2);
												$mon = substr($garydate, 5, 2);
												$year = substr($garydate, 0, 4);
												/////
												$persian_date = gregorian_to_jalali($year, $mon, $day);

												$edit_tariff = new tariff($_SESSION["Admin_GUID"]);
												$tariff_detail = $edit_tariff->gettariffDetial($ID);
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
												<h6 class="panel-title txt-dark"> تاریخ اعتبار : ' . $persian_date[0] . "/" . $persian_date[1] . "/" . $persian_date[2] . '</h6>
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
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" id="tariff-ID" name="tariff-ID" value="<?php echo $Tariff_ID; ?>">
										<div id="example-basic">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="input-group col-sm-6">
														<label class="control-label mb-10" for="validation-Date"> تاریخ اعتبار تعرفه : </label>
														<div class="input-group">
															<div class="input-group-addon"><i class="icon-info"></i></div>
															<input type="text" class="form-control" id="validationDate" name="validationDate" value="<?php echo $persian_date[0] . "/" . $persian_date[1] . "/" . $persian_date[2]; ?>">
														</div>
													</div>

													<br />
													<div class="input-group">

														<button class="btn btn-info btn-anim" type="submit" name="Edit" id="Edit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
													</div>
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
		<!-- JavaScript -->

		<?php MainJavasc(); ?>
		<script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
		<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	</div>
	<!-- /#wrapper -->

</body>

</html>