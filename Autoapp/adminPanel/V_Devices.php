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
require_once('Model/DeviceModel.php');

use fateh\Devices\Devices as dev;

if (isset($_POST['action'])) {
	if ($_POST['action'] == "remove") {
		$ID = $_POST['devicesId'];

		$dev = new dev($_SESSION["Admin_GUID"]);
		$dev->removedevice($ID);
		$url = "./V_Devices.php";
		header("Location: $url");
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Colorpicker CSS -->
    <link href="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- switchery CSS -->
    <link href="vendors/bower_components/switchery/dist/switchery.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-select CSS -->
    <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-tagsinput CSS -->
    <link href="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-touchspin CSS -->
    <link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <!-- multi-select CSS -->
    <link href="vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Switches CSS -->
    <link href="vendors/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Datetimepicker CSS -->
    <link href="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>
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
										<h6 class="panel-title txt-dark"> فهرست دستگاه های خاص : </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30">
												<thead>
													<tr>
														<th> نام دستگاه </th>
														<th> شرح </th>
														<th> فایل ها </th>
														<th> عملیات </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th> نام دستگاه </th>
														<th> شرح </th>
														<th> فایل ها </th>
														<th> عملیات </th>
													</tr>
												</tfoot>
												<tbody>
													<?php
													$dev = new dev($_SESSION["Admin_GUID"]);
													$data = $dev->V_Devices();
													if (!empty($data)) {
														foreach ($data as $row) {
															echo "<tr>";
															echo "<td>" . $row['Name'] . "</td>";
															echo "<td>" . $row['Comment'] . "</td>";
															echo "<td>";
															$dev->getdevfile($row['filekey']);
															echo "</td>";
															echo '<td style ="width: 35%; white-space: nowrap;">';
															$validation = "onclick='return validation()'";
															$backurl = current_url();
															echo '<form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data">
																	<input type="hidden" id="devicesId" name="devicesId" value="' . $row['ID'] . '">
																	<input type="hidden" id="action" name="action" value="remove">
																	<button class="btn btn-default btn-icon-anim" ' . $validation . '><i class="icon-trash"></i> حذف </button>
																	</form>
																	<form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="edit-devices.php">
																	<input type="hidden" id="devicesId" name="devicesId" value="' . $row['ID'] . '">
																	<input type="hidden" id="action" name="action" value="edit">
																	<button class="btn btn-default btn-icon-anim"><i class="icon-settings"></i> ویرایش </button>
																	</form>
																	<form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="upload-file.php">
																	<input type="hidden" id="devicesId" name="devicesId" value="' . $row['ID'] . '">
																	<input type="hidden" id="backlocation" name="backlocation" value="' . $backurl . '">
																	<input type="hidden" id="eltype" name="eltype" value="20">
																	<button class="btn btn-default btn-icon-anim "><i class="icon-cloud-upload"></i> آپلود فایل </button>
																</form>';
															echo "</td></tr>";
														}
													} else {
														echo '<tr><td colspan="5" style="text-align: center;"> دستگاهی تعریف نشده است </td></tr>';
													}

													?>
												</tbody>
											</table>
										</div>
									</div>
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
	<?php MainJavasc(); ?>
	<!-- Gallery JavaScript -->
	<script src="dist/js/isotope.js"></script>
	<script src="dist/js/lightgallery-all.js"></script>
	<script src="dist/js/froogaloop2.min.js"></script>
	<script src="dist/js/gallery-data.js"></script>
	<script LANGUAGE="JavaScript">
		function validation() {
			var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
	</script>
	</div>
</body>

</html>