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
require_once("$rootDir/config/public_conf.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/GarageModel.php");
require("$rootDir/Model/N_PhoneContactModel.php");

use fateh\Member\Member as member;
use fateh\Phonebook\GarageContact as Tell;
use fateh\AutoShop\AutoShop as garage;

$Mechanic_Obj = new member($_SESSION["Admin_GUID"]);
$Garage_Obj = new garage($_SESSION["Admin_GUID"]);
$Tell_Obj = new Tell($_SESSION["Admin_GUID"]);

$G_Provinces = $Garage_Obj->Get_GarageProvinces();
$G_Devices = $Garage_Obj->Get_GarageDevicesAll();
$Deed_topic = $Garage_Obj->GetDeedTopic();
$Certificate_Status = $Garage_Obj->GetStatusTopic();
$Issuer_topic = $Garage_Obj->GetIssuerTopic();
$facility_topic = $Garage_Obj->GetFacilityTopic();
$Ownership_topic = $Garage_Obj->GetOwnershipTopic();

$Personel = $Mechanic_Obj->Get_MechanicAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['SaveGarage'])) {
		$Error_STR = 0;
		$Garage_Info = array();
		$Garage_Address = array();
		$Garage_Personel = array();
		$Garage_Devices = array();
		$Garage_Contact = array();
		$Garage_Facility = array();
		$Garage_Certification = array();
		$Garage_Social = array();
		$Garage_Experts = array();
		//############ info
		if (isset($_POST['Garage_Name']) && !empty($_POST['Garage_Name'])) {
			$Garage_Info[0] = $_POST['Garage_Name'];
		} else {
			$Error_STR = 2;
		}
		if (isset($_POST['G_PropertyOwnership']) && !empty($_POST['G_PropertyOwnership'])) { // نوع ملک
			$Garage_Info[1] = $_POST['G_PropertyOwnership'];
		} else {
			$Garage_Info[1] = 0;
		}

		if (isset($_POST['G_DeedStatus']) && !empty($_POST['G_DeedStatus'])) { //وضعیت سند
			$Garage_Info[2] = $_POST['G_DeedStatus'];
		} else {
			$Garage_Info[2] = 0;
		}
		if (isset($_POST['G_RegistrationNumber']) && !empty($_POST['G_RegistrationNumber'])) { //  شماره پلاک ثبتی
			$Garage_Info[3] = $_POST['G_RegistrationNumber'];
		} else {
			$Garage_Info[3] = 0;
		}
		
		if (isset($_POST['G_BluePelak']) && !empty($_POST['G_BluePelak'])) { //  شماره پلاک شهرداری
			$Garage_Info[4] = $_POST['G_BluePelak'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_Capacity']) && !empty($_POST['G_Capacity'])) {
			$Garage_Info[5] = $_POST['G_Capacity'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_Enrolment']) && !empty($_POST['G_Enrolment'])) {
			$Garage_Info[6] = $_POST['G_Enrolment'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_Area']) && !empty($_POST['G_Area'])) {
			$Garage_Info[7] = $_POST['G_Area'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_Tags']) && !empty($_POST['G_Tags'])) {
			$Garage_Info[8] = $_POST['G_Tags'];
		} else {
			$Garage_Info[8] = "";
		}

		//############ G_Address

		if (isset($_POST['G_Address']) && !empty($_POST['G_Address'])) { // آدرس
			$Garage_Address[0] = $_POST['G_Address'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_Longitude']) && !empty($_POST['G_Longitude'])) { //طول جغرافیایی
			$Garage_Address[1] = $_POST['G_Longitude'];
		} else {
			$Garage_Address[1] = 0;
		}
		if (isset($_POST['G_Latitude']) && !empty($_POST['G_Latitude'])) { // عرض جغرافیایی
			$Garage_Address[2] = $_POST['G_Latitude'];
		} else {
			$Garage_Address[2] = 0;
		}

		if (isset($_POST['G_PostalCode']) && !empty($_POST['G_PostalCode'])) { //کد پستی
			$Garage_Address[3] = $_POST['G_PostalCode'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['N_Province']) && !empty($_POST['N_Province'])) { //استان
			$Garage_Address[4] = $_POST['N_Province'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_City']) && !empty($_POST['G_City'])) { //شهر
			$Garage_Address[5] = $_POST['G_City'];
		} else {
			$Error_STR = 2;
		}

		if (isset($_POST['G_TellNumbers']) && count($_POST['G_TellNumbers']) > 0) { //تلفن
			$Garage_Contact = $_POST['G_TellNumbers'];
		} else {
			$Garage_Contact = [];
		}


		//############ G_Social

		if (isset($_POST['Instagram']) && !empty($_POST['Instagram'])) { // ID Instagram
			$Garage_Social[0] = $_POST['Instagram'];
		} else {
			$Garage_Social[0] = "";
		}

		if (isset($_POST['Telegram']) && !empty($_POST['Telegram'])) { // ID Telegram
			$Garage_Social[1] = $_POST['Telegram'];
		} else {
			$Garage_Social[1] = "";
		}

		if (isset($_POST['WhatsApp']) && !empty($_POST['WhatsApp'])) { // ID Telegram
			$Garage_Social[2] = $_POST['WhatsApp'];
		} else {
			$Garage_Social[2] = "";
		}

		//############ Certificate


		if (isset($_POST['G_CertificateIssuer']) && !empty($_POST['G_CertificateIssuer'])) {
			$Garage_Certification[0] = $_POST['G_CertificateIssuer'];
		} else {
			$Garage_Certification = [];
		}

		if (isset($_POST['G_CertificateStatus']) && !empty($_POST['G_CertificateStatus'])) {
			$Garage_Certification[1] = $_POST['G_CertificateStatus'];
		} else {
			$Garage_Certification = [];
		}
		if (isset($_POST['G_CertificationNumber']) && !empty($_POST['G_CertificationNumber'])) {
			$Garage_Certification[2] = $_POST['G_CertificationNumber'];
		} else {
			$Garage_Certification = [];
		}

		//############ Facility

		if (isset($_POST['G_Facility']) && count($_POST['G_Facility']) > 0) { //امکانات رفاهی
			$Garage_Facility = $_POST['G_Facility'];
		} else {
			$Garage_Facility = [];
		}

		//############ Special Device

		if (isset($_POST['SpecialDevice']) && count($_POST['SpecialDevice']) > 0) {
			$Garage_Devices = $_POST['SpecialDevice'];
		} else {
			$Garage_Devices = [];
		}
		//############ Personel and Exports

		if (isset($_POST['G_Experts']) && count($_POST['G_Experts']) > 0) {
			$Garage_Experts = $_POST['G_Experts'];
		} else {
			$Garage_Experts = [];
		}

		if (isset($_POST['G_Owner']) && !empty($_POST['G_Owner'])) {
			$Garage_Personel[0] = $_POST['G_Owner'];
		} else {
			$Garage_Personel[0] = ""; //
		}

		if (isset($_POST['cert_Owner']) && !empty($_POST['cert_Owner'])) {
			$Garage_Personel[1] = $_POST['cert_Owner'];
		} else {
			$Garage_Personel[1] = ""; //
		}

		if (isset($_POST['G_Manager']) && !empty($_POST['G_Manager'])) {
			$Garage_Personel[2] = $_POST['G_Manager'];
		} else {
			$Garage_Personel[2] = ""; //
		}

		if (isset($_POST['mobasher']) && !empty($_POST['mobasher'])) {
			$Garage_Personel[3] = $_POST['mobasher'];
		} else {
			$Garage_Personel[3] = ""; //
		}
		if ($Error_STR == 0) {
			$Garage = $Garage_Obj->C_Garage($Garage_Info);

			if ($Garage[0] == 1) {

				if (!empty($Garage_Address[0])) {
					$C_GarageAddressByID = $Garage_Obj->C_GarageAddressByID($Garage[1], $Garage_Address);
					if ($C_GarageAddressByID != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Certification) > 0 && !empty($Garage_Certification[0])) {
					$C_Certificate = $Garage_Obj->C_GarageCertification_ByID($Garage[1], $Garage_Certification);
					if ($C_Certificate != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Facility) > 0) {
					$C_GarageFacility = $Garage_Obj->C_GarageFacility_ByID($Garage[1], $Garage_Facility);
					if ($C_GarageFacility != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Personel) > 0) {
					$C_GaragePersonel = $Garage_Obj->C_GaragePersonel_ByID($Garage[1], $Garage_Personel);
					if ($C_GaragePersonel != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Devices) > 0) {
					$C_GargeSpecialDevices = $Garage_Obj->C_GargeSpecialDevices($Garage[1], $Garage_Devices);
					if ($C_GargeSpecialDevices != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Experts) > 0) {
					$C_GarageExperts = $Garage_Obj->C_GarageExperts_ByID($Garage[1], $Garage_Experts);
					if ($C_GarageExperts != true) {
						$Error_STR = 1;
					}
				}

				if (count($Garage_Social) > 0) {
					$C_GarageSocial = $Garage_Obj->C_GarageSocial_ByID($Garage[1], $Garage_Social);
					if ($C_GarageSocial != true) {
						$Error_STR = 1;
					}
				}

				if (isset($_POST['G_TellNumbers']) && !empty($_POST['G_TellNumbers'])) {
					$route = $Tell_Obj->C_GarageContact($Garage[1], 0, $_POST['G_TellNumbers']);
					if ($route == 1) {
						$url = "./V_GarageAll.php";
						header("Location: $url");
					} else {
						print_r($Garage);  // error trace
						echo $route;  // error trace
						$Error_STR = 1;
					}
				}
			} elseif ($Garage[0] == 0) {
				print_r($Garage);  // error trace
				$Error_STR = 1;
			}
		}
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag();?>

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

	<link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
	<!-- jquery-steps css -->
	<link rel="stylesheet" href="vendors/bower_components/jquery.steps/demo/css/jquery.steps.css">

	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />


	<!-- Custom CSS -->
	<link href="dist/css/style.css" rel="stylesheet" type="text/css">

	<script src="dist/js/jquery.min.js"></script>

	<script type="text/javascript">
		function onlyNumberKey(evt) {

			// Only ASCII charactar in that range allowed 
			var ASCIICode = (evt.which) ? evt.which : evt.keyCode
			if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
				return false;
			return true;
		}
		$(document).ready(function() {
			$('#N_Province').on('change', function() {
				var ProvinceID = $(this).val();
				if (ProvinceID != '') {
					$.ajax({
						type: 'POST',
						url: 'Ajax-ProvinceCity.php',
						data: {
							ProvinceID: ProvinceID
						},
						success: function(html) {
							$('#G_City').html(html);
						}
					});
				} else {
					$('#G_City').html('Error');
				}
			});
		});

		$(document).ready(function() {

			// allowed maximum input fields
			var max_input = 3;

			// initialize the counter for textbox
			var x = 1;

			// handle click event on Add More button
			$('.add-Telephone').click(function(e) {
				e.preventDefault();
				if (x < max_input) { // validate the condition
					x++; // increment the counter
					$('.Telephone').append(
						'<div class="col-sm-6"><label class="control-label mb-10"> شماره تلفن : </label>' +
						'<input type="text" class="form-control" id="G_TellNumbers[]" Name= "G_TellNumbers[]" onkeypress="return onlyNumberKey(event)"  placeholder="21" minlength="10" maxlength="10" >' +
						'<br /><a href="#" class="remove-Telephone btn btn-info btn-outline fancy-button btn-0">حذف کردن</a>' +
						'<br /></div>'); // add input field
				} else {
					alert('برای اضافه کردن شماره تلفن بیشتر با ادمین تماس بگیرید');
				}
			});

			// handle click event of the remove link
			$('.Telephone').on("click", ".remove-Telephone", function(e) {
				e.preventDefault();
				$(this).parent('div').remove(); // remove input field
				x--; // decrement the counter
			})

		});
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
							<form method="post" enctype="multipart/form-data" name="GarageReg" onsubmit="return validateForm()">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark"> ثبت نام تعمیرگاه </h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<div id="example-basic">
											<!-- example-basic -->
											<h3><span class="head-font capitalize-font"> عمومی </span></h3>
											<section>
												<div class="col-sm-12" style="padding-bottom: 10px;">
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label mb-10" for="Garage_Name"><i class="text-info mb-10">*</i> نام تعمیرگاه : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-home"></i></div>
																<input type="text" class="form-control" id="Garage_Name" name="Garage_Name" placeholder="نام تعمیرگاه" value="<?php echo $_POST['Garage_Name']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="garage_capacity"><i class="text-info mb-10">*</i> ظرفیت سرویس دهی (تعداد خودرو) : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="G_Capacity" name="G_Capacity" placeholder="ظرفیت سرویس دهی" value="<?php echo $_POST['G_Capacity']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_DeedStatus"> وضعیت سند : </label>
															<select class="selectpicker" name="G_DeedStatus" id="G_DeedStatus" data-style="form-control btn-default btn-outline">

																<?php
																
																foreach ($Deed_topic as $key) {
																	echo "<option value='". $key[0] ."'>" . $key[1] . "</option>";
																}
																?>
															</select>
														</div>
														<br>
														<div class="form-group">
															<div class="input-group">
																<div class="input-group-addon"> ID Telegram : </div>
																<input type="text" class="form-control" id="Telegram" name="Telegram" placeholder="ID Telegram">
															</div>
														</div>
														<div class="form-group">
															<div class="input-group">
																<div class="input-group-addon"> WhatsApp : </div>
																<input type="text" class="form-control" id="WhatsApp" name="WhatsApp" placeholder="ID WhatsApp">
															</div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label mb-10" for="G_Area"><i class="text-info mb-10">*</i> مساحت (متر مربع) : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="G_Area" name="G_Area" placeholder="مساحت" value="<?php echo $_POST['G_Area']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_Facility"> امکانات رفاهی : </label>
															<select class="selectpicker" multiple name="G_Facility[]" id="G_Facility[]" data-style="form-control btn-default btn-outline">
																<?php

																foreach ($facility_topic as $key) {
																	echo '<option value=' . $key[0] . '>' . $key[1] . '</option>';
																}
																?>

															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_PropertyOwnership"> نوع ملک : </label>
															<select class="selectpicker" data-style="form-control btn-default btn-outline" name="G_PropertyOwnership" id="G_PropertyOwnership">
																<?php

																if(!empty($Ownership_topic)){
																	foreach ($Ownership_topic as $key) {
																		echo '<option value="' . $key["ID"] . '" >' . $key["PersianName"] . '</option>';
																	}

																}


																?>
															</select>
														</div>
														<br>
														<div class="form-group">
															<div class="input-group">
																<div class="input-group-addon"> ID Instagram : </div>
																<input type="text" class="form-control" id="Instagram" name="Instagram" placeholder="ID Instagram">
															</div>
														</div>

														<div class="form-group">
															<label class="control-label mb-10 text-left" for="G_Tags"> کلمات کلیدی : </label>
															<input type="text" value="" data-role="tagsinput" id="G_Tags" name="G_Tags" placeholder="کلمات کلیدی">
														</div>
													</div>
												</div>
											</section>
											<h3><span class="head-font capitalize-font">آدرس</span></h3>
											<section>
												<div class="col-sm-12" style="padding-bottom: 10px;">
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label mb-10"><i class="text-info mb-10">*</i> استان : </label>
															<select class="form-control select2" dir="rtl" Name="N_Province" id="N_Province">
																<?php
																if (!empty($G_Provinces)) {
																	foreach ($G_Provinces as $row) {
																		echo '<option value= "'. $row['ID'] .'" >' . $row['Name'] . '</option>';
																	}
																} else {
																	echo '<option value=""> استانی تعریف نشده </option>';
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10"><i class="text-info mb-10">*</i> شهر : </label>
															<select class="form-control select2" dir="rtl" Name="G_City" id="G_City">
															</select>
														</div>

														<div class="form-group">
															<label class="control-label mb-10 text-left"><i class="text-info mb-10">*</i> نشانی : </label>
															<textarea class="form-control" rows="5" name="G_Address" id="G_Address"><?php echo $_POST['G_Address']; ?></textarea>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_RegistrationNumber"> شماره پلاک ثبتی : (شمارۀ اصلی/شمارۀ فرعی)</label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																<input type="text" class="form-control" name="G_RegistrationNumber" id="G_RegistrationNumber" placeholder="شمارۀ اصلی/شمارۀ فرعی" value="<?php echo $_POST['G_RegistrationNumber']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="zipCode"><i class="text-info mb-10">*</i> کد 10 رقمی پستی : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i>Zip Code</div>
																<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" name="G_PostalCode" id="G_PostalCode" placeholder="کد پستی" value="<?php echo $_POST['G_PostalCode']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_BluePelak"><i class="text-info mb-10">*</i> شماره پلاک شهرداری : (پلاک آبی ) </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" name="G_BluePelak" id="G_BluePelak" placeholder="شماره پلاک آبی" value="<?php echo $_POST['G_BluePelak']; ?>">
															</div>
														</div>
														<div class="form-group">
															<div class="input-group">
																<div class="input-group-addon"> عرض جغرافیایی : </div>
																<input type="text" class="form-control" id="G_Latitude" name="G_Latitude" placeholder="عرض جغرافیایی" value="<?php echo $_POST['G_Latitude']; ?>">
															</div>
														</div>
														<div class="form-group">
															<div class="input-group">
																<div class="input-group-addon"> طول جغرافیایی : </div>
																<input type="text" class="form-control" id="G_Longitude" name="G_Longitude" placeholder="طول جغرافیایی" value="<?php echo $_POST['G_Longitude']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10"> شماره تلفن : (اصلی) </label>
															<input type="text" class="form-control" id="G_TellNumbers[]" Name="G_TellNumbers[]" onkeypress="return onlyNumberKey(event)" placeholder="21" minlength="10" maxlength="10" value="<?php echo $_POST['G_TellNumbers[]']; ?>">
														</div>
														<br />
														<a class="add-Telephone btn btn-success btn-outline fancy-button btn-0"> اضافه کردن شماره تلفن</a>

														<div class="Telephone">
															<br />
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<div name="tehpart" id="tehpart"></div>
														</div>
													</div>
												</div>
											</section>
											<h3><span class="head-font capitalize-font"> اشخاص </span></h3>
											<section>
												<div class="col-sm-12" style="padding-bottom: 300px;">
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label mb-10" for="G_Enrolment"><i class="text-info mb-10">*</i> تعداد پرسنل : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="G_Enrolment" name="G_Enrolment" placeholder="تعداد پرسنل" value="<?php echo $_POST['G_Enrolment']; ?>">
															</div>
														</div>
														<div class="form-group">
															<label class="control-label mb-10"> مشخصات مالک : </label>
															<select dir="rtl" class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="G_Owner" id="G_Owner">
																<?php
																
																echo '<option value=""> انتخاب نمایید </option>';
																foreach ($Personel as $key) {
																	echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																}
																?>
															</select>

														</div>
														<div class="form-group">
															<label class="control-label mb-10"> صاحب جواز : </label>
															<select dir="rtl" class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="cert_Owner" id="cert_Owner">
																<?php
																
																echo '<option value=""> انتخاب نمایید </option>';
																foreach ($Personel as $key) {
																	echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																}
																?>
															</select>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label class="control-label mb-10"> مباشر : </label>
															<select dir="rtl" class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="mobasher" id="mobasher">
																<?php
																
																echo '<option value=""> انتخاب نمایید </option>';
																foreach ($Personel as $key) {
																	echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10"> مدیریت : </label>
															<select dir="rtl" class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="G_Manager" id="G_Manager">
																<?php
																
																echo '<option value=""> انتخاب نمایید </option>';
																foreach ($Personel as $key) {
																	echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10">کارشناسان : </label>
															<select class="select2 select2-multiple" multiple="multiple" data-placeholder="Choose" name="G_Experts[]" id="G_Experts[]">
																<?php
																
																echo '<option value=""> انتخاب نمایید </option>';
																foreach ($Personel as $key) {
																	echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																}
																?>s
															</select>
														</div>
													</div>
												</div>
											</section>
											<h3><span class="head-font capitalize-font"> تخصص ها </span></h3>
											<section>
												<div class="col-sm-12">
													<div class="form-group">
														<div class="form-group">
															<label class="control-label mb-10"> دستگاه های تخصصی : </label>
															<div class="panel-wrapper collapse in">
																<div class="panel-body">
																	<select multiple="multiple" id="SpecialDevice" name="SpecialDevice[]">
																		<optgroup label="اضافه کردن گروهی">
																			<?php
																			if (!empty($G_Devices)) {
																				foreach ($G_Devices as $row) {
																					echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																				}
																			} else {
																				echo '<option value=""> دستگاهی ثبت نشده است </option>';
																			}

																			?>
																		</optgroup>
																	</select>
																</div>
															</div>
															<script type="text/javascript">
																$('#SpecialDevice').multiSelect({
																	selectableOptgroup: true
																});
															</script>
														</div>
													</div>
												</div>
											</section>
											<h3><span class="head-font capitalize-font">پروانه کسب</span></h3>
											<section>
												<div class="col-sm-12" style="padding-bottom: 10px;">
													<div class="col-sm-4">
														<div class="form-group">
															<label class="control-label mb-10" for="G_CertificateIssuer"> صادر کننده جواز : </label>
															<select dir="rtl" class="form-control select2" name="G_CertificateIssuer" id="G_CertificateIssuer">
																<?php

																echo '<option value=""> انتخاب نمایید </option>';
																if(!empty($Issuer_topic)){
																	foreach ($Issuer_topic as $key) {
																		echo '<option value="' . $key[0] . '" >' . $key[1] . '</option>';
																	}
																}else{
																	echo "<option value='' > موردی ثبت نشده است </option>";
																}
																?>
															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_CertificateStatus"> وضعیت پروانه کسب : </label>
															<select dir="rtl" class="form-control select2" name="G_CertificateStatus" id="G_CertificateStatus">
																<?php

																echo '<option value=""> انتخاب نمایید </option>';
																if(!empty($Certificate_Status)){
																	foreach ($Certificate_Status as $key) {
																		echo '<option value="' . $key["ID"] . '" >' . $key["PersianName"] . '</option>';
																	}

																}else{
																	echo "<option value='' > موردی ثبت نشده است </option>";
																}
																
																?>
															</select>
														</div>
														<div class="form-group">
															<label class="control-label mb-10" for="G_CertificationNumber"> شماره جواز </label>
															<input type="text" class="form-control" name="G_CertificationNumber" id="G_CertificationNumber" placeholder="شماره جواز" value="<?php echo $_POST['G_CertificationNumber']; ?>">
															<br>
														</div>
														<div class="form-group">
															<button class="btn btn-info btn-anim" type="submit" name="SaveGarage" id="SaveGarage"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
														</div>
													</div>
											</section>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Row -->
			</div>
		</div>
		<!-- Main Content -->
		<div class="page-wrapper">

			<!-- Footer -->
			<?php footer(); ?>
			<!-- /Footer -->

		</div>
		<!-- /Main Content -->
	</div>
	</div>

	<!-- jQuery -->
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	<!-- Form Wizard JavaScript -->
	<script src="vendors/bower_components/jquery.steps/build/jquery.steps.min.js"></script>

	<!-- Form Wizard Data JavaScript -->
	<script src="dist/js/form-wizard-data.js"></script>


	<!-- Moment JavaScript -->
	<script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js"></script>

	<!-- Bootstrap Colorpicker JavaScript -->
	<script src="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

	<!-- Select2 JavaScript -->
	<script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

	<!-- Bootstrap Select JavaScript -->
	<script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

	<!-- Bootstrap Tagsinput JavaScript -->
	<script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

	<!-- Data table JavaScript -->
	<script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>

	<!-- Bootstrap Touchspin JavaScript -->
	<script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>

	<!-- Starrr JavaScript -->
	<script src="dist/js/starrr.js"></script>

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

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>

	<?php
	if (!empty($Error_STR)) {
		if ($Error_STR == 0) {
			echo '<script language="javascript">';
			echo '$(document).ready(function () {
	                $("#RegisterationForm").submit(function () {
	                    $(".disabled-btn").attr("disabled", true);
	                });
	            });';
			echo '</script>';
		}
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
	            text: 'فیلدهای ستاره دار باید مقدار مناسب بگیرند.' ,
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