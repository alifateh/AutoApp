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


if (isset($_POST['Edit'])) {
	$ID = $_POST['tariff-ID'];
	$tariff = new tariff($_SESSION["Admin_GUID"]);
	$get_ver_ID = $tariff->gettariffver($ID);
	$post_version_ID = $_POST['tariffver'];

	if ($post_version_ID !== $get_ver_ID) {

		$tariff->updateTariffverion($ID, $post_version_ID);
		$url = "./V_TariffValid.php";
		header("Location: $url");
	} else {

		$tariff->updateTariffverion($ID, $get_ver_ID);
		$url = "./V_TariffValid.php";
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
												$version_ID = $_POST['tariff-ver'];
												$edit_tariff = new tariff($_SESSION["Admin_GUID"]);
												$tariff_detail = $edit_tariff->gettariffDetial($ID);
												$tariff_auto = $edit_tariff->gettariffauto($tariff_detail[0]);
												$man_name = "خودروساز : " . $edit_tariff->gettariffautoman($tariff_auto[0]);
												if ($tariff_auto[1] !== 0) {
													$tip_name = $edit_tariff->gettariffautotip($tariff_auto[1]);
													$tip = " تیپ " . $tip_name;
												} else {
													$tip = "بدون تیپ";
												}
												$auto_name = " نام خودرو: " . $tariff_auto[2];
												$Vdate = $edit_tariff->gettariffdateinhejri($tariff_detail[2]);
												$tariff_date = gregorian_to_jalali($Vdate[0], $Vdate[1], $Vdate[2]);
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

									<form method="post" enctype="multipart/form-data">
										<input type="hidden" id="tariff-ID" name="tariff-ID" value="<?php echo $_POST['editVer-ID']; ?>">
										<div id="example-basic">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label mb-10" for="tariffver"> نسخه تعرفه : </label>
													<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="tariffver" id="tariffver">
														<?php
														require('config/config_DB.php');
														$data = $pdo->query('SELECT * FROM `Tariff_Version_ID` WHERE `Visible` = 1 ORDER BY `ID`')->fetchAll();
														foreach ($data as $row) {
															if ($row['ID'] == $version_ID) {
																echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
															} else {
																echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
															}
														}
														?>

													</select>

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
	</div>
	<!-- /#wrapper -->
	<!-- JavaScript -->
	<?php MainJavasc(); ?>

</body>

</html>