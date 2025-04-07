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
require('Model/AutoModel.php');
require('Model/N_TariffModel.php');

use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
$Auto_Obj = new auto($_SESSION["Admin_GUID"]);
$data = $Tariff_Obj->V_NTariffAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "remove") {
            $GUID = $_POST['tariff-ID'];
            $route = $Tariff_Obj->D_NTariff_ByID($GUID);
        }
        if ($_POST['action'] == "valid") {
            $GUID = $_POST['tariff-ID'];
            $Validation = 1;
            $route = $Tariff_Obj->U_NTariffValidation_ByID($GUID, $Validation);

        }
        if ($_POST['action'] == "not-valid") {
            $GUID = $_POST['tariff-ID'];
            $Validation = 0;
            $route = $Tariff_Obj->U_NTariffValidation_ByID($GUID, $Validation);
        }
    }
    if (!empty($route) && $route == 1) {
        // return VALUES
        $url = "V_NTariffAll.php";
        header("Location: $url");
    } elseif(!empty($route)) {
        $Error_STR = 1;
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
                                        <h6 class="panel-title txt-dark"> فهرست تعرفه ها </h6>
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
                                                        <th> نوع تعرفه </th>
                                                        <th> نسخه تعرفه </th>
                                                        <th> تاریخ اعتبار تعرفه </th>
                                                        <th> وضعیت اعتبار </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> مشخصات خودرو [ خودروساز] [نام خودرو] [تیپ خودرو] </th>
                                                        <th> نوع تعرفه </th>
                                                        <th> نسخه تعرفه </th>
                                                        <th> تاریخ اعتبار تعرفه </th>
                                                        <th> وضعیت اعتبار </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($data)) {
                                                        foreach ($data as $row) {
                                                            echo "<tr>";
                                                            $auto = $Auto_Obj -> Get_Automobile_ByID ($row['AutoGUID']);
                                                            $auto_name = " نام خودرو: " . $auto[0]['Name'];
                                                            $auto_tipID =  $auto[0]['ModelID'];
                                                            $auto_manID = $auto[0]['ManufacturerID'];
                                                            $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
                                                            $tip = " تیپ :" . $auto_tipName [0]['ModelName'];
                                                            $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
                                                            $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];
                                                            echo "<td> [" . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . "]</td>";
                                                            
                                                            $NTariffType = $Tariff_Obj->Get_TariffType_ByID($row['TariffTypeGUID']);
                                                            echo "<td>" . $NTariffType[0]['NameFa'] . "</td>";

                                                            $tarrif_verion_name = $Tariff_Obj->Get_NTariffVersion_ByID($row['TariffVerGUID']);
                                                            echo "<td>" . $tarrif_verion_name[0]['NameFa'] . "</td>";

                                                            $tarif_date = $Tariff_Obj->Gat_HejriDate($row['ValidateDate']);
                                                            $valid_date = gregorian_to_jalali($tarif_date[0], $tarif_date[1], $tarif_date[2]);
                                                            echo "<td>" . $valid_date[0] . "/" . $valid_date[1] . "/" . $valid_date[2] . "</td>";

                                                            if ($row['Validation'] == 1) {
                                                                $str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
		                                                    		<button class="btn btn-default btn-icon-anim " onclick="return changetonotvalid()"> معتبر </button>
		                                                    		<input type="hidden" name="tariff-ID" value="' . $row['GUID'] . '">
		                                                    		<input type="hidden" name="action" value="not-valid"></form>';
                                                            } else {
                                                                $str_validation = '<form name="myform" method="post" enctype="multipart/form-data">
		                                                    		<button class="btn btn-pinterest btn-icon-anim " onclick="return changetovalid()"> نامعتبر </button>
		                                                    		<input type="hidden" name="tariff-ID" value="' . $row['GUID'] . '">
		                                                    		<input type="hidden" name="action" value="valid"></form>';
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
		                                                    					<form method="post" enctype="multipart/form-data" action="U_NTariffVer.php">
		                                                    						<input type="hidden" name="NTariffGUID" value="' . $row["GUID"] . '">
		                                                    						<input type="hidden" name="action" value="Edit-NTariffVer">
		                                                    					    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش نسخه </button>
		                                                    					</form>
		                                                    					</li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data" action="U_NTariffDate.php">
		                                                    							<input type="hidden" name="NTariffGUID" value="' . $row["GUID"] . '">
		                                                    							<input type="hidden" name="action" value="Edit-NTariffDate">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> ویرایش تاریخ اعتبار </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						 <form method="post" enctype="multipart/form-data" action="U_NTariffValue.php">
		                                                    							<input type="hidden" name="NTariffGUID" value="' . $row['GUID'] . '">
		                                                    							<input type="hidden" name="action" value="Edit-NTariffValue">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> ویرایش اجرت ها </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						<form method="post" enctype="multipart/form-data" action="U_NTariffOrder.php">
                                                                                    <input type="hidden" name="NTariffGUID" value="' . $row['GUID'] . '">
                                                                                    <input type="hidden" name="action" value="Edit-NTariffOrder">
		                                                    							 <button style ="border:none;"><i class="icon-pencil"></i> تنظیم ترتیب نمایش </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					 <li>
		                                                    						<form method="post" enctype="multipart/form-data" action="V_NTariffLive.php">
		                                                    							 <input type="hidden" name="NTariffGUID" value="' . $row['GUID'] . '">
                                                                                         <input type="hidden" name="action" value="View-NTariffLive">
		                                                    							 <button style ="border:none;"><i class="icon-book-open"></i> نمایش تعرفه </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data" action="C_NTariffPDF.php">
                                                                                        <input type="hidden" name="NTariffGUID" value="' . $row['GUID'] . '">
		                                                    							<input type="hidden" name="action" value="pdf">
		                                                    							<button style ="border:none;"><i class="icon-printer"></i> دریافت PDF </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					<li class="divider"></li>
                                                                                <li>
		                                                    						<form method="post" enctype="multipart/form-data" action="C_NTariffIncrease.php">
		                                                    							<input type="hidden" name="NTariffGUID" value="' . $row['GUID'] . '">
		                                                    							<input type="hidden" name="action" value="increase">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> افزایش درصدی </button>
		                                                    						</form>
		                                                    					</li>
		                                                    					<li class="divider"></li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data">
		                                                    							<input type="hidden" name="tariff-ID" value="' . $row['GUID'] . '">
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
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

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
    }


    ?>

</body>

</html>