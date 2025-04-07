<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
    session_start();
    session_unset();
    session_write_close();
    $url = "./sysAdmin.php";
    header("Location: $url");
}
require('config/public_conf.php');
require('Model/N_TariffModel.php');
require('Model/AutoModel.php');

use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['editauto'])) {
        if ($_POST['action'] == "Edit_Automobil") {
            $ID = $_POST['autoId'];
            $auto = new auto($_SESSION["Admin_GUID"]);
            $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);
            $TariffAllTypes = $Tariff_Obj->V_TariffTypes();
            $AutoTariffType = $Tariff_Obj->Get_TariffType_ByAutoID($ID);

            $Car_Info = $auto->Get_Automobile_ByID($ID);
            $Manufacture = $auto->Get_Manufactuer_ByID($Car_Info[0]["ManufacturerID"]);
            $ManufactureAll = $auto->Get_ManufactuerAll();
            $TipAll = $auto->Get_Tip_ByManufactuererID($Car_Info[0]["ManufacturerID"]);
            $auto_tip = $auto->Get_Tip_ByID($Car_Info[0]["ModelID"]);
            $auto_internal = $auto->Get_AutoInternal_ByID($ID);
            $auto_files = $auto->Get_AutoPic($Car_Info[0]["filekey"]);
        }
    }
    if (isset($_POST['remove_file'])) {
        if ($_POST['action'] == "remove_file") {
            $ID = $_POST['file_Id'];
            $auto_remove_file = new auto($_SESSION["Admin_GUID"]);
            $route = $auto_remove_file->D_AutomonbileFile_ByID($ID);
            if (!empty($route)) {
                $Error_STR = 0;
                // return VALUES
                $url = "V_Automobiles.php";
                header("Location: $url");
            } else {
                $Error_STR = 1;
            }
        }
    }
    if (isset($_POST['editauto'])) {
        $ID = $_POST['autoId'];
        $updateauto = new auto($_SESSION["Admin_GUID"]);
        $Tariff_Obj = new NewTariff($_SESSION["Admin_GUID"]);

        $get_auto = $updateauto->Get_AutoDetial_ByID($ID);
        $get_AutoTariffType = $Tariff_Obj->Get_TariffType_ByAutoID($ID);
        if (!empty($get_AutoTariffType)) {
            $i = 0;
            foreach ($get_AutoTariffType as $row) {
                $SavedTariffType[$i] = $row['TariffTypeGUID'];
                $i++;
            }
        } else {
            $SavedTariffType = array();
        }

        $get_autoname = $get_auto[0]["Name"];
        $get_automan = $get_auto[0]["ManufacturerID"];
        $get_autotip = $get_auto[0]["ModelID"];
        $get_autointernal = $get_auto[0]["internalproduct"];

        //
        /// form values
        //
        $post_autoname = $_POST['autoname'];
        $post_automan = $_POST['Manufacturer'];
        $post_autotip = $_POST['auto_tips'];
        if (isset($_POST['optgroup'])) {
            $post_autotariff = $_POST['optgroup'];
        } else {
            $post_autotariff = array();
        }

        $arr_diff_Decrease = array_diff($SavedTariffType, $post_autotariff); // kam beshe
        $arr_diff_Increase = array_diff($post_autotariff, $SavedTariffType); // ziyad beshe


        if ($_POST['inetrnal'] == 'on') {
            $post_autointernal = 1;
        } else {
            $post_autointernal = 0;
        }

        if (empty($post_autoname)) {
            $autoname = $get_autoname;
        } else {
            if ($post_autoname == $get_autoname) {
                $autoname = $get_autoname;
            } else {
                $autoname = $post_autoname;
            }
        }

        if ($post_automan == $get_automan) {
            $automan = $get_automan;
        } else {
            $automan = $post_automan;
        }

        if ($post_autotip == $get_autotip) {
            $autotip = $get_autotip;
        } else {
            $autotip = $post_autotip;
        }

        if ($post_autointernal == $get_autointernal) {
            $autointernal = $get_autointernal;
        } else {
            $autointernal = $post_autointernal;
        }


        if (isset($post_autotariff) && count($post_autotariff) === 0) {
            $route = $updateauto->D_AllAutoTariffType_ByAutoID($ID);
        } else {
            if (count($SavedTariffType) == count($post_autotariff) && !array_diff($SavedTariffType, $post_autotariff)) {
                $route = $updateauto->U_Automobile($ID, $autoname, $automan, $autotip, $autointernal);
            }
            if (!empty($arr_diff_Decrease)) {
                foreach ($arr_diff_Decrease as $key) {
                    $route = $updateauto->D_AutoTariffType_ByAutoID($ID, $key);
                }
            }
            if (!empty($arr_diff_Increase)) {
                foreach ($arr_diff_Increase as $key) {
                    $route = $updateauto->U_AutoTariff($ID, $autoname, $automan, $autotip, $autointernal, $key);
                }
            }
        }

        if (!empty($route) && $route == 1) {
            // return VALUES
            $url = "V_Automobiles.php";
            header("Location: $url");
        } else {
            $Error_STR = 1;
        }
    }
} else {
    $url = "V_Automobiles.php";
    header("Location: $url");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php Metatag(); ?>
    <link href="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
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

    <!-- bootstrap-touchspin CSS -->
    <link href="vendors/bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" type="text/css" />

    <!-- multi-select CSS -->
    <link href="vendors/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Switches CSS -->
    <link href="vendors/bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Datetimepicker CSS -->
    <link href="vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">

    <script src="dist/js/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#Manufacturer').on('change', function() {
                var manID = $(this).val();
                if (manID) {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax-manufacture-tip-auto.php',
                        data: 'man_id=' + manID,
                        success: function(html) {
                            $('#autotip').html(html);
                        }
                    });
                }
            });
        });
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
                            <div class="panel-heading">
                                <div class="pull-right">
                                    <a class="pull-left inline-block" data-toggle="collapse" href="#collapse_2" aria-expanded="true" aria-controls="collapse_2">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                        <i class="zmdi zmdi-chevron-up"></i>
                                    </a>
                                </div>
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h6 class="panel-title txt-dark"> ویرایش خودرو </h6>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <hr>

                            <div id="collapse_2" class="panel-wrapper collapse in">
                                <div class="panel-body">
                                    <div id="example-basic">
                                        <div class="col-sm-6">
                                            <form method="post" enctype="multipart/form-data">
                                                <input type="hidden" id="autoId" name="autoId" value="<?php echo $_POST['autoId']; ?>">
                                                <div class="form-group">
                                                    <div class="input-group col-sm-6">

                                                        <label class="control-label mb-10"> نام خودروساز : </label>
                                                        <select class="form-control select2 select2-hidden-accessible" aria-hidden="true" name="Manufacturer" id="Manufacturer">
                                                            <option> انتخاب نمایید </option>
                                                            <?php

                                                            foreach ($ManufactureAll as $row) {

                                                                if ($row['ID'] == $Manufacture[0]["ID"]) {
                                                                    echo '<option value="' . $row['ID'] . '" selected="selected" >' . $row['Name'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <br />
                                                    <label class="control-label mb-10" for="exampleInputuname_1"> نام خودرو : </label>
                                                    <div class="input-group col-sm-6">
                                                        <div class="input-group-addon"><i class="icon-info"></i></div>
                                                        <input type="text" class="form-control" name="autoname" id="autoname" placeholder="<?php echo $Car_Info[0]["Name"]; ?>">

                                                    </div>
                                                    <br />
                                                    <div class="input-group col-sm-6">
                                                        <label class="control-label mb-10"> تیپ خودرو </label>
                                                        <select class="form-control select2 select2-hidden-accessible" tabindex="-1" aria-hidden="true" name="auto_tips" id="auto_tips">
                                                            <?php
                                                            if (!empty($auto_tip)) {
                                                                foreach ($TipAll as $row) {
                                                                    if ($row['ID'] == $auto_tip[0]["ID"]) {
                                                                        echo '<option value="' . $row['ID'] . '" selected="selected" >' . $row['ModelName'] . '</option>';
                                                                    } else {
                                                                        echo '<option value="' . $row['ID'] . '">' . $row['ModelName'] . '</option>';
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<option value=""> اطلاعات خودور معیوب است </option>';
                                                                foreach ($TipAll as $row) {
                                                                    echo '<option value="' . $row['ID'] . '">' . $row['ModelName'] . '</option>';
                                                                }
                                                            }

                                                            ?>
                                                        </select>
                                                    </div>
                                                    <br />
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="exampleInputuname_1"> تولید خودرو </label>
                                                        <div class="checkbox checkbox-primary">
                                                            <input id="checkbox2" type="checkbox" <?php if ($auto_internal == 1) {
                                                                        echo 'checked= "checked"';
                                                                    }  ?> name="inetrnal" id="inetrnal">
                                                            <label for="checkbox2"> داخلی </label>
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <br />
                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body">
                                                            <div class="row mt-40">
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label mb-10" for="exampleInputuname_1"> نوع تعرفه ها برای خودرو : </label>
                                                                        <br />
                                                                        <select multiple id="optgroup" name="optgroup[]" multiple='multiple'>
                                                                            <?php

                                                                            if (!empty($AutoTariffType)) {
                                                                                $i = 0;
                                                                                foreach ($AutoTariffType as $row) {
                                                                                    $TariffName = $Tariff_Obj->Get_TariffID($row['TariffTypeGUID']);
                                                                                    $SelectedTariffType[$i] = $row['TariffTypeGUID'];
                                                                                    echo '<option value="' . $row['TariffTypeGUID'] . '" Selected >' . $TariffName[0]['NameFa'] . '</option>';
                                                                                    $i++;
                                                                                }
                                                                                if (!empty($TariffAllTypes)) {
                                                                                    $i = 0;
                                                                                    foreach ($TariffAllTypes as $key) {
                                                                                        $AllTariffType[$i] = $key['GUID'];
                                                                                        $i++;
                                                                                    }

                                                                                    $result = array_diff($AllTariffType, $SelectedTariffType);
                                                                                    foreach ($result as $diff) {
                                                                                        $TariffName = $Tariff_Obj->Get_TariffID($diff);
                                                                                        echo '<option value="' . $diff . '">' . $TariffName[0]['NameFa'] . '</option>';
                                                                                    }
                                                                                } else {
                                                                                    echo '<option value=""> انواع تعرفه ها موجود نمی باشد </option>';
                                                                                }
                                                                            } else {
                                                                                foreach ($TariffAllTypes as $key) {
                                                                                    echo '<option value="' . $key['GUID'] . '">' . $key['NameFa'] . '</option>';
                                                                                }
                                                                            }



                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br />
                                                    <div class="form-group">
                                                        <button class="btn btn-info btn-anim" type="submit" name="editauto" id="editauto"><i class="icon-check"></i><span class="btn-text">ثبت</span></button>

                                                    </div>
                                            </form>
                                            <div class="row">
                                                <!-- Table Hover -->
                                                <div class="col-sm-12">
                                                    <div class="panel panel-default card-view">
                                                        <div class="panel-heading">
                                                            <div class="pull-left">
                                                                <h6 class="panel-title txt-dark"> فهرست فایل </h6>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <div class="panel-wrapper collapse in">
                                                            <div class="panel-body">
                                                                <div class="table-wrap mt-40">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-hover mb-0">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th> دانلود فایل </th>
                                                                                    <th> عملیات </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $backurl = "V_Automobiles.php";
                                                                                if (!empty($auto_files)) {
                                                                                    foreach ($auto_files as $row) {
                                                                                        if ($row['location']  !== 0) {
                                                                                            $validation = "onclick='return validation()'";
                                                                                            echo '<tr><td>';
                                                                                            echo '<a href="' . $row['location'] . '"><span class="btn-text btn-default btn-icon-anim"><i class="icon-cloud-download"></i> ذخیره فایل </span></a>';
                                                                                            echo ' </td>';
                                                                                            echo '<td style ="width: 5%;">
																								<form method="post" enctype="multipart/form-data">
																									<input type="hidden" id="file_Id" name="file_Id" value="' . $row['ID'] . '">
																									<input type="hidden" id="action" name="action" value="remove_file">
																									<button class="btn btn-info btn-icon-anim" type="submit" name="remove_file" id="remove_file" ' . $validation . '><i class="icon-trash"></i> حذف </button>
																								</form>
																							</td></tr>';
                                                                                        } else {
                                                                                            echo '<tr><td>
																									<form style ="float: right; padding-left: 2%;" method="post" enctype="multipart/form-data" action ="Upload-image.php">
																										<input type="hidden" id="autoId" name="autoId" value="' . $ID . '">
																										<input type="hidden" id="backlocation" name="backlocation" value="' . $backurl . '">
																										<input type="hidden" id="eltype" name="eltype" value="9">
																										<button class="btn btn-default btn-icon-anim "><i class="icon-cloud-upload"></i> آپلود فایل </button>
																									</form>
																								</td></tr>';
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                   // echo "<tr><td> با ادمین تماس بگیرید </td></tr>";
                                                                                   echo ' <tr>';
                                                                                        echo '<td style ="width: 5%;">
                                                                                                <form method="post" enctype="multipart/form-data" action ="Upload-image.php">
		                                            	                                            <input type="hidden" id="ItemGUId" name="ItemGUId" value="' . $Car_Info[0]["filekey"] . '">
		                                            	                                            <input type="hidden" id="eltype" name="eltype" value="9">
		                                            	                                            <button class="btn btn-default btn-icon-anim "><i class="icon-cloud-upload"></i> آپلود فایل </button>
		                                            	                                        </form>
                                                                                            </td></tr>';
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
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row -->

                </div>

                <!-- Main Content -->

                <div class="container-fluid">
                    <!-- Footer -->
                    <!-- Footer -->
                    <?php footer(); ?>
                    <!-- /Footer -->
                    <!-- /Footer -->
                </div>

                <!-- /Main Content -->
                <!-- JavaScript -->
                <!-- JavaScripts -->


            </div>

        </div>

        <script LANGUAGE="JavaScript">
            function validation() {
                var agree = confirm("آیا از حذف ایتم مطمئن می باشید؟");
                if (agree)
                    return true;
                else
                    return false;
            }
        </script>
        <!-- /#wrapper -->
        <!-- jQuery -->
        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
        <script src="vendors/bower_components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

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

        <!-- Init JavaScript -->
        <script src="dist/js/init.js"></script>

        <?php
        if (!empty($Error_STR)) {
            if ($Error_STR = 1) {
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
        }


        ?>

</body>

</html>