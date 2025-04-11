<?php
session_start();
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/GarageModel.php");
require("$rootDir/Model/AutoModel.php");
require("$rootDir/Model/InvoiceModel.php");
require("$rootDir/Model/TariffModel.php");
require("$rootDir/Model/N_TariffModel.php");
require("$rootDir/Model/N_PhoneContactModel.php");

use fateh\Finance\Invoice as invoice;
use fateh\Automobile\Automobile as auto;
use fateh\AutoShop\AutoShop as garage;
use fateh\Member\Member as member;
use fateh\Phonebook\MechanicContact as Contact;
use fateh\tariff\tariff as tariff;
use fateh\tariff\NewTariff as NewTariff;

$Mechanic_obj = new member($_SESSION["Admin_GUID"]);
$Phone_Obj = new Contact($_SESSION["Admin_GUID"]);
$Garage_obj = new garage($_SESSION["Admin_GUID"]);
$Cars_obj = new auto($_SESSION["Admin_GUID"]);
$Invoice_obj = new invoice($_SESSION["Admin_GUID"]);
$tariffver = new tariff($_SESSION["Admin_GUID"]);
$Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mem-ID']) && !empty($_POST['mem-ID'])) {
        $Mechanic_GUID = $_POST['mem-ID'];
        $_SESSION["GUID"] = $_POST['mem-ID'];
    } elseif (isset($_SESSION["GUID"]) && $_SESSION["GUID"] != "") {
        $Mechanic_GUID = $_SESSION["GUID"];
    } else {
        $url = "./V_MemberAll.php";
        header("Location: $url");
    }



    if (isset($_POST['ActionID']) && !empty($_POST['ActionID'])) {
        $Error_STR = 0;
        //////////////////////////////////////////////////////////////////////////////////Invoice
        if ($_POST['ActionID'] == "multipayment") {
            $Inv_GUID = $_POST['Inv_GUID'];
            $Mechanic_GUID = $_POST['MechanicGUID'];
            $Payed_ID = $_POST['Payed-ID'];
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Invoice_obj->U_MultiPay($Payed_ID);
            }
        }

        //////////////////////////////////////////////////////////////////////////////////change Mobile phone

        if ($_POST['ActionID'] == "ChangePhone") {
            $MemberID = $_POST['MemberID'];

            if (isset($_POST['N_ContactNum']) && !empty($_POST['N_ContactNum'])) {
                $N_Contact = $_POST['N_ContactNum'];
            } else {
                $Error_STR = 2;
            }

            if (isset($_POST['OLDContactID']) && !empty($_POST['OLDContactID'])) {
                $OLDContactID = $_POST['OLDContactID'];
            } else {
                $Error_STR = 3;    /// Mechanic does not have any mobile number!!!!
            }

            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Phone_Obj->U_MechanicPhone_ByID($MemberID, $OLDContactID, $N_Contact);
            }
        }

        if ($_POST['ActionID'] == "RemoveFiles") {
            $Member_ID = $_POST['MemberID'];
            $file_ID = $_POST['FileID'];
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Mechanic_obj->D_MechanicFiles_ByID($Member_ID, $file_ID);
            }
        }

        if ($_POST['ActionID'] == "ChangePass") {

            $Member_ID = $_POST['MemberID'];

            if (isset($_POST['NewPass']) && !empty($_POST['NewPass'])) {
                $New_pass = $_POST['NewPass'];
            } else {
                $Error_STR = 2;
            }
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Mechanic_obj->U_MechanicPassword_ByID($Member_ID, $New_pass);
            }
        }

        if ($_POST['ActionID'] == "Active-License") {
            $Member_ID = $_POST['MemberID'];
            $License_status = $_POST['Licensestatus'];
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Mechanic_obj->U_MechanicLicenseStatus_ByID($Member_ID, $License_status);
            }
        }

        if ($_POST['ActionID'] == "Update-License") {
            $Member_ID = $_POST['MemberID'];
            $Up_License = array();

            if (isset($_POST['Category']) && !empty($_POST['Category'])) {
                $Up_License[0] = $_POST['Category'];
            } else {
                $Up_License[0] = $_POST['Old-Category'];
            }

            if (isset($_POST['LicenseNUM']) && !empty($_POST['LicenseNUM'])) {
                $Up_License[1] = $_POST['LicenseNUM'];
            } else {
                $Up_License[1] = $_POST['Old-LicenseNUM'];
            }

            if (isset($_POST['LicenseDate']) && !empty($_POST['LicenseDate'])) {

                //#######Convert to miladi 
                $persiandate =  str_replace("/", "", $_POST['LicenseDate']);
                $day = substr($persiandate, 8, 2);
                $mon = substr($persiandate, 5, 2);
                $year = substr($persiandate, 0, 4);
                $ENvalidate = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
                $ENvalidate = date('Y-m-d', $ENvalidate);
                $Up_License[2] = $ENvalidate;
            } else {
                $Up_License[2] = $_POST['Old-LicenseDate'];
            }
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Mechanic_obj->U_MechanicLicense_ByID($Member_ID, $Up_License);
            }
        }

        if ($_POST['ActionID'] == "EditMemInfo") {

            $Member_ID = $_POST['MemberID'];
            $mem_info = array();

            if (isset($_POST['fname']) && !empty($_POST['fname'])) {
                $mem_info[0] = $_POST['fname'];
            } else {
                $mem_info[0] = $_POST['Old-fname'];
            }

            if (isset($_POST['Lname']) && !empty($_POST['Lname'])) {
                $mem_info[1] = $_POST['Lname'];
            } else {
                $mem_info[1] = $_POST['Old-Lname'];
            }

            if (isset($_POST['gender']) && !empty($_POST['gender'])) {
                $mem_info[2] = $_POST['gender'];
            } else {
                $mem_info[2] = 0;
            }

            if (isset($_POST['National-Code']) && !empty($_POST['National-Code'])) {
                $mem_info[3] = $_POST['National-Code'];
            } else {
                $mem_info[3] = $_POST['Old-National-Code'];
            }

            if (isset($_POST['Ncode-Serial']) && !empty($_POST['Ncode-Serial'])) {
                $mem_info[4] = $_POST['Ncode-Serial'];
            } else {
                $mem_info[4] = $_POST['Old-Ncode-Serial'];
            }

            if (isset($_POST['Birth-Loc']) && !empty($_POST['Birth-Loc'])) {
                $mem_info[5] = $_POST['Birth-Loc'];
            } else {
                $mem_info[5] = $_POST['Old-Birth-Loc'];
            }

            if (isset($_POST['Religion']) && !empty($_POST['Religion'])) {
                $mem_info[6] = $_POST['Religion'];
            } else {
                $mem_info[6] = $_POST['Old-Religion'];
            }

            if (isset($_POST['Edu-Level']) && !empty($_POST['Edu-Level'])) {
                $mem_info[7] = $_POST['Edu-Level'];
            } else {
                $mem_info[7] = $_POST['Old-Edu-Level'];
            }

            if (isset($_POST['Duty']) && !empty($_POST['Duty'])) {
                $mem_info[8] = $_POST['Duty'];
            } else {
                $mem_info[8] = $_POST['Old-Duty'];
            }

            if (isset($_POST['Fam-Num']) && !empty($_POST['Fam-Num'])) {
                $mem_info[9] = $_POST['Fam-Num'];
            } else {
                $mem_info[9] = $_POST['Old-Fam-Num'];
            }

            if (isset($_POST['address']) && !empty($_POST['address'])) {
                $mem_info[10] = $_POST['address'];
            } else {
                $mem_info[10] = $_POST['Old-address'];
            }

            if (isset($_POST['Email']) && !empty($_POST['Email'])) {
                $mem_info[11] = $_POST['Email'];
            } else {
                $mem_info[11] = $_POST['Old-Email'];
            }

            if (isset($_POST['tags']) && !empty($_POST['tags'])) {
                $mem_info[12] = $_POST['tags'];
            } else {
                $mem_info[12] = $_POST['Old-tags'];
            }

            if (isset($_POST['Birthday']) && !empty($_POST['Birthday'])) {
                $persiandate =  str_replace("/", "", $_POST['Birthday']);
                $day = substr($persiandate, 8, 2);
                $mon = substr($persiandate, 5, 2);
                $year = substr($persiandate, 0, 4);
                $Birthday = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
                $mem_info[13] = date('Y-m-d', $Birthday);
            } else {
                $mem_info[13] = $_POST['Old-Birthday'];
            }
            if (isset($Error_STR) && $Error_STR == 0) {
                $route = $Mechanic_obj->U_MechanicInfo_ByID($Member_ID, $mem_info);
            }
        }

        //////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////Route
        //////////////////////////////////////////////////////////////////////////////////
        if (isset($route) && !empty($route)) {
            if ($route ==  true) {
                $url = "V_MechanicProfile.php";
                header("Location: $url");
            } else {
                $Error_STR = 1; //Invoice Not Created
            }
        }
    }

    if (isset($_POST['UpdateProfile'])) {
        $guid = $_POST['GUID'];
        $Mechanic_obj = new member($_SESSION["Admin_GUID"]);

        if (file_exists($_FILES['Newphoto']['tmp_name']) || is_uploaded_file($_FILES['Newphoto']['tmp_name'])) {

            $target_dir = "images/Profile_Photo/";
            $temp = explode(".", $_FILES["Newphoto"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $target_file = $target_dir . $newfilename;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image

            $check = filesize($_FILES["Newphoto"]["tmp_name"]);
            if ($check !== false) {
                //echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                // echo "File is not an image.";
                $uploadOk = 0;
            }


            // Check if file already exists
            if (file_exists($target_file)) {
                $Error_STR = 7;
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["Newphoto"]["size"] > 5 * MB) {
                $Error_STR = 5;
                $uploadOk = 0;
            }

            // Allow certain file formats

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $Error_STR = 6;
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $route = 0;
            } else {
                if (move_uploaded_file($_FILES["Newphoto"]["tmp_name"], $target_file)) {
                    //add with file

                    $Mechanic_obj->U_MechanicPhoto_ByID($guid, $target_file);
                    echo "<meta http-equiv='refresh' content='0'>";
                } else {
                    $Error_STR = 1;
                }
            }
        } else {
            $Mechanic_obj->U_MechanicPhoto_ByID($guid, "images/Profile_Photo/vector.png");
            echo "<meta http-equiv='refresh' content='0'>";
        }
    }

    if (isset($_POST['insertdoc'])) {
        $guid = $_POST['GUID'];
        $Mechanic_obj = new member($_SESSION["Admin_GUID"]);

        // Count # of uploaded files in array
        $total = count($_FILES['upload']['name']);

        // Loop through each file
        for ($i = 0; $i < $total; $i++) {

            //Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

            //Make sure we have a file path
            if ($tmpFilePath != "") {
                //Setup our new file path
                $temp = explode(".", $_FILES["upload"]["name"][$i]);
                $newfilename = round(microtime(true)) . '.' . end($temp);
                $newFilePath = "images/Doc/" . $newfilename;
                $imageFileType = strtolower(pathinfo($newFilePath, PATHINFO_EXTENSION));

                if ($_FILES["upload"]["size"][$i] < 5 * MB) {
                    if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {

                        //Upload the file into the temp dir
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                            //Handle other code here
                            $Mechanic_obj->C_MechanicFiles_ByID($guid, $newFilePath);
                            echo "<meta http-equiv='refresh' content='0'>";
                        }
                    } else {
                        $Error_STR = 6;
                    }
                } else {
                    $Error_STR = 5;
                }
            }
        }
    }
} else {
    if (isset($_SESSION["GUID"]) && $_SESSION["GUID"] != "") {
        $Mechanic_GUID = $_SESSION["GUID"];
    } else {
        $url = "./V_MemberAll.php";
        header("Location: $url");
    }
}


