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
require('Model/N_TariffModel.php');

use fateh\tariff\NewTariff as NewTariff;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == "EditNTariff") {
        $TariffGUID = $_POST['Tariff_GUID'];
        $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
        $Tariff = $Tariff_Obj->Get_TariffType_ByID($TariffGUID);
        if (empty($Tariff)) {
            // return VALUES
            $url = "V_TariffType.php";
            header("Location: $url");
        }
    }
    if ($_POST['action'] !== "EditNTariff") {

        $TariffGUID = $_POST['Post_TariffGUID'];
        $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
        $get_tariff = $Tariff_Obj->Get_TariffType_ByID($TariffGUID);

        $get_tariffnamefa = $get_tariff[0]["NameFa"];
        $get_tariffnameen = $get_tariff[0]["NameEn"];

        //
        /// form values
        //
        $post_TariffTypeFa = $_POST['TariffType_Fa'];
        $post_TariffTypeEn = $_POST['TariffType_En'];


        if (empty($post_TariffTypeFa) || $post_TariffTypeFa == $get_tariffnamefa) {
            $TariffTypeFA = $get_tariffnamefa;
        } else {
            $TariffTypeFA = $post_TariffTypeFa;
        }

        if (empty($post_TariffTypeEn) || $post_TariffTypeEn == $get_tariffnameen) {
            $TariffTypeEN = $get_tariffnameen;
        } else {
            $TariffTypeEN = $post_TariffTypeEn;
        }

        $route = $Tariff_Obj->U_TariffType_ByID($TariffGUID, $TariffTypeFA, $TariffTypeEN);

        if (!empty($route)  ) {
            // return VALUES
            $url = "V_TariffType.php";
            header("Location: $url");
        } else {
            $Error_STR = 1;
        }
    }
} else {
    $url = "V_TariffType.php";
    header("Location: $url");
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
                                                <input type="hidden" id="Post_TariffGUID" name="Post_TariffGUID" value="<?php echo $TariffGUID; ?> ">
                                                <div id="example-basic">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label mb-10" for="TariffType_Fa"> نام فارسی : </label>
                                                            <div class="input-group col-sm-6">
                                                                <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                <input type="text" class="form-control" name="TariffType_Fa" id="TariffType_Fa" placeholder="<?php echo $Tariff[0]['NameFa']; ?>" value="<?php echo $_post['TariffType_Fa']; ?>">
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10" for="TariffType_En"> نام انگلیسی : </label>
                                                            <div class="input-group col-sm-6">
                                                                <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                <input type="text" class="form-control" name="TariffType_En" id="TariffType_En" placeholder="<?php echo $Tariff[0]['NameEn']; ?>" value="<?php echo $_post['TariffType_En']; ?>">
                                                            </div>
                                                        </div>
                                                        <br>
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
                <!-- JavaScripts -->


            </div>

        </div>

        <!-- /#wrapper -->
        <!-- jQuery -->
        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

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

        <?php
        if (!empty($Error_STR)) {
            if ($Error_STR = 1) {
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
        }


        ?>

</body>

</html>