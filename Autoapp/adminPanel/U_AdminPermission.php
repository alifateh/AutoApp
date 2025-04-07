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
require_once('Model/Admin-Users.php');

use fateh\login\Admin as admin;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['action'] == "EditePermission") {
        $ID = $_POST['Admin_GUID'];
        $Admin = new admin($_SESSION["Admin_GUID"]);
        $Admin_Profile = $Admin->Get_Admin($ID);
        $AdminPermission = $Admin->Get_AdminPermission($Admin_Profile[0]['GUID']);
        $PermissionNUm = $Admin->Get_PermissionByValue($AdminPermission[0]['AccessValue']);
    } elseif (isset($_POST['U_Permission'])) {
        $ID = $_POST['AdminGUID'];
        $OldPermission = $_POST['AdminPermission'];

        $updateAdmin = new admin($_SESSION["Admin_GUID"]);
        $AdminUser = $updateAdmin->Get_Admin($ID);

        if (!empty($_POST['PermissionLevel'])) {

            if ($_POST['PermissionLevel'] !== $OldPermission) {
                $Permission = $_POST['PermissionLevel'];
            } else {
                $Permission = $OldPermission;
            }

            $route = $updateAdmin->U_AdminPermission($AdminUser[0]['GUID'], $Permission);
            if (!empty($route)  ) {
                // return VALUES
                $url = "V_AdminUsers.php";
                header("Location: $url");
            } else {
                $ErrorStr = 2;
            }
        } else {
            $ErrorStr = 1;
        }
    }
} else {
    $url = "V_AdminUsers.php";
    header("Location: $url");
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
                                        <h6 class="panel-title txt-dark"> ویرایش مشخصات </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="example-basic">
                                        <div class="col-sm-6">
                                            <form method="post" enctype="multipart/form-data">
                                                <input type="hidden" id="AdminGUID" name="AdminGUID" value="<?php echo $Admin_Profile[0]['GUID']; ?>">
                                                <input type="hidden" id="AdminPermission" name="AdminPermission" value="<?php echo $AdminPermission[0]['AccessValue']; ?>">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Fname"> نام : <?php echo $Admin_Profile[0]["Fname"]; ?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Lname"> نام خانوادگی : <?php echo $Admin_Profile[0]["Lname"]; ?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Aname"> نام کاربری : <?php echo $Admin_Profile[0]["Aname"]; ?></label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="PermissionLevel"> سطح دسترسی </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="PermissionLevel" id="PermissionLevel">
                                                            <?php

                                                            $data = $Admin->Get_Permission();
                                                            if (!empty($data)) {
                                                                foreach ($data as $row) {
                                                                    if ($row['ID'] == $PermissionNUm[0]['ID']) {
                                                                        echo '<option value="' . $row['Start_Value'] . '" selected >' . $row['PermissionFa'] . '</option>';
                                                                    } else {
                                                                        echo '<option value="' . $row['Start_Value'] . '">' . $row['PermissionFa'] . '</option>';
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<option value=""> تعرفه ایی ثبت نشده </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit" name="U_Permission" id="U_Permission"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->

            </div>

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

    </div>
    <!-- /#wrapper -->
    <!-- JavaScript -->
    <?php MainJavasc(); ?>
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
    <?php


    if (!empty($ErrorStr)) {
        if ($ErrorStr = 2 OR $ErrorStr = 1 ) {
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