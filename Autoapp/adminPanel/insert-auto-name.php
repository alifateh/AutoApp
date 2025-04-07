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
require_once('Model/AutoModel.php');
use fateh\Automobile\Automobile as auto;
$aut_obj = new auto($_SESSION["Admin_GUID"]);
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

	<script src="dist/js/jquery.min.js"></script>

	<script>
		$(document).ready(function() {
			$('#Manufacturer').on('change', function() {
				var manID = $(this).val();
				if (manID) {
					$.ajax({
						type: 'POST',
						url: 'ajax-manufacture-tip-auto.php',
						data: 'man_id=' + manID,
						success: function(html) {
							$('#automodel').html(html);
						}
					});
				}
			});
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
							<div class="panel-heading">
								<div class="pull-right">
									<a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2">
										<i class="zmdi zmdi-chevron-down"></i>
										<i class="zmdi zmdi-chevron-up"></i>
									</a>
								</div>
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark"> تعریف خودرو </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">

									<form method="post" enctype="multipart/form-data">
										<div id="example-basic">
											<div class="col-sm-6">
												<div class="form-group">
													<div class="input-group col-sm-6">

														<label class="control-label mb-10"> نام خودروساز : </label>
														<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Manufacturer" id="Manufacturer">
															<option> انتخاب نمایید </option>
															<?php
															$data = $aut_obj ->V_AutoManufactures();
															if(!empty($data)){
																foreach($data as $row){
																	echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																}
															}else{
																echo '<option value=""> خودروسازی تعرف نشده است </option>';
															}
															?>

														</select>
													</div>
													<br />
													<label class="control-label mb-10" for="exampleInputuname_1"> نام خودرو : </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="autoname" id="autoname" placeholder=" نام خودرو " value="<?php echo $_POST['autoname'] ;?>">
													</div>
													<br />
													<div class="input-group col-sm-6">
														<label class="control-label mb-10"> تیپ خودرو </label>
														<select class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="automodel" id="automodel">
														</select>
													</div>
													<br />
													<div class="form-group">
														<label class="control-label mb-10" for="exampleInputuname_1"> تولید خودرو </label>
														<div class="checkbox checkbox-primary">
															<input id="checkbox2" type="checkbox" checked="" name="inetrnal" id="inetrnal">
															<label for="checkbox2"> داخلی </label>
														</div>
													</div>
													<br />
													<div class="form-group mb-30">
														<label class="control-label mb-10 text-left"> تصویر خودرو : </label>
														<div class="fileinput input-group fileinput-new" data-provides="fileinput">
															<div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
															<span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span> <span class="fileinput-exists btn-text"> تغییر فایل </span>
																<input type="file" name="fileToUpload" id="fileToUpload">
															</span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف </span></a>
														</div>
													</div>
													<br />
													<br />
													<div class="input-group">

														<button class="btn btn-info btn-anim" type="submit" name="automobileREG" id="automobileREG"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
													</div>
												</div>
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
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>

	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>

	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

	<?php

	if (isset($_POST['automobileREG']) and $_POST['autoname'] !== "") {
		if ($_POST['inetrnal'] == 'on') {
			$inetrnal = 1;
		} else {
			$inetrnal = 0;
		}
		$autobj = new auto($_SESSION["Admin_GUID"]);
		if (file_exists($_FILES['fileToUpload']['tmp_name']) || is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {


			$target_dir = "images/automobile/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$fileName = md5(uniqid(mt_rand(100000, 999999)));

			// Check if image file is a actual image or fake image

			$check = filesize($_FILES["fileToUpload"]["tmp_name"]);
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
				echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا فایل آپلود نشد!!!',
									text: 'فایلی با این نام در سرور موجود میباشد'+'<br />'+'برای آپلود نام فایل را عوض کنید.' ,
									position: 'top-center',
									loaderBg:'#ed3236',
									hideAfter: 6500,
									stack: 6
								});
								return false;
						});";
				echo '</script>';
				$uploadOk = 0;
			}

			// Check file size
			if ($_FILES["fileToUpload"]["size"] > 5000000) {
				echo '<script language="javascript">';
				echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا فایل آپلود نشد!!!',
									text: 'سایز فایل بیشتر از 50 مگابایت می باشد'+'<br />'+'برای آپلود سایز فایل را کمتر نمایید.' ,
									position: 'top-center',
									loaderBg:'#ed3236',
									hideAfter: 6500,
									stack: 6
								});
								return false;
						});";
				echo '</script>';
				$uploadOk = 0;
			}

			// Allow certain file formats

			if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
				echo '<script language="javascript">';
				echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا فایل آپلود نشد!!!',
									text: 'لطفا قالب فایل را از بین قالب های '+'<br />'+'[jpg] یا [jpeg] یا [gif] یا [png]'+'<br />'+' انتخاب نمایید',
									position: 'top-center',
									loaderBg:'#ed3236',
									hideAfter: 6500,
									stack: 6
								});
								return false;
						});";
				echo '</script>';
				$uploadOk = 0;
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				// echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file

			} else {
				$FileMove = $target_dir.$fileName.".".$imageFileType;
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $FileMove)) {

					//add with file

					$autobj->C_Automobile($_POST['autoname'], $_POST['Manufacturer'], $_POST['automodel'], $inetrnal, $FileMove, $fileName);
					$url = "./insert-auto-name.php";
					header("Location: $url");
				} else {
					echo '<script language="javascript">';
					echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا فایل آپلود نشد!!!',
									text: 'مشکلی در آپلود فایل بوجود آمده است.' ,
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
		} else {

			//echo $inetrnal;
			//add wit no file

			$autobj->C_Automobile($_POST['autoname'], $_POST['Manufacturer'], $_POST['automodel'], $inetrnal, "", "");
			$url = "./insert-auto-name.php";
			header("Location: $url");
		}
	}


	?>

</body>

</html>