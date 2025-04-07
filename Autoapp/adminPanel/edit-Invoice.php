<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
require('config/public_conf.php');
require('Model/InvoiceModel.php');
require_once('Model/TariffModel.php');

use fateh\Finance\Invoice as invoice;
use fateh\tariff\tariff as tariff;

    // Something posted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['action'] == "Edit_Inv") {
            $Inv_Update = new invoice($_SESSION["Admin_GUID"]);
            $Inv = array();
            $Inv[0] = $_POST['Inv_OldGUID'];
            $_SESSION["Inv_GUID"] = $_POST['Inv_OldGUID'];

            if (!isset($_POST['Inv_Title']) || trim($_POST['Inv_Title']) == '') {
                $Inv[1] = $_POST['Inv_OldTtile'];
            } else {
                $Inv[1] = $_POST['Inv_Title'];
            }
            if (!isset($_POST['Inv_validationDate']) || trim($_POST['Inv_validationDate']) == '') {
                $Inv[2] = $_POST['Inv_OldStartDate'];
            } else {
                $persiandate =  str_replace("/", "", $_POST['Inv_validationDate']);
                $day = substr($persiandate, 8, 2);
                $mon = substr($persiandate, 5, 2);
                $year = substr($persiandate, 0, 4);
                $Inv_Start_Date = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
                $Inv[2] = date('Y-m-d', $Inv_Start_Date);
            }

            if (!isset($_POST['Inv_Amount']) || trim($_POST['Inv_Amount']) == '') {
                $Inv[3] = $_POST['Inv_OldAmount'];
                $Inv[3] = str_replace(",", "", $Inv[3]);
            } else {
                $Inv[3] = $_POST['Inv_Amount'];
                $Inv[3] = str_replace(",", "", $Inv[3]);
            }

            if (!isset($_POST['Inv_Comment']) || trim($_POST['Inv_Comment']) == '') {
                $Inv[4] = $_POST['Inv_OldComment'];
            } else {
                $Inv[4] = $_POST['Inv_Comment'];
            }

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
                if ($_FILES["fileToUpload"]["size"] > 500000) {
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
                    //   echo "خطایی در سمت سرور هست";
                    $url = "./edit-Invoice.php";
                    header("Location: $url");
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $Inv_Update->Register_Invoice_File($Inv[0], $target_file);
                        $Inv_Update->Update_Invoice_ByGUID($Inv);
                        $url = "./edit-Invoice.php";
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
                $Inv_Update->Update_Invoice_ByGUID($Inv);
                $url = "./edit-Invoice.php";
                header("Location: $url");
            }
        } elseif ($_POST['action'] == "edit-Invoice") {
            $GUID = $_POST['Inv_GUID'];
            $_SESSION["Inv_GUID"] = $_POST['Inv_GUID'];
            $Invoice_obj = new invoice($_SESSION["Admin_GUID"]);
            $inv = $Invoice_obj->Get_Invoice_ByGUID($GUID);
            $inv_Doc = $Invoice_obj->V_Invoice_Doc($GUID);
            foreach ($inv as $v) {
                $Inv_Title = $v['Title'];
                $Inv_Amount = $v['Amount'];
                $Inv_StartDate = $v['Start_Date'];
                $GUID_Old = $v['GUID'];
                $miladidate =  str_replace("-", "", $Inv_StartDate);
                $day = substr($miladidate, 6, 2);
                $mon = substr($miladidate, 4, 2);
                $year = substr($miladidate, 0, 4);
                $Inv_StartDatePer = gregorian_to_jalali($year, $mon, $day, '-');
                $Inv_Comment = $v['Comment'];
            }
        } elseif ($_POST['remove_file'] == "remove_file") {
            $Inv_RemoveDoc = new invoice($_SESSION["Admin_GUID"]);
            $ID = $_POST['file_Id'];
            $Inv_RemoveDoc->Remove_Invoice_DocByID($ID);
            $url = "./edit-Invoice.php";
            header("Location: $url");
        }
    } else { //server request
        $url = "./V_AllInvoives.php";
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
                                        <h6 class="panel-title txt-dark"> ویرایش صورت حساب </h6>
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
                                                <input type="hidden" id="action" name="action" value="Edit_Inv">
                                                <input type="hidden" id="Inv_OldGUID" name="Inv_OldGUID" value="<?php echo $GUID_Old; ?>">
                                                <input type="hidden" id="Inv_OldTtile" name="Inv_OldTtile" value="<?php echo $Inv_Title; ?>">
                                                <input type="hidden" id="Inv_OldAmount" name="Inv_OldAmount" value="<?php echo $Inv_Amount; ?>">
                                                <input type="hidden" id="Inv_OldStartDate" name="Inv_OldStartDate" value="<?php echo $Inv_StartDate; ?>">
                                                <input type="hidden" id="Inv_OldComment" name="Inv_OldComment" value="<?php echo $Inv_Comment; ?>">
                                                <input type="hidden" id="Inv_Title" name="Inv_Title" value="<?php echo $Inv_Title; ?>">
                                                <?php
                                                foreach ($inv_Doc as $Doc) {
                                                    $Doc_ID = $Doc['ID'];
                                                    $Doc_GUID = $Doc['Filekey'];
                                                    $Doc_Address = $Doc['location'];

                                                    if (!empty($Doc['location'])) {
                                                    } else {
                                                    }
                                                }
                                                ?>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Inv_StartDate"> موضوع صورت حساب :
                                                        <?php
                                                        $tariffver = new tariff($_SESSION["Admin_GUID"]);
                                                        $TariffTittle = $tariffver->getversion($Inv_Title);
                                                        echo  $TariffTittle;
                                                        ?>
                                                    </label>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Inv_StartDate"> تاریخ شروع
                                                    </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" id="Inv_validationDate" name="Inv_validationDate" value="<?php echo $Inv_StartDatePer; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Inv_Amount"> قیمت (به ریال)
                                                    </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Inv_Amount" id="Inv_Amount" onkeypress="return onlyNumberKey(event)" onkeyup="format(this)" value="<?php echo $Inv_Amount; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label mb-10" for="Inv_Comment"> توضیحات
                                                    </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="Inv_Comment" id="Inv_Comment" value="<?php echo $Inv_Comment; ?>">
                                                    </div>
                                                </div>
                                                <br />
                                        </div>
                                        <div class="form-group mb-30">
                                            <label class="control-label mb-10 text-left"> تصاویر مستندات :
                                            </label>
                                            <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                                                <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                    <span class="fileinput-filename"></span>
                                                </div>
                                                <span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i> <span class="fileinput-new btn-text">
                                                        انتخاب فایل </span>
                                                    <span class="fileinput-exists btn-text"> تغییر فایل
                                                    </span>
                                                    <input type="file" name="fileToUpload" id="fileToUpload">
                                                </span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text"> حذف
                                                    </span></a>
                                            </div>
                                        </div>
                                        <br />
                                        <br />
                                        <div class="form-group">
                                            <button class="btn btn-info btn-anim" type="submit" name="Inv_Edite_Submit" id="Inv_Edite_Submit">
                                                <i class="icon-check"></i><span class="btn-text">ثبت</span>
                                            </button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row -->

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
                                                        <th> دانلود فایل </th>
                                                        <th> عملیات </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (!empty($inv_Doc)) {
                                                        foreach ($inv_Doc as $Doc) {
                                                            $Doc_ID = $Doc['ID'];
                                                            $Doc_Address = $Doc['location'];
                                                            $validation = "onclick='return validation()'";
                                                            echo ' <tr>';
                                                            echo ' <td>';
                                                            echo '<a href="' . $Doc_Address . '"><span class="btn-text btn-default btn-icon-anim"><i class="icon-cloud-download"></i> ذخیره فایل </span></a>';
                                                            echo ' </td>';
                                                            echo '<td style ="width: 5%;">
											                    <form method="post" enctype="multipart/form-data">
											            	     <input type="hidden" id="file_Id" name="file_Id" value="' . $Doc_ID . '">
											            	    	<input type="hidden" id="remove_file" name="remove_file" value="remove_file">
											            	    	<button class="btn btn-info btn-icon-anim" type="submit"' . $validation . '> <i class="icon-trash"></i> حذف </button>
											            	    </form></td></tr> ';
                                                        }
                                                    } else {
                                                        echo "<tr role='row' class='odd'>";
                                                        echo '<td colspan="2" style="text-align: center;">';
                                                        echo 'فایلی برای نمایش وجود ندارد';
                                                        echo "</td>";
                                                        echo "</tr>";
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
        <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#Inv_validationDate").persianDatepicker({
                    formatDate: "YYYY-0M-0D"
                });
            });
        </script>
        <?php


        if (!empty($Error_STR)) {
            if ($Error_STR = 1) {
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