if (!empty($Mechanic_GUID)) {

    //#############################################################	  
    $Mechanic_TTariff = $Tariff_Obj->Get_TariffType_ByMemberID($Mechanic_GUID);
    //#############################################################	  

    $info = $Mechanic_obj->Get_MechanicInfo_ByID($Mechanic_GUID);
    $Detials = $Mechanic_obj->Get_MechanicDetails_ByID($Mechanic_GUID);
    $MechanicCategory = $Mechanic_obj->Get_MechanicCategory($Mechanic_GUID);
    $MechanicCity = $Mechanic_obj->Get_MechanicCity();
    $MechanicReligion = $Mechanic_obj->Get_MechanicReligion();
    $MechanicEducationlevels = $Mechanic_obj->Get_MechanicEducationlevels();
    $MechanicMilitary = $Mechanic_obj->Get_MechanicMilitary();

    $Birthday_City = $Mechanic_obj->Get_MechanicCity_ByID($Detials[0]['BirthdayLocation']);
    $Religion = $Mechanic_obj->Get_MechanicReligion_ByID($Detials[0]['Religion']);
    $Military = $Mechanic_obj->Get_MechanicMilitary_ByID($Detials[0]['DutySystem']);
    $Degree = $Mechanic_obj->Get_MechanicDegree_ByID($Detials[0]['Educationlevel']);
    $Birthday = $Mechanic_obj->Get_DateHejri($Detials[0]['Birthday']);
    $Per_Birthday = gregorian_to_jalali($Birthday[0], $Birthday[1], $Birthday[2]);
    //#############################################################	

    $Profile_photo = $Mechanic_obj->Get_MechanicPhoto_ByID($Mechanic_GUID);
    $files = $Mechanic_obj->Get_MechanicFiles_ByID($Mechanic_GUID);
    //#############################################################	


    $address = $Mechanic_obj->GetMemAddress($Mechanic_GUID);
    $phone = $Phone_Obj->Get_MechanicContact_ByID($Mechanic_GUID);
    $License = $Mechanic_obj->GetMemLicense($Mechanic_GUID);
    if ($License[0] !== 0) {
        $Category = $Mechanic_obj->GetNameCategory($License[0]);
        $ExLicense = $Mechanic_obj->Get_DateHejri($License[2]);
        $Per_ExLicense = gregorian_to_jalali($ExLicense[0], $ExLicense[1], $ExLicense[2]);
    } else {
        $Category = $Mechanic_obj->GetNameCategory($License[0]);
        $Per_ExLicense = array(0000, 00, 00);
    }
    //#############################################################	

    $auto = $Cars_obj->Get_MechanicAuto_ByID($Mechanic_GUID);
    $auto_count = $Cars_obj->Get_AutoCount_ByUserID($Mechanic_GUID);

    //#############################################################	
    $inv = $Invoice_obj->V_MechanicInvoice($Mechanic_GUID);
    $payed = $Invoice_obj->View_PayedBill_ByGUID($Mechanic_GUID);
    //$Invoice = $Mem_invoice -> GetMemInvoice($Mechanic_GUID);

    //#############################################################		

    $Service = $Mechanic_obj->GetMemService($Mechanic_GUID);
    $Devices = $Mechanic_obj->GetMemSpecialDevice($Mechanic_GUID);

    $total_skills1 = $Mechanic_obj->GetMemCountSDevice($Mechanic_GUID);
    $total_skills2 = $Mechanic_obj->GetMemCountService($Mechanic_GUID);
    $total_skills = $total_skills1 + $total_skills2;

    //###############################################################
    $garage = $Garage_obj->GetGaragebyMemID($Mechanic_GUID);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>

    <!-- Jasny-bootstrap CSS -->
    <link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Data table CSS -->
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>

    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />

    <script type="text/javascript">
        $(function() {
            $("#Birthday").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
            $("#LicenseDate").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
        });

        function onlyNumberKey(evt) {

            // Only ASCII charactar in that range allowed 
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }

        function validatePass() {
            var x = document.forms["ChangPassForm"]["NewPass"].value;
            if (x == "") {
                alert("[ رمز عبور جدید ] مشخص نشده است");
                return false;
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
                    <div class="col-lg-3 col-xs-12">
                        <div class="panel panel-default card-view  pa-0">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body  pa-0">
                                    <div class="profile-box">
                                        <div class="profile-cover-pic">
                                            <div class="profile-image-overlay"></div>
                                        </div>
                                        <div class="profile-info text-center">
                                            <div class="profile-img-wrap">
                                                <img class="inline-block mb-10" src="<?php echo $Profile_photo[0]['Path']; ?>" alt="user">
                                            </div>
                                            <br />
                                            <h5 class="block capitalize-font pb-20">
                                                <?php
                                                if (!empty($info)) {
                                                    echo $info[0]['FName'] . " " . $info[0]['LName'];
                                                } else {
                                                    echo 'خطا در برنامه';
                                                }
                                                ?></h5>

                                        </div>
                                        <br />
                                        <div class="social-info">
                                            <div class="row">
                                                <div class="col-xs-4 text-center">
                                                    <span class="counts block head-font"><span class="counter-anim"><?php echo $auto_count; ?></span></span>
                                                    <span class="counts-text block">تعداد خودرو</span>
                                                </div>
                                                <div class="col-xs-4 text-center">
                                                    <span class="counts block head-font"><span class="counter-anim"><?php echo $total_skills; ?></span></span>
                                                    <span class="counts-text block">تخصص ها</span>
                                                </div>
                                                <div class="col-xs-4 text-center">
                                                    <span class="counts block head-font"><span class="counter-anim"><?php echo $total_skills; ?></span></span>
                                                    <span class="counts-text block">امتیاز</span>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" type="button"> ویرایش <span class="caret"></span></button>
                                                <ul role="menu" data-dropdown-in="flipInY" data-dropdown-out="flipOutY" class="dropdown-menu">
                                                    <li>
                                                        <?php
                                                        if (!empty($Mechanic_TTariff)) {
                                                            echo '<a href="U_MechanicTTariff.php" ><span> نوع تعرفه ها </span></a>';
                                                        } else {
                                                            echo '<a href="U_MechanicTTariff.php" ><span> اضافه کردن نوع تعرفه </span></a>';
                                                        }
                                                        ?>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-toggle="modal" data-target="#profilepic"><span>تصویر پروفایل</span></a>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-toggle="modal" data-target="#profile"><span>اطلاعات عمومی</span></a>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-toggle="modal" data-target="#certificate"><span>اطلاعات جواز</span></a>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-toggle="modal" data-target="#document"><span>
                                                                حذف مدارک </span></a>
                                                    </li>
                                                    <li>
                                                        <?php
                                                        if (!empty($Service)) {
                                                            echo '<a href="edit-member-skill.php" ><span> تخصص ها </span></a>';
                                                        } else {
                                                            echo '<a href="edit-member-skill.php" ><span> اضافه کردن تخصص ها </span></a>';
                                                        }
                                                        ?>
                                                    </li>
                                                    <li>
                                                        <?php
                                                        if (!empty($auto)) {
                                                            echo '<a href="edit-member-auto.php" ><span>  خودروهای تخصصی </span></a>';
                                                        } else {
                                                            echo '<a href="edit-member-auto.php" ><span> اضافه کردن خودروهای تخصصی </span></a>';
                                                        }
                                                        ?>

                                                    </li>
                                                    <li>
                                                        <?php
                                                        if (!empty($Devices)) {
                                                            echo '<a href="edit-member-devices.php" ><span> دستگاه های تخصصی </span></a>';
                                                        } else {
                                                            echo '<a href="edit-member-devices.php" ><span> اضافه کردن دستگاه های تخصصی </span></a>';
                                                        }
                                                        ?>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div id="profilepic" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">آپلود تصویر</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form method="post" enctype="multipart/form-data">
                                                                                            <input type="hidden" id="GUID" name="GUID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group mb-30">
                                                                                                    <label class="control-label mb-10 text-left">
                                                                                                        آپلود تصویر
                                                                                                        پروفایل :
                                                                                                    </label>
                                                                                                    <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                                                                                                        <div class="form-control" data-trigger="fileinput">
                                                                                                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                                                                            <span class="fileinput-filename"></span>
                                                                                                        </div>
                                                                                                        <span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i>
                                                                                                            <span class="fileinput-new btn-text">
                                                                                                                انتخاب
                                                                                                                فایل
                                                                                                            </span>
                                                                                                            <span class="fileinput-exists btn-text">
                                                                                                                تغییر
                                                                                                                فایل
                                                                                                            </span>
                                                                                                            <input type="file" name="Newphoto" id="Newphoto">
                                                                                                        </span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text">
                                                                                                                حذف
                                                                                                            </span></a>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-actions mt-10">
                                                                                                <button type="submit" class="btn btn-success mr-10 mb-30" name="UpdateProfile" id="UpdateProfile">
                                                                                                    ثبت تصویر </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <button class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" data-toggle="modal" data-target="#changeMemPass"><span class="btn-text">تغییر رمز عبور</span></button>
                                            <div id="changeMemPass" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">تغییر رمز عبور
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form name="ChangPassForm" method="post" enctype="multipart/form-data" onsubmit="return validatePass()">
                                                                                            <input type="hidden" id="MemberID" name="MemberID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <input type="hidden" id="Old-Pass" name="Old-Pass" value="<?php echo $info[0]['Pass']; ?>">
                                                                                            <input type="hidden" id="ActionID" name="ActionID" value="ChangePass">
                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group mb-30">
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="National-Code">
                                                                                                            رمزعبور جدید
                                                                                                            : </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" Name="NewPass" id="NewPass" placeholder="رمز عبور جدید" minlength="4" maxlength="10">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-actions mt-10">
                                                                                                <button type="submit" class="btn btn-success mr-10 mb-30" name="ChangePass" id="ChangePass"> ثبت
                                                                                                    رمز عبور </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <button class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" data-toggle="modal" data-target="#Cellphone"><span class="btn-text">تغییر شماره همراه</span></button>
                                            <div id="Cellphone" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">تغییر شماره همراه
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form name="form-ChangeContact" method="post" enctype="multipart/form-data">
                                                                                            <input type="hidden" id="MemberID" name="MemberID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <input type="hidden" id="ActionID" name="ActionID" value="ChangePhone">
                                                                                            <?php
                                                                                            foreach ($phone as $row) {
                                                                                                if ($row['Mobile'] == 1) {
                                                                                                    echo "<input type='hidden' id='OLDContactID' name='OLDContactID' value='" . $row['GUID'] . "'>";
                                                                                                }
                                                                                            }

                                                                                            ?>

                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group mb-30">
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="N_ContactNum">شماره
                                                                                                            همراه جدید :
                                                                                                        </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="tel" class="form-control" name="N_ContactNum" id="N_ContactNum" placeholder="...912" maxlength="10" value="<?php echo $_POST['N_ContactNum']; ?>">
                                                                                                        </div>
                                                                                                        <br>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-actions mt-10">
                                                                                                <button type="submit" class="btn btn-success mr-10 mb-30" name="ChangeCell" id="ChangeCell"> ثبت
                                                                                                    شماره جدید </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <button class="btn btn-default btn-block btn-outline fancy-button btn-0 mt-30" data-toggle="modal" data-target="#Documents"><span class="btn-text">
                                                    آپلود مدارک </span></button>
                                            <div id="Documents" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel"> انتخاب مدارک
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form method="post" enctype="multipart/form-data">
                                                                                            <input type="hidden" id="GUID" name="GUID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group mb-30">
                                                                                                    <label class="control-label mb-10 text-left">
                                                                                                        تصاویر مدارک :
                                                                                                    </label>
                                                                                                    <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                                                                                                        <div class="form-control" data-trigger="fileinput">
                                                                                                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                                                                            <span class="fileinput-filename"></span>
                                                                                                        </div>
                                                                                                        <span class="input-group-addon fileupload btn btn-default btn-icon-anim btn-file"><i class="fa fa-upload"></i>
                                                                                                            <span class="fileinput-new btn-text">
                                                                                                                انتخاب
                                                                                                                فایل
                                                                                                            </span>
                                                                                                            <span class="fileinput-exists btn-text">
                                                                                                                تغییر
                                                                                                                فایل
                                                                                                            </span>
                                                                                                            <input name="upload[]" type="file" multiple="multiple" id="upload[]">
                                                                                                        </span> <a href="#" class="input-group-addon btn btn-default btn-icon-anim fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash"></i><span class="btn-text">
                                                                                                                حذف
                                                                                                            </span></a>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-actions mt-10">
                                                                                                <button type="submit" class="btn btn-success mr-10 mb-30" name="insertdoc" id="insertdoc"> ثبت
                                                                                                    مدارک </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>

                                            <div id="profile" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">ویرایش پروفایل
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form method="post" enctype="multipart/form-data" name="MemberRegForm">
                                                                                            <!-- OLD Value -->
                                                                                            <input type="hidden" id="MemberID" name="MemberID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <input type="hidden" id="Old-fname" name="Old-fname" value="<?php echo $info[0]['FName']; ?>">
                                                                                            <input type="hidden" id="Old-Lname" name="Old-Lname" value="<?php echo $info[0]['LName']; ?>">
                                                                                            <input type="hidden" id="Old-gender" name="Old-gender" value="<?php echo $info[0]['Gender']; ?>">
                                                                                            <input type="hidden" id="Old-National-Code" name="Old-National-Code" value="<?php echo $Detials[0]['NationalID']; ?>">
                                                                                            <input type="hidden" id="Old-Ncode-Serial" name="Old-Ncode-Serial" value="<?php echo $Detials[0]['NationalSerial']; ?>">
                                                                                            <input type="hidden" id="Old-Birth-Loc" name="Old-Birth-Loc" value="<?php echo $Detials[0]['BirthdayLocation']; ?>">
                                                                                            <input type="hidden" id="Old-Religion" name="Old-Religion" value="<?php echo $Detials[0]['Religion']; ?>">
                                                                                            <input type="hidden" id="Old-Edu-Level" name="Old-Edu-Level" value="<?php echo $Detials[0]['Educationlevel']; ?>">
                                                                                            <input type="hidden" id="Old-Duty" name="Old-Duty" value="<?php echo $Detials[0]['DutySystem']; ?>">
                                                                                            <input type="hidden" id="Old-Fam-Num" name="Old-Fam-Num" value="<?php echo $Detials[0]['FamilyNum']; ?>">
                                                                                            <input type="hidden" id="Old-address" name="Old-address" value="<?php echo $address[0]; ?>">
                                                                                            <input type="hidden" id="Old-Email" name="Old-Email" value="<?php echo $address[1]; ?>">
                                                                                            <input type="hidden" id="Old-tags" name="Old-tags" value="<?php echo $address[2]; ?>">
                                                                                            <input type="hidden" id="Old-Birthday" name="Old-Birthday" value="<?php echo $Detials[0]['Birthday']; ?>">
                                                                                            <input type="hidden" id="Old-HomePhone" name="Old-HomePhone" value="<?php echo $Detials[0]; ?>">
                                                                                            <input type="hidden" id="ActionID" name="ActionID" value="EditMemInfo">

                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label mb-10" for="fname"><i class="text-info mb-10">*</i>
                                                                                                        نام : </label>
                                                                                                    <div class="input-group">
                                                                                                        <div class="input-group-addon">
                                                                                                            <i class="icon-info"></i>
                                                                                                        </div>
                                                                                                        <input type="text" class="form-control" name="fname" id="fname" placeholder="<?php echo $info[0]['FName']; ?>" value="<?php echo $info[0]['FName']; ?>">
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="Lname"><i class="text-info mb-10">*</i>
                                                                                                            نام خانوادگی
                                                                                                            : </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="text" class="form-control" name="Lname" id="Lname" placeholder="<?php echo $info[0]['LName']; ?>" value="<?php echo $info[0]['LName']; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10 text-left">
                                                                                                            جنسیت :
                                                                                                        </label>
                                                                                                        <div class="radio radio-info">
                                                                                                            <?php
                                                                                                            if ($info[0]['Gender'] == 1) {
                                                                                                                echo '<input type="radio" id="male" name="gender" value="1" checked>';
                                                                                                            } else {
                                                                                                                echo '<input type="radio" id="male" name="gender" value="1">';
                                                                                                            }
                                                                                                            ?>

                                                                                                            <label for="male"><i class="icon-user"></i>
                                                                                                                مرد
                                                                                                            </label>
                                                                                                        </div>
                                                                                                        <div class="radio radio-info">
                                                                                                            <?php
                                                                                                            if ($info[0]['Gender'] == 1) {
                                                                                                                echo '<input type="radio" id="female" name="gender" value="0">';
                                                                                                            } else {
                                                                                                                echo '<input type="radio" id="female" name="gender" value="0" checked>';
                                                                                                            }
                                                                                                            ?>

                                                                                                            <label for="female"><i class="icon-user-female"></i>
                                                                                                                زن
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="National-Code"><i class="text-info mb-10">*</i>
                                                                                                            کد ملی :
                                                                                                        </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" Name="National-Code" id="National-Code" placeholder="<?php echo $Detials[0]['NationalID']; ?>" value="<?php echo $Detials[0]['NationalID']; ?>" minlength="10" maxlength="10">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="Ncode-Serial"><i class="text-info mb-10">*</i>
                                                                                                            شماره سریال
                                                                                                            کد ملی :
                                                                                                        </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="text" class="form-control" Name="Ncode-Serial" id="Ncode-Serial" placeholder="<?php echo $Detials[0]['NationalSerial']; ?>" value="<?php echo $Detials[0]['NationalSerial']; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="Birth-day"><i class="text-info mb-10">*</i>
                                                                                                            تاریخ تولد :
                                                                                                        </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="icon-info"></i>
                                                                                                            </div>
                                                                                                            <input type="text" class="form-control" id="Birthday" name="Birthday" placeholder="<?php echo $Per_Birthday[0] . "-" . $Per_Birthday[1] . "-" . $Per_Birthday[2]; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10"><i class="text-info mb-10">*</i>
                                                                                                            محل تولد :
                                                                                                        </label>
                                                                                                        <select class="form-control" dir="rtl" Name="Birth-Loc" id="Birth-Loc">
                                                                                                            <?php
                                                                                                            if (!empty($MechanicCity)) {
                                                                                                                foreach ($MechanicCity as $row) {
                                                                                                                    if ($row['ID'] == $Detials[0]['BirthdayLocation']) {
                                                                                                                        echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
                                                                                                                    } else {
                                                                                                                        echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                                                    }
                                                                                                                }
                                                                                                            }


                                                                                                            ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10">
                                                                                                            دین و مذهب
                                                                                                        </label>
                                                                                                        <select class="form-control " dir="rtl" Name="Religion" id="Religion">
                                                                                                            <?php
                                                                                                            if (!empty($MechanicReligion)) {
                                                                                                                foreach ($MechanicReligion as $row) {
                                                                                                                    if ($row['ID'] == $Detials[0]['Religion']) {
                                                                                                                        echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
                                                                                                                    } else {
                                                                                                                        echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                            ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10">
                                                                                                            میزان
                                                                                                            تحصیلات :
                                                                                                        </label>
                                                                                                        <select class="form-control " dir="rtl" Name="Edu-Level" id="Edu-Level">
                                                                                                            <?php
                                                                                                            if (!empty($MechanicEducationlevels)) {
                                                                                                                foreach ($MechanicEducationlevels as $row) {
                                                                                                                    if ($row['ID'] == $Detials[0]['Educationlevel']) {
                                                                                                                        echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
                                                                                                                    } else {
                                                                                                                        echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                                                    }
                                                                                                                }
                                                                                                            }

                                                                                                            ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10">
                                                                                                            وضعیت نظام
                                                                                                            وظیفه :
                                                                                                        </label>
                                                                                                        <select class="form-control " dir="rtl" Name="Duty" id="Duty">
                                                                                                            <?php
                                                                                                            if (!empty($MechanicMilitary)) {
                                                                                                                foreach ($MechanicMilitary as $row) {
                                                                                                                    if ($row['ID'] == $Detials[0]['DutySystem']) {
                                                                                                                        echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
                                                                                                                    } else {
                                                                                                                        echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                                                    }
                                                                                                                }
                                                                                                            }

                                                                                                            ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10"><i class="text-info mb-10">*</i>
                                                                                                            تعداد افراد
                                                                                                            تحت تکلف :
                                                                                                        </label>
                                                                                                        <input class="vertical-spin" type="text" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default" value="<?php echo $Detials[0]['FamilyNum']; ?>" id="Fam-Num" name="Fam-Num">
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10"><i class="text-info mb-10">*</i>
                                                                                                            آدرس محل
                                                                                                            سکونت :
                                                                                                        </label>
                                                                                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $address[0]; ?></textarea>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10" for="Email">
                                                                                                            آدرس ایمیل
                                                                                                        </label>
                                                                                                        <div class="input-group">
                                                                                                            <div class="input-group-addon">
                                                                                                                <i class="fa fa-dot-circle-o"></i>
                                                                                                            </div>
                                                                                                            <input type="text" class="form-control" id="Email" name="Email" placeholder="<?php echo $address[1]; ?>" value="<?php echo $address[1]; ?>">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label mb-10 text-left" for="tags">
                                                                                                            کلمات کلیدی
                                                                                                            : </label>
                                                                                                        <input type="text" value="<?php echo $address[2]; ?>" data-role="tagsinput" id="tags" name="tags" placeholder="">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-actions mt-10">
                                                                                                    <button type="submit" class="btn btn-success mr-10 mb-30" id="EditMemInfo" name="EditMemInfo">
                                                                                                        بروز رسانی
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <div id="certificate" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">ویرایش اطلاعات
                                                                مجوز</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="form-wrap">
                                                                                        <form method="post" enctype="multipart/form-data">
                                                                                            <input type="hidden" id="ActionID" name="ActionID" value="Update-License">
                                                                                            <input type="hidden" id="MemberID" name="MemberID" value="<?php echo $Mechanic_GUID; ?>">
                                                                                            <input type="hidden" id="Old-LicenseNUM" name="Old-LicenseNUM" value="<?php echo $License[1]; ?>">
                                                                                            <input type="hidden" id="Old-Category" name="Old-Category" value="<?php echo $License[0]; ?>">
                                                                                            <input type="hidden" id="Old-LicenseDate" name="Old-LicenseDate" value="<?php echo $License[2]; ?>">
                                                                                            <input type="hidden" id="Old-LicenseValid" name="Old-LicenseValid" value="<?php echo $License[3]; ?>">
                                                                                            <div class="form-body overflow-hide">
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label mb-10"><i class="text-info mb-10">*</i>
                                                                                                        رسته های شغلی :
                                                                                                    </label>
                                                                                                    <select class="form-control" aria-hidden="true" dir="rtl" id="Category" name="Category">
                                                                                                        <?php
                                                                                                        if (!empty($MechanicCategory)) {
                                                                                                            foreach ($MechanicCategory as $row) {
                                                                                                                if ($row['ID'] == $License[0]) {
                                                                                                                    echo '<option value="' . $row['ID'] . '" selected >' . $row['Name'] . '</option>';
                                                                                                                } else {
                                                                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                                                }
                                                                                                            }
                                                                                                        }


                                                                                                        ?>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label mb-10" for="LicenseNUM"><i class="text-info mb-10">*</i>
                                                                                                        شماره مجوز :
                                                                                                    </label>
                                                                                                    <div class="input-group">
                                                                                                        <div class="input-group-addon">
                                                                                                            <i class="icon-info"></i>
                                                                                                        </div>
                                                                                                        <input type="text" class="form-control" id="LicenseNUM" name="LicenseNUM" placeholder="<?php echo $License[1]; ?>" value="<?php echo $License[1]; ?>">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label class="control-label mb-10" for="exampleInputuname_1"><i class="text-info mb-10">*</i>
                                                                                                        تاریخ اعتبار
                                                                                                        جواز : </label>
                                                                                                    <div class="input-group">
                                                                                                        <div class="input-group-addon">
                                                                                                            <i class="icon-info"></i>
                                                                                                        </div>
                                                                                                        <input type="text" class="form-control" id="LicenseDate" name="LicenseDate" placeholder=" <?php echo $Per_ExLicense[0] . "-" . $Per_ExLicense[1] . "-" . $Per_ExLicense[2] ?> ">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-actions mt-10">
                                                                                                <button type="submit" class="btn btn-success mr-10 mb-30" id="Edit-Cert" name="Edit-Cert">
                                                                                                    بروز رسانی </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>

                                            <div id="document" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h5 class="modal-title" id="myModalLabel">حذف مدارک</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Row -->
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="">
                                                                        <div class="panel-wrapper collapse in">
                                                                            <div class="panel-body pa-0">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <div class="table-wrap">
                                                                                        <div class="table-responsive">
                                                                                            <table id="datable_1" class="table table-hover display  pb-30">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th> مدرک </th>
                                                                                                        <th> عملیات
                                                                                                        </th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tfoot>
                                                                                                    <tr>
                                                                                                        <th> مدرک </th>
                                                                                                        <th> عملیات
                                                                                                        </th>
                                                                                                    </tr>
                                                                                                </tfoot>
                                                                                                <tbody>
                                                                                                    <?php

                                                                                                    if (!empty($files)) {
                                                                                                        foreach ($files as $row) {

                                                                                                            echo "<tr>";
                                                                                                            echo '<td><img class="img-responsive" src="' . $row['Path'] . '" alt="Doc" width="150" height="auto"></td>';
                                                                                                            $validation = "onclick='return validation()'";
                                                                                                            echo '<td><form method="post" enctype="multipart/form-data">
                                                                                                        <input type="hidden" id="MemberID" name="MemberID" value="' . $id . '">
                                                                                                        <input type="hidden" id="ActionID" name="ActionID" value="RemoveFiles">
                                                                                                        <input type="hidden" id="FileID" name="FileID" value="' . $row['ID'] . '">
                                                                                                        <button class="btn btn-default btn-icon-anim" ' . $validation . ' name="RemoveFile" id="RemoveFile"><i class="icon-trash"></i> حذف </button>
                                                                                                        </td>';
                                                                                                            echo "</tr>";
                                                                                                        }
                                                                                                    } else {
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
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-xs-12">
                        <div class="panel panel-default card-view pa-0">
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body pb-0">
                                    <div class="tab-struct custom-tab-1">
                                        <ul role="tablist" class="nav nav-tabs nav-tabs-responsive" id="myTabs_8">
                                            <li class="active" role="presentation"><a data-toggle="tab" id="profile_tab_8" role="tab" href="#profile_8" aria-expanded="true"><span>عمومی</span></a></li>
                                            <li role="presentation" class="next"><a data-toggle="tab" id="Tariff_tab_8" role="tab" href="#Tariff_8" aria-expanded="false"><span>نوع تعرفه</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="Cert_tab_8" role="tab" href="#Cert_8" aria-expanded="false"><span>مجوز</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="photos_tab_8" role="tab" href="#photos_8" aria-expanded="false"><span>مدارک</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="auto_tab_8" role="tab" href="#auto_8" aria-expanded="false"><span>خودروها</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="settings_tab_8" role="tab" href="#settings_8" aria-expanded="false"><span>تخصص
                                                        ها</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="Devices_tab_8" role="tab" href="#Devices_8" aria-expanded="false"><span>دستگاه
                                                        تخصصی</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="Grage_tab_8" role="tab" href="#Grage_8" aria-expanded="false"><span>تعمیرگاه</span></a></li>
                                            <li role="presentation" class=""><a data-toggle="tab" id="earning_tab_8" role="tab" href="#earnings_8" aria-expanded="false"><span> مالی
                                                    </span></a></li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent_8">
                                            <div id="profile_8" class="tab-pane fade active in" role="tabpanel">
                                                <div class="col-md-12">
                                                    <div class="pt-20">
                                                        <table class="table table-striped display product-overview" id="datable_1">
                                                            <tbody>
                                                                <?php
                                                                echo "<tr>";
                                                                echo "<td> تاریخ تولد : </td>";
                                                                echo '<td>' . $Per_Birthday[0] . "/" . $Per_Birthday[1] . "/" . $Per_Birthday[2] . '</td>';
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> محل تولد : </td>";
                                                                echo "<td>" . $Birthday_City[0]['Name'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> کد ملی : </td>";
                                                                echo "<td>" . $Detials[0]['NationalID'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> سریال کد ملی : </td>";
                                                                echo "<td>" . $Detials[0]['NationalSerial'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> میزان تحصیلات : </td>";
                                                                echo "<td>" . $Degree[0]['Name'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> دین مذهب : </td>";
                                                                echo "<td>" . $Religion[0]['Name'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> وضعیت نظام وظیفه : </td>";
                                                                echo "<td>" . $Military[0]['Name'] . "</td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> تعداد افراد تحت تکلف : </td>";
                                                                echo "<td>" . $Detials[0]['FamilyNum'] . " نفر </td>";
                                                                echo "</tr>";
                                                                echo "<tr>";
                                                                echo "<td> آدرس محل سکونت : </td>";
                                                                echo "<td>" . $address[0] . "</td>";
                                                                echo "</tr>";
                                                                if (!empty($phone)) {
                                                                    foreach ($phone as $value) {
                                                                        if ($value['Mobile'] == 0) {
                                                                            echo "<tr>";
                                                                            echo "<td> تلفن محل سکونت : </td>";
                                                                            echo "<td>" . $value['Number'] . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                        if ($value['Mobile'] == 1) {
                                                                            echo "<tr>";
                                                                            echo "<td> تلفن همراه : </td>";
                                                                            echo "<td>" . $value['Number'] . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<tr>";
                                                                    echo "<td> شماره تماس : </td>";
                                                                    echo "<td> شماره ایی ثبت نشده است </td>";
                                                                    echo "</tr>";
                                                                }
                                                                echo "<td> Email : </td>";
                                                                echo "<td>" . $address[1] . "</td>";
                                                                echo "</tr>";
                                                                echo "<td> کلید واژه ها : </td>";
                                                                echo "<td>" . str_replace(",", " - ", $address[2]) . "</td>";
                                                                echo "</tr>";
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="Tariff_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <table class="table table-striped display product-overview" id="datable_1">
                                                                <tbody>

                                                                    <?php
                                                                    $TTariff_lst = $Mechanic_obj->Get_AutoTariffType_ByAutoID($Mechanic_GUID);
                                                                    if (!empty($TTariff_lst)) {
                                                                        $i = 0;
                                                                        $list = "";
                                                                        foreach ($TTariff_lst as $key) {
                                                                            $Mechanic_Tariff[$i] = $key['TariffTypeGUID'];
                                                                            $i++;
                                                                        }
                                                                    } else {
                                                                        $list = "نوع تعرفه تعیین نشده";
                                                                    }
                                                                    if (!empty($Mechanic_Tariff)) {
                                                                        foreach ($Mechanic_Tariff as $key) {
                                                                            $TT =  $Tariff_Obj->Get_TariffType_ByID($key);
                                                                            echo "<tr><td>" . $TT[0]['NameFa'] . "</td></tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<tr><td> $list </td></tr>";
                                                                    }

                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="Cert_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <table class="table table-striped display product-overview" id="datable_1">
                                                                <tbody>
                                                                    <?php
                                                                    echo "<tr>";
                                                                    echo "<td> رسته شغلی : </td>";
                                                                    echo "<td>" . $Category . "</td>";
                                                                    echo "</tr>";
                                                                    echo "<tr>";
                                                                    echo "<td> سریال جواز : </td>";
                                                                    echo "<td>" . $License[1] . "</td>";
                                                                    echo "</tr>";
                                                                    echo "<tr>";
                                                                    echo "<td> تاریخ اعتبار جواز : </td>";
                                                                    echo '<td><h6 class="panel-title txt-dark">' . $Per_ExLicense[0] . "/" . $Per_ExLicense[1] . "/" . $Per_ExLicense[2] . '</h6></td>';
                                                                    echo "</tr>";
                                                                    echo "<tr>";
                                                                    echo "<td> وضعیت اعتبار جواز : </td>";
                                                                    if ($License[3] == 1) {
                                                                        echo '<td><form method="post" enctype="multipart/form-data">
																				<input type="hidden" id="MemberID" name="MemberID" value="' . $Mechanic_GUID . '">
                                                                                <input type="hidden" id="ActionID" name="ActionID" value="Active-License">
																				<input type="hidden" id="Licensestatus" name="Licensestatus" value="0">
																				<button class="btn btn-default btn-icon-anim " onclick="return credential()" id="Active-License" name="Active-License"> فعال </button>
																				</form></td>';
                                                                    } else {
                                                                        echo '<td><form method="post" enctype="multipart/form-data">
																				<input type="hidden" id="MemberID" name="MemberID" value="' . $Mechanic_GUID . '">
                                                                                <input type="hidden" id="ActionID" name="ActionID" value="Active-License">
																				<input type="hidden" id="Licensestatus" name="Licensestatus" value="1">
																				<button class="btn btn-pinterest btn-icon-anim " onclick="return credential()" id="Active-License" name="Active-License"> غیرفعال </button>
																				</form></td>';
                                                                    }
                                                                    echo "</tr>";
                                                                    ?>
                                                                </tbody>
                                                            </table>


                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Row -->
                                            </div>
                                            <div id="photos_8" class="tab-pane fade" role="tabpanel">
                                                <div class="col-md-12 pb-20">
                                                    <?php
                                                    if (!empty($files)) {
                                                        echo '<div class="gallery-wrap"><div class="portfolio-wrap project-gallery" style="width: 0px;">';
                                                        echo '<ul id="portfolio_1" class="portf auto-construct  project-gallery" data-col="4" style="position: relative; height: 20px;">';
                                                        foreach ($files as $value) {
                                                            echo '<li class="item" data-src="' . $value['Path'] . '" data-sub-html="<h6> مدارک </h6><p>' . $info[0]['FName'] . " " . $info[0]['LName'] . '</p>" style="width: 0px; height: auto; margin: 10px; position: absolute; left: 0px; top: 0px;">
																	<a href="">
																	<img class="img-responsive" src="' . $value['Path'] . '" alt=" مدارک ">
																	<span class="hover-cap"> مدارک </span>
																	</a>
																</li>';
                                                        }
                                                        echo '</ul></div></div>';
                                                    } else {
                                                        echo '<table class="table table-striped display product-overview" id="datable_1">
															<tbody>';
                                                        echo "<tr>";
                                                        echo '<td style="text-align: center;">';
                                                        echo 'مدارکی ثبت نشده';
                                                        echo "</td>";
                                                        echo "</tr>";
                                                        echo '</tbody></table>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div id="auto_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <div class="table-wrap">
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped display product-overview" id="datable_1">
                                                                        <tbody>
                                                                            <?php
                                                                            if (!empty($auto)) {
                                                                                foreach ($auto as $key) {
                                                                                    echo "<tr>";
                                                                                    echo "<td> نام خودرو : </td>";
                                                                                    $Cars = $Cars_obj->Get_AutoDetial_ByID($key["AutoID"]);
                                                                                    if (!empty($Cars)) {
                                                                                        if ($Cars[0]['ModelID'] != 0) {
                                                                                            $tip = $Cars_obj->Get_Tip_ByID($Cars[0]['ModelID']);
                                                                                            $Auto_Tip =  $tip[0]["ModelName"];
                                                                                        } else {
                                                                                            $Auto_Tip = "بدون تیپ";
                                                                                        }
                                                                                        $manufacture = $Cars_obj->Get_Manufactuer_ByID($Cars[0]["ManufacturerID"]);
                                                                                        if (!empty($manufacture)) {
                                                                                            $Auto_Manufacturer = $manufacture[0]["Name"];
                                                                                        } else {
                                                                                            $Auto_Manufacturer = "سازنده خودرو یافت نشد";
                                                                                        }
                                                                                        $MemberAuto_list = "<td>" .  $Auto_Manufacturer . " " . $Auto_Tip . " " . $Cars[0]["Name"] . "</td>";
                                                                                    } else {
                                                                                        $MemberAuto_list = "<td> خودرو با مشخصات مدنظر در دیتابیس یافت نشد </td>";
                                                                                    }
                                                                                    echo $MemberAuto_list;
                                                                                    echo "</tr>";
                                                                                }
                                                                            } else {
                                                                                echo "<tr>";
                                                                                echo '<td colspan="2" style="text-align: center;">';
                                                                                echo 'خودرویی ثبت نشده';
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
                                                <!-- Row -->
                                            </div>
                                            <div id="settings_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <table class="table table-striped display product-overview" id="datable_1">
                                                                <tbody>
                                                                    <?php
                                                                    if (!empty($Service)) {
                                                                        foreach ($Service as $value) {
                                                                            echo "<tr>";
                                                                            echo "<td> نام سرویس : </td>";
                                                                            echo "<td>" . $Mechanic_obj->GetNameSkills($value) . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<tr>";
                                                                        echo '<td colspan="2" style="text-align: center;">';
                                                                        echo 'سرویس تخصصی ثبت نشده';
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br />
                                            </div>

                                            <div id="Devices_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <table class="table table-striped display product-overview" id="datable_1">
                                                                <tbody>
                                                                    <?php
                                                                    if (!empty($Devices)) {
                                                                        foreach ($Devices as $value) {
                                                                            echo "<tr>";
                                                                            echo "<td> دستگاه تخصصی : </td>";
                                                                            echo "<td>" . $Mechanic_obj->GetNameDevices($value) . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<tr>";
                                                                        echo '<td colspan="2" style="text-align: center;">';
                                                                        echo 'دستگاهی ثبت نشده';
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                    }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br />
                                                <!-- Row -->
                                            </div>
                                            <div id="Grage_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="followers-wrap">
                                                            <table class="table table-striped display product-overview" id="datable_1">
                                                                <tbody>
                                                                    <?php

                                                                    if (!empty($garage)) {
                                                                        foreach ($garage as $value) {
                                                                            $role = $Garage_obj->GetRoleTopicbyID($value[1]);
                                                                            $Garage_Name = $Garage_obj->Get_GarageInfo_ByID($value[2]);

                                                                            echo "<tr>";
                                                                            echo "<td> نام تعمیرگاه : </td>";
                                                                            echo "<td>" . $Garage_Name[1] . "</td>";
                                                                            echo "<td> نقش : </td>";
                                                                            echo "<td>" . $role[0] . "</td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<tr>";
                                                                        echo '<td colspan="2" style="text-align: center;">';
                                                                        echo 'تعمیرگاهی ثبت نشده';
                                                                        echo "</td>";
                                                                        echo "</tr>";
                                                                    }

                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                            <br />
                                                        </div>
                                                    </div>
                                                </div>
                                                <br />
                                                <br />
                                                <br />
                                                <!-- Row -->
                                            </div>
                                            <div id="earnings_8" class="tab-pane fade" role="tabpanel">
                                                <!-- Row -->
                                                <div class="col-sm-12">
                                                    <div class="panel panel-inverse card-view">
                                                        <div class="panel-heading ">
                                                            <div class="pull-left">
                                                                <h6 class="panel-title txt-dark"> صورت حساب ها </h6>
                                                            </div>
                                                            <div class="pull-right">
                                                                <a href="#" class="pull-left inline-block full-screen">
                                                                    تمام صفحه
                                                                </a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div class="panel-wrapper collapse in">
                                                            <div class="panel-body row pa-0">
                                                                <div class="table-wrap">
                                                                    <div class="table-responsive">
                                                                        <div id="support_table_wrapper" class="dataTables_wrapper no-footer">
                                                                            <table class="table display table-striped product-overview border-none dataTable no-footer" id="payedtbl" role="grid">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            موضوع صروت حساب
                                                                                        </th>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            مبلغ
                                                                                            (ریال)
                                                                                        </th>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            تاریخ صدور </th>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            توضیحات </th>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            تصاویر مستندات
                                                                                        </th>
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            پرداخت </th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php

                                                                                    if (!empty($inv)) {
                                                                                        foreach ($inv as $Inv_Value) {
                                                                                            $inv_mem = $Invoice_obj->Get_Invoice_ByGUID($Inv_Value['Inv_GUID']);
                                                                                            foreach ($inv_mem as $value) {
                                                                                                echo "<tr>";
                                                                                                $title = $Tariff_Obj->getversion($value['Title']);
                                                                                                //$inv_Title = $Invoice_obj->Get_InvoiceTtitle_ByID ($title[0]['NameFa']);
                                                                                                
                                                                                                if (!empty($title)) {
                                                                                                    echo "<td>".$title[0]['NameFa']."</td>";
                                                                                                } else {
                                                                                                    echo "<td> بدون موضوع </td>";
                                                                                                }

                                                                                                echo "<td>" . number_format($value['Amount']) . "</td>";
                                                                                                $miladidate =  str_replace("-", "", $value['Start_Date']);
                                                                                                $day = substr($miladidate, 6, 2);
                                                                                                $mon = substr($miladidate, 4, 2);
                                                                                                $year = substr($miladidate, 0, 4);
                                                                                                echo "<td>" . gregorian_to_jalali($year, $mon, $day, '/') . "</td>";
                                                                                                echo "<td>" . $value['Comment'] . "</td>";
                                                                                                $Inv_pic = $Invoice_obj->V_Invoice_Doc($value['GUID']);
                                                                                                echo "<td>";
                                                                                                foreach ($Inv_pic as $pic) {
                                                                                                    echo '<a href="https://adminpanel.autoapp.ir/' . $pic['location'] . '"> دانلود فایل  </a> <br />';
                                                                                                }
                                                                                                echo "</td>";
                                                                                                $validation = "onclick='return validation()'";
                                                                                                echo '
																					      <td style ="width: 5%;">
																						  <form method="post" enctype="multipart/form-data" action ="Invoice_Pay.php">
																						  <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $value['GUID'] . '">
																						  <input type="hidden" id="MechanicGUID" name="MechanicGUID" value="' . $Mechanic_GUID . '">
																						  <input type="hidden" id="ActionID" name="ActionID" value="ChosePayMethod">
																						  <button class="btn btn-success btn-outline fancy-button btn-0"><span class="btn-text"> پرداخت </span></button>
																						  </form></td></tr>';
                                                                                            }
                                                                                        }
                                                                                    } else {
                                                                                        echo "<tr role='row' class='odd'>";
                                                                                        echo '<td colspan="6" style="text-align: center;">';
                                                                                        echo 'صورت حسابی برای پرداخت موجود نمی باشد';
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
                                                </div>
                                                <!-- Row -->
                                                <br>
                                                <br>
                                                <div class="col-sm-12">
                                                    <div class="panel panel-success card-view">
                                                        <div class="panel-heading">
                                                            <div class="pull-left">
                                                                <h6 class="panel-title txt-dark"> پرداخت ها
                                                                </h6>
                                                            </div>
                                                            <div class="pull-right">
                                                                <a href="#" class="pull-left inline-block full-screen">
                                                                    تمام صفحه
                                                                </a>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div class="panel-wrapper collapse in">
                                                            <div class="panel-body row pa-0">
                                                                <div class="table-wrap">
                                                                    <div class="table-responsive">
                                                                        <div id="support_table_wrapper" class="dataTables_wrapper no-footer">
                                                                            <table class="table display table-striped product-overview border-none dataTable no-footer" id="payedtbl" role="grid">
                                                                                <thead>
                                                                                    <tr role="row">
                                                                                        <th class="sorting_asc" tabindex="0" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            موضوع
                                                                                        </th>
                                                                                        <th class="sorting_asc" tabindex="1" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            مبلغ
                                                                                            (ریال)</th>
                                                                                        <th class="sorting_asc" tabindex="2" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            نحوه
                                                                                            پرداخت</th>
                                                                                        <th class="sorting_asc" tabindex="3" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;"> شماره
                                                                                            سفارش </th>
                                                                                        <th class="sorting_asc" tabindex="4" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            شماره
                                                                                            پیگیری</th>
                                                                                        <th class="sorting_asc" tabindex="5" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            شماره
                                                                                            ارجاع</th>
                                                                                        <th class="sorting_asc" tabindex="6" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;"> تاریخ
                                                                                            پرداخت </th>
                                                                                        <th class="sorting_asc" tabindex="7" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            شرح
                                                                                        </th>
                                                                                        <th class="sorting_asc" tabindex="8" aria-controls="support_table" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ticket ID: activate to sort column descending" style="width: 259px;">
                                                                                            صورت حساب
                                                                                        </th>

                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    if (!empty($payed)) {
                                                                                        foreach ($payed as $value) {

                                                                                            $inv_info = $Invoice_obj->Get_Invoice_ByGUID($value['Invoice_GUID']);
                                                                                            
                                                                                            echo "<tr role='row' class='odd'>";
                                                                                            $title = $Tariff_Obj->getversion($inv_info[0]['Title']);
                                                                                            
                                                                                            echo "<td>". $title[0]['NameFa'] ."</td>";
                                                                                            echo "<td>" . number_format($value['Amount']) . "</td>";
                                                                                            if ($value['Payment_Method'] == 0) {
                                                                                                echo "<td class='sorting_1'> غیر اینترنتی </td>";
                                                                                            } else {
                                                                                                echo "<td class='sorting_1'> اینترنتی </td>";
                                                                                            }
                                                                                            echo "<td>" . $value['OrderID'] . "</td>";
                                                                                            echo "<td>" . $value['SysTraceNum'] . "</td>";
                                                                                            echo "<td>" . $value['RetrivalRefNum'] . "</td>";
                                                                                            $miladidate =  str_replace("-", "", $value['Payment_Date']);
                                                                                            $day = substr($miladidate, 6, 2);
                                                                                            $mon = substr($miladidate, 4, 2);
                                                                                            $year = substr($miladidate, 0, 4);
                                                                                            echo "<td class='sorting_1'>" . gregorian_to_jalali($year, $mon, $day, '/') . "</td>";
                                                                                            echo "<td>" . $value['Comment'] . "</td>";
                                                                                            echo "<td>";
                                                                                            echo '<form method="post" enctype="multipart/form-data">
                                                                                            <input type="hidden" id="Payed-ID" name="Payed-ID" value="' . $value['ID'] . '">
                                                                                            <input type="hidden" id="ActionID" name="ActionID" value="multipayment">
                                                                                            <button class="btn btn-success btn-outline fancy-button btn-0" onclick="return credential()" ><span class="btn-text"> ایجاد </span></button>
                                                                                            </form>';
                                                                                            echo "</td>";
                                                                                            echo "</tr>";
                                                                                        }
                                                                                    } else {
                                                                                        echo "<tr role='row' class='odd'>";
                                                                                        echo '<td colspan="8" style="text-align: center;">';
                                                                                        echo 'پرداختی تا کنون در نرم افزار ثبت نشده';
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
                                                </div>
                                                <!-- Row -->
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
    </div>
    <!-- /#wrapper -->


    <!-- JavaScript -->

    <!-- jQuery -->


    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Moment JavaScript -->

    <script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js">
    </script>

    <!-- Bootstrap Select JavaScript -->
    <script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Touchspin JavaScript -->
    <script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js">
    </script>

    <!-- Multiselect JavaScript -->
    <script src="vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>

    <!-- Bootstrap Switch JavaScript -->
    <script src="vendors/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>

    <!-- Counter Animation JavaScript -->
    <script src="vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="vendors/bower_components/jquery.counterup/jquery.counterup.min.js"></script>

    <!-- Data table JavaScript -->
    <script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>


    <!-- Sparkline JavaScript -->
    <script src="vendors/jquery.sparkline/dist/jquery.sparkline.min.js"></script>

    <script src="vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Data table JavaScript -->
    <script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>


    <!-- Moment JavaScript -->

    <!-- Form Advance Init JavaScript -->
    <script src="dist/js/form-advance-data.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Gallery JavaScript -->
    <script src="dist/js/isotope.js"></script>
    <script src="dist/js/lightgallery-all.js"></script>
    <script src="dist/js/froogaloop2.min.js"></script>
    <script src="dist/js/gallery-data.js"></script>

    <!-- Spectragram JavaScript -->
    <script src="dist/js/spectragram.min.js"></script>
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script src="dist/js/widgets-data.js"></script>
    <script src="dist/js/skills-counter-data.js"></script>

    <script src="config/Hejri-Shamsi/js/persianDatepicker.min.js"></script>

    <script LANGUAGE="JavaScript">
        function credential() {
            var agree = confirm("آیا از اجرای این دستور مطمین می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }

        function validation() {
            var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }

        function InvoiceRemovevalid() {
            var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
            if (agree)
                return true;
            else
                return false;
        }
    </script>
    <?php
    if (!empty($Error_STR)) {
        if ($Error_STR == 1) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در عملیات',
								text: 'عملیات مدنظر انجام نشد' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
            echo '</script>';
        }

        if ($Error_STR == 2) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
            $.toast({
                heading: 'خطا در ثبت اطلاعات',
                text: 'فیلدهای خالی مانده را پرنمایید.' ,
                position: 'top-center',
                loaderBg:'#ed3236',
                hideAfter: 6500,
                stack: 6
            });
            return false;
    });";
            echo '</script>';
        }

        if ($Error_STR == 3) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در ثبت شماره تلفن',
								text: 'لطفا با ادمین تماس بگیرید' ,
								position: 'top-center',
								loaderBg:'#ed3236',
								hideAfter: 6500,
								stack: 6
							});
							return false;
					});";
            echo '</script>';
        }
        if ($Error_STR == 4) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
            $.toast({
                heading: 'خطا در ثبت اطلاعات',
                text: 'فایل خراب می باشد و امکان بارگذاری بر روی سرور نیست' ,
                position: 'top-center',
                loaderBg:'#ed3236',
                hideAfter: 6500,
                stack: 6
            });
            return false;
    });";
            echo '</script>';
        }
        if ($Error_STR == 5) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
            $.toast({
                heading: 'خطا در ثبت اطلاعات',
                text: 'سایز فایل بیشتر از 5 مگابایت می باشد' ,
                position: 'top-center',
                loaderBg:'#ed3236',
                hideAfter: 6500,
                stack: 6
            });
            return false;
    });";
            echo '</script>';
        }

        if ($Error_STR == 6) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
            $.toast({
                heading: 'خطا در ثبت اطلاعات',
                text: 'قالب فایل صحیح نمی باشد' ,
                position: 'top-center',
                loaderBg:'#ed3236',
                hideAfter: 6500,
                stack: 6
            });
            return false;
    });";
            echo '</script>';
        }

        if ($Error_STR == 7) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
            $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
            $.toast({
                heading: 'خطا در ثبت اطلاعات',
                text: 'فایل با این نام قبلا در سمت سرور قرار گرفته است' ,
                position: 'top-center',
                loaderBg:'#ed3236',
                hideAfter: 6500,
                stack: 6
            });
            return false;
    });";
            echo '</script>';
        }

        if ($Error_STR == 9) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
							$('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
							$.toast({
								heading: 'خطا در کاربری',
								text: 'کاربر فعال نشده است' ,
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