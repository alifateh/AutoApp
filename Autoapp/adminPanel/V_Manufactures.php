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
require_once('Model/AutoModel.php');

use fateh\Automobile\Automobile as auto;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == "remove") {
        $ID = $_POST['ManufacturId'];
        $remove = new auto($_SESSION["Admin_GUID"]);
        $route = $remove->D_Manufactur_ByID($ID);
        if (!empty($route) AND $route == true ) {
            // return VALUES
            $url = "V_Manufactures.php";
            header("Location: $url");
        } else {
            $Error_STR = 1;
        }
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
                                        <h6 class="panel-title txt-dark"> فهرست خودروسازان </h6>
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
                                                            <th> نام خودروساز </th>
                                                            <th> لوگو خودروساز</th>
                                                            <th> عملیات </th>
                                                        </tr>
                                                </thead>
                                                <tfoot>
                                                        <tr>
                                                            <th> نام خودروساز </th>
                                                            <th> لوگو خودروساز</th>
                                                            <th> عملیات </th>
                                                        </tr>
                                                </tfoot>
                                                    <tbody>
                                                        <?php
                                                        $autobj = new auto($_SESSION["Admin_GUID"]);
                                                        $data = $autobj->V_AutoManufactures();
                                                        if (!empty($data)) {
                                                            foreach ($data as $row) {
                                                                echo "<tr>";
                                                                echo "<td>" . $row['Name'] . "</td>";
                                                                echo "<td>";
                                                                $logo = $autobj->Get_ManufactureLogo_ByID($row['Filekey']);
                                                                if (!empty($logo)) {
                                                                    foreach ($logo as $logo_row) {
                                                                        if ($logo_row["location"] !== "0") {
                                                                            echo '<ul id="portfolio" class="portf auto-construct  project-gallery">
		                                                    	        <li class="item tall design" data-src="' . $logo_row['location'] . '" style="width: 65px; height: auto;">
		                                                    	        <a href="">
		                                                    	        	<img class="img-responsive" src="' . $logo_row['location'] . '" alt="Image description">
		                                                    	        </a>
		                                                    	        </li>
                                                                    </ul>';
                                                                        } else {
                                                                            echo 'فایل لوگو پاک شده است';
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo 'لوگویی ندارد';
                                                                }
                                                                echo "</td>";
                                                                $validation = "onclick='return validation()'";
                                                                echo '<td style ="width: 50%; white-space: nowrap;">
                                                            <form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="U_Manufacture.php">
		                                            	    <input type="hidden" id="ManufacturId" name="ManufacturId" value="' . $row['ID'] . '">
		                                            	    <input type="hidden" id="action" name="action" value="edit">
		                                            	    <button class="btn btn-default btn-icon-anim"><i class="icon-settings"></i> ویرایش </button>
		                                            	    </form>
                                                            
                                                            <form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data">
		                                            	    <input type="hidden" id="ManufacturId" name="ManufacturId" value="' . $row['ID'] . '">
		                                            	    <input type="hidden" id="action" name="action" value="remove">
		                                            	    <button class="btn btn-default btn-icon-anim" ' . $validation . '><i class="icon-trash"></i> حذف </button>
		                                            	    </form>
		                                            	    
                                                            </td></tr>';
                                                            }
                                                        } else {
                                                            echo '<tr><td colspan="3" style="text-align: center;"> خودروسازی تعریف نشده </td></tr>';
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
            function validation() {
                var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
                if (agree)
                    return true;
                else
                    return false;
            }
        </script>

    </div>
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