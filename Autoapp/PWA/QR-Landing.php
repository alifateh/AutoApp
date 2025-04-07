<?php
session_start();
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/MemberModel.php");
require("$rootDir/Model/GarageModel.php");

use fateh\Member\Member as member;
use fateh\Member\Mechanic_Member as Mechanic;
use fateh\AutoShop\AutoShop as garage;



    if (!isset($_SESSION["Mechanic_GUID"]) && !isset($_SESSION["qr_guid"])) {
        $_SESSION["qr_guid"] = $_GET["id"];
        $url = "/";
        header("Location: $url");
    }else {
        $member_GUID = $_SESSION["Mechanic_GUID"];
        if(isset($_SESSION["qr_guid"])){
            $qr = $_SESSION["qr_guid"];
        }elseif(isset($_GET["id"])){
            $qr = $_GET["id"];
            $_SESSION["qr_guid"] = $_GET["id"];
        }else{
            $url = "/";
            header("Location: $url");
        }
        $Mechanic_Obj = new member($member_GUID);
        $qr_Mechanic = new Mechanic($member_GUID);
        $Garage_obj = new garage($member_GUID);
    
        $mem_info = $Mechanic_Obj->Get_MechanicsInfo_ByID($member_GUID);
        $photo = $Mechanic_Obj->Get_MechanicPhoto_ByID($member_GUID);
        $Detials = $Mechanic_Obj->GetMemDetials($member_GUID);
    
        $files = $Mechanic_Obj->GetMemFiles($qr);
        $qr_info = $Mechanic_Obj->Get_MechanicsInfo_ByID($qr);
        $qr_Detials = $Mechanic_Obj->GetMemDetials($qr);
        $qr_address = $Mechanic_Obj->GetMemAddress($qr);
    
    
        $qr_Devices = $qr_Mechanic->listDevices($qr);
        $qr_skilles = $qr_Mechanic->listSkilles($qr);
        $qr_license = $qr_Mechanic->Get_MechanicLicense_ByID($qr);
    
        $garage = $Garage_obj->GetOwnerGarage($qr);
        $garage_info = $Garage_obj->Get_GarageInfo_ByID($garage[0]);
        $Garage_address = $Garage_obj->Get_GarageAddress_ByID($garage[0]);
        $_SESSION["qr_guid"] ="";
        unset($_SESSION['qr_guid']);

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
    <div class="page-content-wrapper py-3">
        <div class="container">
            <!-- User Information-->
            <!-- User Meta Data-->
            <div class="card mb-3 rounded-0 rounded-bottom">
                <div class="card-body">
                    <div class="accordion accordion-style-two" id="accordionStyle2">
                        <!-- Single accordion-->
                        <div class="accordion-item">
                            <div class="accordion-header" id="accordionFour">
                                <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleFour" aria-expanded="true" aria-controls="accordionStyleFour" class=""><i class="fa fa-plus"></i>مشخصات هویتی
                                </h6>
                            </div>
                            <div class="accordion-collapse collapse show" id="accordionStyleFour" aria-labelledby="accordionFour" data-bs-parent="#accordionStyle2">
                                <div class="accordion-body">
                                    <div class="card user-data-card">
                                        <div class="card-body">
                                            <label class="form-label" dir="rtl"> صاحب نرخنامه
                                            </label>
                                            <ul class="list-group">
                                                <li class="list-group-item active">
                                                    <?php
                                                    echo " نام : $qr_info[0]";
                                                    ?>
                                                </li>
                                                <li class="list-group-item active">
                                                    <?php
                                                    echo " نام خانوادگی : $qr_info[1]";
                                                    ?>
                                                </li>
                                            </ul>
                                            <label class="form-label" dir="rtl"> شماره جواز </label>
                                            <ul class="list-group">
                                                <li class="list-group-item active"> <?php echo $qr_license[0]['LicenseNum']; ?>
                                                </li>
                                            </ul>
                                            <label class="form-label" dir="rtl"> نام تعمیرگاه </label>
                                            <ul class="list-group">
                                                <li class="list-group-item active"> <?php echo $garage_info[1]; ?> </li>
                                            </ul>
                                            <label class="form-label" dir="rtl"> آدرس تعمیرگاه </label>
                                            <ul class="list-group">
                                                <li class="list-group-item active"> <?php echo $Garage_address[1]; ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- Single accordion-->
                        <div class="accordion-item">
                            <div class="accordion-header" id="accordionSix">
                                <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleSix" aria-expanded="false" aria-controls="accordionStyleSix"><i class="fa fa-plus"></i>
                                    تخصص
                                    ها </h6>
                            </div>
                            <div class="accordion-collapse collapse" id="accordionStyleSix" aria-labelledby="accordionSix" data-bs-parent="#accordionStyle2">
                                <div class="accordion-body">
                                    <label class="form-label" dir="rtl"> تخصص ها : </label>
                                    <!-- List group active & disabled state -->
                                    <ul class="list-group">
                                        <?php
                                        if (!empty($qr_skilles)) {
                                            echo '<li class="list-group-item active"><a href ="Support-Contact.php" > موردی ثبت نشده
                                               <h6> جهت تکمیل پرونده با پشتیبانی تماس حاصل بفرمایید </h6>
                                                </a>
                                                </li>';
                                        } else {
                                            foreach ($qr_skilles as $value) {
                                                echo '<li class="list-group-item active">' . $value . '</li>';
                                            }
                                        }

                                        ?>
                                    </ul>


                                    <label class="form-label" dir="rtl"> دستگاه های تخصصی : </label>

                                    <!-- List group active & disabled state -->
                                    <ul class="list-group">
                                        <?php
                                        if (!empty($Devices)) {
                                            echo '<li class="list-group-item active"><a href ="Support-Contact.php" > موردی ثبت نشده
                                                 <h6> جهت تکمیل پرونده با پشتیبانی تماس حاصل بفرمایید </h6>
                                                 </a>
                                                </li>';
                                        } else {
                                            foreach ($Devices as $value) {
                                                echo '<li class="list-group-item active">' . $value . '</li>';
                                            }
                                        }

                                        ?>
                                    </ul>

                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <!-- Element Heading-->
                            <div class="divider divider-center-icon border-primary"><i class="fa fa-cog"></i></div>

                            <div class="element-heading">
                                <h6> مدارک و گواهی نامه ها </h6>
                            </div>

                            <div class="card">
                                <div class="card image-gallery-card">
                                    <div class="card-body">
                                        <?php

                                        if (isset($files)) {
                                            echo ' <!-- Gallery Wrapper-->
                                                   <div class="gallery-wrapper row g-3" >
                                                   <div class="col-6">
			                         			  <div class="single-image-gallery Documents" style="position: inherit !important;">';
                                            foreach ($files as $value) {
                                                echo '<a class="gallery-img" href="' . $value . '" data-effect="mfp-zoom-in"><img class="img-responsive" src="' . $value . '" alt=" مدارک "></a>';
                                            }
                                            echo '</div></div></div>';
                                        } else {
                                            echo '
                                                 <p dir="rtl">
                                                 <div class="element-heading">
                                                   <h5> مدارک دوره های تخصصی شما ثبت نشده است </h5>
                                                 </div>
                                                   <a href ="Support-Contact.php" >
                                                     <h6> جهت تکمیل پرونده با پشتیبانی تماس حاصل بفرمایید </h6>
                                                   </a>
                                                 </P>';
                                        }


                                        ?>

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
</body>

</html>