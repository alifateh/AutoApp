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
//require_once('Model/TariffModel.php');
require_once('Model/N_TariffModel.php');
require_once('Model/MemberModel.php');
require_once('Model/InvoiceModel.php');

use fateh\tariff\NewTariff as Ntariff;
use fateh\Finance\Invoice as invoice;
use fateh\Member\Member as member;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tariffver = new Ntariff($_SESSION["Admin_GUID"]);
    $V_Invoce = new invoice($_SESSION["Admin_GUID"]);
    $mem = new member($_SESSION["Admin_GUID"]);

    $TarrifID = $_POST['Inv_Topic'];
    $bill = $V_Invoce->V_PaidBills($TarrifID);
} else {
    $tariffver = new Ntariff($_SESSION["Admin_GUID"]);
    $V_Invoce = new invoice($_SESSION["Admin_GUID"]);
    $mem = new member($_SESSION["Admin_GUID"]);

    $bill = '';
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
                                <div id="collapse_2" class="panel-wrapper collapse in">
                                    <div class="panel-body">
                                        <form method="post" enctype="multipart/form-data">
                                            <div id="example-basic">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-3">
                                                            <label class="control-label" for="Inv_Topic">حق عضویت سال :
                                                                <select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Inv_Topic" id="Inv_Topic">
                                                                    <?php
                                                                    $data = $tariffver->V_AllTariffVersion();
                                                                    if (!empty($data)) {
                                                                        $numItems = count($data);
                                                                        $i = 0;
                                                                        foreach ($data as $row) {
                                                                            if (++$i === $numItems) {
                                                                                echo '<option value="' . $row['ID'] . '" selected >' . $row['NameFa'] . '</option>';
                                                                            }else{
                                                                                echo '<option value="' . $row['ID'] . '">' . $row['NameFa'] . '</option>';
                                                                            }
                                                                            
                                                                        }
                                                                    } else {
                                                                        echo '<option value=""> تعرفه ایی ثبت نشده </option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <br />
                                                            <button class="form-control col-sm-2 btn btn-success btn-anim" type="submit" name="Add_Btn" id="Add_Btn"><i class="icon-check"></i><span class="btn-text"> نمایش </span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> فهرست پرداختی ها </h6>
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
                                                        <th> نام اعضا </th>
                                                        <th> شماره ارجاع </th>
                                                        <th> شماره پیگیری </th>
                                                        <th> نوع پرداخت </th>
                                                        <th> مبلغ </th>
                                                        <th> تاریخ پرداخت </th>
                                                        <th> شرح </th>
                                                        <th> مجموع از ابتدا </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام اعضا </th>
                                                        <th> شماره ارجاع </th>
                                                        <th> شماره پیگیری </th>
                                                        <th> نوع پرداخت </th>
                                                        <th> مبلغ </th>
                                                        <th> تاریخ پرداخت </th>
                                                        <th> شرح </th>
                                                        <th> مجموع از ابتدا </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($bill)) {
                                                        $sum_bills = 0;
                                                        foreach ($bill as $row) {
                                                            echo "<tr>";
                                                            $MemberInfo = $mem->Get_MechanicsInfo_ByID($row['Member_GUID']);
                                                            echo "<td>" . $MemberInfo[0] . " " . $MemberInfo[1] . "</td>";
                                                            echo "<td>" . $row['RetrivalRefNum'] . "</td>";
                                                            echo "<td>" . $row['SysTraceNum'] . "</td>";
                                                            if ($row['Payment_Method'] == 1) {
                                                                $x = "اینترنتی";
                                                            } else {
                                                                $x = "حضوری";
                                                            }
                                                            echo "<td>" . $x . "</td>";
                                                            echo "<td>" . number_format($row['Amount']) . "</td>";
                                                            $PaymentDate_Miladi = $mem->Get_DateHejri($row['Payment_Date']);
                                                            $PaymentDate_Hejri = gregorian_to_jalali($PaymentDate_Miladi[0], $PaymentDate_Miladi[1], $PaymentDate_Miladi[2]);
                                                            echo "<td>" . $PaymentDate_Hejri[0] . "-" . $PaymentDate_Hejri[1] . "-" . $PaymentDate_Hejri[2] . "</td>";

                                                            echo "<td>" . $row['Comment'] . "</td>";
                                                            $sum_bills = $sum_bills + $row['Amount'];
                                                            echo "<td>" . number_format($sum_bills) . "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="8" style="text-align: center;"> پرداختی ثبت نشده است </td></tr>';
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

</body>

</html>