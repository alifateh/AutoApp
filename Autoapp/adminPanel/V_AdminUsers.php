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
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset ($_POST['action'])){
		if($_POST['action'] == "valid"){
			$ID = $_POST['Admin_GUID'];
			$notvalid_tariff = new admin($_SESSION["Admin_GUID"]);
			//$notvalid_tariff -> D_Admin($ID);
			$url = "V_AdminUsers.php";
			header("Location: $url");
		}
	}
	
		if (isset ($_POST['action'])){
		if($_POST['action'] == "not-valid"){
			$ID = $_POST['Admin_GUID'];
			$notvalid_tariff = new admin($_SESSION["Admin_GUID"]);
            $notvalid_tariff -> U_ActiveAdmin($ID);
			$url = "V_AdminUsers.php";
			header("Location: $url");
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
                                        <h6 class="panel-title txt-dark"> فهرست مدیران سیستم </h6>
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
                                                        <th> نام </th>
                                                        <th>نام خانوادگی</th>
                                                        <th>نام کاربری</th>
                                                        <th>وضعیت در سیستم</th>
                                                        <th> سطح دسترسی </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام </th>
                                                        <th>نام خانوادگی</th>
                                                        <th>نام کاربری</th>
                                                        <th>وضعیت در سیستم</th>
                                                        <th> سطح دسترسی </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $ViewAdmin = new admin($_SESSION["Admin_GUID"]);
                                                    $data = $ViewAdmin->V_Admin_Users();
                                                    if (!empty($data)) {
                                                        foreach ($data as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . $row['Fname'] . "</td>";
                                                            echo "<td>" . $row['Lname'] . "</td>";
                                                            echo "<td>" . $row['Aname'] . "</td>";
                                                            if ($row['Visible'] == 1) {
                                                                $visible = '<form name="myform" method="post" enctype="multipart/form-data">
                                                                <button class="btn btn-default btn-icon-anim " onclick="return changetonotvalid()"> فعال </button>
                                                                <input type="hidden" name="Admin_GUID" value="'.$row['GUID'].'">
                                                                <input type="hidden" name="action" value="valid"></form>';
                                                            } else {
                                                                $visible = '<form name="myform" method="post" enctype="multipart/form-data">
                                                                <button class="btn btn-pinterest btn-icon-anim " onclick="return changetovalid()"> غیر فعال </button>
                                                                <input type="hidden" name="Admin_GUID" value="'.$row['GUID'].'">
                                                                <input type="hidden" name="action" value="not-valid"></form>';
                                                            }
                                                            echo "<td>" . $visible . "</td>";
                                                            $AdminPermission = $ViewAdmin->Get_AdminPermission($row['GUID']);
                                                            if (!empty($AdminPermission)) {
                                                                $Perm_Data =  $ViewAdmin-> Get_PermissionByValue($AdminPermission[0]['AccessValue']);
                                                                $Permission =  $Perm_Data[0]['PermissionFa'];
                                                            } else {
                                                                $Permission = "سطح دسترسی نا مشخص";
                                                            }
                                                            echo "<td>" . $Permission . "</td>";


                                                            echo '<td>';
		                                                    	echo '<div class="dropdown">
		                                                    			<a class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		                                                    				عملیات
		                                                    				<span class="caret"></span>
		                                                    				</a>
		                                                    				<ul class="dropdown-menu" data-dropdown-in="bounceIn" data-dropdown-out="bounceOut" style="background-color: #efefef;">
		                                                    					<li>
		                                                    					<form method="post" enctype="multipart/form-data" action="U_AdminProfile.php">
		                                                    						<input type="hidden" name="Admin_GUID" value="'.$row['GUID'].'">
		                                                    						<input type="hidden" name="action" value="EditeProfile">
		                                                    					    <button style ="border:none;"><i class="icon-pencil"></i> ویرایش مشخصات </button>
		                                                    					</form>
		                                                    					</li>
                                                                                <li class="divider"></li>
		                                                    					<li>
		                                                    						<form method="post" enctype="multipart/form-data" action="U_AdminPermission.php">
		                                                    							<input type="hidden" name="Admin_GUID" value="'.$row['GUID'].'">
		                                                    							<input type="hidden" name="action" value="EditePermission">
		                                                    							<button style ="border:none;"><i class="icon-pencil"></i> ویرایش سطح دسترسی </button>
		                                                    						</form>
		                                                    					</li>
		                                                    				</ul>
		                                                    		</div>';
		                                                    	echo "</td>";
                                                            
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="4" style="text-align: center;"> مدیری تعریف نشده است </td></tr>';
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
    </div>
    <script LANGUAGE="JavaScript">

function removevalidation() {
    var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
    if (agree)
        return true;
    else
        return false;
}

function changetonotvalid() {
    var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
    if (agree)
        return true;
    else
        return false;
}

function changetovalid() {
    var agree = confirm("آیا از صحت این عمل مطمین می باشید؟");
    if (agree)
        return true;
    else
        return false;
}

</script>
</body>

</html>