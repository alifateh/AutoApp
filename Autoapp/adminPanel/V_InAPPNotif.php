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
require('Model/NotificationModel.php');

use fateh\Notification\InAppNotifications as Notif;

$Notif_Obj = new Notif($_SESSION["Admin_GUID"]);
$data = $Notif_Obj->V_InAPPNotif();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "remove") {
            $GUID = $_POST['Notif_GUID'];
            $route = $Notif_Obj->D_Notification_ByID($GUID);
        }
    }
}

if (!empty($route) && $route == 1) {
    // return VALUES
    $url = "V_InAPPNotif.php";
    header("Location: $url");
} elseif(!empty($route)) {
    $Error_STR = 1;
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
                                        <h6 class="panel-title txt-dark"> فهرست اعلان های درون برنامه </h6>
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
                                                        <th> موضوع </th>
                                                        <th> شرح </th>
                                                        <th> تاریخ شروع </th>
                                                        <th> تاریخ پایان </th>
                                                        <th> وضعیت اعتبار </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> موضوع </th>
                                                        <th> شرح </th>
                                                        <th> تاریخ شروع </th>
                                                        <th> تاریخ پایان </th>
                                                        <th> وضعیت اعتبار </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    if (!empty($data)) {
                                                        foreach ($data as $row) {

                                                            echo "<tr>";
                                                            echo "<td>". $row['Title']."</td>";
                                                            echo "<td>". $row['Text']."</td>";

                                                            $Notif_Sdate = $Notif_Obj->Gat_HejriDate($row['Start']);
                                                            $S_date = gregorian_to_jalali($Notif_Sdate[0], $Notif_Sdate[1], $Notif_Sdate[2]);
                                                            echo "<td>" . $S_date[0] . "/" . $S_date[1] . "/" . $S_date[2] . "</td>";

                                                            $Notif_Edate = $Notif_Obj->Gat_HejriDate($row['End']);
                                                            $E_date = gregorian_to_jalali($Notif_Edate[0], $Notif_Edate[1], $Notif_Edate[2]);
                                                            echo "<td>" . $E_date[0] . "/" . $E_date[1] . "/" . $E_date[2] . "</td>";

                                                            if ($row['Validation'] == 1) {
                                                                $str_validation = '<button class="btn btn-default btn-icon-anim "> معتبر </button>';
                                                            } else {
                                                                 $str_validation = '<button class="btn btn-pinterest btn-icon-anim "> نامعتبر </button>';
                                                            }
                                                            echo "<td>" . $str_validation . "</td>";

                                                            echo '<td>';
                                                            echo '<div class="dropdown">
		                                                    			<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    				عملیات
		                                                    				<span class="caret"></span>
		                                                    				</a>
		                                                    				<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    					<li>
		                                                    					<form method="post" enctype="multipart/form-data" action="U_InAPPNotif.php">
		                                                    						<input type="hidden" name="NotifGUID" value="' . $row["GUID"] . '">
		                                                    						<input type="hidden" name="action" value="Edit-Notif">
		                                                    					    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش </button>
		                                                    					</form>
		                                                    					</li>
		                                                    					<li class="divider"></li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data">
		                                                    							<input type="hidden" name="Notif_GUID" value="' . $row['GUID'] . '">
		                                                    							<input type="hidden" name="action" value="remove">
		                                                    							<button style ="border:none;" onclick="return removevalidation()"><i class="icon-close"></i> حذف </button>
		                                                    						</form>
		                                                    					</li>
		                                                    				</ul>
		                                                    		</div>';
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="5" style="text-align: center;"> اعلانی ثبت نشده است </td></tr>';
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
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <script LANGUAGE="JavaScript">
        function removevalidation() {
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
    }


    ?>

</body>

</html>