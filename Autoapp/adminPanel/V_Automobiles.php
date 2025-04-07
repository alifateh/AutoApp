<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
require_once ('config/public_conf.php');
require ('Model/AutoModel.php');
require ('Model/N_TariffModel.php');

use fateh\tariff\NewTariff as NewTariff;
use fateh\Automobile\Automobile as auto;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] == "remove") {
        $ID = $_POST['autoId'];
        $remove = new auto($_SESSION["Admin_GUID"]);
        $remove->removeAuto($ID);
        $url = "V_Automobiles.php";
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
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet"
        type="text/css">

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
                                    <a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2"
                                        aria-expanded="true" aria-controls="collapse_2">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <i class="zmdi zmdi-chevron-up"></i>
                                    </a>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> فهرست خودروها </h6>
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
                                                        <th> تیپ خودرو </th>
                                                        <th> نام خودرو </th>
                                                        <th> انواع تعرفه ها </th>
                                                        <th> تصویر خودرو </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام خودروساز </th>
                                                        <th> تیپ خودرو </th>
                                                        <th> نام خودرو </th>
                                                        <th> انواع تعرفه ها </th>
                                                        <th> تصویر خودرو </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $auto_obj = new auto($_SESSION["Admin_GUID"]);
                                                    $TTariff_obj = new NewTariff($_SESSION["Admin_GUID"]);
                                                    $data = $auto_obj->V_Automobiles();
                                                    if (!empty($data)) {
                                                        foreach ($data as $row) {
                                                            echo "<tr>";
                                                            $manname = $auto_obj->Get_Manufactuer_ByID($row['ManufacturerID']);
                                                            echo "<td>" . $manname[0]['Name'] . "</td>";
                                                            $Tip = $auto_obj->Get_Tip_ByID($row["ModelID"]);
                                                            echo "<td>" . $Tip[0]["ModelName"] . "</td>";
                                                            echo "<td>" . $row['Name'] . "</td>";
                                                            $TTariff_lst = $auto_obj->Get_AutoTariffType_ByAutoID($row['ID']);
                                                            if (!empty($TTariff_lst)) {
                                                                $list = "";
                                                                foreach ($TTariff_lst as $key) {
                                                                    $TType = $TTariff_obj->Get_TariffType_ByID($key['TariffTypeGUID']);
                                                                    $list .= $TType[0]['NameFa'] . "<br>";
                                                                }

                                                            } else {
                                                                $list = "نوع تعرفه تعیین نشده";
                                                            }
                                                            echo "<td> $list </td>";
                                                            echo "<td>";
                                                            $pic = $auto_obj->Get_AutoPic($row['filekey']);
                                                            if (!empty($pic)) {
                                                                foreach ($pic as $key) {
                                                                    if ($key['location'] !== "" && $key['Visible'] = 1) {
                                                                        echo '<ul id="portfolio" class="portf auto-construct  project-gallery">
		                                                    	    <li class="item tall design" data-src="' . $key['location'] . '" style="width: 65px; height: auto;">
		                                                    	    		<a href="">
		                                                    	    		<img class="img-responsive" src="' . $key['location'] . '" alt="Image description">
		                                                    	    		</a>
		                                                    	    </li></ul> <br />';
                                                                    }
                                                                }
                                                            } else {
                                                           echo '<ul><li style="text-align: center;"> تصویری یافت نشد </li></ul>';
                                                            }
//
                                                            echo "</td>";
                                                            $validation = "onclick='return validation()'";
                                                            echo '<td style ="width: 35%; white-space: nowrap;">
                                                            <form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data">
		                                            	        <input type="hidden" id="autoId" name="autoId" value="' . $row['ID'] . '">
		                                            	        <input type="hidden" id="action" name="action" value="remove">
		                                            	        <button class="btn btn-default btn-icon-anim" ' . $validation . '><i class="icon-trash"></i> حذف </button>
		                                            	    </form>
		                                            	    <form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="U_Automobile.php">
		                                            	        <input type="hidden" id="autoId" name="autoId" value="' . $row['ID'] . '">
		                                            	        <input type="hidden" id="action" name="action" value="Edit_Automobil">
		                                            	        <button class="btn btn-default btn-icon-anim"><i class="icon-settings"></i> ویرایش </button>
		                                            	    </form>
                                                            </td>';
                                                            echo "</span></td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="8" style="text-align: center;"> خودرویی ثبت نشده است </td></tr>';
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

    </div>
</body>

</html>