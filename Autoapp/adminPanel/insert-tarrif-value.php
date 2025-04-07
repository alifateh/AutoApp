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
require_once('Model/AutoModel.php');

use fateh\tariff\tariff as tariff;
use fateh\Automobile\Automobile as auto;

$aut_obj = new auto($_SESSION["Admin_GUID"]);
$tariff_obj = new tariff($_SESSION["Admin_GUID"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<!-- Jasny-bootstrap CSS -->
	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!-- jquery-steps css -->
	<link rel="stylesheet" href="vendors/bower_components/jquery.steps/demo/css/jquery.steps.css">

	<!-- bootstrap-touchspin CSS -->
	<link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

	<!-- Data table CSS -->
	<link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

	<link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

	<!-- Custom CSS -->
	<link href="dist/css/style.css" rel="stylesheet" type="text/css">

	<script src="dist/js/jquery.min.js"></script>

	<link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

	<script type="text/javascript">
		$(function() {
			$("#validationDate").persianDatepicker({
				formatDate: "YYYY-0M-0D"
			});
		});

		$(document).ready(function() {
			$('#autoname').on('change', function() {
				var manID = $(this).val();
				if (manID) {
					$.ajax({
						type: 'POST',
						url: 'ajax-tariff.php',
						data: 'man_id=' + manID,
						success: function(html) {
							$('#tariffitem').html(html);
						}
					});
				}
			});
		});

		$(document).ready(function() {
			$('#Manufacturer').on('change', function() {
				var manID = $(this).val();
				if (manID) {
					$.ajax({
						type: 'POST',
						url: 'ajax-tariff-tip.php',
						data: 'man_id=' + manID,
						success: function(html) {
							$('#autoname').html(html);
						}
					});
				}
			});
		});

		$(document).ready(function() {
			var max_fields = 10;
			var wrapper = $(".container1");
			var add_button = $(".add_form_field");

			var x = 1;
			$(add_button).click(function(e) {
				e.preventDefault();
				if (x < max_fields) {
					x++;
					$(wrapper).append('<br /><div>' +
						'<div class="input-group ">' +
						'<div class="input-group-addon"><i class="icon-info"></i></div>' +
						'<input type="text" class="form-control" name="newservicename[]" id="newservicename[]" placeholder=" نام سرویس ">' +
						'</div><br>' +
						'<div class="input-group">' +
						'<div class="input-group-addon"><i class="pe-7s-cash"></i></div>' +
						'<input type="number" class="form-control" id="addserviceprice[]" onkeypress="return onlyNumberKey(event)" name="addserviceprice[]" placeholder="قیمت به ریال">' +
						'</div>' +
						'<br /><a class="btn delete btn-pinterest btn-icon-anim"> حذف </a></div>'); //add input box
				} else {
					alert('شما 10 سرویس جدید اضافه کردید، چنانچه نیاز به اضافه کردن سرویس جدید است با ادمین تماس بگیرید.')
				}
			});

			$(wrapper).on("click", ".delete", function(e) {
				e.preventDefault();
				$(this).parent('div').remove();
				x--;
			})
		});

		function onlyNumberKey(evt) {

			// Only ASCII charactar in that range allowed 
			var ASCIICode = (evt.which) ? evt.which : evt.keyCode
			if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
				return false;
			} else {
				return true;
			}
		}
	</script>
	<?php

	header("Content-Type: text/html;charset=UTF-8");
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$add_tariff = new tariff($_SESSION["Admin_GUID"]);
		$version = $_POST['tariffver'];
		$autoname = $_POST['autoname'];
		$ItemID = $_POST['ItemID'];
		$serviceprice = $_POST['serviceprice'];
		$persiandate = $_POST['validationDate'];
		$add_serviceprice = $_POST['addserviceprice'];
		$add_servicename = $_POST['newservicename'];

		$route = $add_tariff->addvalue($version, $autoname, $ItemID, $serviceprice, $persiandate, $add_serviceprice, $add_servicename);
		if ($route == 1) {
			$url = "V_TariffValid.php";
			header("Location: $url");
		} else {
			echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
		}
	}
	?>
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
										<h6 class="panel-title txt-dark"> ثبت تعرفه سرویس ها </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data" accept-charset="utf-8">
										<div class="panel-body">
											<div id="example-basic">
												<h3><span class="head-font capitalize-font">شناسنامه</span></h3>
												<section>
													<div class="col-sm-12">
														<div class="col-sm-6">
															<div class="form-group">
																<div class="input-group col-sm-6">

																	<label class="control-label mb-10"> نام خودروساز : </label>
																	<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Manufacturer" id="Manufacturer">
																		<option value="0"> انتخاب نمایید </option>
																		<?php
																		$data = $aut_obj->V_AutoManufactures();
																		if (!empty($data)) {
																			foreach ($data as $row) {
																				echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																			}
																		} else {
																			echo '<option value=""> خودروسازی تعرف نشده است </option>';
																		}
																		?>

																	</select>
																</div>
															</div>
															<br>
															<br>


															<div class="form-group">
																<div class="input-group col-sm-6">
																	<label class="control-label mb-10" for="tariffver"> نسخه تعرفه : </label>
																	<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="tariffver" id="tariffver">
																		<?php
																		$data = $tariff_obj->Get_TariffVersion();
																		if (!empty($data)) {
																			$numItems = count($data);
																			$i = 0;
																			foreach ($data as $row) {
																				if (++$i === $numItems) {
																					echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
																				}else{
																					echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																				}
																				
																			}
																		} else {
																			echo '<option value=""> تعرفه ایی ثبت نشده </option>';
																		}
																		?>

																	</select>

																</div>
															</div>
															<br>
														</div>
														<div class="col-sm-6">
															<div class="form-group">
																<div class="input-group col-sm-6">
																	<div class="form-group">
																		<label class="control-label mb-10"> نام خودرو : </label>
																		<select class="form-control select2 select2-hidden-accessible" name="autoname" id="autoname"></select>
																	</div>
																</div>
																<br />
																<br />
																<div class="input-group col-sm-6">
																	<label class="control-label mb-10" for="validation-Date"> تاریخ اعتبار تعرفه : </label>
																	<div class="input-group">
																		<div class="input-group-addon"><i class="icon-info"></i></div>
																		<input type="text" class="form-control" id="validationDate" name="validationDate" placeholder=" 1403-01-15" value="1403-01-15">
																	</div>
																</div>
															</div>
														</div>
													</div>
												</section>
												<h3><span class="head-font capitalize-font">سرویس ها</span></h3>
												<section>
													<div class="col-sm-6">
														<div class="form-group">
															<div name="tariffitem" id="tariffitem">
															</div>
															<div class="row">
																<div class="container1">
																	<br />
																	<a class="btn add_form_field btn-facebook btn-icon-anim">اضافه کردن سرویس</a>
																	<br />
																	<div></div>
																	<br />
																</div>
															</div>
															<br />
															<button class="btn btn-info btn-anim" type="submit" name="submit_row"><i class="icon-check"></i><span class="btn-text"> ثبت </span></button>
														</div>
													</div>
												</section>
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

		<!-- /#wrapper -->
		<!-- JavaScript -->


		<?php MainJavasc(); ?>

		<script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
		<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
		<!-- Form Wizard JavaScript -->
		<script src="vendors/bower_components/jquery.steps/build/jquery.steps.min.js"></script>
		<!-- Form Wizard Data JavaScript -->
		<script src="dist/js/form-wizard-data.js"></script>
	</div>

</body>

</html>