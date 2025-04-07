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
require_once('Model/AutoModel.php');

use fateh\Automobile\Automobile as auto;

if (isset($_POST['login-btn'])) {
	$autobj = new auto($_SESSION["Admin_GUID"]);

	$autobj->C_AutoModel_ByID($_POST['modelname'], $_POST['Manufacturer']);
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
										<h6 class="panel-title txt-dark"> تعریف تیپ خودرو </h6>
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

														<label class="control-label mb-10"> نام خودروساز : </label>
														<select class="form-control select2 select2-hidden-accessible" name="Manufacturer" id="Manufacturer">
															<option> انتخاب نمایید </option>
															<?php
															require('config/config_DB.php');
															$data = $pdo->query('SELECT * FROM `Automobile_Manufacturer` WHERE `Visible` = 1')->fetchAll(PDO::FETCH_ASSOC);
															foreach ($data as $row) {
																echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
															}
															?>

														</select>
													</div>
													<br>
													<label class="control-label mb-10" for="exampleInputuname_1"> تیپ خودرو : </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="modelname" id="modelname" placeholder="تیپ خودرو" value="<?php echo $_POST['modelname'] ;?>">
													</div>
													<br>
													<div class="input-group">

														<button class="btn btn-info btn-anim" type="submit" name="login-btn" id="login-btn"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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

</body>

</html>