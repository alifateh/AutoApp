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
    if (!isset($_POST['action'])) {

        $Post_NTariffGUID = $_POST['Post_NTariffGUID'];

        if (isset($_POST['ondemandPrice'])) {
            $Ondemand_SER_Price = $_POST['ondemandPrice'];
        } else {
            $Ondemand_SER_Price = array();
        }

        if (isset($_POST['ondemandSRV_NameFA'])) {
            $Ondemand_SER_FA = $_POST['ondemandSRV_NameFA'];
        } else {
            $Ondemand_SER_FA = array();
        }

        if (isset($_POST['ondemandSRV_NameEN'])) {
            $Ondemand_SER_EN = $_POST['ondemandSRV_NameEN'];
        } else {
            $Ondemand_SER_EN = array();
        }

        $POST_SERGUID = $_POST['SER_GUID'];
        if (isset($_POST['SER_Price'])) {
            $POST_SERPrice = $_POST['SER_Price'];
        } else {
            $POST_SERPrice = array();
        }


        if (!empty($POST_SERGUID)) {
            for ($i = 0; $i < count($POST_SERGUID); $i++) {
                if (empty($POST_SERPrice[$i])) {
                    $POST_SERPrice[$i] = 0;
                }
                $route = $Tariff_Obj->U_TariffValue_ByID($POST_SERGUID[$i], $POST_SERPrice[$i]);
            }
            echo "sssss";
            if (!empty($Ondemand_SER_Price) && !empty($Ondemand_SER_FA)) {
                $limit = 0;
                if (count($Ondemand_SER_Price) == count($Ondemand_SER_FA)) {
                    $limit = count($Ondemand_SER_Price);
                } elseif (count($Ondemand_SER_Price) > count($Ondemand_SER_FA)) {
                    $limit = count($Ondemand_SER_Price);
                } else {
                    $limit = count($Ondemand_SER_FA);
                }
                $Num = $Tariff_Obj->Get_NTariffLastOne_ByID($Post_NTariffGUID);
                $OrderNum = $Num[0]['SortOrder'];

                for ($j = 0; $j < $limit; $j++) {
                    if(!empty($Ondemand_SER_Price[$j]) && !empty($Ondemand_SER_FA[$j])){
                        $SER_OndemandGUID = $Tariff_Obj->C_NTariffOndemand($Post_NTariffGUID, $Ondemand_SER_FA[$j], $Ondemand_SER_EN[$j]);
                        if ($SER_OndemandGUID !== 0) {
                            if (empty($Ondemand_SER_Price[$j])) {
                                $Ondemand_SER_Price[$j] = 0;
                            }
                            $Tariff_Obj->C_NTariffPrice($Post_NTariffGUID, $Ondemand_SER_Price[$j], $SER_OndemandGUID);
                            $Tariff_Obj->C_NTariffOrder($Post_NTariffGUID, $SER_OndemandGUID, $OrderNum);
                        }
                        $OrderNum++;
                    }
                }
            }
        } else {
            $route = 1;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == "Edit-NTariffValue") {
        if (isset($_POST['NTariffGUID'])) {
            $NTariffGUID = $_POST['NTariffGUID'];
            $get_Tariff = $Tariff_Obj->GET_NTariff_ByID($NTariffGUID);
            $get_TariffAllValues = $Tariff_Obj->GET_NTariffValue_ByID($NTariffGUID); ///bayad join beshe ba order

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
    } elseif ($route == 1) {
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
    <script src="dist/js/jquery.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var max_fields = 10;
            var wrapper = $(".container1");
            var add_button = $(".add_form_field");

            var x = 1;
            $(add_button).click(function(e) {
                e.preventDefault();
                if (x < max_fields) {
                    x++;
                    $(wrapper).append('<br /><div>' +
                        '<div class="input-group ">' +
                        '<div class="input-group-addon"><i class="icon-info"></i></div>' +
                        '<input type="text" class="form-control" name="ondemandSRV_NameFA[]" id="ondemandSRV_NameFA[]" placeholder=" نام فارسی سرویس ">' +
                        '<input type="text" class="form-control" name="ondemandSRV_NameEN[]" id="ondemandSRV_NameEN[]" placeholder=" نام انگلیسی سرویس ">' +
                        '</div><br>' +
                        '<div class="input-group">' +
                        '<div class="input-group-addon"><i class="pe-7s-cash"></i></div>' +
                        '<input type="number" class="form-control" id="ondemandPrice[]" onkeypress="return onlyNumberKey(event)" name="ondemandPrice[]" placeholder="قیمت به ریال">' +
                        '</div>' +
                        '<br /><a class="btn delete btn-pinterest btn-icon-anim"> حذف </a></div>'); //add input box
                } else {
                    alert('شما 10 سرویس جدید اضافه کردید، چنانچه نیاز به اضافه کردن سرویس جدید است با ادمین تماس بگیرید.')
                }
            });

            $(wrapper).on("click", ".delete", function(e) {
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })
        });

        function onlyNumberKey(evt) {

            // Only ASCII charactar in that range allowed 
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
                return false;
            } else {
                return true;
            }
        }
    </script>

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
                                        <input type="hidden" id="Post_NTariffGUID" Name="Post_NTariffGUID" value="<?php echo $NTariffGUID; ?>">
                                        <div id="example-basic">
                                            <div class="col-sm-6">
                                                <?php
                                                if (!empty($get_TariffAllValues)) {
                                                    foreach ($get_TariffAllValues as $row) {
                                                        if (!empty($row['SRV_NameFA']) && $row['SRV_NameFA'] !== null) {
                                                            $Service_NameFa = $row['SRV_NameFA'];
                                                        } else {
                                                            $Service_NameFa = $row['NameFa'];
                                                        }
                                                        echo '<div class="form-group">
                                                            <label class="control-label mb-10" for="serviceprice">' . $Service_NameFa . '</label>';
                                                        echo '<div class="input-group">
                                                            <div class="input-group-addon"><i class="pe-7s-cash"></i></div>';
                                                        echo '<input type="text" class="form-control" onkeypress="return onlyNumberKey(event)" id="SER_Price[]" name="SER_Price[]" placeholder="' . number_format($row['ServicePrice']) . '" value="' . $row['ServicePrice'] . '">';
                                                        echo '<input type="hidden" id="SER_GUID[]" name="SER_GUID[]" value="' . $row['PGUID'] . '">';
                                                        echo '</div></div>';
                                                    }
                                                }

                                                ?>
                                                </section>
                                                <h3><span class="head-font capitalize-font">سرویس ها</span></h3>
                                                <section>
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="container1">
                                                                <br />
                                                                <a class="btn add_form_field btn-facebook btn-icon-anim">اضافه کردن سرویس</a>
                                                                <br />
                                                                <div></div>
                                                                <br />
                                                            </div>
                                                            <br />
                                                            <button class="btn btn-info btn-anim" type="submit" name="submit_row"><i class="icon-check"></i><span class="btn-text"> ثبت </span></button>
                                                        </div>
                                                </section>
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
        <?php MainJavasc(); ?>
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