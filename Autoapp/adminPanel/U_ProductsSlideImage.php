<?php
session_start();
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

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
        $POST_SlideGUID = $_POST['Post_SlideGUID'];
        $P_SlideFile = $_POST['Post_SlideFile'];

        if (file_exists($_FILES['fileToUpload']['tmp_name']) || is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {

            $target_dir = "images/A_D_V/ProductsSlides/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image

            $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                // echo "File is not an image.";
                $uploadOk = 0;
                $Error_STR = 4;
            }


            // Check if file already exists
            if (file_exists($target_file)) {
                $uploadOk = 0;
                $Error_STR = 3;
            }

            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 5 * MB) {
                $uploadOk = 0;
                $Error_STR = 5;
            }

            // Allow certain file formats

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp") {
                $Error_STR = 6;
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk != 0) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                    //add with file
                    $route = $Slide_Obj->U_SlideImage_ByID($POST_SlideGUID, $target_file);
                } else {
                    $Error_STR = 1;
                }
            }
        } else {
            $Error_STR = 2;
        }
        if ($Error_STR == 0) {
            $url = "V_ProductSlide.php";
            header("Location: $url");
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-SlideImage") {
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
                                        <input type="hidden" id="Post_SlideGUID" name="Post_SlideGUID" value="<?php echo $SlideGUID; ?>">
                                        <input type="hidden" id="Post_SlideFile" name="Post_SlideFile" value="<?php echo $get_Slide[0]['File']; ?>">

                                        <div id="example-basic">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="control-label mb-10 text-left"> تصویر جدید بنر : </label>
                                                    <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                                        <span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span> <span class="fileinput-exists btn-text"> تغییر فایل </span>
                                                            <input type="file" name="fileToUpload" id="fileToUpload">
                                                        </span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف </span></a>
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


    </div>
    <!-- /#wrapper -->
    <!-- JavaScript -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>


    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

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
                        text: 'فایل با این نام قبلا در سمت سرور قرار گرفته است' ,
                        position: 'top-center',
                        loaderBg:'#ed3236',
                        hideAfter: 6500,
                        stack: 6
                    });
                    return false;
            });";
            echo '</script>';
        }
        if ($Error_STR == 4) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                    $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                    $.toast({
                        heading: 'خطا در ثبت اطلاعات',
                        text: 'فایل خراب می باشد و امکان بارگذاری بر روی سرور نیست' ,
                        position: 'top-center',
                        loaderBg:'#ed3236',
                        hideAfter: 6500,
                        stack: 6
                    });
                    return false;
            });";
            echo '</script>';
        }
        if ($Error_STR == 5) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                    $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                    $.toast({
                        heading: 'خطا در ثبت اطلاعات',
                        text: 'سایز فایل بیشتر از 5 مگابایت می باشد' ,
                        position: 'top-center',
                        loaderBg:'#ed3236',
                        hideAfter: 6500,
                        stack: 6
                    });
                    return false;
            });";
            echo '</script>';
        }

        if ($Error_STR == 6) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                    $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                    $.toast({
                        heading: 'خطا در ثبت اطلاعات',
                        text: 'قالب فایل صحیح نمی باشد' ,
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