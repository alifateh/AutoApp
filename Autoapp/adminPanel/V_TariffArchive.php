<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "sysAdmin.php";
	header("Location: $url");
}
require_once('config/public_conf.php');
require('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;

if (isset($_POST['action'])) {
	if ($_POST['action'] == "remove") {
		$ID = $_POST['tariff-ID'];
		$remove_tariff = new tariff($_SESSION["Admin_GUID"]);
		$remove_tariff->removetariff($ID);
		$url = "V_TariffValid.php";
		header("Location: $url");
	}
}

if (isset($_POST['action'])) {
	if ($_POST['action'] == "valid") {
		$ID = $_POST['tariff-ID'];
		$notvalid_tariff = new tariff($_SESSION["Admin_GUID"]);
		$notvalid_tariff->maketariffnotvalid($ID);
		$url = "V_TariffValid.php";
		header("Location: $url");
	}
}

if (isset($_POST['action'])) {
	if ($_POST['action'] == "not-valid") {
		$ID = $_POST['tariff-ID'];
		$notvalid_tariff = new tariff($_SESSION["Admin_GUID"]);
		$notvalid_tariff->maketariffvalid($ID);
		$url = "V_TariffValid.php";
		header("Location: $url");
	}
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
										<h6 class="panel-title txt-dark"> آرشیو تعرفه ها </h6>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<hr>

							<div id="collapse_2" class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30">
												<thead>
													<tr>
														<th> مشخصات خودرو [ خودروساز] [نام خودرو] [تیپ خودرو] </th>
														<th> نسخه تعرفه </th>
														<th> تاریخ اعتبار تعرفه </th>
														<th> وضعیت اعتبار </th>
														<th> عملیات </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th> مشخصات خودرو [ خودروساز] [نام خودرو] [تیپ خودرو] </th>
														<th> نسخه تعرفه </th>
														<th> تاریخ اعتبار تعرفه </th>
														<th> وضعیت اعتبار </th>
														<th> عملیات </th>
													</tr>
												</tfoot>
												<tbody>
													<?php
													$tariff = new tariff($_SESSION["Admin_GUID"]);
													$data = $tariff->V_TariffArchive();
													if (!empty($data)) {
														foreach ($data as $row) {
															echo "<tr>";
															$auto = $tariff->gettariffauto($row['AutomobileID']);
															$auto_name = " نام خودرو: " . $auto[2];
															$auto_tipID = $auto[1];
															if ($auto_tipID !== 0 && $auto_tipID !== '****') {
																$auto_tipName = $tariff->gettariffautotip($auto_tipID);
																$tip = " تیپ :" . $auto_tipName;
															} else {
																$tip = "بدون تیپ";
															}
															$auto_manID = $auto[0];
															if ($auto_manID !== '****') {
																$auto_manName = "خودروساز : " . $tariff->gettariffautoman($auto_manID);
															}
															echo "<td> [" . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . "]</td>";
															$tarrif_verion_name = $tariff->getversion($row['Version']);
															echo "<td>" . $tarrif_verion_name . "</td>";
															$tarif_date = $tariff->gettariffdateinhejri($row['ValidateDate']);
															$valid_date = gregorian_to_jalali($tarif_date[0], $tarif_date[1], $tarif_date[2]);
															echo "<td>" . $valid_date[0] . "/" . $valid_date[1] . "/" . $valid_date[2] . "</td>";
															if ($row['Validation'] == 1) {
																$str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
		                                                    		<button class="btn btn-default btn-icon-anim " onclick="return changetonotvalid()"> معتبر </button>
		                                                    		<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    		<input type="hidden" name="action" value="valid"></form>';
															} else {
																$str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
		                                                    		<button class="btn btn-pinterest btn-icon-anim " onclick="return changetovalid()"> نامعتبر </button>
		                                                    		<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    		<input type="hidden" name="action" value="not-valid"></form>';
															}
															echo "<td>" . $str_validation . "</td>";
															echo '<td>';
															echo '<div class="dropdown">
		                                                    			<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    				عملیات
		                                                    				<span class="caret"></span>
		                                                    				</a>
		                                                    				<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    					<li>
		                                                    					<form method="post" enctype="multipart/form-data" action="edit-tariff-VersionID.php">
		                                                    						<input type="hidden" name="editVer-ID" value="' . $row["ID"] . '">
		                                                    						<input type="hidden" name="tariff-ver" value="' . $row["Version"] . '">
		                                                    						<input type="hidden" name="action" value="editVersionID">
		                                                    					    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش نسخه </button>
		                                                    					</form>
		                                                    					</li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data" action="edit-tariff-Datevalid.php">
		                                                    							<input type="hidden" name="editVer-ID" value="' . $row["ID"] . '">
		                                                    							<input type="hidden" name="ValidateDate" value="' . $row["ValidateDate"] . '">
		                                                    							<input type="hidden" name="action" value="edittariffDate">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> ویرایش تاریخ اعتبار </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						 <form method="post" enctype="multipart/form-data" action="edit-tariff-value.php">
		                                                    							<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    							<input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
		                                                    							<input type="hidden" name="action" value="not-valid">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> ویرایش اجرت ها </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						<form method="post" enctype="multipart/form-data" action="edit-tariff-order.php">
		                                                    							 <input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    							 <input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
		                                                    							 <button style ="border:none;"><i class="icon-pencil"></i> ترتیب نمایش </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						<form method="post" enctype="multipart/form-data" action="V_TariffOver.php">
		                                                    							 <input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    							 <input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
		                                                    							 <button style ="border:none;"><i class="icon-book-open"></i> نمایش تعرفه </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data" action="PDFGenerator.php">
		                                                    							<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    							<input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
		                                                    							<input type="hidden" name="pdf" value="pdf">
		                                                    							<button style ="border:none;"><i class="icon-printer"></i> دریافت PDF </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					<li class="divider"></li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data">
		                                                    							<input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
		                                                    							<input type="hidden" name="action" value="remove">
		                                                    							<button style ="border:none;" onclick="return removevalidation()"><i class="icon-close"></i> حذف تعرفه </button>
		                                                    						</form>
		                                                    					</li>
		                                                    				</ul>
		                                                    		</div>';
															echo "</td>";
															echo "</tr>";
														}
													} else {
														echo '<tr><td colspan="5" style="text-align: center;"> تعرفه ایی موجود نمی باشد </td></tr>';
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

	<script LANGUAGE="JavaScript">
		function removevalidation() {
			var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}

		function changetonotvalid() {
			var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}

		function changetovalid() {
			var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
			if (agree)
				return true;
			else
				return false;
		}
	</script>

</body>

</html>