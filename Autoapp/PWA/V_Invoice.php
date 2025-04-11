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

require("$rootDir/config/public_conf.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/InvoiceModel.php");
require("$rootDir/Model/TariffModel.php");

use fateh\Finance\Invoice as invoice;
use fateh\Member\Member as member;
use fateh\tariff\tariff as tariff;


$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];

if (!empty($member_GUID)) {
    $Mechanic_Obj = new member($member_GUID);
    $Invoic_Obj = new invoice($member_GUID);
    $Tariff_Obj = new tariff($member_GUID);
    $Mechanic_Info = $Mechanic_Obj->Get_MechanicsInfo_ByID($member_GUID);
    $photo = $Mechanic_Obj->Get_MechanicPhoto_ByID($member_GUID);
    $Invoic = $Invoic_Obj->V_MechanicInvoice_PWA($member_GUID);
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
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                <!-- Navbar Toggler-->
                <div></div>
                <!-- Logo Wrapper-->
                <div class="logo-wrapper" id="affanNavbarToggler"><a id="affanNavbarToggler" href="#"><img src="images/pwa-autoapp-logo.png" alt="logo-autoapp"></a></div>
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
                <path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z"></path>
                <path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z"></path>
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
                    if ($Mechanic_Info[3] == 1) {
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
                        <table class="data-table w-100 dataTable no-footer" id="dataTable" role="grid" aria-describedby="dataTable_info" dir="rtl">
                            <thead>
                                <tr>
                                    <th> موضوع </th>
                                    <th> مبلغ مصوب (ريال) </th>
                                    <th> قابل پرداخت (ريال) </th>
                                    <th> پرداخت </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($Invoic)) {
                                    foreach ($Invoic as $Inv_Value) {
                                        $inv_mem = $Invoic_Obj -> Get_Invoice_ByGUID($Inv_Value['Inv_GUID']);
                                        if (!empty($inv_mem)) {
                                            foreach ($inv_mem as $value) {
                                                echo "<tr>";
                                                //$title = $Tariff_Obj -> getversion($value['Title']);
                                                
                                                $inv_Title = $Invoic_Obj->Get_InvoiceTtitle_ByID ($value['Title']);
                                                if (!empty($inv_Title)){
                                                    echo "<td>" . $inv_Title[0]['NameFa'] . "</td>";
                                                }else{
                                                    echo "<td> بدون موضوع </td>";
                                                }
                                            
                                                echo "<td>" . $value['Comment'] . "</td>";
                                                echo "<td>" . $value['Amount'] . "</td>";
                                                $validation = "onclick='return validation()'";
                                                //IPG_GW.php
                                                #echo '
						                        #    <td style ="width: 5%;">
						        	            #    <form method="post" enctype="multipart/form-data" action ="V_Contract.php">
						        	            #    <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $value['GUID'] . '">
						        	            #    <input type="hidden" id="mem-ID" name="mem-ID" value="' . $member_GUID . '">
						        	            #    <button class="btn btn-success btn-outline fancy-button btn-0"><span class="btn-text"> پرداخت </span></button>
						        	            #    </form></span></td></tr>';

                                                //contact US 

                                                echo '
						                            <td style ="width: 5%;">
						        	                <form method="post" enctype="multipart/form-data" action ="Support-Contact.php">
						        	                <input type="hidden" id="Inv_GUID" name="Inv_GUID" value="' . $value['GUID'] . '">
						        	                <input type="hidden" id="mem-ID" name="mem-ID" value="' . $member_GUID . '">
						        	                <button class="btn btn-success btn-outline fancy-button btn-0"><span class="btn-text"> پرداخت </span></button>
						        	                </form></span></td></tr>';
                                            }
                                        }
                                    }
                                } else {
                                    echo "<tr role='row' class='odd'>";
                                    echo '<td colspan="4" style="text-align: center;">';
                                    echo 'صورت حسابی برای نمایش وجود ندارد';
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