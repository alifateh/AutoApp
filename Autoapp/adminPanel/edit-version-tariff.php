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

if (!isset($_POST['edit-ver'])) {
	if ($_POST['action'] == "edit") {
		$ID = $_POST['versionId'];
		$getversion = new tariff($_SESSION["Admin_GUID"]);
		$versionName = $getversion->getversion($ID);
	}
}

if (isset($_POST['edit-ver'])) {
	$ID = $_POST['version-ID'];
	$updatever = new tariff($_SESSION["Admin_GUID"]);
	$get_version = $updatever->getversion($ID);
	$post_version = $_POST['editvername'];

	if (empty($post_version)) {
		$up_ver = $get_version;
	} else {
		if ($post_version == $get_version) {
			$up_ver = $get_version;
		} else {
			$up_ver = $post_version;
		}
	}

	$return_val = $updatever->updateversion($ID, $up_ver);
	if (empty($return_val)){
		// return VALUES
		$url = "./V_TariffVersion.php";
		header("Location: $url");
	}else{
		echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
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
										<h6 class="panel-title txt-dark"> ویرایش نام نسخه تعرفه </h6>
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
													<label class="control-label mb-10" for="exampleInputuname_1"> نسخه : </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="editvername" id="editvername" placeholder="<?php echo $versionName; ?>" value="<?php echo $_POST['editvername'] ;?>" >
														<input type="hidden" id="version-ID" name="version-ID" value="<?php echo $ID; ?>">
													</div>
												</div>
												<br>
												<br>
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="edit-ver" id="edit-ver"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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