<?php 
session_start(); 
if(!isset( $_SESSION["Admin_GUID"]) || !isset( $_SESSION["Admin_ID"])){
session_start();
session_unset();
session_write_close();
$url = "./sysAdmin.php";
header("Location: $url");
	
}
	require('config/public_conf.php');

	require('Model/TellModel.php');
	use fateh\phonebook\phonebook as Tell;
		$Phone = new Tell($_SESSION["Admin_GUID"]);
		
		
	if (!isset($_POST['Edit'])){
		if(isset($_POST['SendEditNum'])){
			$ID = $_POST['phoneID'];
			$Phone_Detial = $Phone -> Get_PhoneFree_ByID ($ID);
		}else{
			$url = "./V_Phonebook.php";
			header("Location: $url");
		}
	}	
	
	if (isset($_POST['Edit'])) {
			$ID = $_POST['id'];
			
			if(!empty ($_POST['Fname'])){
				$Fname = $_POST['Fname'];
			}else{
				$Fname = $_POST['old-Fname'];
			}
			
			if(!empty ($_POST['Lname'])){
				$Lname = $_POST['Lname'];
			}else{
				$Lname = $_POST['old-Lname'];
			}
			
			if(!empty ($_POST['Number'])){
				$Number = $_POST['Number'];
			}else{
				$Number = $_POST['old-Number'];
			}
			
			if(!empty ($_POST['Operator'])){
				$Operator = $_POST['Operator'];
			}else{
				$Operator = $_POST['old-Operator'];
			}
			
			if(!empty ($_POST['tags'])){
				$tags = $_POST['tags'];
			}else{
				$tags = $_POST['old-tags'];
			}
			
		$Phone -> UpdateFreeTellByID($ID, $Fname, $Lname, $Number, $Operator, $tags);
		$url = "./V_Phonebook.php";
		header("Location: $url");
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
<?php Metatag (); ?> 

	<!-- Jasny-bootstrap CSS -->
	<link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css"/>

	<!-- bootstrap-touchspin CSS -->
	<link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css"/>
	
	<!-- bootstrap-tagsinput CSS -->
	<link href="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>

	<!-- Custom CSS -->
	<link href="dist/css/style.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		
    function onlyNumberKey(evt) { 
          
        // Only ASCII charactar in that range allowed 
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) 
            return false; 
        return true; 
    }
	</script>
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
								<h6 class="panel-title txt-dark"> ویرایش شماره تلفن </h6>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
						<hr >
						<div id="collapse_2" class="panel-wrapper collapse in">
							<div class="panel-body">
									<form method="post" enctype="multipart/form-data">
									<input type="hidden" id="id" name="id" value="<?php echo $ID; ?>">
									<input type="hidden" id="old-Fname" name="old-Fname" value="<?php echo $Phone_Detial[0]; ?>">
									<input type="hidden" id="old-Lname" name="old-Lname" value="<?php echo $Phone_Detial[1]; ?>">
									<input type="hidden" id="old-Number" name="old-Number" value="<?php echo $Phone_Detial[2]; ?>">
									<input type="hidden" id="old-Operator" name="old-Operator" value="<?php echo $Phone_Detial[3]; ?>">
									<input type="hidden" id="old-tags" name="old-tags" value="<?php echo $Phone_Detial[4]; ?>">
									
												<div id="example-basic">
													<div class="col-sm-6">
													<div class="form-group">
														<label class="control-label mb-10" for="Fname"> نام : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="icon-info"></i></div>
																<input type="text" class="form-control" name ="Fname" id="Fname" value="<?php echo $Phone_Detial[0]; ?>"  >
															</div>
													</div>
													<div class="form-group">
														<label class="control-label mb-10" for="Lname"> نام خانوادگی : </label>
															<div class="input-group">
																<div class="input-group-addon"><i class="icon-info"></i></div>
																<input type="text" class="form-control" name ="Lname" id="Lname" value="<?php echo $Phone_Detial[1]; ?>"  >
															</div>
													</div>
													<div class="form-group">
														<label class="control-label mb-10" for="Number"> شماره تلفن : </label>
														<div class="input-group col-sm-6">
														<div class="input-group-addon"><i class="icon-info"></i></div>
														<input type="text" class="form-control" name="Number" id="Number" onkeypress="return onlyNumberKey(event)" value="<?php echo $Phone_Detial[2]; ?>" minlength="10" maxlength="10">
														</div>
													</div>
													<div class="form-group">
													<label class="control-label mb-10"> نوع شماره تماس : </label>
												<select class="form-control " dir="rtl" name="Operator" id="Operator"> 
													<?php
													$op = $Phone-> Get_PhoneOperator();
													echo '<option value=""> انتخاب نمایید </option>';
													foreach ($op as $key ){
														if($Phone_Detial[3] == $key[0]){
															echo '<option value='.$key[0].' selected >'.$key[1].'</option>';
														}else{
															echo '<option value='.$key[0].'>'.$key[1].'</option>';
														}
													}
													
													?>
												</select>
												</div>
												<div class="form-group">
													<label class="control-label mb-10 text-left" for="tags" > کلمات کلیدی : </label>
														<input type="text" value="<?php echo $Phone_Detial[4]; ?>" data-role="tagsinput" id="tags" name="tags">
												</div>
														<br>
														<div class="form-group">
																<button class="btn btn-success mr-10 mb-30" type="submit" name="Edit" id="Edit"> ثبت </button>
														</div>
													</div>
												</div>
												</form>
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
			<?php MainJavasc (); ?>
	<script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
		<!-- Bootstrap Tagsinput JavaScript -->
	<script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
	</body>
</html>