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
	<?php

	require_once('Model/SmsChanel.php');

	use fateh\smschanel\SMS as sms;

	if (isset($_POST['sendsinglesms']) and isset($_POST['txt'])) {
		if ($_POST['searchNUM'] !== "") {
			$sendsinglesms = new sms();
			$number = $_POST['searchNUM'];
			$txt = $_POST['txt'];
			$cell_arr = array();
			$cell_arr[0] = $number;
			$topic = 3;
			$responce = $sendsinglesms->send_sms($cell_arr, $txt, $topic);
		} else {
			echo '<script language="javascript">';
			echo 'alert("لطفا شماره تلفن گیرنده را وارد کنید.")';
			echo '</script>';
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
										<h6 class="panel-title txt-dark"> ارسال پیامک تکی </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<form method="post" enctype="multipart/form-data" action="#">
										<div id="example-basic">
											<div class="col-sm-6">

												<div class="form-group">
													<label class="control-label mb-10" for="exampleInputuname_1"> شماره همراه : </label>
													<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="searchNUM" id="searchNUM" placeholder="نام یا شماره" minlength="10" maxlength="10" required>
													</div>
													<br />

												</div>
												<br />
												<br />
												<div class="form-group">
													<label class="control-label mb-10 text-left"> متن پیامک : </label>
													<textarea class="form-control" rows="3" name="txt" id="txt" required></textarea>
												</div>
												<div class="form-group">
													<button class="btn btn-info btn-anim" type="submit" name="sendsinglesms" id="sendsinglesms"><i class="ti-comment-alt"></i><span class="btn-text"> ارسال </span></button>
												</div>
									</form>
								</div>
								<div class="col-sm-6">
									<div class="panel panel-inverse card-view">
										<div class="panel-heading">
											<div class="pull-left">
												<h6 class="panel-title">نتیجه جستجوی دفترچه تلفن</h6>
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="panel-wrapper collapse in">
											<div class="panel-body">
												<div id="results"></div>
											</div>
										</div>
									</div>
									<?php
									if (isset($responce)) {
										if ($responce == 1) {
											echo '<div class="panel panel-success card-view">
																<div class="panel-heading">
																	<div class="pull-left" data-icon="">
																		<h6 class="panel-title"><i class="ti-email"></i> پیام سیستم</h6>
																	</div>
																	<div class="clearfix"></div>
																</div>
																<div class="panel-wrapper collapse in">
																	<div class="panel-body">
																		<p>پیامک با موفقیت ارسال شد</p></div></div></div>';
										} else {

											echo '<div class="panel panel-info card-view">
																<div class="panel-heading">
																	<div class="pull-left">
																		<h6 class="panel-title"><i class="ti-email"></i> پیام سیستم</h6>
																	</div>
																	<div class="clearfix"></div>
																</div>
																<div class="panel-wrapper collapse in">
																	<div class="panel-body">
																		<p> پیامک ارسال نشد </p></div></div></div>';
										}
									}

									?>
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
	<?php MainJavasc(); ?>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#searchNUM").on("keyup", function() {
				var num = $(this).val();
				if (num !== "") {
					$.ajax({
						url: "ajax-phonebook-search.php",
						type: "POST",
						cache: false,
						data: {
							searchval: num
						},
						success: function(data) {
							$("#results").html(data);
							$("#results").fadeIn();
						}
					});
				} else {
					$("#results").html("");
					$("#results").fadeOut();
				}
			});

			// click one particular city name it's fill in textbox
			$(document).on("click", "li", function() {
				$('#searchNUM').val($(this).text());
				$('#results').fadeOut("fast");
			});
		});


		function findselect(obj) {
			var t = $(obj).text();
			document.getElementById("searchNUM").value = t;
		}

		$('#find').click(function() {
			document.getElementById("searchNUM").placeholder = $(this).text();
		});
	</script>
</body>

</html>