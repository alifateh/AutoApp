<?php 
session_start(); 
if(!isset( $_SESSION["Admin_GUID"]) || !isset( $_SESSION["Admin_ID"])){
session_start();
session_unset();
session_write_close();
$url = "./sysAdmin.php";
header("Location: $url");
	
}
require_once('config/public_conf.php');


require('Model/SkillsModel.php');
use fateh\skills\skills;
if (isset ($_POST['action'])){
	if($_POST['action'] == "remove"){
		$ID = $_POST['skillsId'];
		
		$viewskills = new skills($_SESSION["Admin_GUID"]);
		$viewskills-> removeskils($ID);
		$url = "./view-skills.php";
		header("Location: $url");
	}
}

 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag (); ?>
    <!-- Morris Charts CSS -->
    <link href="vendors/bower_components/morris.js/morris.css" rel="stylesheet" type="text/css" />

    <!-- Data table CSS -->
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet"
        type="text/css">

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
        <?php minimenu (); ?>
        <!-- /mini Menu Items -->

        <!-- Right Sidebar Menu -->
        <?php Mainmenu (); ?>
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
                                    <a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2"
                                        aria-expanded="true" aria-controls="collapse_2">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <i class="zmdi zmdi-chevron-up"></i>
                                    </a>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> فهرست تخصص ها و مهارت ها </h6>
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
                                                        <th> نام تخصص </th>
                                                        <th> عملیات </th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th> نام تخصص </th>
                                                        <th> عملیات </th>
                                                        
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
												  
														$viewskills = new skills($_SESSION["Admin_GUID"]);
														$data = $viewskills-> V_SkillsAll();
														if(!empty($data)){
														foreach ($data as $row ){
		                                                	//$logo = $this-> viewmanufacturelogo($row['Filekey']);
		                                                	echo "<tr>";
		                                                	echo "<td>".$row['Name']."</td>";
		                                                	echo '<td style ="width: 35%; white-space: nowrap;">';
		                                                	$validation = "onclick='return validation()'";
		                                                	echo '<form style ="float: right; padding-left: 2%;" method="post">
		                                                	<input type="hidden" id="skillsId" name="skillsId" value="'.$row['ID'].'">
		                                                	<input type="hidden" id="action" name="action" value="remove">
		                                                	<button class="btn btn-default btn-icon-anim" '.$validation.'><i class="icon-trash"></i> حذف </button>
		                                                	</form>
		                                                	<form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="edit-skills.php">
		                                                	<input type="hidden" id="skillsId" name="skillsId" value="'.$row['ID'].'">
		                                                	<input type="hidden" id="action" name="action" value="edit">
		                                                	<button class="btn btn-default btn-icon-anim "><i class="icon-settings"></i> ویرایش </button>
		                                                	</form>';
		                                                	echo "</td></tr>";
		                                                	
		                                                	} 

														}else{
															echo '<tr><td colspan="2" style="text-align: center;"> مهارتی ثبت نشده است </td></tr>';
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
    <?php MainJavasc (); ?>
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