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
    if ($_POST['action'] == "EditeProfile") {
        $ID = $_POST['Admin_GUID'];
        $Admin = new admin($_SESSION["Admin_GUID"]);
        $Admin_Profile = $Admin->Get_Admin($ID);
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
    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['UpdateProfile'])) {
            $ID = $_POST['AdminGUID'];
            $updateAdmin = new admin($_SESSION["Admin_GUID"]);
            $OLDProfile = $updateAdmin->Get_Admin($ID);

            if (!empty($OLDProfile)) {

                if (!empty($_POST['Fname']) && $_POST['Fname'] !== $OLDProfile[0]["Fname"]) {
                    $Fname = $_POST['Fname'];
                } else {
                    $Fname = $OLDProfile[0]["Fname"];
                }

                if (!empty($_POST['Lname']) && $_POST['Lname'] !== $OLDProfile[0]["Lname"]) {
                    $Lname = $_POST['Lname'];
                } else {
                    $Lname = $OLDProfile[0]["Lname"];
                }

                if (!empty($_POST['Aname']) && $_POST['Aname'] !== $OLDProfile[0]["Aname"]) {
                    $Aname = $_POST['Aname'];
                } else {
                    $Aname = $OLDProfile[0]["Aname"];
                }

                if (!empty($_POST['email']) && $_POST['email'] !== $OLDProfile[0]["Email"]) {
                    $email = $_POST['email'];
                } else {
                    $email = $OLDProfile[0]["Email"];
                }

                if (!empty($_POST['pass'])){
                    if (password_verify($_POST['pass'], $OLDProfile[0]["Pass"])) {
                        $Pass = $OLDProfile[0]["Pass"];
                    }else{
                        $Pass = password_hash($_POST['pass'], '2y');
                    }
                }else{
                    $Pass = $OLDProfile[0]["Pass"];
                }

                $route = $updateAdmin->U_AdminProfile($ID, $Fname, $Lname, $Aname, $email, $Pass);
                if (!empty($route)  ) {
                    // return VALUES
                    $url = "V_AdminUsers.php";
                    header("Location: $url");
                } else {
                    echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
                }
            }else{
                echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
            }
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
                                                <input type="hidden" id="AdminGUID" name="AdminGUID" value="<?php echo $_POST['Admin_GUID']; ?>">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Fname"> نام </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Fname" id="Fname" placeholder="<?php echo $Admin_Profile[0]["Fname"]; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Lname"> نام خانوادگی </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Lname" id="Lname" placeholder="<?php echo $Admin_Profile[0]["Lname"]; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Aname"> نام کاربری </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Aname" id="Aname" placeholder="<?php echo $Admin_Profile[0]["Aname"]; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="email"> آدرس ایمیل </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="email" id="email" placeholder="<?php echo $Admin_Profile[0]["Email"]; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="pass"> رمز عبور </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="pass" id="pass" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit" name="UpdateProfile" id="UpdateProfile"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
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

</body>

</html>