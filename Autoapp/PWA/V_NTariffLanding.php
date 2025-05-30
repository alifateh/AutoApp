<?php
session_start();
if (!isset($_SESSION["Mechanic_GUID"]) || !isset($_SESSION["User_Status"])) {
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
require("$rootDir/Model/BannerModel.php");
require("$rootDir/Model/SliderModel.php");

use fateh\Finance\Invoice as invoice;
use fateh\Finance\Mechanic_Payment as M_invoice;
use fateh\Advertisements\Banners as Banner;
use fateh\Member\Member as member;
use fateh\Advertisements\MainSlideShow as slide;
use fateh\Advertisements\ProductSlideShow as productslide;

$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];
$user_status = $_SESSION["User_Status"];

if (!empty($member_GUID)) {
    $MechanicObj = new member($member_GUID);
    // $Inv_fetch = new invoice();
    $Inv_fetch = new invoice($member_GUID);
    $License = $MechanicObj->Get_MechanicLicense_ByID($member_GUID);

    $mem_info = $MechanicObj->Get_MechanicsInfo_ByID($member_GUID);
    $photo = $MechanicObj->Get_MechanicPhoto_ByID($member_GUID);
    $Inv = $Inv_fetch->V_MechanicInvoice($member_GUID);

    //Banners
    $Banner_Obj = new Banner($member_GUID);
    $Banners = $Banner_Obj->Get_Banners_ByDate();

    //Slide
    $Slide_Obj = new slide($member_GUID);
    $Slides = $Slide_Obj->Get_VisibleSlides();

    //Product Slide
    $ProductSlide_Obj = new productslide($member_GUID);
    $PSlides = $ProductSlide_Obj->Get_VisibleSlides();

    
    //Financial
    $Inv_fetch = new invoice($member_GUID);
    $fetchMechINV = new M_invoice($member_GUID);
    $Inv = $Inv_fetch->V_MechanicInvoice($member_GUID);
    $last_INV = $fetchMechINV -> Get_LastINV();
    $INV_status = 0;
    foreach ($Inv as $key){
        if ($key['Inv_GUID'] == $last_INV[0]['GUID']){
            $INV_status = 1; ///show the invoice notification
            break;
        }else{
            $INV_status = 0 ; 
        }
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
        <!-- Hero Slides-->
        <div class="owl-carousel-one owl-carousel">
            <?php
            if (!empty($Slides)) {
                foreach ($Slides as $key) {
                    echo '<a href="' . $key['LinkAddress'] . '">';
                    echo '<div class="single-hero-slide" style=" background-image:';
                    echo "url('" . $key['FileAddress'] . "')";
                    echo '">';
                    echo "</div></a>";
                }
            }
            ?>
            <!-- Single Hero Slide-->
        </div>
        <div class="affan-features-wrap py-3">
            <div class="container">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card text-center shadow-sm wow fadeInUp" data-wow-duration="1s" style="height: 10rem;">
                            <!-- <a href="V_GarageProfile.php"> -->
                            <a href="V_NTariffAll.php">
                                <div class="card-body"><img src="img/Car-list.png" alt="">
                                    <h6 class="mb-0"> فهرست تعرفه ها </h6>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center shadow-sm wow fadeInUp" data-wow-duration="1s" style="height: 10rem;">
                            <a href="V_TariffAutoWizard.php">
                                <div class="card-body"><img src="img/SearchTariff.png" alt="">
                                    <h6 class="mb-0"> فهرست براساس نام خودروساز و خودرو </h6>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card text-center shadow-sm wow fadeInUp" data-wow-duration="1s" style="height: 10rem;">
                        </div>
                   </div>
                   <div class="col-6">
                        <div class="card text-center shadow-sm wow fadeInUp" data-wow-duration="1s" style="height: 10rem;">
                            <a href="V_TariffTypeWizard.php">
                                <div class="card-body"><img src="img/SearchTariff.png" alt="">
                                    <h6 class="mb-0"> فهرست براساس نوع تعرفه </h6>
                            </a>
                        </div>
                    </div>
                </div>


                <div class="container">
                    <div class="card bg-danger mb-3 shadow-sm element-card wow fadeInUp" data-wow-duration="1s">
                        <div class="card-body">
                            <h2 class="text-white"> محل تبلیغات </h2>
                            <p class="text-white mb-4"> جهت هماهنگی برای سفارش با شماره تعاونی اتحادیه تماس حاصل نمایید
                            </p><a class="btn btn-light" href="Support-Contact.php">شماره تلفن هماهنگی</a>
                        </div>
                    </div>
                </div>
                <!-- Bnners Show -->
                <?php
                for ($i = 0; $i < count($Banners); $i++) {

                    for ($i = 0; $i < 3; $i++) {
                        echo '<div class="container" style="padding:2%">
                                <div class="mb-3 shadow-sm element-card wow fadeInUp" data-wow-duration="2s">
                                    <a class="AdvCounter" data-AdvGUID="' . $Banners[$i]['GUID'] . '" href="' . $Banners[$i]['Link'] . '" >
                                        <img class="img-fluid" src="' . $Banners[$i]['File'] . '" alt="' . $Banners[$i]['Slogn'] . '">
                                    </a>
                                </div>
                             </div>';
                    }
                }
                ?>
                <!-- Bnners Show -->
                <!-- Welcome Toast-->
                <?php

                if (!empty($License)) {
                    if ($License[0]['LicenseStatus'] == 0) {
                        echo
                        '<a href="Support-Contact.php">
                        <div class="toast toast-autohide custom-toast-1 toast-primary home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="20000" data-bs-autohide="true">
                          <div class="toast-body">
                            <div class="toast-text ms-3 me-2">
                              <p class="mb-1 text-white" style="float: right;">عضو محترم</p><small class="d-block" style="float: right;"> مجوز شما منقضی شده است  لطفا جهت تمدید مجوز با پشتیبانی تماس حاصل فرمایید </small>
                              </div>
                              <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
                          </div>
                        </div>
                        </a>';
                    } else {
                        if ($INV_status == 1) {
                            echo
                            '<a href="V_Invoice.php">
                            <div class="toast toast-autohide custom-toast-1 toast-info home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="20000" data-bs-autohide="true">
                              <div class="toast-body">
                                <div class="toast-text ms-3 me-2">
                                  <p class="mb-1 text-white" style="float: right;">عضو محترم</p><small class="d-block" style="float: right;">شما صورت حساب پرداخت نشده دارید</small>
                                  </div>
                                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
                              </div>
                            </div>
                            </a>';
                        }
                    }
                }

                ?>
                <!-- End Toast-->
            </div>
        </div>
        <div class="container" style="padding:2%">
            <div class="card image-gallery-card">
                <div class="card-body">
                    <div class="row gx-2 align-items-end">
                        <div class="col-12">
                            <div class="image-gallery-text mb-4">
                                <h3 class="mb-0"> اتواپ کالا </h3>
                                <p class="mb-0"> منتخب جدید ترین کالاها</p>
                            </div>
                        </div>
                        <div class="col-4 text-end"><a class="btn btn-primary btn-sm mb-4" href="https://shop.autoapp.ir/">
                                مشاهده </a></div>
                    </div>
                    <div class="image-gallery-carousel owl-carousel">
                        <!-- Single Image Gallery-->
                        <!-- Gallery Image-->
                        <?php
                        if (!empty($PSlides)) {

                            foreach ($PSlides as $key) {
                                echo '<div class="single-image-gallery">
                                           <a class="AdvCounter gallery-img2" data-AdvGUID="' . $key['GUID'] . '" href="' . $key['LinkAddress'] . '"  data-effect="mfp-zoom-in">
                                               <img class="img-fluid" src="' . $key['FileAddress'] . '" alt="' . $key['Slogn'] . '">
                                           </a>
                                    </div>';
                            }
                        }
                        ?>
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
    <script>
        var clicks = document.querySelectorAll('.AdvCounter'); // IE8
        for (var i = 0; i < clicks.length; i++) {
            clicks[i].onclick = function() {
                var id = this.getAttribute('data-AdvGUID');
                var post = 'AdvGUID=' + id; // post string
                var req = new XMLHttpRequest();
                req.open('POST', 'V_Click_Ajax.php', true);
                req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                req.send(post);
            }
        }
    </script>
</body>

</html>