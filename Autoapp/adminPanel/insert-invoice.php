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

    <script src="dist/js/jquery.min.js"></script>
    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />
    <script type="text/javascript">
        function format(input) {
            var nStr = input.value + '';
            nStr = nStr.replace(/\,/g, "");
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            input.value = x1 + x2;
        }

        function onlyNumberKey(evt) {

            // Only ASCII charactar in that range allowed 
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
                return false;
            } else {
                return true;
            }
        }

        function validateForm() {
            var x = document.forms["MemberRegForm"]["fname"].value;
            if (x == "") {
                alert("مقدار [نام] خالی می باشد");
                return false;
            }
        }
    </script>

</head>

<body>
    <?php
    require_once('Model/InvoiceModel.php');
    require_once('Model/N_TariffModel.php');


    use fateh\tariff\NewTariff as NewTariff;
    use fateh\Finance\Invoice as invoice;
    
    $reg_Invoce = new invoice($_SESSION["Admin_GUID"]);

    $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);

    $get_TariffAllVer = $Tariff_Obj->V_AllTariffVersion();

    if (isset($_POST['Add_Btn']) && $_POST['Inv_Topic'] !== "" && $_POST['Inv_Amount'] !== "") {

        if (empty($_POST['Inv_Amount'])) {
            echo '<script language="javascript">';
            echo 'alert("لطفا قیمت صورت حساب را مشخص نمایید")';
            echo '</script>';
        } else {

            $Inv = array();
            $Inv_Session = md5(uniqid(mt_rand(100000, 999999), true));
            $_SESSION["Inv_Session"] = $Inv_Session;
            $Inv[0] = $Inv_Session;
            $Inv[1] = $_POST['Inv_Topic'];
            $Inv[2] = $_POST['Inv_validationDate'];
            $Inv[3] = $_POST['Inv_Amount'];
            $Inv[4] = $_POST['Inv_Comment'];
            $Inv[5] = $_SESSION["Admin_ID"];

            if (file_exists($_FILES['fileToUpload']['tmp_name']) || is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
                $target_dir = "images/invoiceDoc/";
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if image file is a actual image or fake image

                $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check !== false) {
                    //echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    // echo "File is not an image.";
                    $uploadOk = 0;
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo '<script language="javascript">';
                    echo "$(document).ready(function() {
			    		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
			    		$.toast({
			    			heading: 'خطا فایل آپلود نشد!!!',
			    			text: 'فایلی با این نام در سرور موجود میباشد'+'<br />'+'برای آپلود نام فایل را عوض کنید.' ,
			    			position: 'top-center',
			    			loaderBg:'#ed3236',
			    			hideAfter: 6500,
			    			stack: 6
			    		});
			    		return false;});";
                    echo '</script>';
                    $uploadOk = 0;
                }

                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 5000000) {
                    echo '<script language="javascript">';
                    echo "$(document).ready(function() {
		        		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
		        		$.toast({
		        			heading: 'خطا فایل آپلود نشد!!!',
		        			text: 'سایز فایل بیشتر از 50 مگابایت می باشد'+'<br />'+'برای آپلود سایز فایل را کمتر نمایید.' ,
		        			position: 'top-center',
		        			loaderBg:'#ed3236',
		        			hideAfter: 6500,
		        			stack: 6
		        		});
		        		return false; });";
                    echo '</script>';
                    $uploadOk = 0;
                }

                // Allow certain file formats

                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    echo '<script language="javascript">';
                    echo "$(document).ready(function() {
			    		$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
			    		$.toast({
			    			heading: 'خطا فایل آپلود نشد!!!',
			    			text: 'لطفا قالب فایل را از بین قالب های '+'<br />'+'[jpg] یا [jpeg] یا [gif] یا [png]'+'<br />'+' انتخاب نمایید',
			    			position: 'top-center',
			    			loaderBg:'#ed3236',
			    			hideAfter: 6500,
			    			stack: 6
			    		});
			    		return false;});";
                    echo '</script>';
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "خطایی در سمت سرور هست";
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $reg_Invoce->Register_Invoice_File($Inv_Session, $target_file);
                        $reg_Invoce->Register_Invoice($Inv);
                        $url = "./V_AllInvoives.php";
                        header("Location: $url");
                    } else {
                        echo '<script language="javascript">';
                        echo "$(document).ready(function() {
			          	$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
			          	$.toast({
			          		heading: 'خطا فایل آپلود نشد!!!',
			          		text: 'مشکلی در آپلود فایل بوجود آمده است.' ,
			          		position: 'top-center',
			          		loaderBg:'#ed3236',
			          		hideAfter: 6500,
			          		stack: 6
			          	});
			          	return false;});";
                        echo '</script>';
                    }
                }
            } else {
                $reg_Invoce->Register_Invoice($Inv);
                $url = "./V_AllInvoives.php";
                header("Location: $url");
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
                                        <h6 class="panel-title txt-dark"> تعریف صورت حساب</h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <form method="post" enctype="multipart/form-data">
                                        <div id="example-basic">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group col-sm-6">
                                                        <label class="control-label mb-10" for="Inv_Topic"> نسخه تعرفه :
                                                        </label>
                                                        <select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Inv_Topic" id="Inv_Topic">
                                                        <?php
                                                        if (!empty($get_TariffAllVer)) {

                                                            foreach ($get_TariffAllVer as $row) {
                                                                if ($row['GUID'] == $get_Tariff[0]['TariffVerGUID']) {
                                                                    echo '<option value="' . $row['GUID'] . '" selected >' . $row['NameFa'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $row['GUID'] . '">' . $row['NameFa'] . '</option>';
                                                                }
                                                            }
                                                        }

                                                        ?>

                                                        </select>

                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="Inv_Amount">
                                                            قیمت (به ریال) </label>
                                                        <div class="input-group col-sm-6">
                                                            <div class="input-group-addon"><i class="icon-info"></i>
                                                            </div>
                                                            <input type="text" class="form-control" name="Inv_Amount" id="Inv_Amount" onkeypress="return onlyNumberKey(event)" onkeyup="format(this)" placeholder=" قیمت (به ریال) ">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="input-group col-sm-6">
                                                        <label class="control-label mb-10" for="Inv_validationDate">
                                                            تاریخ
                                                            شروع </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i>
                                                            </div>
                                                            <input type="text" class="form-control" id="Inv_validationDate" name="Inv_validationDate" placeholder=" تاریخ اعتبار تعرفه ">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="control-label mb-12" for="Inv_Comment">
                                                            توضیحات </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i>
                                                            </div>
                                                            <input type="text" class="form-control" name="Inv_Comment" id="Inv_Comment" placeholder=" توضیحات ">
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <div class="form-group mb-30">
                                                        <label class="control-label mb-10 text-left"> تصاویر مستندات :
                                                        </label>
                                                        <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                                                            <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                                <span class="fileinput-filename"></span>
                                                            </div>
                                                            <span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text"> انتخاب فایل </span>
                                                                <span class="fileinput-exists btn-text"> تغییر فایل
                                                                </span>
                                                                <input type="file" name="fileToUpload" id="fileToUpload">
                                                            </span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف
                                                                </span></a>
                                                        </div>
                                                        <br />
                                                        <br />
                                                        <div class="input-group">

                                                            <button class="btn btn-info btn-anim" type="submit" name="Add_Btn" id="Add_Btn"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>
                                                        </div>
                                                    </div>
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
        <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>

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

        <script type="text/javascript">
            $(function() {
                $("#Inv_validationDate").persianDatepicker({
                    formatDate: "YYYY-0M-0D"
                });
            });
        </script>

</body>

</html>