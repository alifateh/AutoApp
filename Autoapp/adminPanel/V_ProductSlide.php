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
require('Model/SliderModel.php');

use fateh\Advertisements\ProductSlideShow as slide;

$Slide_Obj = new slide($_SESSION["Admin_GUID"]);

$data = $Slide_Obj->V_SlideAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "Hide-Slides") {
            $GUID = $_POST['SlideGUID'];
            $route = $Slide_Obj->U_HideSlide_ByID($GUID);
        }

        if ($_POST['action'] == "UnHide-Slides") {
            $GUID = $_POST['SlideGUID'];
            $route = $Slide_Obj->U_UnHideSlide_ByID($GUID);
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
                                        <h6 class="panel-title txt-dark"> فهرست تبلیغات / اسلایدشو محصولات </h6>
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
                                                        <th> ترتیب نمایش </th>
                                                        <th> تصویر </th>
                                                        <th> وضعیت </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> ترتیب نمایش </th>
                                                        <th> تصویر </th>
                                                        <th> وضعیت </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($data)) {
                                                        foreach ($data as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['Position'] . "</td>";
                                                            echo "<td>";
                                                            echo '<ul id="portfolio" class="portf auto-construct  project-gallery">
                                                                    <li class="item tall design" data-src="' . $row['FileAddress'] . '" style="width: 65px; height: auto;">
                                                                         <a href="">
                                                                        <img class="img-responsive" src="' . $row['FileAddress'] . '" alt="Image description">
                                                                         </a>
                                                                     </li>
                                                                    </ul>';
                                                            echo "</td>";
                                                            if ($row['Visible'] == 1) {
                                                                echo '<td> <button class="btn btn-success btn-icon-anim "> فعال </button> </td>';
                                                                $operation = '<div class="dropdown">
                                                                <a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                    عملیات
                                                                    <span class="caret"></span>
                                                                    </a>
                                                                    <ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
                                                                    <li>
                                                                        <form method="post" enctype="multipart/form-data" action="U_ProductSlide.php">
                                                                            <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                            <input type="hidden" name="action" value="Edit-Slide">
                                                                            <button style ="border:none;"><i class="icon-pencil"></i> ویرایش </button>
                                                                        </form>
                                                                        </li>
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <form method="post" enctype="multipart/form-data" action="U_ProductsSlideImage.php">
                                                                            <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                            <input type="hidden" name="action" value="Edit-SlideImage">
                                                                                <button style ="border:none;"><i class="icon-pencil"></i> ویرایش تصویر </button>
                                                                            </form>
                                                                        </li>
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <form method="post" enctype="multipart/form-data">
                                                                                <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                                <input type="hidden" name="action" value="Hide-Slides">
                                                                                <button style ="border:none;" onclick="return HideSlide()"><i class="icon-close"></i>  مخفی سازی </button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                            </div>';
                                                            } else {
                                                                echo '<td> <button class="btn btn-warning btn-icon-anim "> غیر فعال </button> </td>';
                                                                $operation = '<div class="dropdown">
                                                                <a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                    عملیات
                                                                    <span class="caret"></span>
                                                                    </a>
                                                                    <ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
                                                                    <li>
                                                                        <form method="post" enctype="multipart/form-data" action="U_ProductSlide.php">
                                                                            <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                            <input type="hidden" name="action" value="Edit-Banner">
                                                                            <button style ="border:none;"><i class="icon-pencil"></i> ویرایش </button>
                                                                        </form>
                                                                        </li>
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <form method="post" enctype="multipart/form-data" action="U_ProductsSlideImage.php">
                                                                                <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                                <input type="hidden" name="action" value="Edit-SlideImage">
                                                                                <button style ="border:none;"><i class="icon-pencil"></i> ویرایش تصویر </button>
                                                                            </form>
                                                                        </li>
                                                                        <li class="divider"></li>
                                                                        <li>
                                                                            <form method="post" enctype="multipart/form-data">
                                                                                <input type="hidden" name="SlideGUID" value="' . $row["GUID"] . '">
                                                                                <input type="hidden" name="action" value="UnHide-Slides">
                                                                                <button style ="border:none;" onclick="return VisibleSlide()"><i class="icon-close"></i> آشکارسازی </button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                            </div>';
                                                            }
                                                            echo "<td>";
                                                            echo $operation;
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="4" style="text-align: center;"> تصویری برای اسلاید شو تعریف نشده است </td></tr>';
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
        function HideSlide() {
            var agree = confirm("آیا از مخفی سازی ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }

        function VisibleSlide() {
            var agree = confirm("آیا از آشکار سازی ایتم مطمئن می باشید؟");
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

    }


    ?>

</body>

</html>