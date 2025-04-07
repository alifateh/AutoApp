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
require("$rootDir/Model/AutoModel.php");
require("$rootDir/Model/N_TariffModel.php");
require("$rootDir/config/tcpdf_include.php");
require("$rootDir/config/config/phpqrcode/qrlib.php");

use fateh\Automobile\Automobile as auto;
use fateh\tariff\NewTariff as NewTariff;

// Include the main TCPDF library (search for installation path).

$Tariff_Obj = new NewTariff($_SESSION["Mechanic_GUID"]);
$Auto_Obj = new auto($_SESSION["Mechanic_GUID"]);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == "pdf") {
        if (isset($_POST['NTariffGUID'])) {
            $NTariffGUID = $_POST['NTariffGUID'];
            $get_TariffAllValues = $Tariff_Obj->GET_NTariffValue_ByID($NTariffGUID); ///bayad join beshe ba order
            $get_Tariff = $Tariff_Obj->GET_NTariff_ByID($NTariffGUID);
            $get_TariffVer = $Tariff_Obj->Get_NTariffVersion_ByID($get_Tariff[0]['TariffVerGUID']);
            
            $tarif_date = $Tariff_Obj->Gat_HejriDate( $get_Tariff[0]['ValidateDate']);
            $valid_date = gregorian_to_jalali($tarif_date[0], $tarif_date[1], $tarif_date[2]);

            $auto = $Auto_Obj->Get_Automobile_ByID($get_Tariff[0]['AutoGUID']);
            $auto_name = " نام خودرو: " . $auto[0]['Name'];
            $auto_tipID =  $auto[0]['ModelID'];
            $auto_manID = $auto[0]['ManufacturerID'];
            $auto_tipName = $Auto_Obj->Get_Tip_ByID($auto_tipID);
            $tip = " تیپ :" . $auto_tipName[0]['ModelName'];
            $get_AutomanName = $Auto_Obj->Get_Manufactuer_ByID($auto_manID);
            $auto_manName = "خودروساز : " . $get_AutomanName[0]['Name'];

            $string = '<h6 class="panel-title txt-dark"> مشخصات خودور [ ' . $auto_manName . "] &nbsp; [" . $auto_name . "] &nbsp; [" . $tip . ' ]</h6>
			<h6 class="panel-title txt-dark"> تاریخ اعتبار : ' . $valid_date[0] . "/" .  $valid_date[1] . "/" . $valid_date[2] . '</h6>
			<h6 class="panel-title txt-dark"> نسخه تعرفه : ' . $get_TariffVer[0]['NameFa'] . '</h6> <br />';

            if (!empty($get_TariffAllValues)) {
                $counter = 1;
                $table_str = "";
                foreach ($get_TariffAllValues as $row) {
                    if(!empty ($row['SRV_NameFA'])){
                        $SER = $row['SRV_NameFA'];
                    }elseif(!empty ($row['NameFa'])){
                        $SER = $row['NameFa'];
                    }else{
                        $SER = "خطا در برنامه";
                    }

                    if($row['ServicePrice'] == 0 || empty($row['ServicePrice'])){
                        continue;
                    }else{
                        $price =  number_format($row['ServicePrice']);
                    }
                    $table_str .= "<tr><td>" . $counter . "</td><td>" . $SER . "</td><td>" . $price . "</td></tr>";
                $counter++;
                }
            } else {
                echo '<tr><td colspan="3" style="text-align: center;"> سرویسی تعریف نشده است </td></tr>';
            }

        } else {
            $route = 1;
        }
    }

    if (!empty($route) && $route == 1) {
        // return VALUES
        $url = "Dashboard.php";
        header("Location: $url");
    }
}


//############################################################################################
//############################################################################################

$ID = $_SESSION["Mechanic_GUID"];
if ($ID == 1 or $ID == 2 or $ID == 3) {
	$user_ID = 'memberID=' . $_SESSION["Mechanic_GUID"];
} else {

	$user_ID = $_SESSION["Mechanic_GUID"];
}


// how to save PNG codes to server

$tempDir = 'config/config/phpqrcode/temp/';

