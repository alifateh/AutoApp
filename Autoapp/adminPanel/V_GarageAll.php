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
require('Model/GarageModel.php');

use fateh\AutoShop\AutoShop as garage;

$Garage_obj = new garage($_SESSION["Admin_GUID"]);


if (isset($_POST['action'])) {
	if ($_POST['action'] == "remove") {
		$ID = $_POST['Garage-ID'];
		$Garage_obj->D_Garage_ByID($ID);
		$url = "./V_GarageAll.php";
		header("Location: $url");
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
										<h6 class="panel-title txt-dark"> فهرست تعمیرگاه ها </h6>
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
														<th> نام تعمیرگاه </th>
														<th>شماره پلاک شهرداری</th>
														<th> عملیات </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th> نام تعمیرگاه </th>
														<th>شماره پلاک شهرداری</th>
														<th> عملیات </th>
													</tr>
												</tfoot>
												<tbody>
													<?php
													$data = $Garage_obj->V_Garage();
													If(!empty($data)){
														foreach ($data as $row) {
															echo "<tr>";
															echo "<td>" . $row['Name'] . "</td>";
															echo "<td>" . $row['BlueNumber'] . "</td>";
															echo '<td>';
															echo '<div class="dropdown">
																	<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																		عملیات
																		<span class="caret"></span>
																		</a>
																		<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">';
															echo '
																 <li>
																	<form method="post" enctype="multipart/form-data" action="V_GarageProfile.php">
																		 <button style ="border:none;"><i class="icon-book-open"></i> پروفایل </button>
																		 <input type="hidden" name="Garage-ID" value="' . $row['GUID'] . '">
																	</form>
																</li>
																<li>
																<li class="divider"></li>
																<form method="post" enctype="multipart/form-data">
																 <button style ="border:none;" onclick="return removevalidation()"><i class="icon-close"></i> حذف تعمیرگاه </button>
																	<input type="hidden" name="Garage-ID" value="' . $row['GUID'] . '">
																	<input type="hidden" name="action" value="remove">
																</form>
																</li>
															</ul>
																</div>';
															echo "</td>";
															echo "</tr>";
														}
													}else{

														echo '<tr><td colspan="4" style="text-align: center;"> تعمیرگاهی ثبت نشده است </td></tr>';
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
		function removevalidation() {
			var agree = confirm("آیا از  حذف تعمیرگاه مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
	</script>

</body>

</html>