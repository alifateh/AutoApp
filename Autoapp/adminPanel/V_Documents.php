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
require('Model/DocumentsModel.php');

use fateh\Documents\Doc as Doc;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action'])) {
        if ($_POST['action'] == "RemoveDoc") {
            $GUID = $_POST['Doc_GUID'];
            $Doc_Obj = new Doc($_SESSION["Admin_GUID"]);
            $route = $Doc_Obj->D_DocumentByID($GUID);
            if (!empty($route)  ) {
                $url = "V_Documents.php";
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
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
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
                                        <h6 class="panel-title txt-dark"> فهرست مستندات </h6>
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
                                                        <th> نام مستند </th>
                                                        <th> شرح سند </th>
                                                        <th> دانلود </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام مستند </th>
                                                        <th> شرح سند </th>
                                                        <th> دانلود </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $ViewDoc = new Doc($_SESSION["Admin_GUID"]);
                                                    $allDocs = $ViewDoc->V_Documents();

                                                    if (!empty($allDocs)) {
                                                        foreach ($allDocs as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['Title'] . "</td>";
                                                            echo "<td>" . $row['Comment'] . "</td>";
                                                            echo "<td><a href='" . "/" . $row['Address'] . "' class='btn btn-default btn-icon-animt'> دانلود </span></a> </td>";
                                                            echo '<td>';
                                                            echo '<div class="dropdown">
		                                                    			<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    				عملیات
		                                                    				<span class="caret"></span>
		                                                    				</a>
		                                                    				<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data">
		                                                    							<input type="hidden" name="Doc_GUID" value="' . $row['GUID'] . '">
		                                                    							<input type="hidden" name="action" value="RemoveDoc">
		                                                    							<button style ="border:none;"><i class="icon-close"></i> حذف </button>
		                                                    						</form>
		                                                    					</li>
		                                                    				</ul>
		                                                    		</div>';
                                                            echo "</td>";

                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="4" style="text-align: center;"> مستندی تعریف نشده است </td></tr>';
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
    <script LANGUAGE="JavaScript">
        function removevalidation() {
            var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }
    </script>

    
	<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

<!-- Slimscroll JavaScript -->
<script src="dist/js/jquery.slimscroll.js"></script>

<!-- Fancy Dropdown JS -->
<script src="dist/js/dropdown-bootstrap-extended.js"></script>

<!-- Owl JavaScript -->
<script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

<!-- Switchery JavaScript -->
<script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

<!-- Init JavaScript -->
<script src="dist/js/init.js"></script>
<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

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
        } elseif ($Error_STR == 2) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                        $.toast({
                            heading: 'خطا در کاربری',
                            text: 'کاربر فعال نشده است' ,
                            position: 'top-center',
                            loaderBg:'#ed3236',
                            hideAfter: 6500,
                            stack: 6
                        });
                        return false;
                });";
            echo '</script>';
        }else {
			$Error_STR = Null;
		}
    }

    ?>
</body>

</html>