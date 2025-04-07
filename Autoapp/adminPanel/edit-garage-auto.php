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
require('Model/AutoModel.php');
require('Model/GarageModel.php');


use fateh\Automobile\Automobile as auto;
use fateh\AutoShop\AutoShop as garage;

$Garage_obj = new garage($_SESSION["Admin_GUID"]);
$auto_obj = new auto($_SESSION["Admin_GUID"]);

if (!isset($_POST['Edit'])) {
	if (isset($_SESSION["Garage-ID"])) {
		$Garage_GUID = $_SESSION["Garage-ID"];
		$G_Autos = $auto_obj->Get_GarageAuto_ByGUID($Garage_GUID);
		$G_NotAutos = $auto_obj->Get_GarageNOTAuto_ByGUID($Garage_GUID);
	} else {
		$url = "./V_GarageAll.php";
		header("Location: $url");
	}
}

if (isset($_POST['Edit'])) {
	$_SESSION["Garage-ID"] = $_POST['GUID'];
	$Garage_GUID = $_POST['GUID'];
	if (isset($_POST['optgroup'])) {
		$Garage_obj->U_GarageAuto_ByID($Garage_GUID, $_POST['optgroup']);
	} else {
		$Garage_obj->D_GarageAuto_ByID($Garage_GUID);
	}
	$url = "./V_GarageProfile.php";
	header("Location: $url");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>

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
										<h6 class="panel-title txt-dark"> ویرایش خودروهای تخصصی </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" id="GUID" name="GUID" value="<?php echo $Garage_GUID; ?>">
										<div id="example-basic">
											<div class="col-sm-12">

												<div class="form-group">
													<label class="control-label mb-10" for="exampleInputuname_1"> فهرست خودروها : </label>
												</div>

												<div class="panel-wrapper collapse in">
													<div class="panel-body">
														<div class="row mt-40">
															<div class="col-sm-12">
																<select multiple id="optgroup" name="optgroup[]" multiple='multiple'>
																	<?php

																	if (!empty($G_Autos)) {
																		$Manufacturer_All = $auto_obj->V_AutoManufactures();

																		foreach ($Manufacturer_All as $row) {
																			echo '<optgroup label=" اضافه کردن گروه خودرهای [' . $row['Name'] . ']">';
																			foreach ($G_Autos as $g_cars) {
																				$Auto_info = $auto_obj->Get_Automobile_ByID($g_cars['AutoID']);
																				if ($row['ID'] == $Auto_info[0]['ManufacturerID']) {
																					$select = "selected";
																					$Auto_Tip =  $auto_obj->Get_Tip_ByID($Auto_info[0]['ModelID']);
																					if ($Auto_Tip[0]["ModelName"] == "") {
																						$tip = "بدون تیپ";
																					} else {
																						$tip =  $Auto_Tip[0]["ModelName"];
																					}
																					echo '<option value="' . $Auto_info[0]['ID'] . '"' . $select . '>' . $Auto_info[0]['Name'] . ' [' . $tip . '] </option>';
																				}
																			}
																			foreach ($G_NotAutos as $g_NoTcars) {
																				if ($row['ID'] == $g_NoTcars['ManufacturerID']) {
																					$Auto_Tip =  $auto_obj->Get_Tip_ByID($g_NoTcars['ModelID']);
																					if ($Auto_Tip[0]["ModelName"] == "") {
																						$tip = "بدون تیپ";
																					} else {
																						$tip =  $Auto_Tip[0]["ModelName"];
																					}
																					echo '<option value="' . $g_NoTcars['ID'] . '">' . $g_NoTcars['Name'] . ' [' . $tip . '] </option>';
																				}
																			}
																			echo '</optgroup>';
																		}
																	} else {
																		$Auto_All = $auto_obj->V_Automobiles();
																		$Manufacturer_All = $auto_obj->V_AutoManufactures();
																		if (!empty($Auto_All)) {
																			foreach ($Manufacturer_All as $Man) {
																				echo '<optgroup label=" اضافه کردن گروه خودرهای [' . $Man['Name'] . ']">';
																				foreach ($Auto_All as $key) {
																					if ($key['ManufacturerID'] == $Man['ID']) {
																						$Auto_Tip =  $auto_obj->Get_Tip_ByID($key['ModelID']);
																						if ($Auto_Tip[0]["ModelName"] == "") {
																							$tip = "بدون تیپ";
																						} else {
																							$tip =  $Auto_Tip[0]["ModelName"];
																						}
																						echo '<option value="' . $key['ID'] . '">' . $key['Name'] . ' [' . $tip . '] </option>';
																					}
																				}
																			}
																		} else {
																			echo '<optgroup label=""> خودرویی ثبت نشده </optgroup>';
																		}
																	}
																	?>
																</select>
															</div>

														</div>
													</div>
												</div>
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="Edit" id="Edit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
	<!-- JavaScripts -->

	<!-- jQuery -->
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Moment JavaScript -->
	<script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js"></script>

	<!-- Bootstrap Colorpicker JavaScript -->
	<script src="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

	<!-- Select2 JavaScript -->
	<script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

	<!-- Bootstrap Select JavaScript -->
	<script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

	<!-- Bootstrap Tagsinput JavaScript -->
	<script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

	<!-- Bootstrap Touchspin JavaScript -->
	<script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>

	<!-- Multiselect JavaScript -->
	<script src="vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>

	<!-- Bootstrap Switch JavaScript -->
	<script src="vendors/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>

	<!-- Bootstrap Datetimepicker JavaScript -->
	<script type="text/javascript" src="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Form Advance Init JavaScript -->
	<script src="dist/js/form-advance-data.js"></script>

	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>

	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>

	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
</body>

</html>