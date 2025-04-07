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
require('Model/NotificationModel.php');

use fateh\Notification\InAppNotifications as Notif;

$Notif_Obj = new Notif($_SESSION["Admin_GUID"]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['EditNotif'])) {
        $NotifGUID = $_POST['NotifGUID'];

        if (!empty($_POST['NotifGUID']) && !empty($_POST['Notif_StartDate']) && !empty($_POST['Notif_EndDate']) && !empty($_POST['Notif_Title']) && !empty($_POST['Notif_Text'])) {
            $Notif_Obj = new Notif($_SESSION["Admin_GUID"]);
            $data = $Notif_Obj->GET_Notification_ByID($NotifGUID);

            $Notif_Sdate = $Notif_Obj->Gat_HejriDate($data[0]['Start']);
            $S_date = gregorian_to_jalali($Notif_Sdate[0], $Notif_Sdate[1], $Notif_Sdate[2]);
    
            $Notif_Edate = $Notif_Obj->Gat_HejriDate($data[0]['End']);
            $E_date = gregorian_to_jalali($Notif_Edate[0], $Notif_Edate[1], $Notif_Edate[2]);

            if ($_POST['Notif_Title'] == $data[0]['Title']) {
                $post_Title = $data[0]['Title'];
            } else {
                $post_Title = $_POST['Notif_Title'];
            }

            if ($_POST['Notif_Text'] == $data[0]['Text']) {
                $post_Text = $data[0]['Text'];
            } else {
                $post_Text = $_POST['Notif_Text'];
            }

            $post_StartDate = $_POST['Notif_StartDate'];
            if (!empty($post_StartDate)) {
                $fadate =  str_replace("-", "", $post_StartDate);
                $day = substr($fadate, strlen($fadate) - 2, 2);
                $mon = substr($fadate, strlen($fadate) - 4, 2);
                $year = substr($fadate, 0, 4);
                $Notif_SDate = jalali_to_gregorian($year, $mon, $day, '-');
            }

            if ($Notif_SDate == $data[0]['Start']) {
                $Notif_SDate = $data[0]['Start'];
            }

            $post_EndDate = $_POST['Notif_EndDate'];
            if (!empty($post_EndDate)) {
                $fadate =  str_replace("-", "", $post_EndDate);
                $day = substr($fadate, strlen($fadate) - 2, 2);
                $mon = substr($fadate, strlen($fadate) - 4, 2);
                $year = substr($fadate, 0, 4);
                $Notif_EDate = jalali_to_gregorian($year, $mon, $day, '-');
            }

            if ($Notif_EDate == $data[0]['End']) {
                $Notif_EDate = $data[0]['End'];
            }

            if ($Notif_EDate <  $Notif_SDate) {
                $Error_STR = 3;
            } else {
                $route = $Notif_Obj->U_Notification_ByID($NotifGUID, $post_Title, $post_Text, $Notif_SDate, $Notif_EDate);
            }
        } else {
            $Error_STR = 2;
        }
    }else {
        $Notif_Obj = new Notif($_SESSION["Admin_GUID"]);
        $NotifGUID = $_POST['NotifGUID'];
        $data = $Notif_Obj->GET_Notification_ByID($NotifGUID);

        $Notif_Sdate = $Notif_Obj->Gat_HejriDate($data[0]['Start']);
        $S_date = gregorian_to_jalali($Notif_Sdate[0], $Notif_Sdate[1], $Notif_Sdate[2]);

        $Notif_Edate = $Notif_Obj->Gat_HejriDate($data[0]['End']);
        $E_date = gregorian_to_jalali($Notif_Edate[0], $Notif_Edate[1], $Notif_Edate[2]);
    }
}

if (!empty($route) && $route == 1) {
    // return VALUES
    $url = "V_InAPPNotif.php";
    header("Location: $url");
} elseif (!empty($route)) {
    $Error_STR = 1;
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


    <!-- hejri -->

    <script src="dist/js/jquery.min.js"></script>
    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

    <!-- hejri -->
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
                                        <input type="hidden" id="NotifGUID" name="NotifGUID" value="<?php echo $NotifGUID; ?>">

                                        <div id="example-basic">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_Title"><i class="text-info mb-10">*</i> موضوع : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_Title" id="Notif_Title" placeholder="<?php echo $data[0]['Title'] ?>" value="<?php
                                                                                                                                                                                            if (!empty($data[0]['Title'])) {
                                                                                                                                                                                                echo $data[0]['Title'];
                                                                                                                                                                                            } else {
                                                                                                                                                                                                echo $_POST['Notif_Title'];
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_Text"><i class="text-info mb-10">*</i> شرح : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <textarea rows="10" cols="100" class="form-control" name="Notif_Text" id="Notif_Text" placeholder="<?php echo $data[0]['Text'] ?>" value="<?php
                                                                                                                                                                                        if (!empty($data[0]['Text'])) {
                                                                                                                                                                                            echo $data[0]['Text'];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo $_POST['Notif_Text'];
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>"></textarea>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_StartDate"><i class="text-info mb-10">*</i> تاریخ شروع : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_StartDate" id="Notif_StartDate" placeholder="<?php echo $S_date[0] . "-" . $S_date[1] . "-" . $S_date[2] ?>" value="">
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Notif_EndDate"><i class="text-info mb-10">*</i> تاریخ پایان : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Notif_EndDate" id="Notif_EndDate" placeholder="<?php echo $E_date[0] . "-" . $E_date[1] . "-" . $E_date[2]; ?>" value="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit" name="EditNotif" id="EditNotif"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
        <!-- JavaScript -->

        <?php MainJavasc(); ?>
        <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    </div>
    <!-- /#wrapper -->
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $("#Notif_StartDate").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
        });
        $(function() {
            $("#Notif_EndDate").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
        });
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

        if ($Error_STR == 3) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
                            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
                            $.toast({
                                heading: 'خطا در ثبت اطلاعات',
                                text: 'تاریخ اتمام اعلان نمی تواند قبل از تاریخ شروع باشد' ,
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