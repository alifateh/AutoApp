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

require_once('Model/AutoModel.php');

use fateh\Automobile\Automobile as auto;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['editManufactur'])) {
        if ($_POST['action'] == "edit") {
            $ID = $_POST['ManufacturId'];
            $Manufactur = new auto($_SESSION["Admin_GUID"]);
            $ManufactureName = $Manufactur->Get_Manufactuer_ByID($ID);
        }
    }
} else {
    $url = "V_Manufactures.php";
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
    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        if (isset($_POST['remove_file'])) {
            if ($_POST['action'] == "remove_file") {
                $ID = $_POST['file_Id'];
                $remove_file = new auto($_SESSION["Admin_GUID"]);
                $route = $remove_file->D_ManufacturLogo_ByID($ID);
                if (!empty($route)  ) {
                    // return VALUES
                    $url = "V_Manufactures.php";
                    header("Location: $url");
                }
            }
        }

        if (isset($_POST['editManufactur'])) {
            $ID = $_POST['ManufacturId'];
            $updateman = new auto($_SESSION["Admin_GUID"]);
            $get_manname = $updateman->Get_Manufactuer_ByID($ID);

            $post_manname = $_POST['Manufacturname'];

            if (!empty($post_manname)) {

                if ($post_manname !== $get_manname[0]["Name"]) {
                    $route = $updateman->U_Manufacture($ID, $post_manname);
                    if (!empty($route)  ) {
                        // return VALUES
                        $url = "V_Manufactures.php";
                        header("Location: $url");
                    } else {
                        echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
                    }
                } else {
                    $route = $updateman->U_Manufacture($ID, $get_manname[0]["Name"]);
                    if (!empty($route)  ) {
                        // return VALUES
                        $url = "V_Manufactures.php";
                        header("Location: $url");
                    } else {
                        echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
                    }
                }
            } else {

                $route = $updateman->U_Manufacture($ID, $get_manname[0]["Name"]);
                if (!empty($route)  ) {
                    // return VALUES
                    $url = "V_Manufactures.php";
                    header("Location: $url");
                } else {
                    echo '<script type="text/javascript">alert("عملیات انجام نشد!");</script>';
                }
            }
        }
    }
    ?>

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
                                        <h6 class="panel-title txt-dark"> ویرایش خودروساز </h6>
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
                                                <input type="hidden" id="ManufacturId" name="ManufacturId" value="<?php echo $_POST['ManufacturId']; ?>">
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Manufacturname"> نام
                                                        خودروساز : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Manufacturname" id="Manufacturname" placeholder="<?php echo $ManufactureName[0]["Name"] ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-info btn-anim" type="submit" name="editManufactur" id="editManufactur"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
                                                </div>
                                            </form>
                                                <div class="row">
                                                    <!-- Table Hover -->
                                                    <div class="col-sm-12">
                                                        <div class="panel panel-default card-view">
                                                            <div class="panel-heading">
                                                                <div class="pull-left">
                                                                    <h6 class="panel-title txt-dark"> فهرست فایل </h6>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="panel-wrapper collapse in">
                                                                <div class="panel-body">
                                                                    <div class="table-wrap mt-40">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-hover mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th> فایل </th>
                                                                                        <th> عملیات </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    $filedev = new auto($_SESSION["Admin_GUID"]);
                                                                                    $data = $filedev->Get_ManufactureLogo_ByID($ManufactureName[0]["Filekey"]);
                                                                                   
                                                                                    if (!empty($data)) {
                                                                                        foreach ($data as $row) {
                                                                                            if (!empty($row["location"])) {
                                                                                                $validation = "onclick='return validation()'";
                                                                                                echo ' <tr>';
                                                                                                echo ' <td><a href="' . $row['location'] . '"><span class="btn-text btn-default btn-icon-anim"><i class="icon-cloud-download"></i> ذخیره فایل </span></a></td>';

                                                                                                echo '<td style ="width: 5%;">
                                                                                                         <form method="post" enctype="multipart/form-data">
                                                                                                             <input type="hidden" id="file_Id" name="file_Id" value="' . $row['ID'] . '">
                                                                                                             <input type="hidden" id="action" name="action" value="remove_file">
                                                                                                             <button class="btn btn-info btn-icon-anim" type="submit" name="remove_file" id="remove_file" ' . $validation . '><i class="icon-trash"></i> حذف </button>
                                                                                                         </form>
                                                                                            </td></tr>';
                                                                                            }
                                                                                        }
                                                                                    } else {
                                                                                        echo ' <tr>';
                                                                                        echo '<td style ="width: 5%;">
                                                                                                <form method="post" enctype="multipart/form-data" action ="Upload-image.php">
		                                            	                                            <input type="hidden" id="ItemGUId" name="ItemGUId" value="' . $ManufactureName[0]["Filekey"] . '">
		                                            	                                            <input type="hidden" id="eltype" name="eltype" value="8">
		                                            	                                            <button class="btn btn-default btn-icon-anim "><i class="icon-cloud-upload"></i> آپلود فایل </button>
		                                            	                                        </form>
                                                                                            </td></tr>';
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
                                                    <!-- /Table Hover -->

                                                </div>
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
    <!-- /#wrapper -->
    <!-- JavaScript -->
    <?php MainJavasc(); ?>
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

</body>

</html>