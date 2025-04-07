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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!isset($_POST['edittip'])) {
			if ($_POST['action'] == "edit") {
				$ID = $_POST['tipID'];
				$Auto_ = new auto($_SESSION["Admin_GUID"]);
				$Auto_name = $Auto_->Get_Tip_ByID($ID);
				$Manufacture = $Auto_->Get_Manufactuer_ByID($Auto_name[0]["ManufacturerID"]);
				$ManufactureAll = $Auto_->Get_ManufactuerAll();
			}
		}
	} else {
		$url = "./V_Tips.php";
		header("Location: $url");
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
								$('#autotip').html(html);
							}
						});
					}
				});
			});
		</script>

	</head>

	<body>
		<?php

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['edittip'])) {
				$ID = $_POST['tipID'];
				$updatetip = new auto($_SESSION["Admin_GUID"]);
				//get old value 
				$get_tip = $updatetip->Get_Tip_ByID($ID);
				$get_tipname = $get_tip[0]["ModelName"];
				$get_tipman = $get_tip[0]["ManufacturerID"];
				// form post value
				$post_tipname = $_POST['tipname'];
				$post_tipman = $_POST['Manufacturer'];

				if (empty($post_tipname)) {
					$tipname = $get_tipname;
				} else {
					if ($post_tipname == $get_tipname) {
						$tipname = $get_tipname;
					} else {
						$tipname = $post_tipname;
					}
				}

				if ($post_tipman == $get_tipman) {
					$tipman = $get_tipman;
				} else {
					$tipman = $post_tipman;
				}

				$route = $updatetip->U_Tip($ID, $tipname, $tipman);
				if (!empty($route)  ) {
					// return VALUES
					$url = "./V_Tips.php";
					header("Location: $url");
				} else {
					echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
				}
			}
		}

		?>
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
											<h6 class="panel-title txt-dark"> ویرایش خودرو </h6>
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
													<input type="hidden" id="tipID" name="tipID" value="<?php echo $_POST['tipID']; ?>">
													<div class="form-group">
														<div class="input-group col-sm-6">

															<label class="control-label mb-10"> نام خودروساز : </label>
															<select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Manufacturer" id="Manufacturer">
																<option> انتخاب نمایید </option>

																<?php
																foreach ($ManufactureAll as $row) {

																	if ($row['ID'] == $Manufacture[0]["ID"]) {
																		echo '<option value="' . $row['ID'] . '" selected="selected" >' . $row['Name'] . '</option>';
																	} else {
																		echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
																	}
																}
																?>

															</select>
														</div>


														<br />
														<label class="control-label mb-10" for="exampleInputuname_1"> تیپ خودرو : </label>
														<div class="input-group col-sm-6">
															<div class="input-group-addon"><i class="icon-info"></i></div>
															<input type="text" class="form-control" name="tipname" id="tipname" placeholder="<?php echo $Auto_name[0]['ModelName']; ?>">
														</div>
														<br />

														<div class="form-group">
															<button class="btn btn-info btn-anim" type="submit" name="edittip" id="edittip"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>

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
				<!-- JavaScript -->
				<?php MainJavasc(); ?>
				<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
				<script LANGUAGE="JavaScript">
					function validation() {
						var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
						if (agree)
							return true;
						else
							return false;
					}
				</script>
			</div>

		</div>
		<!-- /#wrapper -->

	</body>

	</html>