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

$Auto_Obj = new auto($_SESSION["Admin_GUID"]);
$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
$V_AllSpecialSER = $Tariff_Obj->V_NTariffSepecialSER();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>
    <!-- Morris Charts CSS -->
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
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
                                        <h6 class="panel-title txt-dark"> فهرست سرویس های خاص </h6>
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
                                                        <th> نام فارسی </th>
                                                        <th> نام انگلیسی </th>
                                                        <th> مشخصات خودرو </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام فارسی </th>
                                                        <th> نام انگلیسی </th>
                                                        <th> مشخصات خودرو </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($V_AllSpecialSER)) {
                                                        foreach ($V_AllSpecialSER as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['NameFa'] . "</td>";
                                                            echo "<td>" . $row['NameEn'] . "</td>";
                                                            $TariffGUID = $Tariff_Obj->GET_NTariff_ByID($row['TariffGUID']);
                                                            $auto = $Auto_Obj -> Get_Automobile_ByID ($TariffGUID[0]['AutoGUID']);
                                                            $auto_name = " نام خودرو: " . $auto[0]['Name'];
                                                            $auto_tipID =  $auto[0]['ModelID'];
                                                            $auto_manID = $auto[0]['ManufacturerID'];
                                                            $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
                                                            $tip = " تیپ :" . $auto_tipName [0]['ModelName'];
                                                            $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
                                                            $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];
                                                            echo "<td> [" . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . "]</td>";

                                                            echo '<td><div class="dropdown">
		                                                    	    <a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    	    	عملیات
		                                                    	    	<span class="caret"></span>
		                                                    	    	</a>
		                                                    	    	<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    	    		<li>
		                                                    	    		<form method="post" enctype="multipart/form-data" action="U_NTariffSpecialSRE.php">
		                                                    	    			<input type="hidden" name="Srv_GUID" value="' . $row['GUID'] . '">
		                                                    	    			<input type="hidden" name="action" value="EditNServices">
		                                                    	    		    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش </button>
		                                                    	    		</form>
		                                                    	    		</li>
		                                                    	    	</ul>
		                                                    		</div>';
                                                            echo "</td>";

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

    </div>

    <?php MainJavasc(); ?>
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
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