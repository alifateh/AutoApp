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

if (isset($_POST['eltype'])) {
	$_SESSION["elementype"] = $_POST['eltype'];
	$_SESSION["prevadd"] = $_POST['backlocation'];

	if ($_POST['eltype'] == 20) {
		$_SESSION["devicesId"] = $_POST['devicesId'];
	}
}



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



	<!-- Init JavaScript -->

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
										<h6 class="panel-title txt-dark"> بارگذاری فایل </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<div id="example-basic">
										<div class="col-sm-6">
											<form method="post" enctype="multipart/form-data">
												<input type="hidden" id="elementype" name="elementype" value="<?php echo $_POST['eltype'];  ?>">
												<input type="hidden" id="backlocation" name="backlocation" value="<?php echo $_POST['backlocation'];  ?>">
												<input type="hidden" id="devId" name="devId" value="<?php
																									if ($_POST['eltype'] == 20) {
																										echo $_POST['devicesId'];
																									} elseif ($_POST['eltype'] == 9) {
																										echo $_POST['devicesId'];
																									} elseif ($_POST['eltype'] == 8) {
																										echo $_POST['ManufacturId'];
																									} else {
																										echo $_POST[''];
																									}

																									?>">
												<div class="form-group mb-30">
													<label class="control-label mb-10 text-left"> بارگذاری فایل : </label>
													<div class="fileinput input-group fileinput-new" data-provides="fileinput">
														<div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
														<span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span> <span class="fileinput-exists btn-text"> تغییر فایل </span>
															<input type="file" name="fileToUpload" id="fileToUpload">
														</span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف </span></a>
													</div>
												</div>
												<br />
												<br />
												<br />
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="file_upload" id="file_upload"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
												</div>
											</form>
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



	///			$backurl = $_POST['backlocation'];
	///		echo '<script type="text/javascript">location.href = "'.$backurl.'";</script>';

	session_start();
	require_once('Model/FileModel.php');

	use fateh\managefile\managefile as fileup;

	$elementype = $_SESSION["elementype"];
	$elementID = $_SESSION["devicesId"];
	$backurl = $_SESSION["prevadd"];
	$files = new fileup($_SESSION["Admin_GUID"]);

	if ($elementype == 20) { //devices file
		$target_dir = "images/Devices/";
	}

	if (isset($_POST['file_upload'])) {


		if (file_exists($_FILES['fileToUpload']['tmp_name']) || is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {

			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

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

			if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf" && $imageFileType != "xlsx" && $imageFileType != "zip" && $imageFileType != "rar" && $imageFileType != "mp3" && $imageFileType != "txt" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "mp4") {
				//echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";

				echo '<script language="javascript">';
				echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا فایل آپلود نشد!!!',
									text: 'لطفا قالب فایل را از بین قالب های '+'<br />'+'[jpg] یا [jpeg] یا [gif] یا [pdf] یا [xlsx] یا [zip] یا [rar] یا [mp3] یا [txt] یا [doc] یا [mp4] یا [docx]'+'<br />'+' انتخاب نمایید',
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
			} else {
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

					$files->uploadfile($target_file, $elementype, $elementID);
					echo '<script type="text/javascript">location.href = "' . $backurl . '";</script>';
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

			echo '<script language="javascript">';
			echo "$(document).ready(function() {
								$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
								$.toast({
									heading: 'خطا !!!',
									text: 'فایلی انتخاب نشد' ,
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