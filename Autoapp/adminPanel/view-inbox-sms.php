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

		require_once('Model/SmsChanel.php');
		use fateh\smschanel\SMS as sms;


 ?>
 
<!DOCTYPE html>
<html lang="en">
  <head>
<?php Metatag (); ?> 
 	<!-- Morris Charts CSS -->
    <link href="vendors/bower_components/morris.js/morris.css" rel="stylesheet" type="text/css"/>
	
	<!-- Data table CSS -->
	<link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
	
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
							<a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2">
							<i class="zmdi zmdi-chevron-down"></i>
							<i class="zmdi zmdi-chevron-up"></i>
							</a>
						</div>
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-dark"> فهرست پیامک های دریافتی </h6>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
						<hr >
						
						<div id="collapse_2" class="panel-wrapper collapse in">
							<div class="panel-body">
									<div class="table-wrap">
										<div class="table-responsive">
											<table id="datable_1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
													  <th> ارسال شده از</th>
													  <th> زمان </th>
													  <th> متن </th>
													</tr>
												</thead>
												<tfoot>
													<tr>
													  <th> ارسال شده به </th>
													  <th> موضوع </th>
													  <th> متن </th>
													</tr>
												</tfoot>
												<tbody>
												  <?php

														$viewsendsms = new sms();
														$viewsendsms-> getinboxsms();
														
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
								<!-- Gallery JavaScript -->
		<script src="dist/js/isotope.js"></script>
		<script src="dist/js/lightgallery-all.js"></script>
		<script src="dist/js/froogaloop2.min.js"></script>
		<script src="dist/js/gallery-data.js"></script>

	</div> 
</body>

</html>