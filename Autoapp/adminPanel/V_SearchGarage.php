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
require('Model/GarageModel.php');

use fateh\AutoShop\AutoShop as garage;

$Garage_obj = new garage($_SESSION["Admin_GUID"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['GarageName'])) {
        $result = $Garage_obj->Get_Garage_ByName($_POST['GarageName']);
    } else {
        $Error_STR = 2;
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
                                        <h6 class="panel-title txt-dark"> جستجوی تعمیرگاه بر روی نقشه </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>
                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <div id="example-basic">
                                            <div class="col-sm-6">

                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="exampleInputuname_1"> نام تعمیرگاه : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="GarageName" id="GarageName" placeholder="نام تعمیرگاه" value="<?php echo $_POST['GarageName']; ?>">
                                                    </div>
                                                </div>
                                                <br />
                                                <br />
                                                <div class="form-group">
                                                    <button class="btn btn-warning btn-anim" type="submit"><i class="icon-check"></i><span class="btn-text"> [ جستجو ] </span></button </div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div id="collapse_2" class="panel-wrapper collapse in">
                            <div class="panel-body">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table id="datable_1" class="table table-hover display  pb-30">
                                            <thead>
                                                <tr>
                                                    <th> نام تعمیرگاه </th>
                                                    <th> تگ ها </th>
                                                    <th> نمایش رو نقشه </th>
                                                    <th> عملیات </th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th> نام تعمیرگاه </th>
                                                    <th> تگ ها </th>
                                                    <th> نمایش رو نقشه </th>
                                                    <th> عملیات </th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                if (!empty($_POST['GarageName'])) {
                                                    if (!empty($result)) {
                                                        foreach ($result as $row) {
                                                            $map = $Garage_obj->Getmap($row['GUID']);
                                                            echo "<tr>";
                                                            echo "<td>" . $row['Name'] . "</td>";
                                                            echo "<td>" . str_replace(",", " - ", $row['Tags']) . "</td>";
                                                            echo "<td>" . $map[2] . "</td>";
                                                            echo '<td>';
                                                            echo ' <form method="post" enctype="multipart/form-data" action="V_Garag_Map.php">
                                                            <input type="hidden" id="GarageGUID" name="GarageGUID" value="'.$row['GUID'].'">
                                                            <button class="btn btn-default btn-icon-anim"> نقشه <i class="icon-location-pin"></i> </button>
                                                        </form>';
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {

                                                        echo '<tr><td colspan="4" style="text-align: center;"> تعمیرگاهی یافت نشد </td></tr>';
                                                    }
                                                } else {

                                                    echo '<tr><td colspan="4" style="text-align: center;"> تعمیرگاهی جستجو نشده است </td></tr>';
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

        </div>
    </div>
    <!-- /#wrapper -->
    <!-- JavaScript -->
    <!-- JavaScript -->

    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>


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
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
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
                    heading: 'خطا در فیلد جستجو',
                    text: 'فیلد جستجو خالی مانده است' ,
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