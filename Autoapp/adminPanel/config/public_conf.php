<?php
date_default_timezone_set("Asia/Tehran");
//const PageTitle = 'تعاونی اتحادیه مکانیک های تهران'; P3nd@r493 ---- Xperi@z2mini
//DBfateh - Xperi@DB530
define('PageTitle', 'اتواپ-Autoapp');
define('logopng', '<img src="images/logo-autoapp.png" alt="logo" style="height: 100%;width: 100%;padding: 0px 5px 0px 0px;">');
define('Meta', 'شرکت تعاونی تعمیرکاران خودرو فاوا');
define('copywrite', '© 1399');
define('Developed', 'Developed By #Ali-Fatehchehr2020==> alifatehchehr@gmail.com');
//require_once('config/jdf.php');
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/jdf.php");
$now_date = jstrftime('%Y %B %e');
$now_dayofweek = jstrftime('%A');
$now_time = jstrftime('%H:%M:%S');

function current_url()
{
	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$validURL = str_replace("&", "&amp", $url);
	return $validURL;
}

function Create_url($url)
{
	$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
	$url = trim($url, "-");
	$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
	$url = strtolower($url);
	$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	return $url;
}

function Metatag()
{
	echo '<link rel="icon" href="images/cropped-Untitled-1-64x64.jpg" sizes="32x32" />
		<link rel="icon" href="images/cropped-Untitled-1-200x200.jpg" sizes="192x192" />
		<link rel="apple-touch-icon" href="images/cropped-Untitled-1-200x200.jpg" />
		<meta name="msapplication-TileImage" content="images/cropped-Untitled-1-300x300.jpg" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="description" content="' . Meta . '"></meta>
		<meta name="Developer" content="' . Developed . '"></meta>
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>' . PageTitle . '</title>';
}

function js_number()
{
	$str = "
	function format(input) {
		var nStr = input.value + '';
		nStr = nStr.replace(/\,/g, '');
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		input.value = x1 + x2;
	}

	function onlyNumberKey(evt) {

		// Only ASCII charactar in that range allowed 
		var ASCIICode = (evt.which) ? evt.which : evt.keyCode
		if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
			return false;
		} else {
			return true;
		}
	}";
	return $str;
}


function minimenu()
{
	echo '<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="mobile-only-brand pull-left" style ="height: 70px;">
				<a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block ml-20 pull-left" href="javascript:void(0);">' . logopng . '</a>
				<a id="toggle_mobile_nav" class="mobile-only-view" href="javascript:void(0);"><i class="zmdi zmdi-more"></i></a>
				</div>
			<div id="mobile_only_nav" class="mobile-only-nav pull-right">
				<ul class="nav navbar-right top-nav pull-right">
					<li class="dropdown auth-drp">
						<a href="#" class="dropdown-toggle pr-0" data-toggle="dropdown">
						<img src="images/vector.png" alt="profile" class="user-auth-img img-circle"/>
						</a>
						<ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
							<li>
								<a href="#"><i class="zmdi zmdi-account"></i><span>' . $_SESSION["userFLname"] . '</span></a>
							</li>
							<li>
								<a href="#"><i class="zmdi zmdi-card"></i><span> راهنما </span></a>
							</li>
							<li class="divider"></li>
							<li>
								<a href="logout.php"><i class="zmdi zmdi-power"></i><span> خروج </span></a>
							</li>
						</ul>
					</li>
				</ul>
			</div>	
		</nav>';
}




