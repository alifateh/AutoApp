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
require ('Model/N_TariffModel.php');

use fateh\Member\Mechanic_Member as Mechanic;
use fateh\tariff\NewTariff as NewTariff;

$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];


if (!empty($member_GUID)) {
    $Mechanic = new Mechanic($member_GUID);
    ///Get_TariffTypelist_ByMechanic

    $TariffTypelist = $Mechanic->Get_TariffTypelist_ByMechanic($member_GUID);
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
    <script>
        function reset_options() {
            document.getElementById('AutoManufacture').options.length = 0;
            document.getElementById('AutoID').options.length = 0;
            return true;
        }

        // Function to reload the page
        function reloadPage() {
            location.reload();
        }

        // Attach event listener to the popstate event
        window.addEventListener('popstate', reloadPage);

        // Function to disable the popstate event
        function disableBackReload() {
            window.removeEventListener('popstate', reloadPage);
        }

        // Attach event listener to the page unload event
        window.addEventListener('unload', disableBackReload);
    </script>
</head>

<body onbeforeunload='reset_options()'>
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
            <div class="user-profile"><img src="<?php echo $photo; ?>" alt=""></div>
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



    <div class="page-content-wrapper">

        <br>
        <div class="container wow fadeInUp text-center" data-wow-duration="3s">
            <div class="alert custom-alert-3 alert-success alert-dismissible text-center fade show shadow-lg"
                role="alert">
                <div class="text-center">
                    <h4 class="text-center"> عدم نمایش نوع خاصی از تعرفه </h4>
                    <p class="text-center"> همکار گرامی ؛ <br />
                        چنانچه در لیست زیر نوع تعرفه مربوط به شخص خود را نمی بینید می توانید جهت تصحیح اطلاعات ثبت نامی
                        خود با اتحادیه تماس حاصل فرمایید<br />
                        <a href="tel:02191005525"> <span style="color: blue;">اتحادیه تعمیرکاران خودرو تهران</span>
                            <a href="tel:02191005525">021-91005525</a>
                        </a>
                    </p>
                    <a class="btn btn-sm btn-creative btn-info mt-3" href="tel:02191005525">
                        تماس با اتحادیه
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="page-content-wrapper py-3">
        <!-- User Meta Data-->
        <div class="card mb-3 rounded-0 rounded-bottom">
            <div class="card-body">
                <div class="accordion accordion-style-two" id="accordionStyle2">
                    <!-- Single accordion-->
                    <div class="accordion-item">
                        <div class="accordion-header" id="accordionFour">
                            <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleFour" aria-expanded="true"
                                aria-controls="accordionStyleFour" class=""><i class="fa fa-plus"></i> فیلترهای تعرفه
                            </h6>
                        </div>
                        <div class="accordion-collapse collapse show" id="accordionStyleFour"
                            aria-labelledby="accordionFour" data-bs-parent="#accordionStyle2">
                            <div class="accordion-body">
                                <div class="card user-data-card">
                                    <div class="card-body">
                                        <form method="post" enctype="multipart/form-data"
                                            action="V_NTariffBasedonTyepe.php">
                                            <!-- Large Select -->
                                            <div class="form-group">
                                                <label class="form-label" for="MembersTarrif"> نوع تعرفه </label>
                                                <select class="form-select form-select-lg" id="MembersTarrif"
                                                    name="MembersTarrif" aria-label="Manufacture" autocomplete="off"
                                                    style="direction :rtl;">

                                                    <option value=""> انتخاب نمایید </option>
                                                    <?php
                                                    /// Get_TariffType_ByID
                                                    
                                                    if (!empty($TariffTypelist)) {
                                                        $Tariff_obj = new NewTariff($member_GUID);

                                                        foreach ($TariffTypelist as $row) {
                                                            $TariffName = $Tariff_obj->Get_TariffType_ByID($row['TariffTypeGUID']);

                                                            echo '<option value="' . $row['TariffTypeGUID'] . '" >' . $TariffName[0]['NameFa'] . ' </option>';
                                                        }
                                                    } else {
                                                        echo '<option value="" selected> خودرو سازی تعریف نشده است </option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit"
                                                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center"
                                                    type="button" value=" نمایش ">

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
    <!--
  <script src="js/imagesloaded.pkgd.min.js"></script>
  -->
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/default/dark-mode-switch.js"></script>
    <script src="js/ion.rangeSlider.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/default/active.js"></script>
    <script src="js/default/clipboard.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#AutoID').on('change', function () {
                var AutoID = $(this).val();
                if (AutoID) {
                    $.ajax({
                        type: 'POST',
                        url: 'V_AutoImg_Ajax.php',
                        data: 'Auto_ID=' + AutoID,
                        success: function (html) {
                            $('#AutoIMG').html(html);
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
            $('#AutoManufacture').on('change', function () {
                var manID = $(this).val();
                if (manID) {
                    $.ajax({
                        type: 'POST',
                        url: 'V_Auto_Ajax.php',
                        data: 'man_id=' + manID,
                        success: function (html) {
                            $('#AutoID').html(html);
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
            $('#AutoManufacture').on('change', function () {
                var manID = $(this).val();
                if (manID) {
                    $.ajax({
                        type: 'POST',
                        url: 'V_ManufactureLogo_Ajax.php',
                        data: 'man_id=' + manID,
                        success: function (html) {
                            $('#Man_LOGO').html(html);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>