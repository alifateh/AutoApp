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
require('Model/MemberModel.php');
require('Model/N_TariffModel.php');

use fateh\Member\Member as member;
use fateh\tariff\NewTariff as NewTariff;

if (isset($_SESSION["GUID"])) {
    $Mechanic_GUID = $_SESSION["GUID"];
    $Mechanic_obj = new member($_SESSION["Admin_GUID"]);
    $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
    $TariffAllTypes = $Tariff_Obj->V_TariffTypes();
    $Mechanic_TTariff = $Tariff_Obj->Get_TariffType_ByMemberID($Mechanic_GUID);
} else {
    $url = "./V_MemberAll.php";
    header("Location: $url");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['Edit'])) {
        $_SESSION["GUID"] = $_POST['GUID'];
        $Mechanic_GUID = $_POST['GUID'];
        $Mechanic_obj = new member($_SESSION["Admin_GUID"]);
        $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);

        $Mechanic_TTariff = $Tariff_Obj->Get_TariffType_ByMemberID($Mechanic_GUID);
        if (!empty($Mechanic_TTariff)) {
            $i = 0;
            foreach ($Mechanic_TTariff as $row) {
                $SavedTariffType[$i] = $row['TariffTypeGUID'];
                $i++;
            }
        } else {
            $SavedTariffType = array();
        }

        if (isset($_POST['optgroup'])) {
            $post_MechanicTariff = $_POST['optgroup'];
        } else {
            $post_MechanicTariff = array();
        }

        $arr_diff_Decrease = array_diff($SavedTariffType, $post_MechanicTariff); // kam beshe
        $arr_diff_Increase = array_diff($post_MechanicTariff, $SavedTariffType); // ziyad beshe

        if (isset($post_MechanicTariff) && count($post_MechanicTariff) === 0) {
            $route = $Mechanic_obj->D_MechanicNTariffType_ByMID($Mechanic_GUID);
        } else {
            if (count($SavedTariffType) == count($post_MechanicTariff) && !array_diff($SavedTariffType, $post_MechanicTariff)) {
                $route = NULL;
            }
            if (!empty($arr_diff_Decrease)) {
                foreach ($arr_diff_Decrease as $key) {
                    $route = $Mechanic_obj->D_TTariffMechanic_ByMechID($Mechanic_GUID, $key);
                }
            }
            if (!empty($arr_diff_Increase)) {
                foreach ($arr_diff_Increase as $key) {
                    $route = $Mechanic_obj->U_MechanicTTariff($Mechanic_GUID, $key);
                }
            }
        }
        if (empty($route)  ) {
            // return VALUES
            $url = "./V_MechanicProfile.php";
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
                                        <h6 class="panel-title txt-dark"> انواع تعرفه ها را مشخص نمایید </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" id="GUID" name="GUID" value="<?php echo $Mechanic_GUID; ?>">
                                        <div id="example-basic">
                                            <div class="col-sm-12">

                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="exampleInputuname_1"> فهرست انواع تعرفه ها : </label>
                                                </div>

                                                <div class="panel-wrapper collapse in">
                                                    <div class="panel-body">
                                                        <div class="row mt-40">
                                                            <div class="col-sm-12">
                                                                <select multiple id="optgroup" name="optgroup[]" multiple='multiple'>
                                                                    <?php

                                                                    if (!empty($Mechanic_TTariff)) {
                                                                        $i = 0;
                                                                        foreach ($Mechanic_TTariff as $row) {
                                                                            $TariffName = $Tariff_Obj->Get_TariffID($row['TariffTypeGUID']);
                                                                            $SelectedTariffType[$i] = $row['TariffTypeGUID'];
                                                                            echo '<option value="' . $row['TariffTypeGUID'] . '" Selected >' . $TariffName[0]['NameFa'] . '</option>';
                                                                            $i++;
                                                                        }
                                                                        if (!empty($TariffAllTypes)) {
                                                                            $i = 0;
                                                                            foreach ($TariffAllTypes as $key) {
                                                                                $AllTariffType[$i] = $key['GUID'];
                                                                                $i++;
                                                                            }

                                                                            $result = array_diff($AllTariffType, $SelectedTariffType);
                                                                            foreach ($result as $diff) {
                                                                                $TariffName = $Tariff_Obj->Get_TariffID($diff);
                                                                                echo '<option value="' . $diff . '">' . $TariffName[0]['NameFa'] . '</option>';
                                                                            }
                                                                        } else {
                                                                            echo '<option value=""> انواع تعرفه ها موجود نمی باشد </option>';
                                                                        }
                                                                    } else {
                                                                        foreach ($TariffAllTypes as $key) {
                                                                            echo '<option value="' . $key['GUID'] . '">' . $key['NameFa'] . '</option>';
                                                                        }
                                                                    }

                                                                    ?>
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit" name="Edit" id="Edit"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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
    <!-- JavaScripts -->

    <!-- jQuery -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
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
</body>

</html>