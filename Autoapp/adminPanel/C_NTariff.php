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
require('Model/N_TariffModel.php');

use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

$Auto_Obj = new auto($_SESSION["Admin_GUID"]);
$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);


$V_TariffVer = $Tariff_Obj->V_AllTariffVersion();
$Error_STR = 0;


header("Content-Type: text/html;charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$version = $_POST['tariffver'];
	$Type = $_POST['TariffType'];
	$autoname = $_POST['autoname'];
	$GUID = md5(uniqid(mt_rand(100000, 999999), true));
	$OrderNum = 0;

	$persiandate = $_POST['validationDate'];
	if (!empty($persiandate)) {
		$fadate =  str_replace("-", "", $persiandate);
		$day = substr($fadate, strlen($fadate) - 2, 2);
		$mon = substr($fadate, strlen($fadate) - 4, 2);
		$year = substr($fadate, 0, 4);
		$ValidateDate = jalali_to_gregorian($year, $mon, $day, '-');
	}


	if (isset($_POST['ServiceGUID'])) {
		$Service_GUID = $_POST['ServiceGUID'];
	} else {
		$Service_GUID = array();
	}

	if (isset($_POST['serviceprice'])) {
		$service_Price = $_POST['serviceprice'];
	} else {
		$service_Price = array();
	}

	if (isset($_POST['ondemandPrice'])) {
		$Ondemand_SER_Price = $_POST['ondemandPrice'];
	} else {
		$Ondemand_SER_Price = array();
	}

	if (isset($_POST['ondemandSRV_NameFA'])) {
		$Ondemand_SER_FA = $_POST['ondemandSRV_NameFA'];
	} else {
		$Ondemand_SER_FA = array();
	}

	if (isset($_POST['ondemandSRV_NameEN'])) {
		$Ondemand_SER_EN = $_POST['ondemandSRV_NameEN'];
	} else {
		$Ondemand_SER_EN = array();
	}

	if (!empty($version) && !empty($Type)  && !empty($autoname) && !empty($ValidateDate)) {
		$C_NTariff = $Tariff_Obj->C_NTariff($GUID, $autoname, $version, $Type, $ValidateDate);

		if (!empty($C_NTariff) && $C_NTariff == 1) {
			if (!empty($service_Price) && !empty($Service_GUID)) {
				for ($i = 0; $i < count($Service_GUID); $i++) {
					if (empty($service_Price[$i])) {
						$service_Price[$i] = 0;
					}
					$Tariff_Obj->C_NTariffPrice($GUID, $service_Price[$i], $Service_GUID[$i]);
					$Tariff_Obj->C_NTariffOrder($GUID, $Service_GUID[$i], $OrderNum);
					$OrderNum++;
				}
			}

			if (!empty($Ondemand_SER_Price) && !empty($Ondemand_SER_FA)) {

				if (count($Ondemand_SER_Price) == count($Ondemand_SER_FA)) {
					$limit = count($Ondemand_SER_Price);
				} elseif (count($Ondemand_SER_Price) > count($Ondemand_SER_FA)) {
					$limit = count($Ondemand_SER_Price);
				} else {
					$limit = count($Ondemand_SER_FA);
				}


				for ($j = 0; $j < $limit; $j++) {
					if (!empty($Ondemand_SER_Price[$j]) && !empty($Ondemand_SER_FA[$j])) {
						$SER_OndemandGUID = $Tariff_Obj->C_NTariffOndemand($GUID, $Ondemand_SER_FA[$j], $Ondemand_SER_EN[$j]);
						if ($SER_OndemandGUID !== 0) {
							if (empty($Ondemand_SER_Price[$j])) {
								$Ondemand_SER_Price[$j] = 0;
							}
							$Tariff_Obj->C_NTariffPrice($GUID, $Ondemand_SER_Price[$j], $SER_OndemandGUID);
							$Tariff_Obj->C_NTariffOrder($GUID, $SER_OndemandGUID, $OrderNum);
						}
						$OrderNum++;
					}
				}
			}
		} else {
			$Error_STR = 1;
		}
	} else {
		$Error_STR = 2;
	}

	if (!empty($C_NTariff) && $C_NTariff == 1) {
		// return VALUES
		$url = "V_NTariffAll.php";
		header("Location: $url");
	} else {
		$Error_STR = 1;
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>

	<link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<!-- Jasny-bootstrap CSS -->
	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!-- jquery-steps css -->
	<link rel="stylesheet" href="vendors/bower_components/jquery.steps/demo/css/jquery.steps.css">

	<!-- bootstrap-touchspin CSS -->
	<link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

	<!-- Data table CSS -->
	<link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

	<!-- Custom CSS -->
	<link href="dist/css/style.css" rel="stylesheet" type="text/css">

	<script src="dist/js/jquery.min.js"></script>

	<link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

	<script type="text/javascript">
		$(document).ready(function() {
			$('#TariffType').on('change', function() {
				var TariffID = $(this).val();
				if (TariffID) {
					$.ajax({
						type: 'POST',
						url: 'Ajax-NTariff-Services.php',
						data: 'Tariff_GUID=' + TariffID,
						success: function(html) {
							$('#TariffHeader').html(html);
						}
					});
				}
			});
		});

		$(document).ready(function() {
			$('#autoname').on('change', function() {
				var AutoID = $(this).val();
				if (AutoID) {
					$.ajax({
						type: 'POST',
						url: 'Ajax-NTariff-Header.php',
						data: 'Auto_ID=' + AutoID,
						success: function(html) {
							$('#TariffHeader').html(html);
						}
					});
				}
			});
		});

		$(document).ready(function() {
			$('#autoname').on('change', function() {
				var AutoID = $(this).val();
				if (AutoID) {
					$.ajax({
						type: 'POST',
						url: 'Ajax-NTariff-Type.php',
						data: 'Auto_ID=' + AutoID,
						success: function(html) {
							$('#TariffType').html(html);
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
						url: 'Ajax-NTariff-Auto.php',
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
						'<input type="text" class="form-control" name="ondemandSRV_NameFA[]" id="ondemandSRV_NameFA[]" placeholder=" نام فارسی سرویس ">' +
						'<input type="text" class="form-control" name="ondemandSRV_NameEN[]" id="ondemandSRV_NameEN[]" placeholder=" نام انگلیسی سرویس ">' +
						'</div><br>' +
						'<div class="input-group">' +
						'<div class="input-group-addon"><i class="pe-7s-cash"></i></div>' +
						'<input type="number" class="form-control" id="ondemandPrice[]" onkeypress="return onlyNumberKey(event)" name="ondemandPrice[]" placeholder="قیمت به ریال">' +
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
										<h6 class="panel-title txt-dark"> ثبت مبلغ سرویس ها </h6>
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
														<div class="col-sm-3">
															<div class="input-group">
																<div class="form-group">

																	<label class="control-label mb-10"> نام خودروساز : </label>
																	<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Manufacturer" id="Manufacturer">
																		<option value="0"> انتخاب نمایید </option>
																		<?php
																		$data = $Auto_Obj->V_AutoManufactures();
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
														</div>
														<div class="col-sm-3">
															<div class="input-group">
																<div class="form-group">
																	<label class="control-label mb-10"> نام خودرو : </label>
																	<select class="form-control select2 select2-hidden-accessible" name="autoname" id="autoname"></select>
																</div>
															</div>
														</div>

														<div class="col-sm-3">
															<div class="input-group ">
																<div class="form-group">
																	<label class="control-label mb-10"> نوع تعرفه : </label>
																	<select class="form-control select2 select2-hidden-accessible" name="TariffType" id="TariffType"></select>
																</div>
															</div>
															<br />

															<div class="input-group">
																<div class="form-group">
																	<label class="control-label mb-10" for="tariffver"> نسخه تعرفه : </label>
																	<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="tariffver" id="tariffver">
																		<?php
																		if (!empty($V_TariffVer)) {
																			$numItems = count($V_TariffVer);
																			$i = 0;
																			foreach ($V_TariffVer as $row) {
																				if (++$i === $numItems) {
																					echo '<option value="' . $row['GUID'] . '" selected >' . $row['NameFa'] . '</option>';
																				} else {
																					echo '<option value="' . $row['GUID'] . '">' . $row['NameFa'] . '</option>';
																				}
																			}
																		} else {
																			echo '<option value=""> تعرفه ایی ثبت نشده </option>';
																		}
																		?>
																	</select>

																</div>
															</div>
															<br />
															<br />
															<div class="input-group">
																<label class="control-label mb-10" for="validation-Date"> تاریخ اعتبار تعرفه : </label>
																<div class="form-group">
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
													<div class="col-sm-12">
														<div class="col-sm-6">
															<div name="TariffHeader" id="TariffHeader">
																<div name="tariffitem" id="tariffitem"></div>
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

	</div>

	<?php MainJavasc(); ?>

	<script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
	<!-- Form Wizard JavaScript -->
	<script src="vendors/bower_components/jquery.steps/build/jquery.steps.min.js"></script>
	<!-- Form Wizard Data JavaScript -->
	<script src="dist/js/form-wizard-data.js"></script>

	<script type="text/javascript">
		$(function() {
			$("#validationDate").persianDatepicker({
				formatDate: "YYYY-0M-0D"
			});
		});
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

		if ($Error_STR == 2) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
					$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
					$.toast({
						heading: 'خطا در ثبت اطلاعات',
						text: 'فیلدهای خالی مانده را پرنمایید.' ,
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