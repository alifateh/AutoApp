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
require('Model/AutoModel.php');
require('Model/N_TariffModel.php');

use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
$Auto_Obj = new auto($_SESSION["Admin_GUID"]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Edit-NTariffDate'])) {
        $POST_TariffGUID = $_POST['Post_NTariffGUID'];

        //date hejri to garygori
        $fadate =  str_replace("-", "", $_POST['Post_VDate']);
        $day = substr($fadate, strlen($fadate) - 2, 2);
        $mon = substr($fadate, strlen($fadate) - 4, 2);
        $year = substr($fadate, 0, 4);
        $Post_miladiDate = jalali_to_gregorian($year, $mon, $day, '-');

        $route = $Tariff_Obj->U_NTariffDate_ByID($POST_TariffGUID, $Post_miladiDate);
    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-NTariffDate") {
        if (isset($_POST['NTariffGUID'])) {
            $NTariffGUID = $_POST['NTariffGUID'];
            $get_Tariff = $Tariff_Obj->GET_NTariff_ByID($NTariffGUID);
            $tarif_date = $Tariff_Obj->Gat_HejriDate($get_Tariff[0]['ValidateDate']);
            $valid_date = gregorian_to_jalali($tarif_date[0], $tarif_date[1], $tarif_date[2]);

            $auto = $Auto_Obj->Get_Automobile_ByID($get_Tariff[0]['AutoGUID']);
            $auto_name = " نام خودرو: " . $auto[0]['Name'];
            $auto_tipID =  $auto[0]['ModelID'];
            $auto_manID = $auto[0]['ManufacturerID'];
            $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
            $tip = " تیپ :" . $auto_tipName[0]['ModelName'];
            $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
            $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];

            $str = '<h6 class="panel-title txt-dark">
            ویرایش تاریخ اعتبار تعرفه : </br>
            مشخصات خودور [ ' . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>';
        } else {
            $route = 1;
        }
    }
}

if (!empty($route) && $route == 1) {
    // return VALUES
    $url = "V_NTariffAll.php";
    header("Location: $url");
} elseif (!empty($route)) {
    $Error_STR = 1;
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


    <!-- hejri -->

    <script src="dist/js/jquery.min.js"></script>
    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

    <!-- hejri -->
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
                                        <?php

                                        if (!empty($str)) {
                                            echo $str;
                                        }
                                        ?>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" id="Post_NTariffGUID" name="Post_NTariffGUID" value="<?php echo $NTariffGUID; ?>">

                                        <div id="example-basic">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group col-sm-6">
                                                        <label class="control-label mb-10" for="validation-Date"> تاریخ اعتبار تعرفه : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <?php echo $valid_date[0] . "-" . $valid_date[1] . "-" . $valid_date[2]; ?>
                                                            <input type="text" class="form-control" id="Post_VDate" name="Post_VDate" value="">
                                                        </div>
                                                    </div>

                                                    <br />
                                                    <div class="input-group">

                                                        <button class="btn btn-info btn-anim" type="submit" name="Edit-NTariffDate" id="Edit-NTariffDate"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
        <!-- JavaScript -->

        <?php MainJavasc(); ?>
        <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    </div>
    <!-- /#wrapper -->
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $("#Post_VDate").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
        });
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