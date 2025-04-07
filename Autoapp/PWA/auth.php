<?php
session_start();
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/LoginModel.php");

use fateh\login\Member as member;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $New_member = new member();
    if (!isset($_SESSION["qr_guid"])) {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $ReturnVal = $New_member->login_Mechanic($_POST["username"], $_POST["password"]);
            if ($ReturnVal == 10) {
                $url = "Dashboard.php";
                header("Location: $url");
            } elseif ($ReturnVal == 9) {
                $Error_STR = 9;
            } elseif ($ReturnVal == 8) {
                $Error_STR = 8;
            } elseif ($ReturnVal == 7) {
                $Error_STR = 7;
            } else {
                $Error_STR = 1;
            }
        }
    } else {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $ReturnVal = $New_member->login_Mechanic($_POST["username"], $_POST["password"]);
            if ($ReturnVal == 10) {
                $url = "QR-Landing.php";
                header("Location: $url", true, 301);
            } elseif ($ReturnVal == 9) {
                $Error_STR = 9;
            } elseif ($ReturnVal == 8) {
                $Error_STR = 8;
            } elseif ($ReturnVal == 7) {
                $Error_STR = 7;
            } else {
                $Error_STR = 1;
            }
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
    <!-- Favicon-->
    <link rel="icon" href="img/favicon.ico">
    <link rel="apple-touch-icon" href="img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="img/icons/icon-167x167.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/icons/icon-180x180.png">

    <!-- IOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="green">
    <meta name="apple-mobile-web-app-title" content="FreeCodeCamp">
    <link rel="apple-touch-icon" href="img/icons/icon-72x72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="img/icons/icon-96x96.png" sizes="96x96">
    <link rel="apple-touch-icon" href="img/icons/icon-128x128.png" sizes="128x128">
    <link rel="apple-touch-icon" href="img/icons/icon-144x144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="img/icons/icon-152x152.png" sizes="152x152">
    <link rel="apple-touch-icon" href="img/icons/icon-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon" href="img/icons/icon-384x384.png" sizes="384x384">
    <link rel="apple-touch-icon" href="img/icons/icon-512x512.png" sizes="512x512">
    <!-- IOS -->
    <!-- Core Stylesheet-->
    <link rel="stylesheet" href="Login_Style.css">
    <style>
        .sr-only {
            font-family: "Fatehchehr";
        }

        .stretched-link {
            font-family: "Fatehchehr";
        }

        .btn {
            font-family: "Fatehchehr";
        }
    </style>
    <!-- Web App Manifest-->
    <link rel="manifest" href="manifest.json">
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
    <!-- Login Error Toast-->
    <?php

    if (!empty($Error_STR)) {
        if ($Error_STR == 1) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="10000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <p class="mb-1" style="float: right;">خطا درسمت سرور </p> <br /> <small class="d-block" style="float: right;">مشکلی در سرور پیدا شده است</small>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } elseif ($Error_STR == 2) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <h5 class="mb-1" style="float: right;">خطا در کاربری </h5> <br /> <p><small class="d-block" style="float: right;"> رمز عبور وارد نشده </small></p>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } elseif ($Error_STR == 3) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <h5 class="mb-1" style="float: right;">خطا در کاربری </h5> <br /> <p><small class="d-block" style="float: right;"> نام کاربری ایجاد نشده </small></p>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } elseif ($Error_STR == 9) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <h5 class="mb-1" style="float: right;">خطا در کاربری </h5> <br /> <p><small class="d-block" style="float: right;"> کاربر فعال نشده است </small></p>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } elseif ($Error_STR == 8) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <p class="mb-1" style="float: right;">خطا در کاربری </p> <br /> <small class="d-block" style="float: right;"> رمز عبور اشتباه است </small>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } elseif ($Error_STR == 7) {
            echo
            '<div class="toast toast-autohide custom-toast-1 toast-warning home-page-toast fade hide " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000" data-bs-autohide="true">
              <div class="toast-body">
                <div class="toast-text ms-3 me-2">
                  <p class="mb-1" style="float: right;">خطا در کاربری </p> <br /> <small class="d-block" style="float: right;"> کاربری با این نام پیدا نشد </small>
                  </div>
                  <button class="btn btn-close btn-close-white position-relative p-1 ms-auto" type="button"data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <span class="toast-autohide-line-animation" style="animation-duration: 20000ms;"></span>
              </div>
            </div>';
        } else {
            $Error_STR = Null;
        }
    }
    ?>
    <!-- Login Wrapper Area-->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-xl-5">
                    <div class="text-center px-4"><img class="login-intro-img" src="images/pwa-autoapp-logo.png" alt="Autoapp"></div>
                    <!-- Register Form-->
                    <div class="register-form mt-4 px-4">
                        <form method="post">
                            <div class="form-group text-start mb-3">
                                <input class="form-control" style="font-size: 25px;" type="text" placeholder="کد ملی" required="" name="username" id="username" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                            </div>
                            <div class="form-group text-start mb-3">
                                <input class="form-control" style="font-size: 25px;" type="password" placeholder="رمزعبور" required="" name="password" id="password" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                            </div>
                            <button class="btn btn-primary w-100" type="submit" name="login-btn" id="login-btn" style="font-size: 25px;"> ورود
                            </button>
                        </form>
                    </div>
                    <!-- Login Meta-->
                    <div class="login-meta-data text-center"><a class="stretched-link forgot-password d-block mt-3 mb-1" href="Support-Contact.php"> ثبت نام </a>
                        <p class="mb-0"><a class="ms-1 stretched-link" href="privacy-policy.php">
                                قوانین استفاده
                            </a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3">
                                            <img referrerpolicy="origin" id="jxlzrgvjnbqesizpfukzesgt" class="card-img-top" style="cursor:pointer" onclick="window.open('https://logo.samandehi.ir/Verify.aspx?id=132960&amp;p=rfthxlaouiwkpfvlgvkaobpd;', ';Popup;', ';toolbar=no', scrollbars=no, location=no, statusbar=no, menubar=no, resizable=0, width=450, height=630, 'top=30;')" alt="logo-samandehi" src="https://pwa.autoapp.ir/img/saman-logo.png">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3">
                                            <a target="_blank" href="https://trustseal.enamad.ir/?id=80070&amp;Code=6U7NTt5xOLOaJ1LkmB9p" referrerpolicy="origin">
                                                <img class="card-img-top" src="https://autoapp.ir/star1.png" src="https://Trustseal.eNamad.ir/logo.aspx?id=80070&amp;Code=6U7NTt5xOLOaJ1LkmB9p" alt="Autoapp">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3">
                                            <a onclick="window.open('https://ecunion.ir/verify/banimode.com?token=19111185c2a0339984fd', 'Popup','toolbar=no, location=no, statusbar=no, menubar=no, scrollbars=1, resizable=0, width=580, height=600, top=30')">
                                                <img src="https://www.banimode.com//themes/new/assets/images/footer/logo-itehad@2x.png" alt="اتحادیه کسب و کارهای مجازی" class="card-img-top">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3">
                                            <a href="http://www.otaghasnaftehran.ir/" target="_blank">
                                                <img src="https://autoapp.ir/asnaf-logo.png" class="card-img-top" alt="اتاق اصناف ایران" title="اتاق اصناف ایران">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3">
                                            <a href="https://eanjoman.ir/" target="_blank">
                                                <img src="https://autoapp.ir/eanjoman.png" class="card-img-top" alt="انجمن صنفی کسب و کار اینترنتی" title="انجمن صنفی کسب و کار اینترنتی">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <!-- Partner Slide Card-->
                                    <div class="card partner-slide-card border bg-gray">
                                        <div class="card-body p-3"><a href="https://www.autoapp.ir/" target="_blank">
                                                <img src="https://adminpanel.autoapp.ir/images/fava-250X250-gray.png" class="card-img-top" alt="شرکت تعاونی تعمیرکاران خودرو فاوا" title="شرکت تعاونی تعمیرکاران خودرو فاوا">
                                            </a>
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
    <script>
        if ('serviceWorker' in navigator) {
            console.log("Will the service worker register?");
            navigator.serviceWorker.register('service-worker.js')
                .then(function(reg) {
                    console.log("Yes, it did.");
                }).catch(function(err) {
                    console.log("No it didn't. This happened:", err)
                });
        }
    </script>



</body>

</html>