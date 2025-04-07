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
require('Model/TariffModel.php');

use fateh\tariff\tariff as tarif;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['valid'])) {
		$valid = new tarif($_SESSION["Admin_GUID"]);
		echo $ID = $_POST['tariff-ID'];
		$v = 1;
		$valid->updatetariffgroup($ID, $v);
	}

	if (isset($_POST['invalid'])) {
		$valid = new tarif($_SESSION["Admin_GUID"]);
		echo $ID = $_POST['tariff-ID'];
		$v = 0;
		$valid->updatetariffgroup($ID, $v);
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

	<script LANGUAGE="JavaScript">
		function valid() {
			var agree = confirm("آیا از اجرای دستور مطمین هستید؟");
			if (agree)
				return true;
			else
				return false;
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
										<h6 class="panel-title txt-dark"> مدیریت اعتبار نسخ تعرفه ها </h6>
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
														<label class="control-label mb-10"> نسخه تعرفه : </label>
														<select class="form-control select2 select2-hidden-accessible" name="tariff-ID" id="tariff-ID">
															<option> انتخاب نمایید </option>
															<?php
															require('config/config_DB.php');
															$data = $pdo->query('SELECT * FROM `Tariff_Version_ID` WHERE `Visible` = 1')->fetchAll(PDO::FETCH_ASSOC);
															foreach ($data as $row) {
																echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
															}
															?>

														</select>
													</div>
													<br />
													<br />
													<div class="row">
														<div class="col-sm-12">
															<div class="input-group">
																<button class="btn btn-success btn-anim" onclick="return confirm('آیا از اجرای دستور مطمین هستید؟')" name="valid" id="valid"><i class="icon-check"></i><span class="btn-text"> اعتبار دهی </span></button>
																&nbsp;&nbsp;
																<button class="btn btn-info btn-anim" onclick="return confirm('آیا از اجرای دستور مطمین هستید؟')" name="invalid" id="invalid"><i class="icon-ban"></i><span class="btn-text"> اخذ اعتبار </span></button>
															</div>
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
		<!-- JavaScript -->
		<?php MainJavasc(); ?>


	</div>
	<!-- /#wrapper -->


</body>

</html>