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
require('Model/BannerModel.php');

use fateh\Advertisements\Banners as Banner;

$Banner_Obj = new Banner($_SESSION["Admin_GUID"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Edit'])) {
        $POST_BannerGUID = $_POST['Post_BannerGUID'];

        if (!isset($_POST['Post_VDate']) || trim($_POST['Post_VDate']) == '') {
            $Error_STR = 2;

        } else {
        //date hejri to garygori
        $fadate =  str_replace("-", "", $_POST['Post_VDate']);
        $day = substr($fadate, strlen($fadate) - 2, 2);
        $mon = substr($fadate, strlen($fadate) - 4, 2);
        $year = substr($fadate, 0, 4);
        $Post_miladiDate = jalali_to_gregorian($year, $mon, $day, '-');

        $route = $Banner_Obj->U_BannerEndDate_ByID($POST_BannerGUID, $Post_miladiDate);
        }


    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-BannerDate") {
        if (isset($_POST['BannerGUID'])) {
            $BannerGUID = $_POST['BannerGUID'];
            $get_Banner = $Banner_Obj->Get_Banners_ByID($BannerGUID);
            $Banner_date = $Banner_Obj->Gat_HejriDate($get_Banner[0]['Date_End']);
            $valid_date = gregorian_to_jalali($Banner_date[0], $Banner_date[1], $Banner_date[2]);

            switch ($get_Banner[0]['Position']) {
                case 1:
                    $position = "نخست";
                    break;
                case 2:
                    $position = "دوم";
                    break;
                case 3:
                    $position = "سوم";
                    break;

                default:
                    break;
            }
            $str = '<h6 class="panel-title txt-dark"> ویرایش آخرین تاریخ نمایش بنر در مکان : 
        [ ' . $position . ' ]
            </h6>';
        } else {
            $route = 1;
        }
    }

    if (!empty($route) && $route == 1) {
        // return VALUES
        $url = "V_Banners.php";
        header("Location: $url");
    } elseif (!empty($route)) {
        $Error_STR = 1;
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
                                        <input type="hidden" id="Post_BannerGUID" name="Post_BannerGUID" value="<?php echo $BannerGUID; ?>">

                                        <div id="example-basic">
                                            <div class="col-sm-3">
                                                <div class="form-group">

                                                    <label class="control-label" for="validation-Date"> تاریخ آخرین روز فعال بودن بنر :  </label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <?php echo $valid_date[0] . "-" . $valid_date[1] . "-" . $valid_date[2]; ?>
                                                        <input type="text" class="form-control" id="Post_VDate" name="Post_VDate" value="">
                                                    </div>
                                                </div>
                                                <br />
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <button class="btn btn-info btn-anim" type="submit" name="Edit" id="Edit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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