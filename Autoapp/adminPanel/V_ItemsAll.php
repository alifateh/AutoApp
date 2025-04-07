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

require_once('Model/TariffModel.php');

use fateh\tariff\tariff as tariff;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == "remove") {
        $ID = $_POST['itemid'];
        $removeitem = new tariff($_SESSION["Admin_GUID"]);
        $removeitem->removetariffitem($ID);
        $url = "./view-tariff-item.php";
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
                                        <h6 class="panel-title txt-dark"> فهرست سرویس های موجود در تعرفه </h6>
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
                                                        <th> نام سرویس </th>
                                                        <th> نوع خودرو </th>
                                                        <th> عملیات </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام سرویس </th>
                                                        <th> نوع خودرو </th>
                                                        <th> عملیات </th>
                                                        <th> </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $viewitem = new tariff($_SESSION["Admin_GUID"]);
                                                    $data = $viewitem->V_ItemsAll();
                                                    if (!empty($data)) {

                                                        foreach ($data as $row) {

                                                            //$logo = $this-> viewmanufacturelogo($row['Filekey']);
                                                            echo "<tr>";
                                                            echo "<td>" . $row['Name'] . "</td>";
                                                            echo "<td>";
                                                            if ($row['Foreign'] == 0) {
                                                                echo '<span class="btn btn-default btn-rounded">خودروی خارجی</span>';
                                                            } else {
                                                                echo '<span class="btn btn-default btn-rounded">خودروی داخلی</span>';
                                                            }
                                                            echo "</td>";
                                                            echo '<td style ="width: 5%;"><span class="label">';
                                                            $validation = "onclick='return validation()'";
                                                            echo '<form method="post">
															<input type="hidden" name="itemid" value="' . $row['ID'] . '">
															<input type="hidden" name="action" value="remove">
															<button class="btn btn-default btn-icon-anim" ' . $validation . '><i class="icon-trash"></i> حذف </button>
															</form></td><td style ="width: 5%;">
															<form method="post" enctype="multipart/form-data" action ="edit-tariff-item.php">
															<input type="hidden" name="itemid" value="' . $row['ID'] . '">
															<input type="hidden" name="action" value="edit">
															<button class="btn btn-default btn-icon-anim"><i class="icon-settings"></i> ویرایش </button>
															</form>';
                                                            echo "</span></td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {

                                                        echo '<tr><td colspan="4" style="text-align: center;"> سرویسی تعریف نشده است </td></tr>';
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
</body>

</html>