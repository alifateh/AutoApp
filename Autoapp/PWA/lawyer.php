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
                    <h6 class="mb-0"> مشاوره حقوقی </h6>
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
                    <img src="/img/DADAR.jpg" alt="">
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-body" dir="rtl">
                <p>
                    <h4>ارتباط با گروه حقوقی دادار</h4>
                   تاریخ آخرین به‌روزرسانی : 30 بهمن 1403
                   <br /><p>
                        <p>
                        <br />
                        </p>
                        
                            تیم مجرب دادار با سابقه درخشان و آشنایی کامل به مسایل بروز در کسب و کار تعمیرکاران 
                            جهت پاسخگویی به همکاران اتحادیه در روزهای<b style="font-size:larger;color:darkblue"> یکشنبه</b> آماده ارائه مشاوره تلفنی می باشند
                        <br /><p>
                        <h5> کانال های ارتباطی با بخش پشتیبانی <h5>
                            <h5>شماره تلفن : <a href="tel:09122069723">09122069723</a></h5>
                            <h5>شماره تلفن : <a href="tel:09126661647">09126661647</a></h5>
                    </p>
                </div>
            </div>
        </div>


        <div class="card mb-3 rounded-0 rounded-bottom">
      <div class="card-body">
        <div class="accordion accordion-style-two" id="accordionStyle2">
          <!-- Single accordion-->
          <div class="accordion-item">
            <div class="accordion-header" id="accordionFour">
              <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleFour" aria-expanded="true" aria-controls="accordionStyleFour" class=""><i class="fa fa-plus"></i>سوال های حقوقی متداول</h6>
            </div>
            <div class="accordion-collapse collapse show" id="accordionStyleFour" aria-labelledby="accordionFour" data-bs-parent="#accordionStyle2">
              <div class="accordion-body">
                <div class="card user-data-card">
                  <div class="card-body">
                    <label class="form-label" dir="rtl">آیا تعمیرکاران خودرو می‌توانند با مشتری قرارداد رسمی تنظیم کنند؟ </label>
                    <ul class="list-group">
                      <li class="list-group-item active"> بله، تعمیرکاران خودرو می‌توانند با استفاده از قراردادهای رسمی، شرایط خدمات، هزینه‌ها و تعهدات خود را به صورت شفاف مشخص کنند. ما می‌توانیم در تهیه و تنظیم این قراردادها به شما کمک کنیم </li>
                    </ul>
                    <br />

                    <label class="form-label" dir="rtl"> در صورت بروز اختلاف با مشتری، چه اقداماتی باید انجام دهیم؟ </label>
                    <ul class="list-group">
                      <li class="list-group-item active"> در صورت بروز اختلاف، ابتدا سعی کنید از طریق مذاکره دوستانه آن را حل کنید. اگر اختلاف باقی ماند، می‌توانید از طریق اتحادیه یا مراجع قضایی اقدام کنید. تیم ما آماده است تا شما را در تمامی مراحل قانونی همراهی کند </li>
                    </ul>
                    <br />

                    <label class="form-label" dir="rtl"> اگر مشتری هزینه تعمیر را پرداخت نکند، چه حقوقی داریم؟ </label>
                    <ul class="list-group">
                      <li class="list-group-item active"> طبق قوانین، شما می‌توانید اقدام به مطالبه حقوق خود از طریق مراجع قانونی کنید. همچنین می‌توانید از قراردادهای مکتوب به‌عنوان مدرک استفاده کنید </li>
                    </ul>
                    <br />

                    <label class="form-label" dir="rtl"> اتحادیه تعمیرکاران چه حمایت‌هایی از اعضای خود می‌کند؟</label>
                    <ul class="list-group">
                      <li class="list-group-item active"> اتحادیه معمولاً در زمینه‌های حقوقی، قراردادهای کاری، حل اختلافات و تنظیم تعرفه‌ها به اعضای خود کمک می‌کند. در صورت نیاز، تیم ما می‌تواند به نمایندگی از شما با اتحادیه همکاری کند </li>
                    </ul>
                    <br />

                    <label class="form-label" dir="rtl">  در چه مواردی نیاز به مشاوره حقوقی داریم؟</label>
                    <ul class="list-group">
                      <li class="list-group-item active"> تنظیم قراردادها با مشتریان یا تأمین‌کنندگان </li>
                      <li class="list-group-item active"> دفاع از حقوق در دعاوی حقوقی و کیفری </li>
                      <li class="list-group-item active"> حل اختلافات مالی یا کاری </li>
                      <li class="list-group-item active"> آگاهی از قوانین جدید و مقررات مرتبط با صنعت خودرو </li>
                    </ul>
                    <br />

                    <label class="form-label" dir="rtl"> هزینه خدمات حقوقی شما چقدر است؟</label>
                    <ul class="list-group">
                      <li class="list-group-item active"> هزینه خدمات بسته به نوع مشاوره یا پرونده متفاوت است. برای اطلاعات دقیق‌تر، می‌توانید با ما تماس بگیرید یا فرم درخواست مشاوره را تکمیل کنید </li>
                    </ul>
                    <br />

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
</body>

</html>