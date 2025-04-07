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

if (!isset($_POST['editdevice'])) {
	if ($_POST['action'] == "edit") {
		$ID = $_POST['devicesId'];
		$namedev = new dev($_SESSION["Admin_GUID"]);
		$dev_name = $namedev->getdevname($ID);
		$dev_tag = $namedev->getdevtag($ID);
	}
}

if (isset($_POST['remove_file'])) {
	if ($_POST['action'] == "remove_file") {
		$ID = $_POST['file_Id'];
		$dev_remove_file = new dev($_SESSION["Admin_GUID"]);
		$dev_remove_file->removedevfile($ID);
	}
}

if (isset($_POST['editdevice'])) {
	$ID = $_POST['devicesId'];
	$updatedev = new dev($_SESSION["Admin_GUID"]);
	$get_devname = $updatedev->getdevname($ID);
	$get_devtag = $updatedev->getdevtag($ID);
	$post_devname = $_POST['devicename'];
	$post_devtag = $_POST['devicetag'];

	if (!empty($post_devname)) {

		if ($post_devname !== $get_devname) {

			if (!empty($post_devtag)) {
				if ($post_devtag !== $get_devtag) {

					$updatedev->updatedev($ID, $post_devname, $post_devtag);
					$url = "./V_Devices.php";
					header("Location: $url");
				} else {

					$updatedev->updatedev($ID, $post_devname, $get_devtag);
					$url = "./V_Devices.php";
					header("Location: $url");
				}
			} else {

				$updatedev->updatedev($ID, $post_devname, $get_devtag);
				$url = "./V_Devices.php";
				header("Location: $url");
			}
		} else {

			if (!empty($post_devtag)) {

				if ($post_devtag !== $get_devtag) {

					$updatedev->updatedev($ID, $get_devname, $post_devtag);
					$url = "./V_Devices.php";
					header("Location: $url");
				} else {

					$updatedev->updatedev($ID, $get_devname, $get_devtag);
					$url = "./V_Devices.php";
					header("Location: $url");
				}
			} else {
				$updatedev->updatedev($ID, $get_devname, $get_devtag);
				$url = "./V_Devices.php";
				header("Location: $url");
			}
		}
	}
	if (empty($post_devname)) {

		if (!empty($post_devtag)) {
			if ($post_devtag !== $get_devtag) {

				$updatedev->updatedev($ID, $get_devname, $post_devtag);
				$url = "./V_Devices.php";
				header("Location: $url");
			} else {

				$updatedev->updatedev($ID, $get_devname, $get_devtag);
				$url = "./V_Devices.php";
				header("Location: $url");
			}
		} else {

			$updatedev->updatedev($ID, $get_devname, $get_devtag);
			$url = "./V_Devices.php";
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
										<h6 class="panel-title txt-dark"> تعریف دستگاه هایی تخصصی </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<div id="example-basic">
										<div class="col-sm-6">
											<form method="post" enctype="multipart/form-data">


												<div class="form-group">
													<label class="control-label mb-10" for="exampleInputuname_1"> نام دستگاه : </label>

													<div class="input-group">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="devicename" id="devicename" value="<?php echo $dev_name; ?>">
													</div>
												</div>
												<br>
												<div class="form-group">
													<label class="control-label mb-10 text-left"> کلمات کلیدی : </label>
													<textarea class="form-control" rows="3" name="devicetag" id="devicetag" value="<?php echo $dev_tag; ?>"></textarea>
													<input type="hidden" id="devicesId" name="devicesId" value="<?php echo $_POST['devicesId']; ?>">
												</div>
												<br />
												<br />
												<div class="row">
													<!-- Table Hover -->
													<div class="col-sm-12">
														<div class="panel panel-default card-view">
															<div class="panel-heading">
																<div class="pull-left">
																	<h6 class="panel-title txt-dark"> فهرست فایل </h6>
																</div>
																<div class="clearfix"></div>
															</div>
															<div class="panel-wrapper collapse in">
																<div class="panel-body">
																	<div class="table-wrap mt-40">
																		<div class="table-responsive">
																			<table class="table table-hover mb-0">
																				<thead>
																					<tr>
																						<th> دانلود فایل </th>
																						<th> عملیات </th>
																					</tr>
																				</thead>
																				<tbody>
																					<?php
																					$filedev = new dev($_SESSION["Admin_GUID"]);
																					$data = $filedev->Get_Devices_ByID($_POST['devicesId']);
																					if (!empty($data)) {
																						foreach ($data as $row) {
																							$files = $filedev->Get_DeviceFiles_ByID($row["filekey"]);
																							if (!empty($files)){
																								foreach ($files as $r) {
																									if(!empty($r['location'])){
																										$validation = "onclick='return validation()'";
																										echo ' <tr>';
																										echo ' <td>';
																										echo '<a href="' . $r['location'] . '"><span class="btn-text btn-default btn-icon-anim"><i class="icon-cloud-download"></i> ذخیره فایل </span></a>';
																										echo ' </td>';
																										echo '<td style ="width: 5%;">
																										<form method="post" enctype="multipart/form-data">
																											<input type="hidden" id="file_Id" name="file_Id" value="' . $r['ID'] . '">
																											<input type="hidden" id="action" name="action" value="remove_file">
																											<button class="btn btn-info btn-icon-anim" type="submit" name="remove_file" id="remove_file" ' . $validation . '><i class="icon-trash"></i> حذف </button>
																										</form>
																											</td>';
																										echo "</tr>";
																									} else {
																										echo '<tr><td colspan="2" style="text-align: center;"> دستگاه مستندی ندارد </td></tr>';
																									}
																										
																								}
																							} else {
																								echo '<tr><td colspan="2" style="text-align: center;"> دستگاه مستندی ندارد </td></tr>';
																							}
																						}
																					}else{
																						echo '<tr><td colspan="5" style="text-align: center;"> دستگاهی ثبت نشده است </td></tr>';
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
													<!-- /Table Hover -->

												</div>
												<br />
												<br />
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="editdevice" id="editdevice"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>

												</div>
											</form>
										</div>
									</div>
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
	<!-- /#wrapper -->
	<!-- JavaScript -->
	<?php MainJavasc(); ?>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

</body>

</html>