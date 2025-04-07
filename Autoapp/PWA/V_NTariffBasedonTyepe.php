<?php
session_start();
if (!isset($_SESSION["Mechanic_GUID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "/";
    header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require ("$rootDir/config/public_conf.php");
require ("$rootDir/Model/MemberModel.php");
require ("$rootDir/Model/TariffModel.php");
require ("$rootDir/Model/InvoiceModel.php");
require ("$rootDir/Model/AutoModel.php");
require ("$rootDir/Model/N_TariffModel.php");

use fateh\Finance\Invoice as invoice;
use fateh\tariff\tariff as tariff;
use fateh\Member\Member as member;
use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];


if (!empty($member_GUID) && !empty($_POST['MembersTarrif'])) {

    $Mechanic_Obj = new member($_SESSION["Mechanic_GUID"]);
    $Invoice_Obj = new invoice($_SESSION["Mechanic_GUID"]);
    $Tariff_fetch = new tariff($_SESSION["Mechanic_GUID"]);
    $Tariff_Obj = new NewTariff($_SESSION["Mechanic_GUID"]);
    $Auto_Obj = new auto($_SESSION["Mechanic_GUID"]);

    $mem_info = $Mechanic_Obj->Get_MechanicsInfo_ByID($member_GUID);
    $mem_License = $Mechanic_Obj->Get_MechanicLicense_ByID($member_GUID);
    $photo = $Mechanic_Obj->Get_MechanicPhoto_ByID($member_GUID);
    $LastNTariffVer = $Tariff_Obj->Get_NTariffLastVersion();

    // uncomment after 2024-04-12
    //$Invoice = $Invoice_Obj->getPayedTariff($member_GUID, 4);
    $Invoice = $Invoice_Obj->getPayedTariff($member_GUID, $LastNTariffVer[0]['ID']);


    if (!empty($Invoice)) {
        $data = $Tariff_Obj->V_NTariff_ByMechanicTariffType($_SESSION["Mechanic_GUID"], $_POST['MembersTarrif'], $Invoice[0]['Title']);
        
        if (!empty($data)) {
            $Tariff_credential = 1;
        } else {
            $Tariff_credential = 2; //تعرفه ایی برای خودرو تعریف نشده است
        }

        ///get tariff version from GUID and find ID then V_NTariffAll by ID
        $Mechanic_TTariff = $Tariff_Obj->Get_TariffType_ByMemberID($_SESSION["Mechanic_GUID"]);
        $internal = 0;
        $external = 0;

        foreach ($Mechanic_TTariff as $row) {
            if ($row['TariffTypeGUID'] == 'f8ebae71abffe8be025264761dc90d76') {
                $internal = 1;
            }
            if ($row['TariffTypeGUID'] == '49b983525af5d31711fa6bebb0fd7b86' || $row['TariffTypeGUID'] == 'd0cc0f11f9e071737346cde7a9ab4eb2') {
                $external = 1;
            }
        }
    } else {
        $Tariff_credential = 0; //نمایش صورت حساب
    }
} else {
    $url = "Dashboard.php";
    header("Location: $url");
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
            <!-- # Copy the code from here ...-->
            <!-- Header Content-->
            <div
                class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                <!-- Navbar Toggler-->
                <div></div>
                <!-- Logo Wrapper-->
                <div class="logo-wrapper" id="affanNavbarToggler"><a id="affanNavbarToggler" href="#"><img
                            src="images/pwa-autoapp-logo.png" alt="logo-autoapp"></a></div>
                <!-- Navbar Toggler-->
                <div></div>
            </div>
            <!-- # Header Five Layout End-->
        </div>
    </div>
    <!-- Sidenav Black Overlay-->
    <div class="sidenav-black-overlay"></div>
    <!-- Side Nav Wrapper-->
    <div class="sidenav-wrapper right-side-mode" id="sidenavWrapper">
        <!-- Go Back Button-->
        <div class="go-back-btn" id="goBack">
            <svg class="bi bi-x" width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z"></path>
                <path fill-rule="evenodd"
                    d="M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z"></path>
            </svg>
        </div>
        <!-- Sidenav Profile-->
        <div class="sidenav-profile">
            <div class="sidenav-style1"></div>
            <!-- User Thumbnail-->
            <div class="user-profile"><img src="<?php echo $photo[0]['Path']; ?>" alt=""></div>
            <!-- User Info-->
            <div class="user-info">
                <h6 class="user-name mb-0"><?php echo $member_FName . " " . $member_LName; ?></h6>
                <span>عضو محترم اتحادیه صنف تعمیرکاران خودرو تهران <br />
                    <?php
                    if ($mem_info[3] == 1) {
                        echo 'خوش آمدید';
                    } else {
                        echo '<h6 style="color : #ff3535;"> حساب شما فعال نیست </h6> ';
                    }
                    ?>
                </span>
            </div>
        </div>
        <!-- Sidenav Nav-->
        <?php MainMenu(); ?>
        <!-- Social Info-->
        <div class="social-info-wrap"><img src="images/logo-etehadie.png" style="width: 50%;" alt="logo-autoapp"></div>
        <!-- Copyright Info-->
        <?php footer(); ?>
    </div>
    <div class="page-content-wrapper py-3">
        <div class="container">
            <!-- Element Heading-->
            <div class="element-heading">
            </div>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div id="dataTable_wrapper" class="dataTables_wrapper no-footer">
                        <table class="data-table w-100 dataTable no-footer" id="dataTable" role="grid"
                            aria-describedby="dataTable_info" dir="rtl">
                            <thead>
                                <tr>
                                    <th> مشخصات خودرو </th>
                                    <th> نوع تعرفه </th>
                                    <th> عملیات </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($mem_License) && $mem_License[0]['LicenseStatus'] != 0) {
                                    if ($Tariff_credential == 1) {
                                        if ($internal == 1 && $external == 1) {

                                            // foreach ($tarrif as $row) {
                                            //     $auto = $Tariff_fetch->gettariffauto($row['AutomobileID']);
                                            //     $Internal_Auto = $Auto_Obj->Get_AutoDetial_ByID($row['AutomobileID']);
                                            //     if ($Internal_Auto[0]['internalproduct'] == 1) {
                                            //         $Auto_Type = "خودروهای سبک داخلی";
                                            //     } else {
                                            //         $Auto_Type = "خودروهای سبک خارجی";
                                            //     }
                                            //     $auto_name = $auto[2];
                                            //     $auto_tipID = $auto[1];
                                            //     $auto_manID = $auto[0];
                                            //     $tip = $Tariff_fetch->gettariffautotip($auto_tipID);
                                            //     $auto_manName = $Tariff_fetch->gettariffautoman($auto_manID);
                                            //     echo "<tr>";
                                            //     echo "<td> [ " . $auto_manName . " ] &nbsp; [ " . $auto_name . " ] &nbsp; [ " . $tip . " ]</td>";
                                            //     echo "<td> $Auto_Type </td>";
                                            //     echo '<td>
                                            //             <form method="post" enctype="multipart/form-data" action="PDFGenerator.php">
                                            //             <input type="hidden" name="tariff-ID" value="' . $row['ID'] . '">
                                            //             <input type="hidden" name="tariff-SecID" value="' . $row['SecID'] . '">
                                            //             <input type="hidden" name="pdf" value="pdf">
                                            //             <button class="btn btn-primary">
                                            //             دریافت PDF
                                            //             <svg width="16" height="16" viewBox="0 0 16 16" class="bi bi-arrow-down me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            //             <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                            //             </svg>
                                            //             </button>
                                            //             </form>
                                            //         </td></tr>';
                                            // }
                                
                                            foreach ($data as $key) {

                                                $auto = $Auto_Obj->Get_Automobile_ByID($key['AutoGUID']);
                                                $auto_name = " نام خودرو: " . $auto[0]['Name'];
                                                $auto_tipID = $auto[0]['ModelID'];
                                                $auto_manID = $auto[0]['ManufacturerID'];
                                                $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
                                                $tip = " تیپ :" . $auto_tipName[0]['ModelName'];
                                                $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
                                                $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];
                                                $NTariffType = $Tariff_Obj->Get_TariffType_ByID($key['TariffTypeGUID']);

                                                echo "<tr>";
                                                echo "<td> [" . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . "]</td>";
                                                echo "<td>" . $NTariffType[0]['NameFa'] . "</td>";
                                                echo '<td>
                                                        <form method="post" enctype="multipart/form-data" action="C_NTariffPDF.php">
                                                        <input type="hidden" name="NTariffGUID" value="' . $key['TGUID'] . '">
                                                        <input type="hidden" name="action" value="pdf">
                                                        <button class="btn btn-primary">
                                                         دریافت PDF
                                                         <svg width="16" height="16" viewBox="0 0 16 16" class="bi bi-arrow-down me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                         <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                                         </svg>
                                                         </button>
                                                        </form>';
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                    if ($Tariff_credential == 2) {
                                        echo '<tr><td colspan="3" style="text-align: center;"><a href="#"> تعرفه ایی برای خودروی مدنظر تعریف نشده است </a></td> </tr>';
                                    }
                                    if ($Tariff_credential == 0) {
                                        echo '<tr><td colspan="3" style="text-align: center;"><a href="V_Invoice.php"> شما صورت حسابی برای پرداخت دارید  </a></td> </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3" style="text-align: center;"><a href="Support-Contact.php"> مجوز شما منقضی شده است لطفا جهت تمدید جواز با پشتیبانی تماس بگیرید </a></td> </tr>';
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
    <!-- Footer Nav-->
    <div class="footer-nav-area" id="footerNav">
        <div class="container px-0">
            <!-- Paste your Footer Content from here-->
            <!-- Footer Content-->
            <?php FooterMenu(); ?>
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
    <!-- PWA-->
    <script src="js/pwa.js"></script>
</body>

</html>