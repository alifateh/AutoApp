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
require("$rootDir/Model/N_PhoneContactModel.php");

use fateh\Member\Member as member;
use fateh\Member\Mechanic_Member as Mechanic;
use fateh\phonebook\MechanicContact as Tell;

$member_GUID = $_SESSION["Mechanic_GUID"];
$member_username = $_SESSION["username"];
$member_FName = $_SESSION["User_FName"];
$member_LName = $_SESSION["User_LName"];


if (isset($_SESSION["Mechanic_GUID"]) && !empty($member_GUID)) {
  $mem = new member($member_GUID);
  $Mechanic = new Mechanic($member_GUID);
  $Contac_Obj = new Tell($_SESSION["Mechanic_GUID"]);


  $mem_info = $mem->Get_MechanicsInfo_ByID($member_GUID);
  $photo = $mem->Get_MechanicPhoto_ByID($member_GUID);
  $Detials = $mem->GetMemDetials($member_GUID);
  $Birthday = $mem->Get_DateHejri($Detials[0]);
  $Per_Birthday = gregorian_to_jalali($Birthday[0], $Birthday[1], $Birthday[2]);
  $address = $mem->GetMemAddress($member_GUID);
  $files = $mem->GetMemFiles($member_GUID);
  $License = $mem->Get_MechanicLicense_ByID($member_GUID);
  $Devices = $Mechanic->listDevices($member_GUID);
  $skilles = $Mechanic->listSkilles($member_GUID);
  $phone = $Contac_Obj->Get_MechanicContact_ByID($member_GUID);
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
      <div class="card user-info-card m-3">
        <div class="card-body d-flex align-items-center">
          <img class="rounded" style="height: 250px;width:250px;" src="<?php echo $photo[0]['Path']; ?>" alt="">

        </div>
        <div class="user-info">
          <div class="d-flex align-items-center" dir="rtl">
            <h5 class="p-2"><?php echo $member_FName . " " . $member_LName; ?></h5>
          </div>
        </div>
        <div class="user-info p-1" dir="rtl">

          <span>عضو محترم اتحادیه صنف تعمیرکاران خودرو تهران <br />
            <?php
            if ($mem_info[3] == 1) {
              echo 'خوش آمدید';
            } else {
              echo '<h6 style="color : #ff3535;"> حساب شما فعال نیست </h6> ';
            }
            ?>
          </span>
          <div class="rating"><span> امتیاز شما </span><a href="#"><i class="fa fa-star"></i></a><a href="#"><i class="fa fa-star"></i></a><a href="#"><i class="fa fa-star"></i></a><a href="#"><i class="fa fa-star"></i></a><a href="#"><i class="fa fa-star"></i></a></div>
        </div>
      </div>
    </div>
    <!-- User Meta Data-->
    <div class="card mb-3 rounded-0 rounded-bottom">
      <div class="card-body">
        <div class="accordion accordion-style-two" id="accordionStyle2">
          <!-- Single accordion-->
          <div class="accordion-item">
            <div class="accordion-header" id="accordionFour">
              <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleFour" aria-expanded="true" aria-controls="accordionStyleFour" class=""><i class="fa fa-plus"></i>مشخصات هویتی</h6>
            </div>
            <div class="accordion-collapse collapse show" id="accordionStyleFour" aria-labelledby="accordionFour" data-bs-parent="#accordionStyle2">
              <div class="accordion-body">
                <div class="card user-data-card">
                  <div class="card-body">
                    <label class="form-label" dir="rtl">تاریخ تولد : </label>
                    <ul class="list-group">
                      <li class="list-group-item active"><?php echo $Per_Birthday[0] . "/" . $Per_Birthday[1] . "/" . $Per_Birthday[2]; ?> </li>
                    </ul>
                    <label class="form-label" dir="rtl"> کد ملی :</label>
                    <ul class="list-group">
                      <li class="list-group-item active"> <?php echo $Detials[1]; ?> </li>
                    </ul>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <!-- Single accordion-->
          <div class="accordion-item">
            <div class="accordion-header" id="accordionFive">
              <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleFive" aria-expanded="false" aria-controls="accordionStyleFive"><i class="fa fa-plus"></i> اطلاعات تماس </h6>
            </div>
            <div class="accordion-collapse collapse" id="accordionStyleFive" aria-labelledby="accordionFive" data-bs-parent="#accordionStyle2">
              <div class="accordion-body">
                <label class="form-label" dir="rtl"> آدرس محل سکونت : </label>
                <ul class="list-group">
                  <li class="list-group-item active"><?php echo $address[0]; ?></li>
                </ul>
                <?php
                if(!empty($phone) && count($phone) > 0){
                  echo '<label class="form-label" dir="rtl" > شماره های تماس : </label>
                          <ul class="list-group">';
                  foreach ($phone as $value) {
                    
                    if ($value['Mobile'] == 0) {
                      echo '<li class="list-group-item active"> تلفن محل سکونت : ' . $value['Number'] . '</li>';
                    }
                    if ($value['Mobile'] == 1) {
                      echo '<li class="list-group-item active"> تلفن همراه : '. $value['Number'] . '</li>';
                    }
                  }

                  echo "</ul>";

                }

                ?>

              </div>
            </div>
          </div>
          <!-- Single accordion-->
          <div class="accordion-item">
            <div class="accordion-header" id="accordionSix">
              <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleSix" aria-expanded="false" aria-controls="accordionStyleSix"><i class="fa fa-plus"></i> تخصص ها </h6>
            </div>
            <div class="accordion-collapse collapse" id="accordionStyleSix" aria-labelledby="accordionSix" data-bs-parent="#accordionStyle2">
              <div class="accordion-body">
                <label class="form-label" dir="rtl"> تخصص ها : </label>
                <!-- List group active & disabled state -->
                <ul class="list-group">
                  <?php
                  if (!$skilles) {
                    echo '<li class="list-group-item active"><a href ="Support-Contact.php" > موردی ثبت نشده
                          <h6> جهت تکمیل پرونده با پشتیبانی تماس حاصل بفرمایید </h6>
                           </a>
                           </li>';
                  } else {
                    foreach ($skilles as $value) {
                      echo '<li class="list-group-item active">' . $value . '</li>';
                    }
                  }

                  ?>
                </ul>


                <label class="form-label" dir="rtl"> دستگاه های تخصصی : </label>

                <!-- List group active & disabled state -->
                <ul class="list-group">
                  <?php
                  if (!$Devices) {
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