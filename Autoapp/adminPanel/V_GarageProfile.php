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
require("$rootDir/Model/GarageModel.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/N_PhoneContactModel.php");
require("$rootDir/Model/AutoModel.php");


use fateh\Member\Member as member;
use fateh\Phonebook\GarageContact as Tell;
use fateh\AutoShop\AutoShop as garage;
use fateh\Automobile\Automobile as auto;

$Garage_obj = new garage($_SESSION["Admin_GUID"]);
$Garage_Personnel = new member($_SESSION["Admin_GUID"]);
$Phone_Obj = new Tell($_SESSION["Admin_GUID"]);
$auto_obj = new auto($_SESSION["Admin_GUID"]);

if (isset($_POST['Garage-ID'])) {

	$_SESSION["Garage-ID"] = "";
	$Garage_GUID = $_POST['Garage-ID'];
	$_SESSION["Garage-ID"] = $_POST['Garage-ID'];
} elseif (isset($_SESSION["Garage-ID"]) && $_SESSION["Garage-ID"] != "") {

	$Garage_GUID = $_SESSION["Garage-ID"];
} else {
	$url = "./V_GarageAll.php";
	header("Location: $url");
}

if (!empty($Garage_GUID)) {


	//#####################################
	//######################## Get_Garage
	//#####################################	
	$Garage_info = $Garage_obj->Get_GarageInfo_ByID($Garage_GUID);
	$Mem_List = $Garage_obj->GetMemGUID($Garage_GUID);
	$Garage_address = $Garage_obj->Get_GarageAddress_ByID($Garage_GUID);
	$Garage_Certificate = $Garage_obj->GetCertbyGUID($Garage_GUID);
	$Garage_Facility = $Garage_obj->GetGarageFacility($Garage_GUID);
	$Profile_photo = $Garage_obj->GetGaragePhoto($Garage_GUID);
	$phone = $Phone_Obj->Get_GarageContact_ByID($Garage_GUID);

	$auto = $auto_obj->Get_GarageAuto_ByGUID($Garage_GUID);


	$files = $Garage_obj->GetGarageFiles($Garage_GUID);
	$Service = $Garage_obj->GetGarageService($Garage_GUID);
	$Devices = $Garage_obj->GetGarageSpecialDevice($Garage_GUID);


	$Garage_Factor1 = $Garage_obj->GetGarageCountSDevice($Garage_GUID);
	$Garage_Factor2 = $Garage_obj->GetGarageCountService($Garage_GUID);
	$Garage_Factor3 = $Garage_obj->GetGarageCountCert($Garage_GUID);
	$Garage_Factor4 = $auto_obj->Get_AutoCount_ByGarageID($Garage_GUID);
	$Garage_Specialist = $Garage_Factor1 + $Garage_Factor2;
	$Garage_Rate = $Garage_Factor1 + $Garage_Factor2 + $Garage_Factor3 + $Garage_Factor4;
	//#################################################################
	//####################### Set Session Fro Map
	//#################################################################
	$_SESSION["Garage_lat"] = $Garage_address[4]; //عرض جغرافیایی
	$_SESSION["Garage_long"] = $Garage_address[3]; //طول جغرافیایی
	$_SESSION["Garage_address"] = $Garage_address[2];
	$_SESSION["Garage_Name"] = $Garage_info[1];
	$_SESSION["GUID"] = $Garage_GUID;

	////////////////////////////////////////////////

	$G_ProvincesAll = $Garage_obj->Get_GarageProvinces();
	$G_CityProvince = $Garage_obj->Get_GarageCity_ByProvinceID($Garage_address[6]);

	$Province = $Garage_obj->Get_GarageProvince_ByID($Garage_address[6]);
	$city = $Garage_obj->Get_GarageCity_ByGUID($Garage_address[7]);
}
if ($_SERVER['REQUEST_METHOD'] === "POST") {
	//################################################
	//################################################ ADD
	//################################################
	if (isset($_POST['ActionID']) && $_POST['ActionID'] == "C_Personnel") {
		$GUID = $_POST['GarageID'];
		$Error_STR = 0;
		if (isset($_POST['PersonnelGUID']) && !empty($_POST['PersonnelGUID'])) {
			$PersonnelGUID = $_POST['PersonnelGUID'];
		} else {
			$Error_STR = 2;
		}
		if (isset($_POST['PersonnelRole']) && !empty($_POST['PersonnelRole'])) {
			$PersonnelRole = $_POST['PersonnelRole'];
		} else {
			$Error_STR = 2;
		}
		if ($Error_STR == 0) {
			$Garage_obj->RegGarage_People($GUID, $PersonnelGUID, $PersonnelRole);
			echo "<meta http-equiv='refresh' content='0'>";
		} else {
			$Error_STR = 1;
		}
	}

	if (isset($_POST['ActionID']) && $_POST['ActionID'] == "C_Contatct") {
		$GUID = $_POST['GarageID'];
		$TellNum = array();
		$Error_STR = 0;
		if (isset($_POST['AddOperator']) && !empty($_POST['AddOperator'])) {
			$Operator = $_POST['AddOperator'];
		} else {
			$Operator = 0;
		}
		if (isset($_POST['N_ContactNum']) && !empty($_POST['N_ContactNum'])) {
			$TellNum[0] = $_POST['N_ContactNum'];
		} else {
			$Error_STR = 2;
		}

		if ($Error_STR == 0) {
			$route = $Phone_Obj->C_GarageContact($GUID, $Operator, $TellNum);
			if (isset($route) && $route == 1) {
				echo "<meta http-equiv='refresh' content='0'>";
			} else {
				$Error_STR = 1;
			}
		}
	}

	//######################################
	//###################################### Remove 
	//######################################
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>

	<!-- Jasny-bootstrap CSS -->
	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

	<!-- Data table CSS -->
	<link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

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

		function validAddCertForm() {

			var x = document.forms["AddCertform"]["CertIssuer"].value;
			if (x == "") {
				alert("مقدار [صادر کننده جواز] خالی می باشد");
				return false;
			}

			var x = document.forms["AddCertform"]["CertStatus"].value;
			if (x == "") {
				alert("مقدار [وضعیت پروانه کسب] خالی می باشد");
				return false;
			}

		}
	</script>
	<?php

	if ($_SERVER['REQUEST_METHOD'] === "POST") {

		//######################################
		//###################################### Remove 
		//######################################


		if (isset($_POST['Removephone']) && isset($_POST['Form_Action'])) {
			if ($_POST['Form_Action'] == "RemoveGaragePhone") {
				$phoneID = $_POST['phoneID'];
				$route = $Phone_Obj->D_GarageContact_ByID($phoneID);
				if ($route == true) {
					echo "<meta http-equiv='refresh' content='0'>";
				} else {
					$Error_STR = 1;
				}
			}
		}

		if (isset($_POST['RemoveFile'])) {
			$GarageGUID = $_POST['GarageGUID'];
			$file_ID = $_POST['FileID'];
			$Garage_obj->D_GarageDoc_ByID($GarageGUID, $file_ID);
			echo "<meta http-equiv='refresh' content='0'>";
		}

		if (isset($_POST['RemoveCert'])) {
			$GarageGUID = $_POST['GarageGUID'];
			$CertID = $_POST['CertID'];
			$Garage_obj->D_GarageCert_ByID($GarageGUID, $CertID);
			echo "<meta http-equiv='refresh' content='0'>";
		}

		if (isset($_POST['Removepersonnel'])) {
			$ID = $_POST['PersonnelID'];
			$GarageGUID = $_POST['GarageGUID'];
			$Garage_obj->D_GaragePersonel_ByID($GarageGUID, $ID);
			echo "<meta http-equiv='refresh' content='0'>";
		}

		//######################################
		//###################################### Edite
		//######################################

		if (isset($_POST['Edite-Location'])) {
			$GarageGUID = $_POST['GarageGUID'];
			if (!empty($_POST['Latitude'])) { // عرض جغرافیایی
				$New_Latitude = $_POST['Latitude'];
				$Garage_obj->U_GarageLatitude_ByID($GarageGUID, $New_Latitude);
			}
			if (!empty($_POST['Longitude'])) { // طول جغرافیایی
				$New_Longitude = $_POST['Longitude'];
				$Garage_obj->U_GarageLongitude_ByID($GarageGUID, $New_Longitude);
			}

			echo "<meta http-equiv='refresh' content='0'>";
		}

		if (isset($_POST['Edite-Address'])) {
			$GarageGUID = $_POST['GarageGUID'];
			if (!empty($_POST['Blue_Num'])) {
				$New_Blue_Num = $_POST['Blue_Num'];
				$Garage_obj->U_GarageBlueNum_ByID($GarageGUID, $New_Blue_Num);
			}
			if (!empty($_POST['PostalCode'])) {
				$New_PostalCode = $_POST['PostalCode'];
			} else {
				$New_PostalCode = $_POST['Old-PostalCode'];
			}

			if (!empty($_POST['address'])) {
				$New_address = $_POST['address'];
			} else {
				$New_address = $_POST['Old-address'];
			}


			if (isset($_POST['N_Province']) && !empty($_POST['N_Province'])) { //استان
				$New_ProvinceID = $_POST['N_Province'];
			} else {
				$New_ProvinceID = $_POST['Old-ProvinceID'];
			}

			if (isset($_POST['N_City']) && !empty($_POST['N_City'])) { //شهر
				$New_CityID = $_POST['N_City'];
			} else {
				$New_CityID = $_POST['Old-CityGUID'];
			}

			$Address_Update = array();

			$Address_Update[0] = $New_address;
			$Address_Update[1] = $New_PostalCode;
			$Address_Update[2] = $New_CityID;
			$Address_Update[3] = $New_ProvinceID;

			$Garage_obj->U_GarageAddress_ByID($GarageGUID, $Address_Update);
			echo "<meta http-equiv='refresh' content='0'>";
		}
	}


	if (isset($_POST['AddCert'])) {
		$GUID = $_POST['GarageID'];
		if (!empty($_POST['CertIssuer'])) {
			$CertIssuer = $_POST['CertIssuer'];
		}
		if (!empty($_POST['CertStatus'])) {
			$CertStatus = $_POST['CertStatus'];
		}
		if (!empty($_POST['CertNum'])) {
			$CertNum = $_POST['CertNum'];
		} else {
			$CertNum = 0;
		}

		$Garage_obj->RegGarage_Certificates($GUID, $CertIssuer, $CertStatus, $CertNum);
		echo "<meta http-equiv='refresh' content='0'>";
	}

	if (isset($_POST['insertdoc'])) {
		$guid = $_POST['Garage_GUID'];
		// Count # of uploaded files in array
		$total = count($_FILES['upload']['name']);

		// Loop through each file
		for ($i = 0; $i < $total; $i++) {

			//Get the temp file path
			$tmpFilePath = $_FILES['upload']['tmp_name'][$i];

			//Make sure we have a file path
			if ($tmpFilePath != "") {
				//Setup our new file path
				$temp = explode(".", $_FILES["upload"]["name"][$i]);
				$newfilename = round(microtime(true)) . '.' . end($temp);
				$newFilePath = "images/GarageDoc/" . $newfilename;
				$imageFileType = strtolower(pathinfo($newFilePath, PATHINFO_EXTENSION));

				if ($_FILES["upload"]["size"][$i] < 500000) {
					if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {

						//Upload the file into the temp dir
						if (move_uploaded_file($tmpFilePath, $newFilePath)) {

							//Handle other code here
							$Garage_obj->RegisterGarage_Files($guid, $newFilePath);
							echo "<meta http-equiv='refresh' content='0'>";
						}
					} else {
						echo '<script language="javascript">';
						echo 'alert("نوع فایل مورد قبول نمی باشد")';
						echo '</script>';
					}
				} else {
					echo '<script language="javascript">';
					echo 'alert("سایز فایل بزرگتر از 5 مگابایت می باشد")';
					echo '</script>';
				}
			}
		}
	}
	if (isset($_POST['UpdateProfile'])) {
		$guid = $_POST['Garage_GUID'];

		if (file_exists($_FILES['Newphoto']['tmp_name']) || is_uploaded_file($_FILES['Newphoto']['tmp_name'])) {

			$target_dir = "images/Garage_Photo/";
			$temp = explode(".", $_FILES["Newphoto"]["name"]);
			$newfilename = round(microtime(true)) . '.' . end($temp);

			$target_file = $target_dir . $newfilename;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			// Check if image file is a actual image or fake image

			$check = filesize($_FILES["Newphoto"]["tmp_name"]);
			if ($check !== false) {
				//echo "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				// echo "File is not an image.";
				$uploadOk = 0;
			}


			// Check if file already exists
			if (file_exists($target_file)) {
				echo '<script language="javascript">';
				echo 'alert("فایلی با این نام در سرور موجود میباشد")';
				echo '</script>';
				$uploadOk = 0;
			}

			// Check file size
			if ($_FILES["Newphoto"]["size"] > 500000) {
				echo '<script language="javascript">';
				echo 'alert("سایز فایل بیشتر از 50 مگابایت می باشد")';
				echo '</script>';
				$uploadOk = 0;
			}

			// Allow certain file formats

			if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
				echo '<script language="javascript">';
				echo 'alert("لطفا قالب فایل را از بین قالب های")';
				echo '</script>';
				$uploadOk = 0;
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				// echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file

			} else {
				if (move_uploaded_file($_FILES["Newphoto"]["tmp_name"], $target_file)) {
					//add with file

					$Garage_obj->U_GarageDoc_ByID($guid, $target_file);
					echo "<meta http-equiv='refresh' content='0'>";
				} else {
					echo '<script language="javascript">';
					echo 'alert("مشکلی در آپلود فایل بوجود آمده است")';
					echo '</script>';
				}
			}
		} else {
			$Garage_obj->U_GarageDoc_ByID($guid, "images/Garage_Photo/Grage_Defualt.jpg");
			echo "<meta http-equiv='refresh' content='0'>";
		}
	}


	if (isset($_POST['Edite-GarageInfo'])) {
		$GarageGUID = $_POST['GarageGUID'];
		if (!empty($_POST['GarageName'])) {
			$New_GarageName = $_POST['GarageName'];
		} else {
			$New_GarageName = $_POST['Old-Name'];
		}
		if (!empty($_POST['Garage-Area'])) {
			$New_Area = $_POST['Garage-Area'];
		} else {
			$New_Area = $_POST['Old-Area'];
		}

		if (!empty($_POST['Garage-Capacity'])) {
			$New_Capacity = $_POST['Garage-Capacity'];
		} else {
			$New_Capacity = $_POST['Old-Capacity'];
		}

		if (!empty($_POST['Garage-DeedStat'])) {
			$New_DeedStat = $_POST['Garage-DeedStat'];
		} else {
			$New_DeedStat = $_POST['Old-DeedStat'];
		}

		if (!empty($_POST['Garage-Ownership'])) {
			$New_Ownership = $_POST['Garage-Ownership'];
		} else {
			$New_Ownership = $_POST['Old-Ownership'];
		}

		if (!empty($_POST['Garage-RegNum'])) {
			$New_RegNum = $_POST['Garage-RegNum'];
		} else {
			$New_RegNum = $_POST['Old-RegNumber'];
		}

		if (!empty($_POST['Garage-Tags'])) {
			$New_Tags = $_POST['Garage-Tags'];
		} else {
			$New_Tags = $_POST['Old-Tags'];
		}

		$info_Update = array();

		$info_Update[0] = $New_GarageName;
		$info_Update[1] = $New_Ownership;
		$info_Update[2] = $New_DeedStat;
		$info_Update[3] = $New_RegNum;
		$info_Update[4] = $New_Capacity;
		$info_Update[5] = $New_Area;
		$info_Update[6] = $New_Tags;

		$Garage_obj->U_GarageInfo_ByID($GarageGUID, $info_Update);
		echo "<meta http-equiv='refresh' content='0'>";
	}

	if (isset($_POST['Edit-CertInfo'])) {
		$GarageGUID = $_POST['GarageGUID'];
		$id = $_POST['id'];
		$CertIssuer = $_POST['CertIssuer'];
		$Old_CertIssuer = $_POST['Old-CertIssuer'];
		$CertStatus = $_POST['CertStatus'];
		$Old_CertStatus = $_POST['Old-CertStatus'];
		$Cert_Num = $_POST['CertNum'];
		$Old_CertNum = $_POST['Old-CertNum'];

		for ($i = 0; $i < count($id); $i++) {
			if (!empty($CertIssuer[$i])) {
				$New_CertIssuer = $CertIssuer[$i];
			} else {
				$New_CertIssuer = $Old_CertIssuer[$i];
			}

			if (!empty($CertStatus[$i])) {
				$New_CertStatus = $CertStatus[$i];
			} else {
				$New_CertStatus = $Old_CertStatus[$i];
			}

			if (!empty($Cert_Num[$i])) {
				$New_CertNum = $Cert_Num[$i];
			} else {
				$New_CertNum = $Old_CertNum[$i];
			}

			$Cert_Update = array();

			$Cert_Update[0] = $id[$i];
			$Cert_Update[1] = $New_CertIssuer;
			$Cert_Update[2] = $New_CertStatus;
			$Cert_Update[3] = $New_CertNum;
			$Garage_obj->U_GarageCert_ByID($GarageGUID, $Cert_Update);
		}

		echo "<meta http-equiv='refresh' content='0'>";
	}


	if (isset($_POST['Edite-Facility'])) {
		$GarageGUID = $_POST['GarageGUID'];

		if (!empty($_POST['G_Facility'])) {
			$Facility = $_POST['G_Facility'];
			$Garage_obj->U_GarageFacility_ByID($GarageGUID, $Facility);
		} else {
			$Garage_obj->D_GarageFacility_ByID($GarageGUID);
		}
		echo "<meta http-equiv='refresh' content='0'>";
	}



	?>


