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

require('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;



if (isset($_POST['Edit'])) {
	//all
	$tariff_ID = $_POST['tariff-ID'];
	$tariff_SecID = $_POST['SecID'];

	//
	$update_tariff = new tariff($_SESSION["Admin_GUID"]);

	//New service and price

	if (!empty($_POST['newservicename']) and !empty($_POST['Newserviceprice'])) {
		$new_serviceName = $_POST['newservicename'];
		$new_servicePrice = $_POST['Newserviceprice'];
		$update_tariff->tariffadditem($new_servicePrice, $new_serviceName, $tariff_SecID);
	}


	//Tariff_Added_Item
	if (!empty($_POST['addedsrvName']) and isset($_POST['addedsrvPrice'])) {
		$add_serviceName = $_POST['addedsrvName'];
		$add_servicePrice = $_POST['addedsrvPrice'];
		$add_serviceID = $_POST['AddItemID'];

		for ($i = 0; $i < count($add_serviceID); $i++) {
			if ($add_servicePrice[$i] !== "") {
				$update_tariff->updateAddSrv($add_serviceID[$i], $add_serviceName[$i], $add_servicePrice[$i]);
			}
		}
	}

	//Tariff_Value
	if (isset($_POST['serviceprice'])) {

		$serviceprice = $_POST['serviceprice'];
		$serviceID = $_POST['ItemID'];
		for ($i = 0; $i < count($serviceID); $i++) {
			if ($serviceprice[$i] !== "") {
				$update_tariff->updateSrv($serviceID[$i], $serviceprice[$i], $tariff_SecID);
			}
		}
	}

	$update_tariff->CleanOrderTBL($tariff_SecID, 1);
	$update_tariff->CleanOrderTBL($tariff_SecID, 2);
	$update_tariff->maketarifforder($tariff_SecID);
	$url = "./V_TariffValid.php";
	header("Location: $url");
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
	<script src="dist/js/jquery.min.js"></script>

	<script type="text/javascript">
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
						'<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="Newserviceprice[]" name="Newserviceprice[]" placeholder="قیمت به ریال">' +
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
			if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
				return false;
			return true;
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
										<?php
										if (!isset($_POST['Edit'])) {
											if (!empty($_POST['tariff-ID'])) {
												$edit_tariff = new tariff($_SESSION["Admin_GUID"]);
												$ID = $_POST['tariff-ID'];
												$SecID = $_POST['tariff-SecID'];
												$tariff_detail = $edit_tariff->gettariffDetial($ID);
												$Vdate = $edit_tariff->gettariffdateinhejri($tariff_detail[2]);
												$tariff_date = gregorian_to_jalali($Vdate[0], $Vdate[1], $Vdate[2]);
												$ver = $edit_tariff->getversion($tariff_detail[1]);

												$tariff_auto = $edit_tariff->gettariffauto($tariff_detail[0]);
												$man_name = "خودروساز : " . $edit_tariff->gettariffautoman($tariff_auto[0]);
												if ($tariff_auto[1] !== 0) {
													$tip_name = $edit_tariff->gettariffautotip($tariff_auto[1]);
													$tip = " تیپ " . $tip_name;
												} else {
													$tip = "بدون تیپ";
												}
												$auto_name = " نام خودرو: " . $tariff_auto[2];
												$ver = $edit_tariff->getversion($tariff_detail[1]);
												echo '<h6 class="panel-title txt-dark"> مشخصات خودور [ ' . $man_name . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>
														<h6 class="panel-title txt-dark"> تاریخ اعتبار : ' . $tariff_date[0] . "/" . $tariff_date[1] . "/" . $tariff_date[2] . '</h6>
															<h6 class="panel-title txt-dark"> نسخه تعرفه : ' . $ver . '</h6>';
											} else {
												echo '<div class="alert alert-info alert-dismissable">
															<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><a href="V_TariffValid.php"> لطفا به فهرست تعرفه ها برگردید </a> 
														</div>';
											}
										}


										?>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap">
										<form method="post" enctype="multipart/form-data" accept-charset="utf-8">
											<input type="hidden" id="tariff-ID" name="tariff-ID" value="<?php echo $_POST['tariff-ID']; ?>">
											<input type="hidden" id="SecID" name="SecID" value="<?php echo $_POST['tariff-SecID']; ?>">
											<div class="table-responsive">
												<?php

												if (!empty($_POST['tariff-ID'])) {
													$edit_tariff = new tariff($_SESSION["Admin_GUID"]);
													$edit_tariff->EditeViewTariff($ID, $SecID);
												} else {
													echo " <a href='V_TariffValid.php'> لطفا به فهرست تعرفه ها برگردید </a>";
												}
												?>
												<div class="row">
													<div class="col-sm-12">
														<div class="col-sm-6">
															<div class="form-group">
																<div class="input-group col-sm-6">
																	<div class="container1">
																		<br />
																		<a class="btn add_form_field btn-facebook btn-icon-anim">اضافه کردن سرویس</a>
																		<br />
																		<div></div>
																		<br />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<br />
												<button class="btn btn-info btn-anim" type="submit" name="Edit" id="Edit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
											</div>

										</form>
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


	</div>
</body>

</html>