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

    if (!isset($_POST['action']) && isset($_POST['change-order'])) {
        $SREID = $_POST['item-ID'];
    
        for ($i = 0; $i < count($SREID); $i++) {
           $route = $Tariff_Obj->U_TariffOrder_BySREID($SREID[$i], $i);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-NTariffOrder") {
        if (isset($_POST['NTariffGUID'])) {
            $NTariffGUID = $_POST['NTariffGUID'];
            $get_TariffAllValues = $Tariff_Obj->GET_NTariffValue_ByID($NTariffGUID); ///bayad join beshe ba order
            $get_Tariff = $Tariff_Obj->GET_NTariff_ByID($NTariffGUID);

            $auto = $Auto_Obj->Get_Automobile_ByID($get_Tariff[0]['AutoGUID']);
            $auto_name = " نام خودرو: " . $auto[0]['Name'];
            $auto_tipID =  $auto[0]['ModelID'];
            $auto_manID = $auto[0]['ManufacturerID'];
            $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
            $tip = " تیپ :" . $auto_tipName[0]['ModelName'];
            $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
            $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];

            $str = '<h6 class="panel-title txt-dark"> مشخصات خودور [ ' . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>';
        } else {
            $route = 1;
        }
    }

    if (!empty($route) && $route == 1) {
        // return VALUES
        $url = "V_NTariffAll.php";
        header("Location: $url");
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
    <!--Nestable CSS -->
    <link href="vendors/bower_components/nestable2/jquery.nestable.css" rel="stylesheet" type="text/css" />


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
                                    <form method="post" enctype="multipart/form-data" id="idForm">
                                        <div class="col-md-6">
                                            <div class="panel panel-default card-view">
                                                <div class="panel-heading">
                                                    <div class="pull-left">
                                                        <h6 class="panel-title txt-dark">ترتیب اجرت ها</h6>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        <div class="dd" id="nestable2">
                                                            <ol class="dd-list">
                                                                <?php
                                                                if (!empty($get_TariffAllValues)) {
                                                                    $counter = 0;
                                                                    foreach ($get_TariffAllValues as $row) {
                                                                        if (!empty($row['SRV_NameFA'])) {
                                                                            $SER = $row['SRV_NameFA'];
                                                                        } elseif (!empty($row['NameFa'])) {
                                                                            $SER = $row['NameFa'];
                                                                        } else {
                                                                            $SER = "خطا در برنامه";
                                                                        }
                                                                        echo '<li class="dd-item dd3-item" data-id="' . $counter . '">';
                                                                        echo '<div class="dd-handle dd3-handle"></div>';
                                                                        echo '<div class="dd3-content">';
                                                                        echo $SER . " = " . $row['ServicePrice'] . " (ريال)";
                                                                        echo '</div>';
                                                                        echo '<input type="hidden" name="item-ID[]" value="' .  $row['ServiceGUID'] . '">';
                                                                        echo "</li>";
                                                                        $counter++;
                                                                    }
                                                                } else {
                                                                    echo '<li class="dd-item dd3-item" data-id=""> سرویسی تعریف نشده است </li>';
                                                                }

                                                                ?>

                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- div dd -->
                                                <button class="btn btn-pinterest btn-icon-anim" type="submit" id="change-order" name="change-order"> ثبت ترتیب </button>
                                            </div>
                                        </div>
                                    </form>
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

    <!-- jQuery -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!--Nestable js -->
    <script src="vendors/bower_components/nestable2/jquery.nestable.js"></script>

    <!-- Nestable Init JavaScript -->
    <script src="dist/js/nestable-data.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script>
        $(document).ready(function() {

            // activate Nestable for list 2
            $('#nestable2').nestable({
                group: 1
            })

        });
    </script>


</body>

</html>