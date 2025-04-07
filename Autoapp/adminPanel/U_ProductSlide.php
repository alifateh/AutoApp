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
require('Model/SliderModel.php');

use fateh\Advertisements\ProductSlideShow as slide;

$Slide_Obj = new slide($_SESSION["Admin_GUID"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Edit'])) {
        $Post_SlideGUID = $_POST['Post_SlideGUID'];

        if (!isset($_POST['P_Slogn']) || trim($_POST['P_Slogn']) == ''){
            $N_Slogn = $_POST['G_Slogn'];
        }else{
            if( $_POST['P_Slogn'] == $_POST['G_Slogn']){
                $N_Slogn = $_POST['G_Slogn'];
            }else{
                $N_Slogn = $_POST['P_Slogn'];
            }
        }

        if (!isset($_POST['P_Position']) || trim($_POST['P_Position']) == ''){
            $N_Position = $_POST['G_Position'];
        }else{
            if( $_POST['P_Position'] == $_POST['G_Position']){
                $N_Position = $_POST['G_Position'];
            }else{
                $N_Position = $_POST['P_Position'];
            }
        }


        if (!isset($_POST['P_Link']) || trim($_POST['P_Link']) == ''){
            $N_Link = $_POST['G_Link'];
        }else{
            if( $_POST['P_Link'] == $_POST['G_Link']){
                $N_Link = $_POST['G_Link'];
            }else{
                $N_Link = $_POST['P_Link'];
            }
        }


        if ($Error_STR !== 2) {
           $route = $Slide_Obj->U_SlideDetails_ByID($Post_SlideGUID, $N_Slogn, $N_Link, $N_Position);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-Slide") {
        if (isset($_POST['SlideGUID'])) {
            $SlideGUID = $_POST['SlideGUID'];
            $get_Slide = $Slide_Obj->Get_Slide_ByID($SlideGUID);
            $str = '<h6 class="panel-title txt-dark"> ویرایش اسلاید در مکان : 
        [ ' . $get_Slide[0]['Position'] . ' ]
            </h6>';
        }
    }

    if (!empty($route) && $route == 1) {
        // return VALUES
        $url = "V_ProductSlide.php";
        header("Location: $url");
    } elseif (!empty($route)) {
        $Error_STR = 1;
    }
} else {
    $url = "V_ProductSlide.php";
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
                                        <input type="hidden" id="Post_SlideGUID" name="Post_SlideGUID" value="<?php echo $SlideGUID; ?>">
                                        <div id="example-basic">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="P_Slogn"> شعار اسلاید : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="P_Slogn" id="P_Slogn" value="<?php echo $_POST['P_Slogn']; ?>" placeholder="<?php echo $get_Slide[0]['Slogn']; ?>">
                                                        <input type="hidden" id="G_Slogn" name="G_Slogn" value="<?php echo $get_Slide[0]['Slogn']; ?>">
                                                    </div>
                                                </div>
                                                <br />
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="P_Position"> ترتیب نمایش اسلاید : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="P_Position" id="P_Position" value="<?php echo $_POST['P_Position']; ?>" placeholder="<?php echo $get_Slide[0]['Position']; ?>">
                                                        <input type="hidden" id="G_Owner" name="G_Position" value="<?php echo $get_Slide[0]['Position']; ?>">
                                                    </div>
                                                </div>
                                                <br />
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="P_Link"> آدرس لینک بنر : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="P_Link" id="P_Link" value="<?php echo $_POST['P_Link']; ?>" placeholder="<?php echo $get_Slide[0]['LinkAddress'];  ?>">
                                                        <input type="hidden" id="G_Link" name="G_Link" value="<?php echo $get_Slide[0]['LinkAddress']; ?>">
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
                                    </form>
                                </div>
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
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    </div>
    <!-- /#wrapper -->
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>


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