$codeContents = "https://pwa.autoapp.ir/QR-Landing.php?id=" . $user_ID;

// we need to generate filename somehow, 
// with md5 or with database ID used to obtains $codeContents...
$fileName = 'Tarrif_' . md5($codeContents) . '.png';

$pngAbsoluteFilePath = $tempDir . $fileName;
$urlRelativeFilePath = 'https://pwa.autoapp.ir/config/config/phpqrcode/temp/' . $fileName;

// generating
if (!file_exists($pngAbsoluteFilePath)) {
	QRcode::png($codeContents, $pngAbsoluteFilePath);
} else {
	QRcode::png($codeContents, $pngAbsoluteFilePath);
}


//############################################################################################
//############################################################################################

class MYPDF extends TCPDF
{
	//Page header
	public function Header()
	{
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = K_PATH_IMAGES . '../images/pdf-back.jpg';
		$this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}
}

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Fatehchehr');
$pdf->SetTitle('تحت نظارت اتحادیه صنف تعمیرکاران خودرو تهران');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 018', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language dependent data:
$lg = array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';

// set some language-dependent strings (optional)
$pdf->setLanguageArray($lg);


// ---------------------------------------------------------

// set font

$pdf->SetFont('dirooz', '', 14);

//$pdf->SetFont('dejavusans', '', 14);

// add a page
$pdf->AddPage();

/*
// Persian and English content
$htmlpersian = $string;
$pdf->WriteHTML($htmlpersian, true, 0, true, 0);

// Persian and English content
$htmlpersian = '<img src="'.$urlRelativeFilePath.'" />';
$pdf->WriteHTML($htmlpersian, true, 0, true, 0);

*/

$qrcode = '<img src="' . $urlRelativeFilePath . '" />';
// set LTR direction for english translation
$pdf->setRTL(true);

$pdf->SetFontSize(12);

// print newline
$pdf->Ln();

$tbl = <<<EOD
<table style="width:100%;height:100%;">
<tr nobr="true" style="text-align: center;">
  <th colspan="2">
  <h3> بسمه تعالی </h3>
  </th>
 </tr>
 <tr nobr="true">
  <th style="width:600px;height:150px">
 $string
  </th>
   <th style="width:300px;height:150px">
 $qrcode
  </th>
 </tr>
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');




// set LTR direction for english translation
$pdf->setRTL(true);

$pdf->SetFontSize(10);

// print newline
$pdf->Ln();

$tbl = <<<EOD
<table border="1" cellpadding="2" cellspacing="2">
 <tr nobr="true" style="text-align: center;">
  <th colspan="3">
  <h3 > اتحادیه صنف تعمیرکاران خودرو تهران © </h3>
  <h1 style="color:red;"> این نرخنامه بدون QR کد فاقد اعتبار می باشد </h1>
  </th>
 </tr>
 <tr nobr="true">
  <td style="width:40px"> ردیف </td>
  <td style="width:400px"> شرح خدمات </td>
  <td style="width:180px"> نرخ مصوب (ریال) </td>
 </tr>
 $table_str
</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

// ---------------------------------------------------------

$info = '<h3> شماره تلفن های رسیدگی به شکایات </h3>' .
	'<p> سازمان بازرگانی استان تهران : 124 <br />
اتاق اصناف تهران : 85543200 - 85543201 <br />
اتحادیه صنف تعمیرکاران خودرو تهران : 77531723 <br />
</p>';
$pdf->WriteHTML($info, true, 0, true, 0);

// ---------------------------------------------------------

$cpoy_write = '<h1> این نرخنامه بدون QR کد فاقد اعتبار می باشد </h1>' . '<h3> تمامی حقوق مادی و معنوی این سند متعلق به اتحادیه صنف تعمیرکاران خودرو تهران است و هرگونه کپی برداری غیر قانونی و قابل پیگرد می باشد </h3>';
$pdf->WriteHTML($cpoy_write, true, 0, true, 0);



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('ETH_'.$auto[0]['Name'] . '-' . $get_TariffVer[0]['NameFa'] . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
