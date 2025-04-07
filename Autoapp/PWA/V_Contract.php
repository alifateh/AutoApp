<?php
session_start();
if (!isset($_SESSION["Mechanic_GUID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "/";
    header("Location: $url");
}

require('config/public_conf.php');
require('Model/MemberModel.php');
require('Model/InvoiceModel.php');
require('Model/TariffModel.php');
require('Model/ContractModel.php');
require('Model/GarageModel.php');

use fateh\Finance\Invoice as invoice;
use fateh\Member\Member as member;
use fateh\tariff\tariff as tariff;
use fateh\AutoShop\AutoShop as garage;
use fateh\Contarct\contarct as contarct;

$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];


if (!empty($member_GUID)) {
    $MechanicObj = new member($member_GUID);
    $Inv_fetch = new invoice($member_GUID);
    $tariffver = new tariff($member_GUID);
    $ContarctObj = new contarct($member_GUID);
    $Garage_obj = new garage($member_GUID);
    $mem_info = $MechanicObj->Get_MechanicsInfo_ByID($member_GUID);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $InvoiceGUID = $_POST['Inv_GUID'];
    $inv = $Inv_fetch->Get_Invoice_ByGUID($InvoiceGUID);
    $_SESSION["Inv_GUID"] = $InvoiceGUID ;


    if (!empty($inv)) {
        $TarrifVersionID = $inv[0]['Title'];
        $MechanicContarct = $ContarctObj->Get_Contract_ByMechanic($TarrifVersionID, $member_GUID);
        $ContractID = $MechanicContarct[0]['ID'];
        $_SESSION["ContractID"] = $ContractID;
        $ContarctDetial = $ContarctObj->Get_Contract_ByID($MechanicContarct[0]['ContractGUID']);
        $garage = $Garage_obj->GetOwnerGarage($member_GUID);
        $MechanicDetials = $MechanicObj->GetMemDetials($member_GUID);
        $MechanicLicense = $MechanicObj->GetMemLicense($member_GUID);
        $CodeMeli = $MechanicDetials[1];
        $BirthLocation = $MechanicObj->Get_MechanicCity_ByID($MechanicDetials[3]);
        $BirthdayLocation = $BirthLocation[0]['Name'];
        $javaz = $MechanicLicense[1];
    } else {
        $url = "/";
        header("Location: $url");
    }
}

?>


<!DOCTYPE html>
<html lang="en" view-mode="rtl">

<head>
    <?php Metatag(); ?>
    <!-- CSS Libraries-->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/ion.rangeSlider.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/apexcharts.css">
    <!-- Core Stylesheet-->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Preloader-->
    <div class="preloader d-flex align-items-center justify-content-center" id="preloader">
        <div class="spinner-grow text-primary" role="status">
            <div class="sr-only"> لطفا منتظر بمانید ... </div>
        </div>
    </div>
    <!-- Internet Connection Status-->
    <div class="internet-connection-status" id="internetStatus"></div>
    <!-- Header Area-->
    <div class="header-area" id="headerArea">
        <div class="container">
            <!-- Paste your Header Content from here-->
            <!-- # Header Five Layout-->
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                <!-- Back Button-->
                <div class="back-button"><a href="Dashboard.php"><svg width="32" height="32" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z" />
                        </svg></a></div>
                <!-- Page Title-->
                <div class="page-heading">
                    <h6 class="mb-0"> تعهد نامه </h6>
                </div>
                <!-- Navbar Toggler-->
                <div></div>
            </div>
        </div>
    </div>

    <div class="page-content-wrapper py-3">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <img src="img\contract-graphic.png" alt="">
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-body" dir="rtl">
                    <?php
                    if (!empty($MechanicContarct)) {
                        if ($MechanicContarct[0]['Status'] !== 1) {
                            $Garage_info = $Garage_obj->Get_GarageAddress_ByID($garage[0]);
                            $Garage_address = $Garage_info[1];
                            echo "<h3 style='text-align: center;'> بسمه تعالی </h3>";
                            echo "<h5 style='text-align: center;'> ((تعهد نامه)) </h4>";
                            echo "<br />";
                            echo "<h4> ریاست محترم اتحادیه صنف تعمیرکاران خودرو تهران </h4>";
                            echo "<p>";
                            echo " احتراما اینجانب $member_FName $member_LName";
                            echo " به شماره ملی $CodeMeli";
                            echo "صادره از $BirthdayLocation ";
                            echo "متصدی واحد صنفی واقع در $Garage_address ";
                            echo "<br />";
                            echo " دارای پرونده به شماره $javaz ";
                            echo "<br />";
                            echo $ContarctDetial[0]['Text'];
                            echo "</p>";
                            echo "<br />";
                            echo "<br />";
                            echo '<h5 style="text-align: center; color: red;"> << تذکر >> </h5>';
                            echo "<p style='text-align: center; color: red;' > با توجه به اینکه بارکد نرخنامه صرفا مختص به واحد صنفی شما می باشد، از ارائه نرخنامه به دیگران خود داریی نمایید زیرا هرگونه دسترسی غیرمجاز قابل پیگرد قانونی می باشد </p>";
                            echo '<form method="post" enctype="multipart/form-data" onsubmit="return checkCheckBoxes(this);" action ="Sep_init.php">


                            <input type="checkbox" id="agreement" name="agreement" value="1" checked="checked" required />
                            <label for="agreement">' . $ContarctDetial[0]['CheckboxText'] . '</label>

                            <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $InvoiceGUID . '">
                            <input type="hidden" id="mem-ID" name="mem-ID" value="' . $member_GUID . '">
                            <input type="hidden" id="ContractID" name="ContractID" value="' . $ContractID . '">
                            <br />
                            <br />
                            <button class="btn btn-success btn-outline fancy-button btn-0" onclick="validate()"><span class="btn-text"> تایید و پرداخت </span></button>
                            </form>';
                        }else {
                        $url = "Sep_init.php";
                        header("Location: $url");
                        }
                    }else {
                        $url = "Sep_init.php";
                        header("Location: $url");
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="container px-0">
            <!-- Paste your Footer Content from here-->
            <!-- Footer Content-->
            <div class="footer-nav position-relative">
            </div>
        </div>
    </div>
    <!-- All JavaScript Files-->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/default/internet-status.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/default/dark-mode-switch.js"></script>
    <script src="js/ion.rangeSlider.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/default/active.js"></script>
    <script src="js/default/clipboard.js"></script>
    <script type="text/javascript">
        function checkCheckBoxes(theForm) {
            if (theForm.agreement.checked == false) {
                alert(' لطفا با تیک زدن باکس اننهایی توافق خود را با تعهدنامه اعلام نمایید');
                form.agreement.focus();
                return false;
            } else {
                return true;
            }
        }
    </script>
</body>

</html>