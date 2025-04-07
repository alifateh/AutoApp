<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/N_PhoneContactModel.php");


use fateh\Phonebook\MechanicContact as MechanicTell;
use fateh\Phonebook\GarageContact as GarageTell;
use fateh\Phonebook\IndependentContact as Independent;

$Mechanic_obj = new MechanicTell($_SESSION["Admin_GUID"]);
$Garage_obj = new GarageTell($_SESSION["Admin_GUID"]);
$Independent_obj = new Independent($_SESSION["Admin_GUID"]);

$Tell_Mechanic = $Mechanic_obj->V_MechanicsContacs();
$Tell_Garage = $Garage_obj->V_GarageContacs();
$Tell_Independent = $Independent_obj->V_IndependentContact();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if($_POST['ActionID'] == "RemoveContact"){
		$ContactGUID = $_POST['Contcat-ID'];
		$route = $Independent_obj ->D_IndependentContact_ByID($ContactGUID);
		if (isset($route) && $route == 1) {
			$url = "./V_Phonebook.php";
			header("Location: $url");
		} else {
			$Error_STR = 1;
		}
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

	<script LANGUAGE="JavaScript">
		function validation() {
			var agree = confirm("آیا از حذف [ شماره تلفن ] مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
		// -->
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
										<h6 class="panel-title txt-dark"> دفترچه تلفن </h6>
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
														<th> نام </th>
														<th> شناسه (کدملی / پلاک آبی) </th>
														<th> شماره </th>
														<th> کلید واژه </th>
														<th> عملیات </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th> نام </th>
														<th> شناسه (کدملی / پلاک آبی) </th>
														<th> شماره </th>
														<th> کلید واژه </th>
														<th> عملیات </th>
													</tr>
												</tfoot>
												<tbody>
													<?php
													if (!empty($Tell_Mechanic)) {
														foreach ($Tell_Mechanic as $row) {
															echo "<tr>";
															echo "<td>" . $row['FName'] . " " . $row['LName'] . "</td>";
															echo "<td>" . $row['UName'] . "</td>";
															echo "<td>" . $row['Number'] . "</td>";
															echo "<td>" . $row['Tag'] . "</td>";
															echo "<td></td>";
															echo "</tr>";
														}
													}

													if (!empty($Tell_Garage)) {
														foreach ($Tell_Garage as $row) {
															echo "<tr>";
															echo "<td>" . $row['Name'] . "</td>";
															echo "<td>" . $row['BlueNumber'] . "</td>";
															echo "<td>" . $row['Number'] . "</td>";
															echo "<td>" . $row['Tag'] . "</td>";
															echo "<td></td>";
															echo "</tr>";
														}
													}


													if (!empty($Tell_Independent)) {
														foreach ($Tell_Independent as $row) {
															echo "<tr>";
															echo "<td>" . $row['FirstName'] . " " . $row['LastName'] . "</td>";
															echo "<td> شماره مستقل </td>";
															echo "<td>" . $row['Number'] . "</td>";
															echo "<td>" . $row['Tags'] . "</td>";
															echo '<td>';
															echo '<div class="dropdown">
																<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
																	عملیات
																	<span class="caret"></span>
																</a>';
															echo '	<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">	
																		<li>
																			<form method="post" enctype="multipart/form-data" action="U_IndependentContact.php">
																				 <button style ="border:none;"><i class="icon-book-open"></i> ویرایش </button>
																				 <input type="hidden" id="Contcat-ID" name="Contcat-ID" value="' . $row['GUID'] . '">
																				 <input type="hidden" id="ActionID" name="ActionID" value="EditContact">
																			</form>
																		</li>
																		<li class="divider"></li>
																		<li>
																			<form method="post" enctype="multipart/form-data">
																			 <button style ="border:none;" onclick="return removevalidation()"><i class="icon-close"></i> حذف </button>
																				<input type="hidden" id="Contcat-ID" name="Contcat-ID" value="' . $row['GUID'] . '">
																				<input type="hidden" id="ActionID" name="ActionID" value="RemoveContact">
																			</form>
																		</li>
																	</ul>
															</div>';
															echo "</td>";
															echo "</tr>";
														}
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
	</div>
	<!-- JavaScript -->

	<!-- jQuery -->
	<?php MainJavasc(); ?>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
	<script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
	<script LANGUAGE="JavaScript">
		function removevalidation() {
			var agree = confirm("آیا از حذف عضو مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
	</script>
	<?php
	if (!empty($Error_STR)) {
		if ($Error_STR == 1) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا درسمت سرور',
			text: 'اطلاعات فرم ثبت نشده است لطفا با ادمین تماس بگیرید' ,
			position: 'top-center',
			loaderBg:'#ed3236',
			hideAfter: 6500,
			stack: 6
		});
		return false;
		});";
			echo '</script>';
		}
	}

	?>


</body>

</html>