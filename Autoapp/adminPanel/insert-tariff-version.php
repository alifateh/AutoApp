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

	<?php
	require_once('Model/TariffModel.php');

	use fateh\tariff\tariff as tariff;

	if (isset($_POST['VersionREG'])) {
		if ($_POST['version-name'] !== "") {
			$tariff_ver = new tariff($_SESSION["Admin_GUID"]);
			$tariff_name = $_POST['version-name'];
			$tariff_ver->addversion($tariff_name);
		} else {

			echo '<script language="javascript">';
			echo 'alert("لطفا نام نسخه را وارد نمایید")';
			echo '</script>';
		}
	}




	?>

	<script type="text/javascript">
		function onlyNumberKey(evt) {

			// Only ASCII charactar in that range allowed 
			var ASCIICode = (evt.which) ? evt.which : evt.keyCode
			if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
				return false;
			return true;
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
									<a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2">
										<i class="zmdi zmdi-chevron-down"></i>
										<i class="zmdi zmdi-chevron-up"></i>
									</a>
								</div>
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark"> تعیین نسخه تعرفه </h6>
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
													<label class="control-label mb-10" for="exampleInputuname_1"> نام نسخه : </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="version-name" id="version-name" onkeypress="return onlyNumberKey(event)" placeholder="139901">
													</div>
												</div>
												<br>
												<br>
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="VersionREG" id="VersionREG"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
</body>

</html>