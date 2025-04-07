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

if (!isset($_POST['login-btn'])) {
	if ($_POST['action'] == "edit") {
		$ID = $_POST['itemid'];
		$tariff = new tariff($_SESSION["Admin_GUID"]);
		$tariff_item_name = $tariff->getitemname($ID);
		$tariff_item_Foreign = $tariff->getForeign($ID);
	}
}

if (isset($_POST['login-btn'])) {
	$ID = $_POST['itemid'];
	$tariff = new tariff($_SESSION["Admin_GUID"]);
	$tariff_item_name = $tariff->getitemname($ID);
	$tariff_item_Foreign = $tariff->getForeign($ID);
	$name = $_POST['servicename'];

	if (empty($_POST['inetrnal'])) {
		$Foreign = 1;
	} elseif ($_POST['inetrnal'] == 'on') {
		$Foreign = 0;
	}

	if (!empty($name)) {

		if ($name !== $tariff_item_name) {

			if ($Foreign !== $tariff_item_Foreign) {

				$tariff->updateitem($ID, $name, $Foreign);
				$url = "./view-tariff-item.php";
				header("Location: $url");
			} else {

				$tariff->updateitem($ID, $name, $tariff_item_Foreign);
				$url = "./view-tariff-item.php";
				header("Location: $url");
			}
		}
	}
	if (empty($name)) {

		if ($Foreign !== $tariff_item_Foreign) {

			$tariff->updateitem($ID, $tariff_item_name, $Foreign);
			$url = "./view-tariff-item.php";
			header("Location: $url");
		} else {

			$tariff->updateitem($ID, $tariff_item_name, $tariff_item_Foreign);
			$url = "./view-tariff-item.php";
			header("Location: $url");
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
										<h6 class="panel-title txt-dark"> تعریف سرویس </h6>
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
														<label class="control-label" for="exampleInputuname_1"> نام سرویس : </label>
														<div class="input-group ">
															<div class="input-group-addon"><i class="icon-info"></i></div>
															<input type="text" class="form-control" name="servicename" id="servicename" placeholder="<?php echo $tariff_item_name; ?>" value="<?php echo $_POST['servicename'] ;?>">
															<input type="hidden" id="itemid" name="itemid" value="<?php echo $ID; ?>">
														</div>
														<br>
														<div class="form-group">
															<label class="control-label mb-10" for="exampleInputuname_1"> سرویس مختص خودرو داخلی : </label>
															<div class="checkbox checkbox-primary">
																<?php
																if ($tariff_item_Foreign == 0) {
																	echo '<input id="checkbox2" type="checkbox" name="inetrnal" id="inetrnal" >';
																} else {

																	echo '<input id="checkbox2" type="checkbox" checked="checked" name="inetrnal" id="inetrnal" >';
																}
																?>
																<label for="checkbox2"> داخلی </label>
															</div>
														</div>

														<br>
														<div class="input-group">

															<button class="btn btn-info btn-anim" type="submit" name="login-btn" id="login-btn"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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