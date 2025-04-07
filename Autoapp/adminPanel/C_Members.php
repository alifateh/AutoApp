<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);

require_once("$rootDir/config/public_conf.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/N_PhoneContactModel.php");
require("$rootDir/Model/AutoModel.php");

use fateh\Automobile\Automobile as auto;
use fateh\Phonebook\MechanicContact as Tell;
use fateh\Member\Member as member;

$Mechanic_Obj = new member($_SESSION["Admin_GUID"]);
$Auto_Obj = new auto($_SESSION["Admin_GUID"]);
$Tell_Obj = new Tell($_SESSION["Admin_GUID"]);

//Initial Values

$EducationLevels = $Mechanic_Obj->Get_MechanicEducationlevels();
$Religion = $Mechanic_Obj->Get_MechanicReligion();
$Category = $Mechanic_Obj->Get_MechanicCategory();
$CapitalCity = $Mechanic_Obj->Get_MechanicCity();
$Duty = $Mechanic_Obj->Get_MechanicMilitary();
$SpecialDevices = $Mechanic_Obj->Get_MechanicSpecialDevices();
$Skills = $Mechanic_Obj->Get_MechanicSkills();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['SaveMember'])) {
        //############ Define Array
        $info = array();
        $Detail = array();
        $Address = array();
        $license = array();
        $SDevices = array();
        $Automobile = array();
        $Services = array();

        //First Initialization
        $Error_STR = 0;

        //############ info
        if (isset($_POST['fname']) && !empty($_POST['fname'])) {
            $info[0] = $_POST['fname'];
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['Lname']) && !empty($_POST['Lname'])) {
            $info[1] = $_POST['Lname'];
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['gender']) && !empty($_POST['Lname'])) {
            $info[2] = $_POST['gender'];
        }

        if (isset($_POST['National-Code']) && !empty($_POST['National-Code'])) {
            $info[3] = $_POST['National-Code'];
            $Detail[1] = $_POST['National-Code'];
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['MechanicMobile']) && !empty($_POST['MechanicMobile'])) {
            $info[4] = $_POST['MechanicMobile'];
        } else {
            $Error_STR = 2;
        }

        //############ Detail

        if (isset($_POST['Birthday']) && !empty($_POST['Birthday'])) {
            $persiandate =  str_replace("/", "", $_POST['Birthday']);
            $day = substr($persiandate, 8, 2);
            $mon = substr($persiandate, 5, 2);
            $year = substr($persiandate, 0, 4);
            $Birthday = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
            $Detail[0] = date('Y-m-d', $Birthday);
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['Ncode-Serial']) && !empty($_POST['Ncode-Serial'])) {
            $Detail[2] = $_POST['Ncode-Serial'];
        } else {
            $Detail[2] = "";
        }

        if (isset($_POST['Birth-Loc']) && !empty($_POST['Birth-Loc'])) {
            $Detail[3] = $_POST['Birth-Loc'];
        } else {
            $Detail[3] = 8;
        }

        if (isset($_POST['Edu-Level']) && !empty($_POST['Edu-Level'])) {
            $Detail[4] = $_POST['Edu-Level'];
        } else {
            $Detail[4] = 0;
        }

        if (isset($_POST['Religion']) && !empty($_POST['Religion'])) {
            $Detail[5] = $_POST['Religion'];
        } else {
            $Detail[5] = 0;
        }

        if (isset($_POST['Duty']) && !empty($_POST['Duty'])) {
            $Detail[6] = $_POST['Duty'];
        } else {
            $Detail[6] = 0;
        }

        if (isset($_POST['FamilyNumbers']) && !empty($_POST['FamilyNumbers'])) {
            $Detail[7] = $_POST['FamilyNumbers'];
        } else {
            $Detail[7] = 0;
        }


        //############ Address

        if (isset($_POST['address']) && !empty($_POST['address'])) {
            $Address[0] = $_POST['address'];
        } else {
            $Address[0] = "";
        }

        if (isset($_POST['Email']) && !empty($_POST['Email'])) {
            $Address[1] = $_POST['Email'];
        } else {
            $Address[1] = "";
        }

        if (isset($_POST['tags']) && !empty($_POST['tags'])) {
            $Address[2] = $_POST['tags'];
        } else {
            $Address[2] = "";
        }

        //############ license

        if (isset($_POST['LicenseDate']) && !empty($_POST['LicenseDate'])) {
            $fadate =  str_replace("/", "", $_POST['LicenseDate']);
            $day = substr($fadate, 8, 2);
            $mon = substr($fadate, 5, 2);
            $year = substr($fadate, 0, 4);
            $LicenseEx = strtotime(jalali_to_gregorian($year, $mon, $day, '-'));
            $license[2] = date('Y-m-d', $LicenseEx);
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['LicenseCategory']) && !empty($_POST['LicenseCategory'])) {
            $license[0] = $_POST['LicenseCategory'];
        } else {
            $Error_STR = 2;
        }

        if (isset($_POST['LicenseNUM']) && !empty($_POST['LicenseNUM'])) {
            $license[1] = $_POST['LicenseNUM'];
        } else {
            $Error_STR = 2;
        }

        //############ SpecialDevice
        if (isset($_POST['SpecialDevice']) && !empty($_POST['SpecialDevice'])) {
            $SDevices = $_POST['SpecialDevice'];
        } else {
            $SDevices = [];
        }

        //############ Automobile
        if (isset($_POST['MechanicAutomobiles']) && !empty($_POST['MechanicAutomobiles'])) {
            $Automobile = $_POST['MechanicAutomobiles'];
        } else {
            $Automobile = [];
        }

        //############ Services
        if (isset($_POST['ServiceType']) && !empty($_POST['ServiceType'])) {
            $Services = $_POST['ServiceType'];
        } else {
            $Services = [];
        }
        if ($Error_STR == 0) {
            $Mechanic = $Mechanic_Obj->C_Mechanics($info);
            if ($Mechanic[0] == 1) {
                $C_MechanicsDetail = $Mechanic_Obj->C_MechanicsDetail($Mechanic[1], $Detail);
                if ($C_MechanicsDetail != true) {
                    $Error_STR = 1;
                }

                $C_MechanicsAddress = $Mechanic_Obj->C_MechanicsAddress($Mechanic[1], $Address);
                if ($C_MechanicsAddress != true) {
                    $Error_STR = 1;
                }
                $C_MechanicsLicense = $Mechanic_Obj->C_MechanicsLicense_ByID($Mechanic[1], $license);
                if ($C_MechanicsLicense != true) {
                    $Error_STR = 1;
                }

                if (count($SDevices) > 0) {
                    $C_MechanicsSpecialDevices = $Mechanic_Obj->C_MechanicsSpecialDevices_ByID($Mechanic[1], $SDevices);
                    if ($C_MechanicsSpecialDevices != true) {
                        $Error_STR = 1;
                    }
                }

                if (count($Automobile) > 0) {
                    $C_MechanicsAutomobile = $Mechanic_Obj->C_MechanicsAutomobile_ByID($Mechanic[1], $Automobile);
                    if ($C_MechanicsAutomobile != true) {
                        $Error_STR = 1;
                    }
                }

                if (count($Services) > 0) {
                    $C_MechanicsServices = $Mechanic_Obj->C_MechanicsServices_ByID($Mechanic[1], $Services);
                    if ($C_MechanicsServices != true) {
                        $Error_STR = 1;
                    }
                }

                if ($_POST['MechanicMobile'] != "" && !empty($_POST['MechanicMobile'])) {
                    $C_MechanicContact = $Tell_Obj->C_MechanicContact($Mechanic[1], 1, $_POST['MechanicMobile']);
                    if ($C_MechanicContact != true) {
                        $Error_STR = 1;
                    }
                }
                echo "sss";
                if ($_POST['HomeContact'] != "" && !empty($_POST['HomeContact'])) {
                    $C_MechanicContact = $Tell_Obj->C_MechanicContact($Mechanic[1], 0, $_POST['HomeContact']);
                    if ($C_MechanicContact != true) {
                        $Error_STR = 1;
                    }
                }
            } else {
                print_r($Mechanic);  // error trace
                $Error_STR = 1;
            }
            $url = "./V_MemberAll.php";
            header("Location: $url");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>
    <!-- Jasny-bootstrap CSS -->
    <link href="vendors/bower_components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Colorpicker CSS -->
    <link href="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/bower_components/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- switchery CSS -->
    <link href="vendors/bower_components/switchery/dist/switchery.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-select CSS -->
    <link href="vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-tagsinput CSS -->
    <link href="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

    <!-- multi-select CSS -->
    <link href="vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Switches CSS -->
    <link href="vendors/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Datetimepicker CSS -->
    <link href="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

    <!-- jquery-steps css -->
    <link rel="stylesheet" href="vendors/bower_components/jquery.steps/demo/css/jquery.steps.css">

    <!-- Data table CSS -->
    <link href="vendors/bower_components/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- bootstrap-touchspin CSS -->
    <link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>


    <link rel="stylesheet" href="config/Hejri-Shamsi/css/persianDatepicker-default.css" />


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
                            <form method="post" enctype="multipart/form-data" name="RegisterationForm">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> ثبت نام اعضا </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-wrapper collapse in">
                                    <!-- member GUID -->
                                    <div class="panel-body">
                                        <div id="example-basic">
                                            <h3><span class="head-font capitalize-font"> عمومی </span></h3>
                                            <section>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="fname"><i class="text-info mb-10">*</i> نام : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <input type="text" class="form-control" name="fname" id="fname" placeholder="نام" value="<?php echo $_POST['fname']; ?>" tabindex="1">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10 text-left"> جنسیت : </label>
                                                        <div class="radio radio-info">
                                                            <input type="radio" id="male" name="gender" value="1" checked tabindex="3">
                                                            <label for="male"><i class="icon-user"></i>
                                                                مرد
                                                            </label>
                                                        </div>
                                                        <div class="radio radio-info">
                                                            <input type="radio" id="female" name="gender" value="0">
                                                            <label for="female"><i class="icon-user-female"></i>
                                                                زن
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <br />
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="National-Code"><i class="text-info mb-10">*</i> کد ملی : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <input type="text" onkeypress="return onlyNumberKey(event)" class="form-control" Name="National-Code" id="National-Code" placeholder="کد ملی" minlength="10" maxlength="10" value="<?php echo $_POST['National-Code']; ?>" tabindex="6">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> میزان تحصیلات : </label>
                                                        <select class="form-control select2" dir="rtl" Name="Edu-Level" id="Edu-Level" tabindex="8">
                                                            <?php
                                                            if (!empty($EducationLevels)) {
                                                                foreach ($EducationLevels as $row) {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value=""> خطا در برنامه </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> دین و مذهب </label>
                                                        <select class="form-control select2" dir="rtl" Name="Religion" id="Religion" tabindex="10">
                                                            <?php
                                                            if (!empty($Religion)) {
                                                                foreach ($Religion as $row) {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value=""> خطا در برنامه </option>';
                                                            }

                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="Email"> آدرس ایمیل </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="fa fa-dot-circle-o"></i></div>
                                                            <input type="text" class="form-control" id="Email" name="Email" placeholder="آدرس ایمیل" value="<?php echo $_POST['Email']; ?>" tabindex="20">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="Lname"><i class="text-info mb-10">*</i> نام خانوادگی : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <input type="text" class="form-control" name="Lname" id="Lname" placeholder="نام خانوادگی" value="<?php echo $_POST['Lname']; ?>" tabindex="2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="Birthday"><i class="text-info mb-10">*</i> تاریخ تولد : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <input type="text" class="form-control" id="Birthday" name="Birthday" placeholder=" تاریخ تولد " value="<?php echo $_POST['Birthday']; ?>" tabindex="4">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> محل تولد : </label>
                                                        <select class="form-control select2" dir="rtl" Name="Birth-Loc" id="Birth-Loc" tabindex="5">
                                                            <?php
                                                            if (!empty($CapitalCity)) {
                                                                foreach ($CapitalCity as $row) {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value=""> خطا در برنامه </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="Ncode-Serial"> شماره سریال کد ملی : </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><i class="icon-info"></i></div>
                                                            <input type="text" class="form-control" Name="Ncode-Serial" id="Ncode-Serial" placeholder="شماره سریال کد ملی" value="<?php echo $_POST['Ncode-Serial']; ?>" tabindex="7">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> وضعیت نظام وظیفه : </label>
                                                        <select class="form-control select2" dir="rtl" Name="Duty" id="Duty" tabindex="9">
                                                            <?php
                                                            if (!empty($Duty)) {
                                                                foreach ($Duty as $row) {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                }
                                                            } else {
                                                                echo '<option value=""> خطا در برنامه </option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> تعداد افراد تحت تکلف : </label>
                                                        <input class="vertical-spin" type="text" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default" value="1" id="FamilyNumbers" name="FamilyNumbers" value="<?php echo $_POST['FamilyNumbers']; ?>" tabindex="11">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10 text-left" for="tags"> کلمات کلیدی : </label>
                                                        <input type="text" value="" data-role="tagsinput" id="tags" name="tags" placeholder="کلمات کلیدی" value="<?php echo $_POST['tags']; ?>" tabindex="21">
                                                    </div>
                                                </div>
                                            </section>
                                            <h3><span class="head-font capitalize-font">جواز</span></h3>
                                            <section>
                                                <div class="col-sm-12">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label class="control-label mb-10"><i class="text-info mb-10">*</i> رسته های شغلی : </label>
                                                                <select class="form-control select2 select2-hidden-accessible" aria-hidden="true" dir="rtl" id="LicenseCategory" name="LicenseCategory" tabindex="12">
                                                                    <?php
                                                                    if (!empty($Category)) {
                                                                        foreach ($Category as $row) {
                                                                            echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                        }
                                                                    } else {
                                                                        echo '<option value=""> خطا در برنامه </option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label mb-10" for="LicenseNUM"><i class="text-info mb-10">*</i> شماره مجوز : </label>
                                                                <div class="input-group">
                                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                    <input type="text" class="form-control" id="LicenseNUM" name="LicenseNUM" placeholder="شماره مجوز" value="<?php echo $_POST['LicenseNUM']; ?>" tabindex="12">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label mb-10" for="exampleInputuname_1"><i class="text-info mb-10">*</i> تاریخ اعتبار جواز : </label>
                                                                <div class="input-group">
                                                                    <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                    <input type="text" class="form-control" id="LicenseDate" name="LicenseDate" placeholder=" تاریخ اعتبار جواز " value="<?php echo $_POST['LicenseDate']; ?>" tabindex="13">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <h3><span class="head-font capitalize-font">نشانی</span></h3>
                                            <section>
                                                <div class="col-sm-12">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label mb-10"><i class="text-info mb-10">*</i> آدرس محل سکونت : </label>
                                                            <textarea class="form-control" id="address" name="address" rows="3" tabindex="14"><?php echo $_POST['address']; ?></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10" for="HomeContact"> تلفن محل سکونت : </label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                <input type="text" class="form-control" id="HomeContact" Name="HomeContact" onkeypress="return onlyNumberKey(event)" placeholder="21" value="<?php echo $_POST['HomeContact']; ?>" tabindex="15">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label mb-10" for="MechanicMobile"><i class="text-info mb-10">*</i>شماره همراه (بدون 0 ) : </label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><i class="icon-info"></i></div>
                                                                <input type="tel" class="form-control" name="MechanicMobile" id="MechanicMobile" onkeypress="return onlyNumberKey(event)" placeholder="...912" minlength="10" maxlength="10" value="<?php echo $_POST['MechanicMobile']; ?>" tabindex="16">
                                                            </div>
                                                        </div>
                                                    </div>
                                            </section>
                                            <h3><span class="head-font capitalize-font">تخصص ها</span></h3>
                                            <section>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label class="control-label mb-10"> دستگاه های تخصصی : </label>
                                                            <div class="panel-wrapper collapse in">
                                                                <div class="panel-body">

                                                                    <select multiple="multiple" id="SpecialDevice" name="SpecialDevice[]">
                                                                        <optgroup label="اضافه کردن همه موارد زیر">
                                                                            <?php
                                                                            if (!empty($SpecialDevices)) {
                                                                                foreach ($SpecialDevices as $row) {
                                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                                }
                                                                            } else {
                                                                                echo '<option value=""> خطا در برنامه </option>';
                                                                            }
                                                                            ?>
                                                                        </optgroup>
                                                                    </select>
                                                                    <script type="text/javascript">
                                                                        $('#SpecialDevice').multiSelect({
                                                                            selectableOptgroup: true
                                                                        });
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10"> سرویس های تخصصی </label>
                                                        <div class="panel-wrapper collapse in">
                                                            <div class="panel-body">
                                                                <select multiple="multiple" id="ServiceType" name="ServiceType[]">
                                                                    <optgroup label="اضافه کردن همه موارد زیر">
                                                                        <?php
                                                                        if (!empty($Skills)) {
                                                                            foreach ($Skills as $row) {
                                                                                echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                            }
                                                                        } else {
                                                                            echo '<option value=""> خطا در برنامه </option>';
                                                                        }
                                                                        ?>
                                                                    </optgroup>
                                                                </select>
                                                                <script type="text/javascript">
                                                                    $('#ServiceType').multiSelect({
                                                                        selectableOptgroup: true
                                                                    });
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <h3><span class="head-font capitalize-font">خودروها</span></h3>
                                            <section>
                                                <div class="col-sm-12">
                                                    <div class="form-group">

                                                        <label class="control-label mb-10" for="exampleInputuname_1"> خودروهای تخصصی </label>
                                                        <br />
                                                        <div class="panel-wrapper collapse in">
                                                            <div class="panel-body">
                                                                <select multiple="multiple" id="MechanicAutomobiles" name="MechanicAutomobiles[]" tabindex="19">
                                                                    <?php
                                                                    $Auto_All = $Auto_Obj->V_Automobiles();
                                                                    $Manufacturer_All = $Auto_Obj->V_AutoManufactures();
                                                                    if (!empty($Auto_All)) {
                                                                        foreach ($Manufacturer_All as $Man) {
                                                                            echo '<optgroup label=" اضافه کردن گروه خودرهای [' . $Man['Name'] . ']">';
                                                                            foreach ($Auto_All as $key) {
                                                                                if ($key['ManufacturerID'] == $Man['ID']) {
                                                                                    $Auto_Tip =  $Auto_Obj->Get_Tip_ByID($key['ModelID']);
                                                                                    if ($Auto_Tip[0]["ModelName"] == "") {
                                                                                        $tip = "بدون تیپ";
                                                                                    } else {
                                                                                        $tip =  $Auto_Tip[0]["ModelName"];
                                                                                    }
                                                                                    echo '<option value="' . $key['ID'] . '">' . $key['Name'] . ' [' . $tip . '] </option>';
                                                                                }
                                                                            }
                                                                        }
                                                                    } else {
                                                                        echo '<optgroup label=""> خودرویی ثبت نشده </optgroup>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <script type="text/javascript">
                                                            $('#MechanicAutomobiles').multiSelect({
                                                                selectableOptgroup: true
                                                            });
                                                        </script>
                                                    </div>
                                                    <br>
                                                    <div class=" form-group">
                                                        <div class="input-group">
                                                            <button class="disabled-btn btn btn-info btn-anim" type="submit" name="SaveMember" id="SaveMember"><i class="icon-check"></i><span class="btn-text">ثبت اطلاعات </span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
    <!-- JavaScript -->
    <!-- jQuery -->
    <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Form Wizard JavaScript -->
    <script src="vendors/bower_components/jquery.steps/build/jquery.steps.min.js"></script>


    <!-- Form Wizard Data JavaScript -->
    <script src="dist/js/form-wizard-data.js"></script>

    <!-- Data table JavaScript -->
    <script src="vendors/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>

    <!-- Starrr JavaScript -->
    <script src="dist/js/starrr.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Touchspin JavaScript -->
    <script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Moment JavaScript -->
    <script type="text/javascript" src="vendors/bower_components/moment/min/moment-with-locales.min.js"></script>

    <!-- Bootstrap Colorpicker JavaScript -->
    <script src="vendors/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

    <!-- Switchery JavaScript -->
    <script src="vendors/bower_components/switchery/dist/switchery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/bower_components/select2/dist/js/select2.full.min.js"></script>

    <!-- Bootstrap Select JavaScript -->
    <script src="vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="vendors/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Touchspin JavaScript -->
    <script src="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script>

    <!-- Multiselect JavaScript -->
    <script src="vendors/bower_components/multiselect/js/jquery.multi-select.js"></script>

    <!-- Bootstrap Switch JavaScript -->
    <script src="vendors/bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>

    <!-- Bootstrap Datetimepicker JavaScript -->
    <script type="text/javascript" src="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Form Advance Init JavaScript -->
    <script src="dist/js/form-advance-data.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/bower_components/owl.carousel/dist/owl.carousel.min.js"></script>

    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <!-- Init JavaScript -->
    <script src="config/Hejri-Shamsi/js/persianDatepicker.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#Birthday").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
            $("#LicenseDate").persianDatepicker({
                formatDate: "YYYY-0M-0D"
            });
        });
    </script>
    <script src="dist/js/init.js"></script>

    <?php
    if (!empty($Error_STR)) {
        if ($Error_STR == 0) {
            echo '<script language="javascript">';
            echo '$(document).ready(function () {
                $("#RegisterationForm").submit(function () {
                    $(".disabled-btn").attr("disabled", true);
                });
            });';
            echo '</script>';
        }
        if ($Error_STR == 1) {
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

        if ($Error_STR == 2) {
            echo '<script language="javascript">';
            echo "$(document).ready(function() {
        $('body').removeAttr('class').removeClass('bottom-center-fullwidth').addClass('top-center-fullwidth');
        $.toast({
            heading: 'خطا در ثبت اطلاعات',
            text: 'فیلدهای ستاره دار باید مقدار مناسب بگیرند.' ,
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