</head>

<body>
	 <!-- 
	<div class="preloader-it">
		<div class="la-anim-1"></div>
	</div>
	 /Preloader -->
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
					<div class="col-lg-3 col-xs-12">
						<div class="panel panel-default card-view  pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body  pa-0">
									<div class="profile-box">
										<div class="profile-cover-pic">
											<div class="profile-image-overlay"></div>
										</div>
										<div class="profile-info text-center">
											<div class="profile-img-wrap">
												<img class="inline-block mb-10" src="<?php echo $Profile_photo; ?>" alt="user">
											</div>
											<br />
											<span class="block mt-10 mb-5 weight-500 capitalize-font txt-danger"> نام تعمیرگاه :
												<h5 class="block capitalize-font pb-20"><?php echo $Garage_info[1]; ?></h5>
											</span>
										</div>
										<br />
										<div class="social-info">
											<div class="row">
												<div class="col-xs-4 text-center">
													<span class="counts block head-font"><span class="counter-anim"><?php echo $Garage_Factor4; ?></span></span>
													<span class="counts-text block">تعداد خودرو</span>
												</div>
												<div class="col-xs-4 text-center">
													<span class="counts block head-font"><span class="counter-anim"><?php echo $Garage_Specialist; ?></span></span>
													<span class="counts-text block">تخصص ها</span>
												</div>
												<div class="col-xs-4 text-center">
													<span class="counts block head-font"><span class="counter-anim"><?php echo $Garage_Rate; ?></span></span>
													<span class="counts-text block">امتیاز</span>
												</div>
											</div>
											<div class="dropdown">
												<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" type="button"> اضافه کردن <span class="caret"></span></button>
												<ul role="menu" data-dropdown-in="flipInY" data-dropdown-out="flipOutY" class="dropdown-menu">
													<li>
														<a href="#" data-toggle="modal" data-target="#Add_Personnel"><span>پرسنل</span></a>
													</li>
													<li>
														<a href="#" data-toggle="modal" data-target="#Add_Phone"><span>شماره تماس</span></a>
													</li>
													<?php
													if (!(is_null($Garage_Facility)) && count($Garage_Facility) == 0) {
														echo '<li>
														<a href="#" data-toggle="modal" data-target="#Add_Facility"><span>امکانات رفاهی</span></a>
														</li>';
													}

													if (!(is_null($Garage_Certificate))  && count($Garage_Certificate) < 3) {
														echo '<li>
														<a href="#" data-toggle="modal" data-target="#Add_Cert"><span>پروانه کسب</span></a>
														</li>';
													}
													?>
													<li>
														<a href="#" data-toggle="modal" data-target="#Upload_Doc"><span>آپلود مدارک</span></a>
													</li>
													<li>
														<a href="#" data-toggle="modal" data-target="#profilepic"><span>تصویر پروفایل</span></a>
													</li>
													<?php
													if (!(is_null($Service)) && count($Service) == 0) {
														echo '<li><a href="edit-garage-skill.php" ><span> تخصص ها </span></a></li>';
													}
													if (!(is_null($auto_obj)) && empty($auto_obj)) {
														echo '<li><a href="edit-garage-auto.php" ><span> خودروهای تخصصی </span></a></li>';
													}
													if (!(is_null($Devices)) && count($Devices) == 0) {
														echo '<li><a href="edit-garage-devices.php" ><span> دستگاه های تخصصی </span></a></li>';
													}
													?>
												</ul>
											</div>

											<div class="dropdown">
												<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" type="button"> ویرایش <span class="caret"></span></button>
												<ul role="menu" data-dropdown-in="flipInY" data-dropdown-out="flipOutY" class="dropdown-menu">
													<li>
														<a href="#" data-toggle="modal" data-target="#Edite_Location"><span>Location</span></a>
													</li>
													<li>
														<a href="#" data-toggle="modal" data-target="#profile"><span>اطلاعات عمومی</span></a>
													</li>
													<li>
														<a href="#" data-toggle="modal" data-target="#AddressInfo"><span>اطلاعات نشانی</span></a>
													</li>
													<?php
													if (!(is_null($Garage_Facility)) && count($Garage_Facility) > 0) {
														echo '<li>
														<a href="#" data-toggle="modal" data-target="#Add_Facility"><span>امکانات رفاهی</span></a>
														</li>';
													}

													if (!(is_null($Garage_Certificate)) && count($Garage_Certificate) > 0) {
														echo '<li><a href="#" data-toggle="modal" data-target="#certificate"><span>اطلاعات جواز</span></a></li>';
													}
													if (!(is_null($Service)) && count($Service) > 0) {
														echo '<li><a href="edit-garage-skill.php" ><span> تخصص ها </span></a></li>';
													}

													if (!(is_null($auto_obj)) && !empty($auto_obj)) {
														echo '<li><a href="edit-garage-auto.php" ><span>  خودروهای تخصصی </span></a></li>';
													}

													if (!(is_null($Devices)) && count($Devices) > 0) {
														echo '<li><a href="edit-garage-devices.php" ><span> دستگاه های تخصصی </span></a></li>';
													}

													?>

												</ul>
											</div>

											<div class="dropdown">
												<button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" type="button"> حذف <span class="caret"></span></button>
												<ul role="menu" data-dropdown-in="flipInY" data-dropdown-out="flipOutY" class="dropdown-menu">
													<?php
													if (!(is_null($Mem_List)) && count($Mem_List) > 0) {
														echo '<li><a href="#" data-toggle="modal" data-target="#Del_Personnel"><span>پرسنل</span></a></li>';
													}

													if (!(is_null($phone)) && count($phone) > 0) {
														echo '<li><a href="#" data-toggle="modal" data-target="#Del_Phone"><span>شماره تماس</span></a></li>';
													}

													if (!(is_null($Garage_Certificate)) && count($Garage_Certificate) > 0) {
														echo '<li><a href="#" data-toggle="modal" data-target="#Del_Cert"><span>پروانه کسب</span></a></li>';
													}

													if (!(is_null($files)) && count($files) > 0) {
														echo '<li><a href="#" data-toggle="modal" data-target="#Del_document"><span>مدارک</span></a></li>';
													}
													?>
												</ul>
											</div>
											<br />
											<br />
											<br />
											<div id="profilepic" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> آپلود تصویر پروفایل </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data">
																							<input type="hidden" id="Garage_GUID" name="Garage_GUID" value="<?php echo $Garage_GUID; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group mb-30">
																									<label class="control-label mb-10 text-left"> آپلود تصویر پروفایل : </label>
																									<div class="fileinput input-group fileinput-new" data-provides="fileinput">
																										<div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
																										<span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span> <span class="fileinput-exists btn-text"> تغییر فایل </span>
																											<input type="file" name="Newphoto" id="Newphoto">
																										</span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف </span></a>
																									</div>
																								</div>
																							</div>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30" name="UpdateProfile" id="UpdateProfile"> ثبت تصویر </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="Add_Phone" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">افزودن شماره تلفن</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data" name="form-AddTellphone">
																							<input type="hidden" id="GarageID" name="GarageID" value="<?php echo $Garage_GUID; ?>">
																							<input type="hidden" id="ActionID" name="ActionID" value="C_Contatct">
																							<div class="form-body overflow-hide">
																								<div class="col-sm-12">
																									<div class="col-sm-6">
																										<div class="form-group">
																											<label class="control-label mb-10"> نوع شماره تماس : </label>
																											<select class="form-control " dir="rtl" name="AddOperator" id="AddOperator">
																												<?php
																												$operator = $Phone_Obj->Get_PhoneOperator();
																												echo '<option value=""> انتخاب نمایید </option>';
																												if (!(is_null($operator)) && !empty($operator)) {
																													foreach ($operator as $key) {
																														echo '<option value=' . $key['ID'] . '>' . $key['PersianName'] . '</option>';
																													}
																												}
																												?>
																											</select>
																										</div>
																									</div>
																									<div class="col-sm-6">
																										<div class="form-group mb-30">
																											<div class="form-group">
																												<label class="control-label mb-10"> شماره تلفن : </label>
																												<input type="text" class="form-control" id="N_ContactNum" Name="N_ContactNum" onkeypress="return onlyNumberKey(event)" minlength="10" maxlength="10" value="<?php echo $_POST['N_ContactNum']; ?>">
																											</div>
																										</div>
																									</div>
																									<div class="form-actions mt-10">
																										<button type="submit" class="btn btn-success mr-10 mb-30" name="AddTellphone" id="AddTellphone"> ثبت شماره جدید </button>
																									</div>

																								</div>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="Add_Cert" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">افزودن پروانه کسب</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data" name="AddCertform" onsubmit="return validAddCertForm()">
																							<input type="hidden" id="GarageID" name="GarageID" value="<?php echo $Garage_GUID; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group mb-30">
																									<div class="form-group">
																										<label class="control-label mb-10" for="CertIssuer"><i class="text-info mb-10">*</i> صادر کننده جواز : </label>
																										<select class="form-control " dir="rtl" name="CertIssuer" id="CertIssuer">
																											<?php
																											$Issuer_topic = $Garage_obj->GetIssuerTopic();
																											echo '<option value=""> انتخاب نمایید </option>';
																											foreach ($Issuer_topic as $key) {
																												echo '<option value="' . $key[0] . '">' . $key[1] . '</option>';
																											}
																											?>
																										</select>
																									</div>
																									<div class="form-group">
																										<label class="control-label mb-10" for="CertStatus"><i class="text-info mb-10">*</i> وضعیت پروانه کسب : </label>
																										<select class="form-control " dir="rtl" name="CertStatus" id="CertStatus">
																											<?php
																											$Autoapp_CertificationStatus = $Garage_obj->GetStatusTopic();
																											echo '<option value=""> انتخاب نمایید </option>';
																											foreach ($Autoapp_CertificationStatus as $key) {
																												echo '<option value="' . $key["ID"] . '">' . $key["PersianName"] . '</option>';
																											}
																											?>
																										</select>
																									</div>
																									<div class="form-group">
																										<label class="control-label mb-10" for="CertNum"> شماره جواز </label>
																										<input type="text" class="form-control" name="CertNum" id="CertNum" placeholder="شماره جواز">
																									</div>
																									<div class="form-actions mt-10">
																										<button type="submit" class="btn btn-success mr-10 mb-30" name="AddCert" id="AddCert"> ثبت </button>
																									</div>
																								</div>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="Add_Facility" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> ویرایش امکانات رفاهی </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form name="ChangeMemberPhone" method="post" enctype="multipart/form-data">
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group">
																									<label class="control-label mb-10" for="G_Facility"> امکانات رفاهی : </label>
																									<select class="form-control" multiple name="G_Facility[]" id="G_Facility[]" data-style="form-control btn-default btn-outline">
																										<?php
																										$Autoapp_LocationFacility = $Garage_obj->GetFacilityTopic();
																										if (!(is_null($Garage_Facility)) && !empty($Garage_Facility)) {
																											foreach ($Autoapp_LocationFacility as $key) {
																												$count = 0;
																												foreach ($Garage_Facility as $value) {
																													if ($key[0] == $value) {
																														$count++;
																													}
																												}

																												if ($count != 0) {
																													echo '<option value=' . $key[0] . ' selected>' . $key[1] . '</option>';
																												} else {
																													echo '<option value=' . $key[0] . '>' . $key[1] . '</option>';
																												}
																											}
																										} else {
																											foreach ($Autoapp_LocationFacility as $key) {
																												echo '<option value=' . $key[0] . '>' . $key[1] . '</option>';
																											}
																										}
																										?>
																									</select>
																								</div>

																								<div class="form-actions mt-10">
																									<button type="submit" class="btn btn-success mr-10 mb-30" name="Edite-Facility" id="Edite-Facility"> ثبت </button>
																								</div>
																							</div>

																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="Add_Personnel" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">افزودن پرسنل</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data" name="form-addpersonnel">
																							<input type="hidden" id="GarageID" name="GarageID" value="<?php echo $Garage_GUID; ?>">
																							<input type="hidden" id="ActionID" name="ActionID" value="C_Personnel">
																							<div class="form-body overflow-hide">
																								<div class="col-sm-12">
																									<div class="col-sm-6">
																										<div class="form-group mb-30">
																											<div class="form-group">
																												<label class="control-label mb-10"> نام و نام خانوادگی : </label>
																												<select class="form-control" dir="rtl" name="PersonnelGUID" id="PersonnelGUID">
																													<?php
																													$Personel = $Garage_Personnel->Get_MechanicAll();
																													echo '<option value=""> انتخاب نمایید </option>';
																													foreach ($Personel as $key) {
																														echo '<option value="' . $key['GUID'] . '">' . $key['FName'] . ' ' . $key['LName'] . ' [' . $key['UName'] . '] </option>';
																													}
																													?>
																												</select>
																											</div>
																										</div>
																									</div>
																									<div class="col-sm-6">
																										<div class="form-group">
																											<label class="control-label mb-10"> نقش : </label>
																											<select class="form-control " dir="rtl" name="PersonnelRole" id="PersonnelRole">
																												<?php
																												$Autoapp_GrageRoles = $Garage_obj->GetRoleTopic();
																												echo '<option value=""> انتخاب نمایید </option>';
																												foreach ($Autoapp_GrageRoles as $key) {
																													echo '<option value=' . $key[0] . '>' . $key[1] . '</option>';
																												}
																												?>
																											</select>
																										</div>
																									</div>
																								</div>
																								<div class="form-actions mt-10">
																									<button type="submit" class="btn btn-success mr-10 mb-30" name="AddPersonnel" id="AddPersonnel"> ثبت </button>
																								</div>

																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="Del_Personnel" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> حذف پرسنل </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="table-wrap">
																						<div class="table-responsive">
																							<table id="datable_1" class="table table-hover display  pb-30">
																								<thead>
																									<tr>
																										<th> نام و نام خانوادگی </th>
																										<th> نقش </th>
																										<th> عملیات </th>
																									</tr>
																								</thead>
																								<tbody>
																									<?php
																									$data = $Garage_obj->V_GaragePersonnel($Garage_GUID);
																									if (!(is_null($data)) && !empty($data)) {
																										foreach ($data as $row) {
																											echo "<tr>";
																											$name = $Garage_obj->GetMemberName($row['Personnel_GUID']);
																											$Role = $Garage_obj->GetRoleTopicbyID($row['RoleTopic']);
																											echo "<td>" . $name[0] . " " . $name[1] . "</td>";
																											echo "<td>" . $Role[0] . "</td>";
																											$validation = "onclick='return validation()'";
																											echo '<td><form method="post" enctype="multipart/form-data">
																											<input type="hidden" name="PersonnelID" value="' . $row['ID'] . '">
																											<input type="hidden" name="GarageGUID" value="' . $id . '">
																											<button class="btn btn-default btn-icon-anim" ' . $validation . ' name="Removepersonnel"><i class="icon-trash"></i> حذف </button>
																											</td>';
																											echo "</tr>";
																										}
																									} else {
																										echo '<tr><td colspan="4" style="text-align: center;"> پرسنلی ثبت نشده است </td></tr>';
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
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="Del_Phone" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> حذف شماره تماس </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="table-wrap">
																						<div class="table-responsive">
																							<table id="datable_1" class="table table-hover display  pb-30">
																								<thead>
																									<tr>
																										<th> اپراتور </th>
																										<th> شماره </th>
																										<th> عملیات </th>
																									</tr>
																								</thead>
																								<tbody>
																									<?php
																									if (!(is_null($phone)) && !empty($phone)) {
																										foreach ($phone as $row) {
																											echo "<tr>";
																											if ($row['Mobile'] == 0) {
																												echo "<td> تلفن تعمیرگاه </td>";
																											} else {
																												echo "<td> تلفن همراه </td>";
																											}
																											echo "<td>" . $row['Number'] . "</td>";
																											$validation = "onclick='return validation()'";
																											echo '<td><form method="post" enctype="multipart/form-data">
																											<input type="hidden" name="phoneID" value="' . $row['GUID'] . '">
																											<input type="hidden" name="Form_Action" value="RemoveGaragePhone">
																											<button class="btn btn-default btn-icon-anim" ' . $validation . ' name="Removephone"><i class="icon-trash"></i> حذف </button>
																											</td>';
																											echo "</tr>";
																											echo "</tr>";
																										}
																									} else {
																										echo '<tr><td colspan="4" style="text-align: center;"> شماره ایی ثبت نشده است </td></tr>';
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
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="Del_Cert" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> حذف پروانه کسب </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="table-wrap">
																						<div class="table-responsive">
																							<table id="datable_1" class="table table-hover display  pb-30">
																								<thead>
																									<tr>
																										<th> صادر کننده </th>
																										<th> وضعیت </th>
																										<th> شماره پروانه </th>
																										<th> عملیات </th>
																									</tr>
																								</thead>
																								<tbody>
																									<?php
																									$data = $Garage_obj->V_GarageCert($Garage_GUID);
																									if (!(is_null($data)) && !empty($data)) {
																										foreach ($data as $row) {
																											$Issuer = $Garage_obj->GetCertIssuerbyID($row['CertIssuer']);
																											$status = $Garage_obj->GetCertStatusbyID($row['CertStatus']);
																											echo '<tr>';
																											echo '<td>' . $Issuer[0] . '</td>';
																											echo '<td>' . $status[0] . '</td>';
																											echo '<td>' . $row['CertNumber'] . '</td>';
																											$validation = "onclick='return validation()'";
																											echo '<td><form method="post" enctype="multipart/form-data">
																											<input type="hidden" name="GarageGUID" value="' . $id . '">
																											<input type="hidden" name="CertID" value="' . $row['ID'] . '">
																											<button class="btn btn-default btn-icon-anim" ' . $validation . ' name="RemoveCert" ><i class="icon-trash"></i> حذف </button>
																											</td>';
																											echo "</tr>";
																										}
																									} else {
																										echo '<tr><td colspan="4" style="text-align: center;"> گواهی نامه ایی ثبت نشده است </td></tr>';
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
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="Edite_Location" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">ویرایش location تعمیرگاه</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form name="ChangPassForm" method="post" enctype="multipart/form-data" onsubmit="return validatePass()">
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<div class="form-group">
																								<div class="input-group">
																									<div class="input-group-addon"> عرض جغرافیایی : </div>
																									<input type="text" class="form-control" id="Latitude" name="Latitude" placeholder="<?php echo $Garage_address[4]; ?>">
																								</div>
																							</div>
																							<div class="form-group">
																								<div class="input-group">
																									<div class="input-group-addon"> طول جغرافیایی : </div>
																									<input type="text" class="form-control" id="Longitude" name="Longitude" placeholder="<?php echo $Garage_address[3]; ?>">
																								</div>
																							</div>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30" name="Edite-Location" id="Edite-Location"> ثبت </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="Upload_Doc" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel"> انتخاب مدارک </h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data">
																							<input type="hidden" id="Garage_GUID" name="Garage_GUID" value="<?php echo $Garage_GUID; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group mb-30">
																									<label class="control-label mb-10 text-left"> تصاویر مدارک : </label>
																									<div class="fileinput input-group fileinput-new" data-provides="fileinput">
																										<div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
																										<span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span> <span class="fileinput-exists btn-text"> تغییر فایل </span>
																											<input name="upload[]" type="file" multiple="multiple" id="upload[]">
																										</span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف </span></a>
																									</div>
																								</div>
																							</div>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30" name="insertdoc" id="insertdoc"> ثبت مدارک </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="profile" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">ویرایش اطلاعات عمومی تعمیرگاه</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data" name="MemberRegForm">
																							<!-- OLD Value -->
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<input type="hidden" id="Old-Name" name="Old-Name" value="<?php echo $Garage_info[1]; ?>">
																							<input type="hidden" id="Old-Area" name="Old-Area" value="<?php echo $Garage_info[8]; ?>">
																							<input type="hidden" id="Old-Capacity" name="Old-Capacity" value="<?php echo $Garage_info[6]; ?>">
																							<input type="hidden" id="Old-DeedStat" name="Old-DeedStat" value="<?php echo $Garage_info[3]; ?>">
																							<input type="hidden" id="Old-Ownership" name="Old-Ownership" value="<?php echo $Garage_info[2]; ?>">
																							<input type="hidden" id="Old-RegNumber" name="Old-RegNumber" value="<?php echo $Garage_info[4]; ?>">
																							<input type="hidden" id="Old-Tags" name="Old-Tags" value="<?php echo $Garage_info[9]; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group">
																									<label class="control-label mb-10" for="Name"><i class="text-info mb-10">*</i> نام تعمیرگاه : </label>
																									<div class="input-group">
																										<div class="input-group-addon"></div>
																										<input type="text" class="form-control" name="GarageName" id="GarageName" placeholder="<?php echo $Garage_info[1]; ?>">
																									</div>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="Area"><i class="text-info mb-10">*</i> مساحت : </label>
																									<div class="input-group">
																										<div class="input-group-addon"><i class="icon-info"></i> متر مربع </div>
																										<input type="text" class="form-control" name="Garage-Area" id="Garage-Area" placeholder="<?php echo $Garage_info[8]; ?>">
																									</div>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="Capacity"><i class="text-info mb-10">*</i>ظرفیت سرویس دهی : </label>
																									<div class="input-group">
																										<div class="input-group-addon"><i class="icon-info"></i> تعداد خودور </div>
																										<input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" Name="Garage-Capacity" id="Garage-Capacity" placeholder="<?php echo $Garage_info[6]; ?>">
																									</div>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="DeedStat"> وضعیت سند : </label>
																									<select class="form-control " dir="rtl" name="Garage-DeedStat" id="Garage-DeedStat">
																										<option value=''> انتخاب نمایید </option>
																										<?php

																										$Autoapp_PropertyDeed = $Garage_obj->GetDeedTopic();
																										foreach ($Autoapp_PropertyDeed as $key) {
																											if ($key[0] == $Garage_info[3]) {
																												echo '<option value=' . $key[0] . ' selected>' . $key[1] . '</option>';
																											} else {
																												echo '<option value=' . $key[0] . '>' . $key[1] . '</option>';
																											}
																										}
																										?>
																									</select>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="Property_Ownership"> نوع ملک : </label>
																									<select class="form-control " dir="rtl" name="Garage-Ownership" id="Garage-Ownership">
																										<option value=''> انتخاب نمایید </option>
																										<?php

																										$Ownership_topic = $Garage_obj->GetOwnershipTopic();
																										foreach ($Ownership_topic as $key) {
																											if ($key['ID'] == $Garage_info[2]) {
																												echo '<option value="' . $key['ID'] . '" selected>' . $key['PersianName'] . '</option>';
																											} else {
																												echo '<option value=' . $key['ID'] . '>' . $key['PersianName'] . '</option>';
																											}
																										}

																										?>
																									</select>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="Reg_Num"> شماره پلاک ثبتی : </label>
																									<div class="input-group">
																										<div class="input-group-addon"><i class="fa fa-arrows"></i> اصلی/ فرعی </div>
																										<input type="text" class="form-control" name="Garage-RegNum" id="Garage-RegNum" placeholder="<?php echo $Garage_info[4]; ?>">
																									</div>
																								</div>
																								<div class="form-group">
																									<label class="control-label mb-10" for="tags"> کلمات کلیدی : </label>
																									<input type="text" class="form-control" value="<?php echo $Garage_info[9]; ?>" data-role="tagsinput" id="Garage-Tags" name="Garage-Tags" placeholder="">
																								</div>


																								<div class="form-actions mt-10">
																									<button type="submit" class="btn btn-success mr-10 mb-30" id="Edite-GarageInfo" name="Edite-GarageInfo"> بروز رسانی </button>
																								</div>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="certificate" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">ویرایش اطلاعات مجوز</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data">
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<?php
																							if (!(is_null($Garage_Certificate)) && !empty($Garage_Certificate)) {

																								foreach ($Garage_Certificate as $value) {
																									$Issuer = $Garage_obj->GetCertIssuerbyID($value[1]);
																									$status = $Garage_obj->GetCertStatusbyID($value[2]);
																									echo '<input type="hidden" id="id[]" name="id[]" value="' . $value[0] . '">';
																									echo '<input type="hidden" id="Old-CertIssuer[]" name="Old-CertIssuer[]" value="' . $value[1] . '">';
																									echo '<input type="hidden" id="Old-CertStatus[]" name="Old-CertStatus[]" value="' . $value[2] . '">';
																									echo '<input type="hidden" id="Old-CertNum[]" name="Old-CertNum[]" value="' . $value[3] . '">';
																									echo '<div class="form-group">
																											<label class="control-label mb-10" for="CertIssuer"> صادر کننده جواز :  </label>
																											<select class="form-control " dir="rtl" name="CertIssuer[]" id="CertIssuer[]">';
																									$Issuer_topic = $Garage_obj->GetIssuerTopic();
																									echo '<option value=""> انتخاب نمایید </option>';
																									foreach ($Issuer_topic as $key) {
																										if ($key[0] == $value[1]) {
																											echo '<option value="' . $key[0] . '" selected >' . $key[1] . '</option>';
																										} else {
																											echo '<option value="' . $key[0] . '">' . $key[1] . '</option>';
																										}
																									}
																									echo '</select></div>';
																									echo '<div class="form-group">
																												<label class="control-label mb-10" for="CertStatus"> وضعیت پروانه کسب :  </label>
																												<select class="form-control " dir="rtl" name="CertStatus[]" id="CertStatus[]">';
																									$Autoapp_CertificationStatus = $Garage_obj->GetStatusTopic();
																									echo '<option value=""> انتخاب نمایید </option>';
																									foreach ($Autoapp_CertificationStatus as $key) {
																										if ($key["ID"] == $value[2]) {
																											echo '<option value="' . $key["ID"] . '" selected >' . $key["PersianName"] . '</option>';
																										} else {
																											echo '<option value="' . $key["ID"] . '">' . $key["PersianName"] . '</option>';
																										}
																									}
																									echo '</select></div>';
																									echo '<div class="form-group">
																											<label class="control-label mb-10" for="Cert_Num"> شماره جواز </label>
																											<input type="text" class="form-control" name="CertNum[]" id="CertNum[]" placeholder="' . $value[3] . '"></div><hr /><br />';
																								}
																							} else {
																								echo '<div class="form-group">';
																								echo 'پروانه کسب از جانب سایر اتحادیه ها برای تعمیرگاه ثبت نشده است';
																								echo "</div>";
																							}
																							?>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30" id="Edit-CertInfo" name="Edit-CertInfo"> بروز رسانی </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="AddressInfo" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">ویرایش اطلاعات نشانی تعمیرگاه</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data">
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<input type="hidden" id="Old-PostalCode" name="Old-PostalCode" value="<?php echo $Garage_address[5]; ?>">
																							<input type="hidden" id="Old-address" name="Old-address" value="<?php echo $Garage_address[2]; ?>">
																							<input type="hidden" id="Old-ProvinceID" name="Old-ProvinceID" value="<?php echo $Garage_address[6]; ?>">
																							<input type="hidden" id="Old-CityGUID" name="Old-CityGUID" value="<?php echo $Garage_address[7]; ?>">

																							<div class="form-group">
																								<label class="control-label mb-10" for="Blue_Num"> شماره پلاک شهرداری : </label>
																								<div class="input-group">
																									<div class="input-group-addon"><i class="fa fa-arrows"></i> پلاک آبی </div>
																									<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" name="Blue_Num" id="Blue_Num" placeholder="<?php echo $Garage_info[5]; ?>">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="control-label mb-10" for="zipCode"> کد پستی : </label>
																								<div class="input-group">
																									<div class="input-group-addon"><i class="fa fa-arrows"></i> 10 رقمی </div>
																									<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" name="PostalCode" id="PostalCode" minlength="10" maxlength="10" placeholder="<?php echo $Garage_address[5]; ?>">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="control-label mb-10 text-left"> نشانی : </label>
																								<textarea class="form-control" rows="5" name="address" id="address" placeholder="<?php echo $Garage_address[2]; ?>"></textarea>
																							</div>
																							<div class="form-group">
																								<label class="control-label mb-10"><i class="text-info mb-10">*</i> استان : </label>
																								<select class="form-control" dir="rtl" Name="N_Province" id="N_Province">
																									<?php
																									if (!(is_null($G_ProvincesAll)) && !empty($G_ProvincesAll)) {
																										foreach ($G_ProvincesAll as $row) {
																											if ($Garage_address[6] == $row['ID']) {
																												echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
																											} else {
																												echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																											}
																										}
																									} else {
																										echo '<option value=""> استانی تعریف نشده </option>';
																									}
																									?>
																								</select>
																							</div>
																							<div class="form-group">
																								<label class="control-label mb-10"><i class="text-info mb-10">*</i> شهر : </label>
																								<select class="form-control" dir="rtl" Name="N_City" id="N_City">
																									<?php
																									if (!(is_null($G_CityProvince)) && !empty($G_CityProvince)) {
																										foreach ($G_CityProvince as $row) {
																											if ($Garage_address[7] == $row['GUID']) {
																												echo '<option value="' . $row['GUID'] . '" selected >' . $row['FAName'] . '</option>';
																											} else {
																												echo '<option value="' . $row['GUID'] . '">' . $row['FAName'] . '</option>';
																											}
																										}
																									} else {
																										echo '<option value=""> شهری تعریف نشده </option>';
																									}
																									?>
																								</select>
																							</div>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30" name="Edite-Address" id="Edite-Address"> بروز رسانی </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>

											<div id="Del_document" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">حذف مدارک</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="table-wrap">
																						<div class="table-responsive">
																							<table id="datable_1" class="table table-hover display  pb-30">
																								<thead>
																									<tr>
																										<th> مدرک </th>
																										<th> عملیات </th>
																									</tr>
																								</thead>
																								<tfoot>
																									<tr>
																										<th> مدرک </th>
																										<th> عملیات </th>
																									</tr>
																								</tfoot>
																								<tbody>
																									<?php
																									$data = $Garage_obj->V_GarageFile($Garage_GUID);
																									$validation = "onclick='return validation()'";

																									foreach ($data as $row) {
																										echo '<tr>
																										<td><img class="img-responsive" src="' . $row['Path'] . '" alt="Doc" width="150" height="auto"></td>';
																										echo '<td><form method="post" enctype="multipart/form-data">
																										<input type="hidden" name="GarageGUID" value="' . $id . '">
																										<input type="hidden" name="FileID" value="' . $row['ID'] . '">
																										<button class="btn btn-default btn-icon-anim" ' . $validation . ' name="RemoveFile"><i class="icon-trash"></i> حذف </button>
																										</td></tr>';
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
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="garage" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">تغییر تعمیرگاه</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="">
																		<div class="panel-wrapper collapse in">
																			<div class="panel-body pa-0">
																				<div class="col-sm-12 col-xs-12">
																					<div class="form-wrap">
																						<form method="post" enctype="multipart/form-data" name="MemberRegForm" onsubmit="return validateForm()">
																							<input type="hidden" id="GarageGUID" name="GarageGUID" value="<?php echo $Garage_GUID; ?>">
																							<input type="hidden" id="Old-PersonnelNum" name="Old-PersonnelNum" value="<?php echo $Garage_info[7]; ?>">
																							<div class="form-body overflow-hide">
																								<div class="form-group">
																									<label class="control-label mb-10" for="Personnel_Num"><i class="text-info mb-10">*</i> تعداد پرسنل : </label>
																									<div class="input-group">
																										<div class="input-group-addon"><i class="fa fa-arrows"></i></div>
																										<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="Personnel_Num" name="Personnel_Num" placeholder="تعداد پرسنل">
																									</div>
																								</div>

																							</div>
																							<div class="form-actions mt-10">
																								<button type="submit" class="btn btn-success mr-10 mb-30"> بروز رسانی </button>
																							</div>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
											<div id="invoicelist" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
															<h5 class="modal-title" id="myModalLabel">فهرست پرداخت ها</h5>
														</div>
														<div class="modal-body">
															<!-- Row -->
															<div class="row">
																<div class="col-lg-12">
																	<div class="table-wrap">
																		<div class="table-responsive">
																			<table class="table table-striped display product-overview" id="datable_1">
																				<thead>
																					<tr>
																						<th>موضوع</th>
																						<th>شماره پیگیری</th>
																						<th>تاریخ فاکتور</th>
																						<th>عملیات</th>
																					</tr>
																				</thead>
																				<tbody>
																					<?php

																					?>
																				</tbody>
																			</table>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- /.modal-content -->
												</div>
												<!-- /.modal-dialog -->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-9 col-xs-12">
						<div class="panel panel-default card-view pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pb-0">
									<div class="tab-struct custom-tab-1">
										<ul role="tablist" class="nav nav-tabs nav-tabs-responsive" id="myTabs_8">
											<li class="active" role="presentation"><a data-toggle="tab" id="profile_tab_8" role="tab" href="#profile_8" aria-expanded="true"><span>عمومی</span></a></li>
											<li role="presentation" class="next"><a data-toggle="tab" id="address_tab_8" role="tab" href="#address_8" aria-expanded="false"><span>نشانی</span></a></li>
											<li role="presentation" class="next"><a data-toggle="tab" id="Cert_tab_8" role="tab" href="#Cert_8" aria-expanded="false"><span>مجوز</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="photos_tab_8" role="tab" href="#photos_8" aria-expanded="false"><span>مدارک</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="auto_tab_8" role="tab" href="#auto_8" aria-expanded="false"><span>خودروها</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="settings_tab_8" role="tab" href="#settings_8" aria-expanded="false"><span>تخصص ها</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="Devices_tab_8" role="tab" href="#Devices_8" aria-expanded="false"><span>دستگاه تخصصی</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="Personnel_tab_8" role="tab" href="#Personnel_8" aria-expanded="false"><span>پرسنل</span></a></li>
											<li role="presentation" class=""><a data-toggle="tab" id="Map_tab_8" role="tab" href="#Map_8" aria-expanded="false"><span>نقشه</span></a></li>
										</ul>
										<div class="tab-content" id="myTabContent_8">
											<div id="profile_8" class="tab-pane fade active in" role="tabpanel">
												<div class="col-md-12">
													<div class="pt-20">
														<table class="table table-striped display product-overview" id="datable_1">
															<tbody>
																<?php
																echo "<tr>";
																echo "<td> مساحت : </td>";
																echo '<td>' . $Garage_info[8] . ' متر مربع </td>';
																echo "</tr>";
																echo "<tr>";
																echo "<td> ظرفیت سرویس دهی : </td>";
																echo "<td>" . $Garage_info[6] . " تعداد خودرو </td>";
																echo "</tr>";
																echo "<tr>";
																echo "<td>  تعداد پرسنل : </td>";
																echo "<td>" . $Garage_info[7] . " نفر </td>";
																echo "</tr>";
																echo "<tr>";
																echo "<td> وضعیت سند : </td>";
																$DeedStat = $Garage_obj->GetDeedStatTopic($Garage_info[3]);
																echo "<td>" . $DeedStat[0] . "</td>";
																echo "</tr>";
																echo "<tr>";
																echo "<td> نوع ملک : </td>";
																$Ownership = $Garage_obj->GetOwnershipTopicbyID($Garage_info[2]);
																echo "<td>" . $Ownership[0] . "</td>";
																echo "</tr>";
																if (!(is_null($Garage_info[4])) && !empty($Garage_info[4])) {
																	echo "<tr>";
																	echo "<td> شماره پلاک ثبتی : </td>";
																	echo "<td>" . $Garage_info[4] . " (شمارۀ اصلی/شمارۀ فرعی) </td>";
																	echo "</tr>";
																} else {
																	echo "<tr>";
																	echo "<td> شماره پلاک ثبتی : </td>";
																	echo "<td> شماره پلاک ثبتی برای این تعمیرگاه ثبت نشده است </td>";
																	echo "</tr>";
																}
																echo "<tr>";
																echo "<tr>";
																echo "<td> امکانات رفاهی : </td>";
																echo "<td>";
																if (!(is_null($Garage_Facility)) && !empty($Garage_Facility)) {
																	$Facility = array();
																	$Facility_name = array();
																	$j = 0;
																	for ($i = 0; $i < count($Garage_Facility); $i++) {
																		$Facility[$i] = $Garage_obj->GetFacilityTopicbyID($Garage_Facility[$i]);
																		$Facility_name[$j] = $Facility[$i][1];
																		$j++;
																	}

																	foreach ($Facility_name as $key) {
																		echo $key . " , ";
																	}
																} else {
																	echo " امکانات رفاهی برای این تعمیرگاه ثبت نشده است ";
																}
																echo "</td>";
																echo "</tr>";
																if (!(is_null($Garage_info[9])) && !empty($Garage_info[9])) {
																	echo "<tr>";
																	echo "<td> کلید واژه ها : </td>";
																	echo "<td>" . str_replace(",", " - ", $Garage_info[9]) . "</td>";
																	echo "</tr>";
																} else {
																	echo "<tr>";
																	echo "<td> کلید واژه ها : </td>";
																	echo "<td> کلید واژه ایی برای این تعمیرگاه ثبت نشده است </td>";
																	echo "</tr>";
																}
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div id="address_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">
															<br />
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Garage_info[5])) && !empty($Garage_info[5])) {
																		echo "<tr>";
																		echo "<td> شماره پلاک شهرداری : </td>";
																		echo "<td>" . $Garage_info[5] . "</td>";
																		echo "</tr>";
																	} else {
																		echo "<tr>";
																		echo "<td> شماره پلاک شهرداری : </td>";
																		echo "<td> شماره پلاکی برای این تعمیرگاه ثبت نشده است </td>";
																		echo "</tr>";
																	}
																	if (!(is_null($Garage_address[5])) && !empty($Garage_address[5])) {
																		echo "<tr>";
																		echo "<td> کد پستی : </td>";
																		echo "<td>" . $Garage_address[5] . "</td>";
																		echo "</tr>";
																	} else {
																		echo "<tr>";
																		echo "<td> کد پستی : </td>";
																		echo "<td> کدپستی برای این تعمیرگاه ثبت نشده است </td>";
																		echo "</tr>";
																	}

																	if (!(is_null($Garage_address[2])) && !empty($Garage_address[2])) {
																		echo "<tr>";
																		echo "<td> نشانی : </td>";
																		echo "<td>" . $Garage_address[2] . "</td>";
																		echo "</tr>";
																	} else {
																		echo "<tr>";
																		echo "<td> نشانی : </td>";
																		echo "<td> آدرسی برای این تعمیرگاه ثبت نشده است </td>";
																		echo "</tr>";
																	}
																	if (!(is_null($phone)) && !empty($phone)) {
																		foreach ($phone as $value) {
																			if ($value['Mobile'] == 0) {
																				echo "<tr>";
																				echo "<td> تلفن تعمیرگاه : </td>";
																				echo "<td>" . $value['Number'] . "</td>";
																				echo "</tr>";
																			}
																			if ($value['Mobile'] == 1) {
																				echo "<tr>";
																				echo "<td> تلفن همراه : </td>";
																				echo "<td>" . $value['Number'] . "</td>";
																				echo "</tr>";
																			}
																		}
																	} else {
																		echo "<tr>";
																		echo "<td> تلفن تعمیرگاه : </td>";
																		echo "<td> شماره تماسی برای این تعمیرگاه ثبت نشده است </td>";
																		echo "</tr>";
																	}
																	echo "<tr>";
																	if (!(is_null($Province)) && !empty($Province)) {
																		echo "<td> نام استان : </td>";
																		echo "<td>" . $Province[0]['Name'] . "</td>";
																	} else {
																		echo "<td> نام استان : </td>";
																		echo "<td>خطا دربرنامه</td>";
												
																	}
																	
																	echo "</tr>";
																	echo "<tr>";
																	if (!(is_null($city)) && !empty($city)) {
																		echo "<td> نام شهر : </td>";
																		echo "<td>" . $city[0]['FAName'] . "</td>";
																	} else {
																		echo "<td> نام شهر : </td>";
																		echo "<td>خطا دربرنامه</td>";
																	}
																	echo "</tr>";
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<!-- Row -->
											</div>
											<div id="Cert_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">

															<div class="panel-heading">
																<div class="pull-left">
																	<h6 class="panel-title txt-dark"> پروانه کسب اتحادیه تعمیرکاران تهران </h6>
																</div>
																<div class="clearfix"></div>
															</div>
															<hr>
															<br />
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Mem_List)) && count($Mem_List) > 0) {
																		foreach ($Mem_List as $value) {
																			if ($value[1] == 2) {
																				$Cert = $Garage_Personnel->GetMemLicense($value[2]);
																				$Category = $Garage_Personnel->GetNameCategory($Cert[0]);
																				if ($Cert[3] == 1) {
																					$status = "فعال";
																				} else {
																					$status = "<code>غیر فعال</code>";
																				}
																				echo "<tr>";
																				echo "<td> رسته شغلی : </td>";
																				echo "<td>" . $Category . "</td>";
																				echo "</tr>";
																				echo "<tr>";
																				echo "<td> شماره پروانه کسب : </td>";
																				echo "<td>" . $Cert[1] . "</td>";
																				echo "</tr>";
																				echo "<tr>";
																				echo "<td> وضعیت پروانه کسب : </td>";
																				echo "<td><p class='text-success mb-10'>" . $status . "</p></td>";
																				echo "</tr>";
																			}
																		}
																	} else {
																		echo "<tr>";
																		echo '<td colspan="2" style="text-align: center;">';
																		echo 'پرسنل صاحب جواز اتحادیه تعمیرکاران تهران برای این تعمیرگاه ثبت نشده است';
																		echo "</td>";
																		echo "</tr>";
																	}

																	?>
																</tbody>
															</table>
															<br />
															<div class="panel-heading">
																<div class="pull-left">
																	<h6 class="panel-title txt-dark"> سایر جوازهای تعمیرگاه </h6>
																</div>
																<div class="clearfix"></div>
															</div>
															<br />
															<hr>
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Garage_Certificate)) && count($Garage_Certificate) > 0) {
																		foreach ($Garage_Certificate as $value) {
																			$Issuer = $Garage_obj->GetCertIssuerbyID($value[1]);
																			$status = $Garage_obj->GetCertStatusbyID($value[2]);
																			echo "<tr>";
																			echo "<td> صادر کننده جواز : </td>";
																			echo "<td>" . $Issuer[0] . "</td>";
																			echo "</tr>";
																			echo "<tr>";
																			echo "<td> وضعیت پروانه کسب : </td>";
																			echo "<td>" . $status[0] . "</td>";
																			echo "</tr>";
																			echo "<tr>";
																			echo "<td> شماره جواز : </td>";
																			echo "<td>" . $value[3] . "</td>";
																			echo "</tr>";
																		}
																	} else {
																		echo "<tr>";
																		echo '<td colspan="2" style="text-align: center;">';
																		echo 'پروانه کسب از جانب سایر اتحادیه ها برای تعمیرگاه ثبت نشده است';
																		echo "</td>";
																		echo "</tr>";
																	}
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<!-- Row -->
											</div>
											<div id="photos_8" class="tab-pane fade" role="tabpanel">
												<div class="col-md-12 pb-20">
													<?php
													if (!(is_null($files)) && count($files) > 0) {
														echo '<div class="gallery-wrap"><div class="portfolio-wrap project-gallery" style="width: 0px;">';
														echo '<ul id="portfolio_1" class="portf auto-construct  project-gallery" data-col="4" style="position: relative; height: 20px;">';
														foreach ($files as $value) {
															echo '<li class="item" data-src="' . $value . '" data-sub-html="<h6> مدارک </h6><p>' . $Garage_info[1] . '</p>" style="width: 0px; height: auto; margin: 10px; position: absolute; left: 0px; top: 0px;">
																		<a href="">
																		<img class="img-responsive" src="' . $value . '" alt=" مدارک ">
																		<span class="hover-cap"> مدارک </span>
																		</a>
																	</li>';
														}
														echo '</ul></div></div>';
													} else {
														echo '<table class="table table-striped display product-overview" id="datable_1">
																<tbody>';
														echo "<tr>";
														echo '<td style="text-align: center;">';
														echo 'مدارکی ثبت نشده';
														echo "</td>";
														echo "</tr>";
														echo '</tbody></table>';
													}
													?>
												</div>
											</div>
											<div id="auto_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">
															<div class="table-wrap">
																<div class="table-responsive">
																	<table class="table table-striped display product-overview" id="datable_1">
																		<tbody>
																			<?php
																			if (!(is_null($auto)) && !empty($auto)) {
																				foreach ($auto as $key) {
																					echo "<tr>";
																					echo "<td> نام خودرو : </td>";
																					$Cars = $auto_obj->Get_AutoDetial_ByID($key["AutoID"]);
																					if (!empty($Cars)) {
																						if ($Cars[0]['ModelID'] != 0) {
																							$tip = $auto_obj->Get_Tip_ByID($Cars[0]['ModelID']);
																							$auto_Tip =  $tip[0]["ModelName"];
																						} else {
																							$auto_Tip = "بدون تیپ";
																						}
																						$manufacture = $auto_obj->Get_Manufactuer_ByID($Cars[0]["ManufacturerID"]);
																						if (!empty($manufacture)) {
																							$auto_Manufacturer = $manufacture[0]["Name"];
																						} else {
																							$auto_Manufacturer = "سازنده خودرو یافت نشد";
																						}
																						$MemberAuto_list = "<td>" .  $auto_Manufacturer . " " . $auto_Tip . " " . $Cars[0]["Name"] . "</td>";
																					} else {
																						$MemberAuto_list = "<td> خودرو با مشخصات مدنظر در دیتابیس یافت نشد </td>";
																					}
																					echo $MemberAuto_list;
																					echo "</tr>";
																				}
																			} else {
																				echo "<tr>";
																				echo '<td colspan="2" style="text-align: center;">';
																				echo 'خودرویی ثبت نشده';
																				echo "</td>";
																				echo "</tr>";
																			}
																			?>
																		</tbody>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- Row -->
											</div>
											<div id="settings_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Service)) && count($Service) > 0) {
																		foreach ($Service as $value) {
																			echo "<tr>";
																			echo "<td> نام سرویس : </td>";
																			echo "<td>" . $Garage_Personnel->GetNameSkills($value) . "</td>";
																			echo "</tr>";
																		}
																	} else {
																		echo "<tr>";
																		echo '<td colspan="2" style="text-align: center;">';
																		echo 'سرویس تخصصی ثبت نشده';
																		echo "</td>";
																		echo "</tr>";
																	}
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<br />
											</div>

											<div id="Devices_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Devices)) && count($Devices) > 0) {
																		foreach ($Devices as $value) {
																			echo "<tr>";
																			echo "<td> دستگاه تخصصی : </td>";
																			echo "<td>" . $Garage_Personnel->GetNameDevices($value) . "</td>";
																			echo "</tr>";
																		}
																	} else {
																		echo "<tr>";
																		echo '<td colspan="2" style="text-align: center;">';
																		echo 'دستگاهی ثبت نشده';
																		echo "</td>";
																		echo "</tr>";
																	}
																	?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<br />
												<!-- Row -->
											</div>
											<div id="Personnel_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="followers-wrap">
															<table class="table table-striped display product-overview" id="datable_1">
																<tbody>
																	<?php
																	if (!(is_null($Mem_List)) && count($Mem_List) > 0) {
																		foreach ($Mem_List as $value) {
																			echo "<tr>";
																			echo "<td> نام و نام خانوادگی : </td>";
																			$name = $Garage_Personnel->GetMemberName($value[2]);
																			echo "<td>" . $name[0] . " " . $name[1] . "</td>";
																			echo "<td> نقش : </td>";
																			$Role = $Garage_obj->GetRoleTopicbyID($value[1]);
																			echo "<td>" . $Role[0] . "</td>";
																			echo "</tr>";
																		}
																	} else {
																		echo "<tr>";
																		echo '<td colspan="2" style="text-align: center;">';
																		echo 'پرسنلی ثبت نشده';
																		echo "</td>";
																		echo "</tr>";
																	}
																	?>
																</tbody>
															</table>
															<br />
														</div>
													</div>
												</div>
												<br />
												<!-- Row -->
											</div>
											<div id="Map_8" class="tab-pane fade" role="tabpanel">
												<!-- Row -->
												<div class="row">
													<div class="col-lg-12">
														<div class="table-wrap">
															<div class="table-responsive">
																<iframe frameborder="0" class="col-sm-12" style="height: 640px;" src="V_Garag_Map.php" marginheight="1" marginwidth="1" scrolling="no" frameborder="0" allowtransparency="true"></iframe>
															</div>
														</div>
													</div>
												</div>
												<!-- Row -->
											</div>
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
	</div>
	<!-- /#wrapper -->


	<!-- JavaScript -->

	<!-- jQuery -->


	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	<!-- Moment JavaScript -->

	<script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js"></script>

	<!-- Bootstrap Select JavaScript -->
	<script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

	<!-- Select2 JavaScript -->
	<script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

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

	<!-- Counter Animation JavaScript -->
	<script src="vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
	<script src="vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>

	<!-- Data table JavaScript -->
	<script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>


	<!-- Sparkline JavaScript -->
	<script src="vendors/jquery.sparkline/dist/jquery.sparkline.min.js"></script>

	<script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>

	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

	<!-- Data table JavaScript -->
	<script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>


	<!-- Moment JavaScript -->

	<!-- Form Advance Init JavaScript -->
	<script src="dist/js/form-advance-data.js"></script>

	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>

	<!-- Gallery JavaScript -->
	<script src="dist/js/isotope.js"></script>
	<script src="dist/js/lightgallery-all.js"></script>
	<script src="dist/js/froogaloop2.min.js"></script>
	<script src="dist/js/gallery-data.js"></script>

	<!-- Spectragram JavaScript -->
	<script src="dist/js/spectragram.min.js"></script>

	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
	<script src="dist/js/widgets-data.js"></script>
	<script src="dist/js/skills-counter-data.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>


	<script LANGUAGE="JavaScript">
		function credential() {
			var agree = confirm("آیا از اجرای این دستور مطمین می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}

		function validation() {
			var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}

		function InvoiceRemovevalid() {
			var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
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

		if ($Error_STR == 3) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا در ثبت اطلاعات',
			text: 'فایل با این نام قبلا در سمت سرور قرار گرفته است' ,
			position: 'top-center',
			loaderBg:'#ed3236',
			hideAfter: 6500,
			stack: 6
		});
		return false;
});";
			echo '</script>';
		}
		if ($Error_STR == 4) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا در ثبت اطلاعات',
			text: 'فایل خراب می باشد و امکان بارگذاری بر روی سرور نیست' ,
			position: 'top-center',
			loaderBg:'#ed3236',
			hideAfter: 6500,
			stack: 6
		});
		return false;
});";
			echo '</script>';
		}
		if ($Error_STR == 5) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا در ثبت اطلاعات',
			text: 'سایز فایل بیشتر از 5 مگابایت می باشد' ,
			position: 'top-center',
			loaderBg:'#ed3236',
			hideAfter: 6500,
			stack: 6
		});
		return false;
});";
			echo '</script>';
		}

		if ($Error_STR == 6) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا در ثبت اطلاعات',
			text: 'قالب فایل صحیح نمی باشد' ,
			position: 'top-center',
			loaderBg:'#ed3236',
			hideAfter: 6500,
			stack: 6
		});
		return false;
});";
			echo '</script>';
		}
		if ($Error_STR == 7) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		$.toast({
			heading: 'خطا در ثبت اطلاعات',
			text: 'تاریخ آخرین روز فعال بودن بنر نمی تواند قبل از امروز باشد' ,
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