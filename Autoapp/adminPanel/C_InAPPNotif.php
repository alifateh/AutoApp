<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
require('config/public_conf.php');
require('Model/NotificationModel.php');

use fateh\Notification\InAppNotifications as Notif;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //
    /// form values
    //
    $Notif_Obj = new Notif($_SESSION["Admin_GUID"]);
    $post_Title = $_POST['Notif_Title'];
    $post_Text = $_POST['Notif_Text'];

    $post_StartDate = $_POST['Notif_StartDate'];
    if (!empty($post_StartDate)) {
		$fadate =  str_replace("-", "", $post_StartDate);
		$day = substr($fadate, strlen($fadate) - 2, 2);
		$mon = substr($fadate, strlen($fadate) - 4, 2);
		$year = substr($fadate, 0, 4);
		$Notif_SDate = jalali_to_gregorian($year, $mon, $day, '-');
	}

    $post_EndDate = $_POST['Notif_EndDate'];
    if (!empty($post_EndDate)) {
		$fadate =  str_replace("-", "", $post_EndDate);
		$day = substr($fadate, strlen($fadate) - 2, 2);
		$mon = substr($fadate, strlen($fadate) - 4, 2);
		$year = substr($fadate, 0, 4);
		$Notif_EDate = jalali_to_gregorian($year, $mon, $day, '-');
	}
    

    $Error_STR = 0;

    if($Notif_SDate > $Notif_EDate ){
        $Error_STR = 3;
    }else{
        if(!empty($post_Title) && !empty($post_Text) && !empty($Notif_SDate) && !empty($Notif_EDate) ){
            $route = $Notif_Obj -> C_Notification($post_Title, $post_Text, $Notif_SDate, $Notif_EDate);
        }else{
            $Error_STR = 2;
        }
    }


    if(!empty($route) && $route == 1){
        $url = "./V_InAPPNotif.php";
        header("Location: $url");
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Colorpicker CSS -->
    <link href="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- switchery CSS -->
    <link href="vendors/bower_components/switchery/dist/switchery.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-select CSS -->
    <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-tagsinput CSS -->
    <link href="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-touchspin CSS -->
    <link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <!-- multi-select CSS -->
    <link href="vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Switches CSS -->
    <link href="vendors/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Datetimepicker CSS -->
    <link href="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>

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
                                        <h6 class="panel-title txt-dark"> تعریف اطلاعیه در برنامه </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <form method="post" enctype="multipart/form-data">
                                        <div id="example-basic">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_Title"><i class="text-info mb-10">*</i> موضوع : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_Title" id="Notif_Title" value="<?php echo $_POST['Notif_Title']; ?>">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_Text"><i class="text-info mb-10">*</i> شرح : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_Text" id="Notif_Text" value="<?php echo $_POST['Notif_Text']; ?>">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_StartDate"><i class="text-info mb-10">*</i> تاریخ شروع : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_StartDate" id="Notif_StartDate" value="<?php echo $_POST['Notif_StartDate']; ?>">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_EndDate"><i class="text-info mb-10">*</i> تاریخ پایان : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_EndDate" id="Notif_EndDate" value="<?php echo $_POST['Notif_EndDate']; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
        </div>
        <!-- /#wrapper -->
        <!-- JavaScripts -->

        <!-- jQuery -->
        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Moment JavaScript -->
        <script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js"></script>

        <!-- Bootstrap Colorpicker JavaScript -->
        <script src="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

        <!-- Switchery JavaScript -->
        <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

        <!-- Select2 JavaScript -->
        <script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

        <!-- Bootstrap Select JavaScript -->
        <script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

        <!-- Bootstrap Tagsinput JavaScript -->
        <script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

        <!-- Bootstrap Touchspin JavaScript -->
        <script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>

        <!-- Multiselect JavaScript -->
        <script src="vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>

        <!-- Bootstrap Switch JavaScript -->
        <script src="vendors/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>

        <!-- Bootstrap Datetimepicker JavaScript -->
        <script type="text/javascript" src="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <!-- Form Advance Init JavaScript -->
        <script src="dist/js/form-advance-data.js"></script>

        <!-- Slimscroll JavaScript -->
        <script src="dist/js/jquery.slimscroll.js"></script>

        <!-- Fancy Dropdown JS -->
        <script src="dist/js/dropdown-bootstrap-extended.js"></script>

        <!-- Owl JavaScript -->
        <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

        <!-- Init JavaScript -->
        <script src="dist/js/init.js"></script>
        <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#Notif_StartDate").persianDatepicker({
                    formatDate: "YYYY-0M-0D"
                });
            });
            $(function() {
                $("#Notif_EndDate").persianDatepicker({
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

            if ($Error_STR == 3) {
                echo '<script language="javascript">';
                echo "$(document).ready(function() {
                            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                            $.toast({
                                heading: 'خطا در ثبت اطلاعات',
                                text: 'تاریخ اتمام اعلان نمی تواند قبل از تاریخ شروع باشد' ,
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