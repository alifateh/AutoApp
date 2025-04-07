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
require('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;

$ID = $_POST['tariff-ID'];
$SecID = $_POST['tariff-SecID'];
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
                                        <?php
                                        if (!isset($_POST['Edit'])) {
                                            if (!empty($_POST['tariff-ID'])) {
                                                $edit_tariff = new tariff($_SESSION["Admin_GUID"]);
                                                $ID = $_POST['tariff-ID'];
                                                $SecID = $_POST['tariff-SecID'];
                                                $tariff_detail = $edit_tariff->gettariffDetial($ID);
                                                $Vdate = $edit_tariff->gettariffdateinhejri($tariff_detail[2]);
                                                $tariff_date = gregorian_to_jalali($Vdate[0], $Vdate[1], $Vdate[2]);
                                                $ver = $edit_tariff->getversion($tariff_detail[1]);
                                                $tariff_auto = $edit_tariff->gettariffauto($tariff_detail[0]);
                                                $man_name = "خودروساز : " . $edit_tariff->gettariffautoman($tariff_auto[0]);
                                                if ($tariff_auto[1] !== 0) {
                                                    $tip_name = $edit_tariff->gettariffautotip($tariff_auto[1]);
                                                    $tip = " تیپ " . $tip_name;
                                                } else {
                                                    $tip = "بدون تیپ";
                                                }
                                                $auto_name = " نام خودرو: " . $tariff_auto[2];
                                                $ver = $edit_tariff->getversion($tariff_detail[1]);
                                                echo '<h6 class="panel-title txt-dark"> مشخصات خودور [ ' . $man_name . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>
									        				<h6 class="panel-title txt-dark"> تاریخ اعتبار : ' . $tariff_date[0] . "/" . $tariff_date[1] . "/" . $tariff_date[2] . '</h6>
									        				<h6 class="panel-title txt-dark"> نسخه تعرفه : ' . $ver . '</h6>';
                                            } else {
                                                echo '<div class="alert alert-info alert-dismissable">
									        				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><a href="V_TariffValid.php"> لطفا به فهرست تعرفه ها برگردید </a> 
									        			</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div class="table-wrap">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width:15%;"> ردیف </th>
                                                        <th style="width:50%;"> شرح خدمات </th>
                                                        <th style="width:35%;"> نرخ مصوب (ریال) </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th style="width:15%;"> ردیف </th>
                                                        <th style="width:50%;"> شرح خدمات </th>
                                                        <th style="width:35%;"> نرخ (ریال) </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($_POST['tariff-ID'])) {

                                                        $tariff = new tariff($_SESSION["Admin_GUID"]);
                                                        $data = $tariff->V_TariffOver($ID, $SecID);

                                                        if (!empty($data)) {
                                                            $counter = 1;
                                                            foreach ($data as $row) {
                                                                $item = $tariff->getitemall($row['Table_Name'], $row['Service_ID']);
                                                                if ($item[0] !== "" and $item[1] !== 0) {
                                                                    echo "<tr>";
                                                                    echo "<td>" . $counter . "</td>";
                                                                    echo "<td>" . $item[0] . "</td>";
                                                                    echo "<td>" . number_format($item[1]) . "</td>";
                                                                    echo "</tr>";
                                                                    $counter++;
                                                                }
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='2'> <a href='V_TariffAll.php'> لطفا به فهرست تعرفه ها برگردید </a> <td></tr>";
                                                        }
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

    <script LANGUAGE="JavaScript">
        function removevalidation() {
            var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }

        function changetonotvalid() {
            var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }

        function changetovalid() {
            var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }
    </script>

    </div>
</body>

</html>