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
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/GarageModel.php");
require("$rootDir/Model/TariffModel.php");
require("$rootDir/Model/AutoModel.php");
require("$rootDir/Model/SmsChanel.php");
require("$rootDir/Model/N_TariffModel.php");
require("$rootDir/Model/NotificationModel.php");

use fateh\AutoShop\AutoShop as garage;
use fateh\Member\Member as member;
use fateh\tariff\tariff as tariff;
use fateh\Automobile\Automobile as auto;
use fateh\smschanel\SMS as sms;
use fateh\tariff\NewTariff as NewTariff;
use fateh\Notification\InAppNotifications as Notif;

$tariff = new tariff($_SESSION["Admin_GUID"]);
$Mechanic_Obj = new member($_SESSION["Admin_GUID"]);
$Garage_obj = new garage($_SESSION["Admin_GUID"]);
$Auto_Obj = new auto($_SESSION["Admin_GUID"]);
$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
$SMS_Obj = new sms();

//Notifications
$Notif_Obj = new Notif($_SESSION["Admin_GUID"]);
$Count_Notif = $Notif_Obj->Gat_NotificationCount();
$Notifications = $Notif_Obj->V_NotificationAll();

//Tariff 

$NTariff_Count = $Tariff_Obj->GET_NTariff_Count();



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
	<script src="/dist/js/mapbox-gl.js"></script>


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
				<div class="col-sm-12">

					<div class="panel panel-default card-view">
						<div class="panel-heading">
							<div class="pull-right">
								<a class="pull-left inline-block" data-toggle="collapse" href="#collapse_1" aria-expanded="true" aria-controls="collapse_2">
									<i class="zmdi zmdi-chevron-down"></i>
									<i class="zmdi zmdi-chevron-up"></i>
								</a>
							</div>
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark"> تحلیل رسته - اعضا </h6>
								</div>
								<div class="clearfix"></div>
							</div>
							<hr>
							<div class="row">
								<div id="collapse_1" class="panel-wrapper collapse in">
									<div class="panel-body">
										<div class="col-lg-6">
											<div class="panel panel-default card-view panel-refresh">
												<div class="refresh-container">
													<div class="la-anim-1"></div>
												</div>
												<div class="panel-heading">
													<div class="pull-left">
														<h6 class="panel-title txt-dark">5 رسته برتر</h6>
													</div>
													<div class="clearfix"></div>
													<hr>
												</div>
												<div class="panel-wrapper collapse in">
													<div class="panel-body row">
														<div class="col-sm-6 pa-0"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
															<canvas id="chart_7" height="164" width="240" style="display: block; width: 240px; height: 164px;"></canvas>
														</div>
														<div class="col-sm-6 pr-0 pt-25">
															<div class="label-chatrs">
																<div class="mb-5">
																	<span class="clabels inline-block bg-yellow mr-5"></span>
																	<span class="clabels-text font-12 inline-block txt-dark capitalize-font">تعمیر و تعویض ترمز و کلاچ</span>
																</div>
																<div class="mb-5">
																	<span class="clabels inline-block bg-pink mr-5"></span>
																	<span class="clabels-text font-12 inline-block txt-dark capitalize-font">تعمیرکار کمک فنر</span>
																</div>
																<div class="mb-5">
																	<span class="clabels inline-block bg-blue mr-5"></span>
																	<span class="clabels-text font-12 inline-block txt-dark capitalize-font">تعمیرکار موتورهای سیار و زمینی</span>
																</div>
																<div class="mb-5">
																	<span class="clabels inline-block bg-red mr-5"></span>
																	<span class="clabels-text font-12 inline-block txt-dark capitalize-font">لنت کوبی</span>
																</div>
																<div class="mb-5">
																	<span class="clabels inline-block bg-green mr-5"></span>
																	<span class="clabels-text font-12 inline-block txt-dark capitalize-font"> گیربکس و دیفرانسیل </span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-lg-6">
											<div class="panel panel-default card-view panel-refresh">
												<div class="panel-heading">
													<div class="pull-left">
														<h6 class="panel-title txt-dark"> وضعیت درخواست های پاسخ داده شده </h6>
													</div>
													<div class="clearfix"></div>
													<hr>
												</div>
												<div class="panel-wrapper collapse in">
													<div class="panel-body pb-0">
														<div class="row">
															<div class="col-sm-4 mb-15 text-center">
																<span id="pie_chart_4" class="easypiechart" data-percent="46">
																	<span class="percent head-font"></span>
																	<canvas height="100" width="100"></canvas></span>
																<p><br /> کارشناسی </p>
															</div>
															<div class="col-sm-4 mb-15 text-center">
																<span id="pie_chart_5" class="easypiechart" data-percent="66">
																	<span class="percent head-font"></span>
																	<canvas height="100" width="100"></canvas></span>
																<p><br /> فک پلمپ </p>
															</div>
															<div class="col-sm-4 mb-15 text-center">
																<span id="pie_chart_6" class="easypiechart" data-percent="90">
																	<span class="percent head-font"></span>
																	<canvas height="100" width="100"></canvas></span>
																<p><br /> تمدید مجوز </p>
															</div>
														</div>
													</div>
												</div>
												<br />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box" style="background: #97999b !important;">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																echo ($Mechanic_Obj->Get_MechanicCount());

																?></span></span>
														<span class="weight-500 uppercase-font txt-light block font-13">تعداد اعضا</span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-id txt-light" style="font-size: xxx-large;"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box" style="background: #97999b !important;">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">

																<?php
																$count = $Auto_Obj->Get_AutoCountAll();
																echo $count;
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد خودروها </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-car txt-light" style="font-size: xxx-large;"></i>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box" style="background: #97999b !important;">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																echo ($Garage_obj->GetGarageCount());
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد تعمیرگاه ها </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 pt-25  data-wrap-right">
														<i class="glyphicon txt-light glyphicon-home" style="font-size: xxx-large;"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box" style="background: #97999b !important;">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																$TariffOLd_count = $tariff->counttariff();
																$count = $NTariff_Count[0]['COUNT(*)'] + $TariffOLd_count;
																echo $count;
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد تعرفه ها </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 pt-25  data-wrap-right">
														<i class="glyphicon txt-light glyphicon-tag" style="font-size: xxx-large;"></i>

													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-right">
									<a class="pull-left inline-block" data-toggle="collapse" href="#collapse_3" aria-expanded="true" aria-controls="collapse_3">
										<i class="zmdi zmdi-chevron-down"></i>
										<i class="zmdi zmdi-chevron-up"></i>
									</a>
								</div>
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-dark"> فهرست اعلان ها </h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<hr>

								<div id="collapse_3" class="panel-wrapper collapse in">
									<div class="panel-body">
										<table id="datable_1" class="table table-hover display  pb-30">
											<thead>
												<tr>
													<th> موضوع </th>
													<th> شرح </th>
													<th> تاریخ شروع </th>
													<th> تاریخ پایان </th>
													<th> وضعیت اعتبار </th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th> موضوع </th>
													<th> شرح </th>
													<th> تاریخ شروع </th>
													<th> تاریخ پایان </th>
													<th> وضعیت اعتبار </th>
												</tr>
											</tfoot>
											<tbody>
												<?php
												if (!empty($Notifications)) {
													foreach ($Notifications as $row) {

														echo "<tr>";
														echo "<td>" . $row['Title'] . "</td>";
														echo "<td>" . $row['Text'] . "</td>";

														$Notif_Sdate = $Notif_Obj->Gat_HejriDate($row['Start']);
														$S_date = gregorian_to_jalali($Notif_Sdate[0], $Notif_Sdate[1], $Notif_Sdate[2]);
														echo "<td>" . $S_date[0] . "/" . $S_date[1] . "/" . $S_date[2] . "</td>";

														$Notif_Edate = $Notif_Obj->Gat_HejriDate($row['End']);
														$E_date = gregorian_to_jalali($Notif_Edate[0], $Notif_Edate[1], $Notif_Edate[2]);
														echo "<td>" . $E_date[0] . "/" . $E_date[1] . "/" . $E_date[2] . "</td>";

														if ($row['Validation'] == 1) {
															$str_validation = '<button class="btn btn-default btn-icon-anim "> معتبر </button>';
														} else {
															$str_validation = '<button class="btn btn-pinterest btn-icon-anim "> نامعتبر </button>';
														}
														echo "<td>" . $str_validation . "</td>";
														echo "</tr>";
													}
												} else {
													echo '<tr><td colspan="5" style="text-align: center;"> اعلانی ثبت نشده است </td></tr>';
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
					<div class="row">
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box bg-red">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																$credit = $SMS_Obj->viewcreditional();
																echo $credit;
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد پیامک های باقی مانده </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-comment txt-light" style="font-size: xxx-large;"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box bg-yellow">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">

																<?php
																echo "3477";

																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد خبرها </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-news-paper txt-light" style="font-size: xxx-large;"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box bg-green">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																echo $Count_Notif['0']['COUNT(*)'];
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد اعلان ها </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-mail-open-file txt-light" style="font-size: xxx-large;"></i>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box bg-blue">
											<div class="container-fluid">
												<div class="row">
													<div class="col-xs-6 text-center pl-0 pr-0 data-wrap-left">
														<span class="txt-light block counter"><span class="counter-anim">
																<?php
																echo "170";
																?>
															</span></span>
														<span class="weight-500 uppercase-font txt-light block"> تعداد مطالب آموزشی </span>
													</div>
													<div class="col-xs-6 text-center  pl-0 pr-0 data-wrap-right">
														<i class="pe-7s-study txt-light" style="font-size: xxx-large;"></i>
													</div>
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
			</div>
			<div>
				<!-- Main Content -->
				<?php footer(); ?>
				<!-- /Main Content -->
			</div>
		</div>
	</div>
	<!-- /#wrapper -->

	<!-- jQuery -->
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
	<script src="dist/js/easypiechart-data.js"></script>

	<!-- Progressbar Animation JavaScript -->
	<script src="vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
	<script src="vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>

	<!-- Data table JavaScript -->
	<script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>

	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>

	<!-- ChartJS JavaScript -->
	<script src="vendors/chart.js/Chart.min.js"></script>

	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>

	<!-- Sparkline JavaScript -->
	<script src="vendors/jquery.sparkline/dist/jquery.sparkline.min.js"></script>

	<script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>

	<!-- Morris Charts JavaScript -->
	<script src="vendors/bower_components/raphael/raphael.min.js"></script>
	<script src="vendors/bower_components/morris.js/morris.min.js"></script>
	<script src="dist/js/morris-data.js"></script>

	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

	<!-- Bootstrap Select JavaScript -->
	<script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>
	<script src="dist/js/widgets-data.js"></script>


</body>

</html>