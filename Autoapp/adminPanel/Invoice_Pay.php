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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ActionID']) && $_POST['ActionID'] == 'ChosePayMethod') {
        $mem_GUID = $_POST['MechanicGUID'];
        $_SESSION["GUID"] = $_POST['MechanicGUID'];
        $Inv_GUID = $_POST['Inv_GUID'];
    } else {
        $url = "./V_MemberAll.php";
        header("Location: $url");
    }
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
                                        <h6 class="panel-title txt-dark"> نحوه پرداخت </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="example-basic">
                                        <div class="col-sm-6">
                                            <form method="post" enctype="multipart/form-data" action="Sep_Init.php">
                                                <input type="hidden" id="Member_GUID" name="Member_GUID" value="<?php echo $mem_GUID; ?>">
                                                <input type="hidden" id="Invoice_GUID" name="Invoice_GUID" value="<?php echo $Inv_GUID; ?>">
                                                <input type="hidden" id="action" name="action" value="Payment-Methods">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Payment-Methods"> پرداخت
                                                        به صورت : </label>
                                                    <div class="input-group">
                                                    <select class="form-control select2" dir="rtl" Name="Payment-Methods" id="Payment-Methods" tabindex="1">
                                                    <option value="1" selected> اینترنتی </option>
                                                    <option value="2"> حضوری </option>
                                                    </select>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <div class="form-group">
                                                        <button class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text"> پرداخت
                                                            </span></button>
                                                    </div>
                                            </form>
                                            <br />
                                            <br />
                                            <div id="payment-form" name="payment-form"></div>
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


</body>

</html>