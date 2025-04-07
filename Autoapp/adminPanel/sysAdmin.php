<?php
require_once('config/public_conf.php');
require_once('Model/LoginModel.php');

use fateh\login\Admin as user;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['login-btn'])) {
		if (!empty($_POST['login-password']) && !empty($_POST['username'])) {
			$member = new user($_SESSION["Admin_GUID"]);
			$ReturnVal = $member->Login_Admin($_POST["username"], $_POST['login-password']);
			if ($ReturnVal == 10) {
				$url = "index.php";
				header("Location: $url");
			} elseif ($ReturnVal == 9) {
				$Error_STR = 9;
			} elseif ($ReturnVal == 8) {
				$Error_STR = 8;
			} elseif ($ReturnVal == 7) {
				$Error_STR = 7;
			} else {
				$Error_STR = 1;
			}
		}
	}
} else {
	$Error_STR = Null;
	$ReturnVal = Null;
	session_unset();
	session_write_close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php Metatag(); ?>
	<!-- vector map CSS -->
	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

	<!-- Custom CSS -->
	<link href="dist/css/style.css" rel="stylesheet" type="text/css">

</head>

<body>
	<div class="wrapper pa-0" style="background: #f4f3f2;">
		<header class="sp-header">
			<div style="display: flex;justify-content: center;align-items: center;">
				<a href="#">
					<img class="brand-img mr-10" src="images/logo-autoapp.png"" alt=" brand" />
				</a>
			</div>
		</header>

		<!-- Main Content -->
		<div class="page-wrapper pa-0 ma-0 auth-page">
			<div class="container-fluid">
				<!-- Row -->
				<div class="table-struct full-width full-height">
					<div class="table-cell vertical-align-middle auth-form-wrap">
						<div class="auth-form  ml-auto mr-auto no-float">
							<div class="row">
								<div class="col-sm-12 col-xs-12" style="border: 0px;">
									<div class="mb-30">
										<hr>
										<form method="post">
											<div class="form-group">
												<label class="control-label mb-10" for="exampleInputEmail_2" style="color: #000000;"> نام کاربری : </label>
												<input type="text" class="form-control" placeholder="Username" required="" name="username" id="username" />
											</div>
											<div class="form-group">
												<label class="pull-left control-label mb-10" for="exampleInputpwd_2" style="color: #000000;"> رمزعبور : </label>
												<input type="password" class="form-control" placeholder="Password" required="" name="login-password" id="login-password" />
												<!-- forgot-password.html -->

												<!-- <a class="capitalize-font txt-primary block mb-10 pull-right font-12" href="forgot-password.html">forgot password ?</a> -->
											</div>
											<!-- forgot-password.html -->

											<!--
												<div class="form-group">
													<div class="checkbox checkbox-primary pr-10 pull-left">
														<input id="checkbox_2" required="" type="checkbox">
														<label for="checkbox_2"> Keep me logged in</label>
													</div>
													<div class="clearfix"></div>
												</div>
												-->

											<div class="form-group text-center">
												<button type="submit" class="btn btn-default btn-anim" name="login-btn" id="login-btn" value="ورود" style="background: #f4f3f2; color: #000000;">
													<i class="glyphicon glyphicon-log-in" style="color: #000000;"></i>
													<span class="btn-text" style="color: #000000;"> ورود </span>
												</button>
											</div>
										</form>
										<hr>
									</div>
								</div>
							</div>
							<!-- trust logo 
								<div class="row">
									<div class="col-xl-12">
										<iframe src="https://autoapp.ir/e.html" width="600" height="500" frameborder="0" scrolling="no">Browser not compatible.</iframe>
									</div>	
								</div>
								
								-->
						</div>

					</div>
				</div>

			</div>


			<div class="container-fluid">
				<!-- Footer -->
				<!-- Footer -->
				<?php footer(); ?>
				<!-- /Footer -->
				<!-- /Footer -->

				<!-- /Row -->

				<!-- /Main Content -->

			</div>
			<!-- /#wrapper -->
		</div>
	</div>
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
	<!-- JavaScript -->
	<?php
	if (!empty($Error_STR)) {
		if ($Error_STR == 1) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا درسمت سرور',
								text: 'مشکلی در سرور پیدا شده است' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
			echo '</script>';
		} elseif ($Error_STR == 9) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در کاربری',
								text: 'کاربر فعال نشده است' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
			echo '</script>';
		} elseif ($Error_STR == 8) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در کاربری',
								text: 'رمز عبور اشتباه است' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
			echo '</script>';
		} elseif ($Error_STR == 7) {
			echo '<script language="javascript">';
			echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در کاربری',
								text: 'کاربری با این نام پیدا نشد' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
			echo '</script>';
		} else {
			$Error_STR = Null;
		}
	}


	?>
</body>

</html>