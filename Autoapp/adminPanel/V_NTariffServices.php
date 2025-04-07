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
require('Model/N_TariffModel.php');

use fateh\tariff\NewTariff as NewTariff;

$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
$V_AllTariffServices = $Tariff_Obj->V_AllTariffServices();
$Error_STR = 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action'])) {
        if ($_POST['action'] == "RemoveNTariff") {
            $GUID = $_POST['Srv_GUID'];
            $route = $Tariff_Obj->D_NTariffService_ByID($GUID);
            if (!empty($route)  ) {
                $url = "V_NTariffServices.php";
                header("Location: $url");
            } else {
                $Error_STR = 1;
            }
        }
    }
}

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
                                        <h6 class="panel-title txt-dark"> فهرست سرویس ها </h6>
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
                                                        <th> انواع تعرفه ها </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام فارسی </th>
                                                        <th> نام انگلیسی </th>
                                                        <th> انواع تعرفه ها </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($V_AllTariffServices)) {
                                                        foreach ($V_AllTariffServices as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['NameFa'] . "</td>";
                                                            echo "<td>" . $row['NameEn'] . "</td>";
                                                            if(!empty($row['TariffTypeGUID'])){
                                                            $TT =  $Tariff_Obj ->Get_TariffType_ByID ($row['TariffTypeGUID']);
                                                            $list = $TT[0]['NameFa'];
                                                            }else{
                                                                $list = "نوع تعرفه تعیین نشده";
                                                            }
                                                            echo "<td> $list </td>";
                                                            echo '<td><div class="dropdown">
		                                                    	    <a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    	    	عملیات
		                                                    	    	<span class="caret"></span>
		                                                    	    	</a>
		                                                    	    	<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    	    		<li>
		                                                    	    		<form method="post" enctype="multipart/form-data" action="U_NTariffService.php">
		                                                    	    			<input type="hidden" name="Srv_GUID" value="' . $row['GUID'] . '">
		                                                    	    			<input type="hidden" name="action" value="EditNServices">
		                                                    	    		    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش </button>
		                                                    	    		</form>
		                                                    	    		</li>
                                                                            <li class="divider"></li>
		                                                    	    		<li>
		                                                    	    			<form method="post" enctype="multipart/form-data">
		                                                    	    				<input type="hidden" name="Srv_GUID" value="' . $row['GUID'] . '">
		                                                    	    				<input type="hidden" name="action" value="RemoveNTariff">
                                                                                    <button style ="border:none;" onclick="return validation()"><i class="icon-close"></i> حذف </button>
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

    <?php
    if (!empty($Error_STR)) {
        if ($Error_STR == 1) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                        $.toast({
                            heading: 'خطا درسمت سرور',
                            text: ' عملیات با مشکل مواجه شد ' ,
                            position: 'top-center',
                            loaderBg:'#ed3236',
                            hideAfter: 6500,
                            stack: 6
                        });
                        return false;
                });";
            echo '</script>';
        } else {
            $Error_STR = Null;
        }
    }

    ?>
</body>

</html>