function Mainmenu()
{

	$now_day = jstrftime('%e');
	$now_month = jstrftime('%B');
	$now_dayofweek = jstrftime('%A');

	echo '<div class="fixed-sidebar-left">
			<ul class="nav navbar-nav side-nav nicescroll-bar" style="background:#f6f6f6;">
				<li class="navigation-header">
					<div class="nav-header" style="padding: 5px 15px 5px 0px;">
					<br />
						<i class="icon-clock"></i>
						<span class="user-online-status" style="position:absolute; padding-right: 20px;">' . $now_dayofweek . ' / ' . $now_day . ' / ' . $now_month . ' </span>
						<hr class="light-grey-hr mb-10">
					</div>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_dr">
						<div class="pull-left">
							<i class="icon-home"></i>
							<span class="right-nav-text"> خانه </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="dashboard_dr" class="collapse collapse-level-1">
						<li><a href="index.php"><i class="icon-grid"></i> داشبرد </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="V_AdminUsers.php"><i class="icon-book-open"></i> فهرست مدیران </a></li>
						<li><a href="V_PermissionAll.php"><i class="icon-book-open"></i> فهرست دسترسی ها </a></li>
						
						
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#member-management">
						<div class="pull-left">
							<i class="ti-id-badge"></i>
							<span class="right-nav-text"> مدیریت اعضا </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							</div><div class="clearfix">
						</div>
					</a>
					<ul id="member-management" class="collapse collapse-level-1 two-col-list">
					  <li><a href="C_Members.php"><i class="icon-user-follow"></i> تعریف اعضا </a></li>
					  <li><a href="V_MemberAll.php"><i class="icon-book-open"></i> فهرست اعضا </a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#repair-shop">
						<div class="pull-left">
							<i class="icon-wrench"></i>
							<span class="right-nav-text"> مدیریت تعمیرگاه ها </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							</div><div class="clearfix">
						</div>
					</a>
					<ul id="repair-shop" class="collapse collapse-level-1 two-col-list">
					  <li><a href="C_Garage.php"><i class="icon-pencil"></i> تعریف تعمیرگاه </a></li>
					  <li><a href="V_GarageAll.php"><i class="icon-book-open"></i> فهرست تعمیرگاه </a></li>
					</ul>
				</li>
				<li>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown_map">
				<div class="pull-left">
				<i class="icon-location-pin"></i>
				<span class="right-nav-text"> نقشه </span>
				</div>
				<div class="pull-right">
				<i class="zmdi zmdi-caret-down"></i>
				</div>
				<div class="clearfix"></div>
				</a>

					<ul id="dropdown_map" class="collapse collapse-level-1">
						<li>
							<li><a href="V_SearchGarage.php"><i class="icon-compass"></i> جستجو بر روی نقشه </a></li>
							<li><a href="V_AllGarage_Map.php"><i class="icon-compass"></i> نقشه تعمیرگاه های مجاز </a></li>
						</li>
						
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#tarrif">
						<div class="pull-left">
							<i class="icon-tag"></i>
							<span class="right-nav-text"> تعرفه های صنفی </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							</div><div class="clearfix">
						</div>
					</a>
					<ul id="tarrif" class="collapse collapse-level-1 two-col-list">
					<li><a href="C_TariffType.php"><i class="icon-pencil"></i>تعریف انواع تعرفه</a></li>
					<li><a href="C_NTariffService.php"><i class="icon-pencil"></i> تعریف سرویس </a></li>
					<li><a href="C_NTariffVersion.php"><i class="icon-pencil"></i> تعریف نسخه </a></li>
					<li><a href="C_NTariff.php"><i class="icon-pencil"></i> تعیین تعرفه </a></li>
					<li><hr class="light-grey-hr mb-10"></li>
					<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown" class="" >
						<div class="pull-left">
						<i class="icon-book-open"></i>
						<span class="right-nav-text"> فهرست سرویس ها  </span>
						</div><div class="pull-right">
						<i class="zmdi zmdi-caret-down"></i>
						</div><div class="clearfix"></div></a>
						<ul id="dropdown" class="collapse-level-1 collapse in">
							<li>
								<a href="V_NTariffServices.php"> فهرست سرویس ها  </a>
							</li>
							<li>
								<a href="V_NTariffSepecialSER.php"> سرویس های خاص </a>
							</li>
						</ul>
					</li>
					<li><hr class="light-grey-hr mb-10"></li>
					<li><a href="V_TariffType.php"><i class="icon-book-open"></i> فهرست انواع تعرفه ها </a></li>
					<li><a href="V_NTariffVersion.php"><i class="icon-book-open"></i> فهرست نسخ </a></li>
					<li><a href="V_NTariffAll.php"><i class="icon-book-open"></i> فهرست تعرفه ها </a></li>
					<li><a href="V_NTariffArchive.php"><i class="icon-book-open"></i> آرشیو تعرفه ها </a></li>
					<li><hr class="light-grey-hr mb-10"></li>
                     <li><a href="U_NTariffCredential.php"><i class="icon-check"></i> مدیریت اعتبار نسخ </a></li>
					<li><hr class="light-grey-hr mb-10"></li>
					  <li><a href="C_Contract.php"><i class="icon-pencil"></i> تعریف تعهدنامه </a></li>
					  <li><a href="#"> فهرست تعهد نامه ها </a></li>
					  <li><a href="#"> فهرست تاییدیه ها </a></li>
					  
					</ul>
				</li>

				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#form_dr">
						<div class="pull-left">
							<i class="icon-pencil"></i>
							<span class="right-nav-text"> فرم ها </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							</div><div class="clearfix">
						</div>
					</a>
					<ul id="form_dr" class="collapse collapse-level-1 two-col-list">
					  <li><hr class="light-grey-hr mb-10"></li>
					  <li><a href="insert-auto-manufacture.php"><i class="pe-7s-car"></i> تعریف خودروساز </a></li>
                      <li><a href="insert-auto-model.php"><i class="pe-7s-car"></i> تعریف تیپ خودرو </a></li>
                      <li><a href="insert-auto-name.php"><i class="pe-7s-car"></i> تعریف خودرو </a></li>
					  <li><hr class="light-grey-hr mb-10"></li>
					  <li><a href="C_Category.php"><i class="icon-pencil"></i> تعریف رسته </a></li>
					  <li><a href="insert-skills.php"><i class="icon-pencil"></i> تعریف تخصص </a></li>
                      <li><a href="insert-devices.php"><i class="icon-pencil"></i> تعریف دستگاه </a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#lists_dr">
						<div class="pull-left">
							<i class="icon-book-open"></i>
							<span class="right-nav-text"> فهرست ها </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="lists_dr" class="collapse collapse-level-1">
					  <li><a href="V_Manufactures.php"><i class="pe-7s-car"></i> فهرست خودروسازان </a></li>
                      <li><a href="V_Tips.php"><i class="pe-7s-car"></i> فهرست تیپ خودروها </a></li>
                      <li><a href="V_Automobiles.php"><i class="pe-7s-car"></i> فهرست خودروها </a></li>
					  <li><hr class="light-grey-hr mb-10"></li>
                      <li><a href="V_Category.php"><i class="icon-book-open"></i> فهرست رسته ها </a></li>
                      <li><a href="V_SkillsAll.php"><i class="icon-book-open"></i> فهرست تخصص ها </a></li>
                      <li><a href="V_Devices.php"><i class="icon-book-open"></i> فهرست دستگاه ها </a></li>					  
					  
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#finance">
						<div class="pull-left">
							<i class="glyphicon glyphicon-usd"></i>
							<span class="right-nav-text"> مدیریت مالی </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="finance" class="collapse collapse-level-1">
						<li><a href="insert-invoice.php"><i class="icon-puzzle"></i> ایجاد صورت حساب </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="V_AllInvoives.php"><i class="icon-book-open"></i> فهرست صورت حساب ها </a></li>
						<li><a href="V_PaidBills.php"><i class="icon-book-open"></i> فهرست پرداختی ها </a></li>
						<li><a href="V_NotPaidBills.php"><i class="icon-book-open"></i> فهرست بدهکاران </a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#client_options">
						<div class="pull-left">
							<i class="icon-equalizer"></i>
							<span class="right-nav-text"> مدیریت پرتال اعضا </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="client_options" class="collapse collapse-level-1">
						<li><a href="C_Documents.php"><i class="icon-pencil"></i> تعریف مستندات </a></li>
						<li><a href="V_Documents.php"><i class="icon-book-open"></i> فهرست مستندات </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="C_InAPPNotif.php"><i class="icon-pencil"></i> تعریف اعلان در برنامه</a></li>
						<li><a href="V_InAPPNotif.php"><i class="icon-book-open"></i> فهرست اعلان ها </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="#"> ایجاد خبر </a></li>
						<li><a href="#"> فهرست اخبار </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="C_MainSlide.php"><i class="icon-pencil"></i> ایجاد اسلاید اصلی </a></li>
						<li><a href="V_MainSlide.php"><i class="icon-book-open"></i> اسلایدهای اصلی </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
						<li><a href="C_ProductSlide.php"><i class="icon-pencil"></i> ایجاد اسلاید محصولات </a></li>
						<li><a href="V_ProductSlide.php"><i class="icon-book-open"></i> اسلایدهای محصولات </a></li>
						<li><hr class="light-grey-hr mb-10"></li>
                        <li><a href="C_Banners.php"><i class="icon-pencil"></i> ایجاد بنر </a></li>
                        <li><a href="V_Banners.php"><i class="icon-book-open"></i> فهرست بنرها </a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#request">
						<div class="pull-left">
							<i class="icon-bell"></i>
							<span class="right-nav-text"> درخواست ها </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="request" class="collapse collapse-level-1">
					  <li><a href="#"> فهرست درخواست ها </a></li>
                      <li><a href="#"> فهرست پاسخ ها </a></li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#smsmanager">
						<div class="pull-left">
							<i class="ti-email"></i>
							<span class="right-nav-text"> مدیریت پیام کوتاه </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="smsmanager" class="collapse collapse-level-1">
					<li><a href="send-single-sms.php"><i class="ti-comment-alt"></i> ارسال پیامک تکی </a></li>
					<li><a href="#"> ارسال پیامک گروهی </a></li>
					<li><hr class="light-grey-hr mb-10"></li>
					  <li><a href="#"><i class="icon-book-open"></i> فهرست ارسالی ها </a></li>
                      <li><a href="#"><i class="icon-book-open"></i> فهرست دریافتی ها </a></li>
					  <li><hr class="light-grey-hr mb-10"></li>
                      <li><a href="#"> نظرسنجی پیامکی </a></li>
                      <li><a href="#"> نتیجه نظرسنجی </a></li>
					  <li><hr class="light-grey-hr mb-10"></li>
					  <li><a href="view-sms-statistics.php"><i class="icon-cup"></i> گزارش های سرویس </a></li>
					</ul>
				</li>				
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#learning">
						<div class="pull-left">
							<i class="icon-graduation"></i>
							<span class="right-nav-text"> آموزش </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="learning" class="collapse collapse-level-1">
					  <li><a href="#"> ایجاد محتوای </a></li>
                      <li><a href="#"> فهرست محتوا </a></li>
					  
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#phonebook_dr">
						<div class="pull-left">
							<i class="icon-notebook"></i>
							<span class="right-nav-text"> دفترچه تلفن </span>
						</div><div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="phonebook_dr" class="collapse collapse-level-1 two-col-list">
					  <li><a href="V_Phonebook.php"><i class="icon-notebook"></i> دفترچه تلفن </a></li>
                      <li><a href="C_IndependentContact.php"><i class="icon-pencil"></i> تعریف تلفن </a></li>
					</ul>
				</li>				
				<li>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown_log_lv1">
					<div class="pull-left">
						<i class="icon-chart"></i>
						<span class="right-nav-text"> گزارش ها </span>
					</div>
					<div class="pull-right">
						<i class="zmdi zmdi-caret-down"></i>
					</div>
					<div class="clearfix"></div>
				</a>
					<ul id="dropdown_log_lv1" class="collapse collapse-level-1">
						<li>
							<li><a href="V_AdminsLogs.php"><i class="icon-cup"></i> گزارش عملکرد مدیران </a></li>
							<li><hr class="light-grey-hr mb-10"></li>
							<li><a href="V_MechanicLogs.php"><i class="icon-cup"></i> گزارش عملکرد اعضا </a></li>
							<li><hr class="light-grey-hr mb-10"></li>
							<li><a href="U_AdvMonitoring.php"><i class="icon-chart"></i> فیلتر گزارش </a></li>
							<li><a href="V_AdvMonitoring.php"><i class="icon-chart"></i> مانیتورینگ تبلیغات </a></li>
						</li>
						
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" data-toggle="collapse" data-target="#adv-search">
						<div class="pull-left">
							<i class="icon-magnifier"></i>
							<span class="right-nav-text"> جستجوی پیشرفته </span>
						</div>
						<div class="pull-right">
							<i class="zmdi zmdi-caret-down"></i>
							
						</div>
						<div class="clearfix"></div>
					</a>
					<ul id="adv-search" class="collapse collapse-level-1">
					  <li><a href="#"> جستجوی مکانیک </a></li>
                      <li><a href="#"> جستجوی تعمیرگاه</a></li>
					  <li><a href="#"> جستجوی تعرفه </a></li>
                      <li><a href="#"> جستجوی خودرو </a></li>
					  
					</ul>
				</li>
				<li>
				<a href="javascript:void(0);" data-toggle="collapse" data-target="#dropdown_links">
				<div class="pull-left">
				<i class="icon-globe"></i>
				<span class="right-nav-text"> اتصالات </span>
				</div>
				<div class="pull-right">
				<i class="zmdi zmdi-caret-down"></i>
				</div>
				<div class="clearfix"></div>
				</a>
					<ul id="dropdown_links" class="collapse collapse-level-1">
						<li>
							<li><a href="http://mechanic-tehran.com/"><i class="icon-action-redo"></i> سایت اتحادیه </a></li>
							<li><hr class="light-grey-hr mb-10"></li>
							<li><a href="https://www.autoapp.ir/"><i class="icon-action-redo"></i> سایت تعاونی </a></li>
							<li><hr class="light-grey-hr mb-10"></li>
							<li><a href="https://shop.autoapp.ir/"><i class="icon-action-redo"></i> فروشگاه </a></li>
						</li>
						
					</ul>
				</li>
			</ul>
		</div>';
}

function MainJavasc()
{
	echo '<!-- jQuery -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
	<!-- Data table JavaScript -->
	<script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="dist/js/dataTables-data.js"></script>
	
	<!-- Slimscroll JavaScript -->
	<script src="dist/js/jquery.slimscroll.js"></script>
	
	<!-- Owl JavaScript -->
	<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>
	
	<!-- Switchery JavaScript -->
	<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>
	
	<!-- Fancy Dropdown JS -->
	<script src="dist/js/dropdown-bootstrap-extended.js"></script>
	
	<!-- Init JavaScript -->
	<script src="dist/js/init.js"></script>';
}


function footer()
{
	echo '<footer class="footer container-fluid pl-30 pr-30">
				<div class="row">
					<div class="col-sm-12" style ="color: #ed3236; margin: 10px 10px 5px 5px;"> تحت نظارت اتحادیه صنف تعمیرکاران خودرو تهران ' . copywrite . '</div>
				</div>
			</footer>';
}
