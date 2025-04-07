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
    <?php
    require_once('Model/InvoiceModel.php');
    //require_once('Model/TariffModel.php');
    require_once('Model/N_TariffModel.php');

    use fateh\Finance\Invoice as invoice;
    ///use fateh\tariff\tariff as tariff;
    use fateh\tariff\NewTariff as Ntariff;

    $reg_Invoce = new invoice($_SESSION["Admin_GUID"]);
    //$tariffver = new tariff($_SESSION["Admin_GUID"]);
    $NewTariffe = new Ntariff($_SESSION["Admin_GUID"]);


    if (isset($_POST['action'])) {
        if ($_POST['action'] == "remove") {
            $ID = $_POST['Inv_GUID'];
            $reg_Invoce->Remove_Invoice($ID);
            $url = "./V_AllInvoives.php";
            header("Location: $url");
        }
    }
    ?>
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
                                        <h6 class="panel-title txt-dark"> فهرست صورت حساب ها </h6>
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
                                                        <th> موضوع صورت حساب </th>
                                                        <th> قیمت (به ریال) </th>
                                                        <th> تاریخ شروع </th>
                                                        <th> توضیحات </th>
                                                        <th> تصاویر مستندات </th>
                                                        <th> حذف </th>
                                                        <th> ویرایش </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> موضوع صورت حساب </th>
                                                        <th> قیمت (به ریال) </th>
                                                        <th> تاریخ شروع </th>
                                                        <th> توضیحات </th>
                                                        <th> تصاویر مستندات </th>
                                                        <th> حذف </th>
                                                        <th> ویرایش </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $inv = $reg_Invoce->View_Invoice_All();
                                                    if (!empty($inv)) {
                                                        foreach ($inv as $Inv_Value) {
                                                            if (!empty($Inv_Value)) {
                                                                echo "<tr>";
                                                                $TariffTittle = $NewTariffe->getversion($Inv_Value['Title']);
                                                                echo "<td>" . $TariffTittle[0]["NameFa"] . "</td>";
                                                                echo "<td>" . $Inv_Value['Amount'] . "</td>";
                                                                $miladidate =  str_replace("-", "", $Inv_Value['Start_Date']);
                                                                $day = substr($miladidate, 6, 2);
                                                                $mon = substr($miladidate, 4, 2);
                                                                $year = substr($miladidate, 0, 4);
                                                                echo "<td>" . gregorian_to_jalali($year, $mon, $day, '/') . "</td>";
                                                                echo "<td>" . $Inv_Value['Comment'] . "</td>";
                                                                $Inv_pic = $reg_Invoce->V_Invoice_Doc($Inv_Value['GUID']);
                                                                echo "<td>";
                                                                foreach ($Inv_pic as $pic) {
                                                                    echo '<a href="https://adminpanel.autoapp.ir/' . $pic['location'] . '"> دانلود فایل  </a> <br />';
                                                                }
                                                                echo "</td>";
                                                                echo '<td style ="width: 5%;">';
                                                                $validation = "onclick='return validation()'";
                                                                echo '<form method="post">
		                                             	        <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $Inv_Value['GUID'] . '">
		                                             	        <input type="hidden" id="action" name="action" value="remove">
		                                             	        <button class="btn btn-default btn-icon-anim" ' . $validation . '><i class="icon-trash"></i> حذف </button>
		                                             	        </form></td><td style ="width: 5%;">
		                                             	        <form method="post" enctype="multipart/form-data" action ="edit-Invoice.php">
		                                             	        <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $Inv_Value['GUID'] . '">
		                                             	        <input type="hidden" id="action" name="action" value="edit-Invoice">
		                                             	        <button class="btn btn-default btn-icon-anim "><i class="icon-settings"></i> ویرایش </button>
		                                             	        </form></span></td></tr>';
                                                            }
                                                        }
                                                    } else {
                                                        echo "<tr role='row' class='odd'>";
                                                        echo '<td colspan="7" style="text-align: center;">';
                                                        echo 'صورت حسابی تاکنون صادر نگردیده';
                                                        echo "</td>";
                                                        echo "</tr>";
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
        function validation() {
            var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }
    </script>

</body>

</html>