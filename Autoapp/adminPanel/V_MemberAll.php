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
require_once('Model/MemberModel.php');

use fateh\Member\Member as member;

//initial First Value
$Mechanic_Obj = new member($_SESSION["Admin_GUID"]);
$data = $Mechanic_Obj->V_MemberAll();

if (isset($_POST['action'])) {
	if ($_POST['action'] == "remove") {
		$ID = $_POST['mem-ID'];
		$Mechanic_Obj->D_Mechanic_ByID($ID);
		$url = "./V_MemberAll.php";
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
										<h6 class="panel-title txt-dark"> فهرست اعضا </h6>
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
														<th> نام و نام خاوادگی </th>
														<th> کدملی </th>
														<th> عضویت </th>
														<th> جواز </th>
														<th> عملیات </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th> نام و نام خاوادگی </th>
														<th> کدملی </th>
														<th> عضویت </th>
														<th> جواز </th>
														<th> عملیات </th>
													</tr>
												</tfoot>
												<tbody>
													<?php

													if (!empty($data)) {

														foreach ($data as $row) {
															echo "<tr>";
															echo "<td>" . $row['FName'] . " " . $row['LName'] . "</td>";
															echo "<td>" . $row['UName'] . "</td>";
															if($row['Status'] == 1){
																echo '<td><button class="btn btn-default btn-icon-anim "> معتبر </button></td>';
															}else{
																echo '<td><button class="btn btn-warning btn-icon-anim "> نامعتبر </button></td>';
															}
															if($row['LicenseStatus'] == 1){
																echo '<td><button class="btn btn-default btn-icon-anim "> معتبر </button></td>';
															}else{
																echo '<td><button class="btn btn-danger btn-icon-anim "> نامعتبر </button></td>';
															}
															echo '<td>';
															echo '<div class="dropdown">
																<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																	عملیات
																	<span class="caret"></span>
																</a>';
															echo '	<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">	
																		<li>
																			<form method="post" enctype="multipart/form-data" action="V_MechanicProfile.php">
																				 <button style ="border:none;"><i class="icon-book-open"></i> پروفایل </button>
																				 <input type="hidden" id="mem-ID" name="mem-ID" value="' . $row['GUID'] . '">
																			</form>
																		</li>
																		<li class="divider"></li>
																		<li>
																			<form method="post" enctype="multipart/form-data">
																			 <button style ="border:none;" onclick="return removevalidation()"><i class="icon-close"></i> حذف عضو </button>
																				<input type="hidden" id="mem-ID" name="mem-ID" value="' . $row['GUID'] . '">
																				<input type="hidden" id="action" name="action" value="remove">
																			</form>
																		</li>
																	</ul>
															</div>';
															echo "</td>";
															echo "</tr>";
														}
													} else {
														echo '<tr><td colspan="3" style="text-align: center;"> عضوی ثبت نشده است </td></tr>';
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
			var agree = confirm("آیا از حذف عضو مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
	</script>

</body>

</html>