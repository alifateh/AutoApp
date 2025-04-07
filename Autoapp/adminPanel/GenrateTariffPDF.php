<?php
require_once('config/tcpdf_include.php');
require_once('config/public_conf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Ali Fatehchehr');
$pdf->SetTitle('اتحادیه صنف تعمیرکاران خودرو');
$pdf->SetSubject('تعرفه');
$pdf->SetKeywords('تعرفه صنفی');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 018', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'fa';
$lg['w_page'] = 'page';

// set some language-dependent strings (optional)
$pdf->setLanguageArray($lg);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 12);

// add a page
$pdf->AddPage();

if(!empty($_POST["auto_ID"])){
$autoID = $_POST["auto_ID"];
require('config/config_DB.php');
$data = $pdo->query('SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and `ID` = '.$autoID)->fetchAll();
$autoname = $data[0]["Name"];

}
if(!empty($_POST["version"])){
$version = $_POST["version"];
$SecID = $_POST["SecID"];

require('config/config_DB.php');
$data = $pdo->query('SELECT * FROM `Tariff_Version` WHERE `Visible` = 1 and `SecID` = '.$SecID.' and `AutomobileID` = '.$autoID.' and `Version` ='.$version)->fetchAll();
 $validationdate = $data[0]["ValidateDate"];
	 $day = substr($validationdate,8,2);
	 $mon = substr($validationdate,5,2);
	 $year = substr($validationdate,0,4);
//$validationdate = strtotime($validationdate);

 $hjrivalidationdate = gregorian_to_jalali($year, $mon, $day,"/");
}	
//
// Persian and English content
$header_tbl = '
<table style="direction: rtl;width: 100%; height: 100%; display: flex;justify-content: center;align-items: center;">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
				<td></td>
				<td style="width: 30%;">به نام خدا</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td style="width: 15%;"><img src="images/logo-etehadie.png"></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td style="width: 20%;"> اتحادیه صنف تعمیرکاران خودرو تهران</td>
				<td></td>
				<td>فهرست نرخ خودرو '.$autoname.'</td>
				<td></td>
				<td style="width: 20%;"><span color="#660000"> تاریخ اعتبار '. $hjrivalidationdate .' </span></td>
			</tr>
		</tbody>
		</table>';
$pdf->WriteHTML($header_tbl, true, 0, true, 0);
$pdf->Ln();
$pdf->SetFontSize(10);

$body_tbl1 = '
<table style="width: 100%; height: 100%;direction: rtl; border-collapse: collapse; border: 1px solid #000000;">
		<thead style="width: 100%; height: 100%;direction: rtl;line-height: 5em;font-size: medium;">
			<tr>
				<th style="width: 10%; border-collapse: collapse; border: 1px solid #000000;"> ردیف </th>
				<th style="width: 60%; border-collapse: collapse; border: 1px solid #000000;"> شرح خدمات </th>
				<th style="width: 30%; border-collapse: collapse; border: 1px solid #000000;"> قیمت (تومان) </th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		</table>';
$pdf->WriteHTML($body_tbl1, true, 0, true, 0);

/*
if(!empty($_POST["SecID"])){
	$version = $_POST["version"];
	$SecID = $_POST["SecID"];
	require('config/config_DB.php');
	$data = $pdo->query('SELECT * FROM `Tariff_Value` WHERE `Visible` = 1 and `SecID` = '.$SecID.' and `VersionID` ='.$version)->fetchAll();
	foreach ($data as $row ){
	echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
	
	}
}

*/

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('tariff.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
