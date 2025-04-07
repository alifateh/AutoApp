<?php
///Remove
///Remove
///Remove
///Remove




session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
require('config/public_conf.php');
require('Model/SmsChanel.php');
require('Model/TellModel.php');

use fateh\smschanel\SMS as sms;
use fateh\phonebook\phonebook as Tell;
$rand = mt_rand(100000, 999999);
$member_GUID = $_POST["member"];
$cell = $_POST["membercell"];

if (!empty($_POST["sendcode"])) {

	if ($_POST["sendcode"] == 1) {
		$TellObj = new Tell($_SESSION["Admin_GUID"]);
		$exist = $TellObj->Get_IsExistNum($cell);
		if ($exist == 0) {
			$cell_arr = array();
			$clean_cell = ltrim($cell, $cell[0]);
			if ($cell[0] == 0) {
				echo '<div class="panel panel-info card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<p>لطفا شماره همراه را <strong> [ بدون صفر ] </strong>  ابتدا و به صورت  [912 ] وارد نمایید</p>
									</div>
								</div>
							</div>
						</div>';
			} else {
				$cell_arr = array();
				$cell_arr[0] = $_POST["membercell"];
				$code = "این پیام از جانب اتواپ میباشد. کد شناسایی شما : " . $rand;
				$topic = 1;
				$sendcode = new sms();
				$sendcode->send_sms($cell_arr, $code, $topic);
				// add to DB
				$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
				require("$rootDir/config/config_DB.php");
				$qur = "INSERT INTO `Users_Cell_Temp`( `MemberGUID`, `Code`, `CellPhoneNum`) VALUES ( ?, ?, ?)";
				$stmt = $pdo->prepare($qur);
				$stmt->bindParam(1, $member_GUID);
				$stmt->bindParam(2, $rand);
				$stmt->bindParam(3, $cell);
				$stmt->execute();
				echo '<div class="panel panel-success card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
								</div>
							<div class="clearfix"></div>
								</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<p>پیام کوتاه حاوی کد شناسایی ارسال گردید</p>
								</div>
							</div>
						</div>';
			}
		} else {
			echo '<div class="panel panel-info card-view">
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
							</div>
						<div class="clearfix"></div>
					</div>
					<div class="panel-wrapper collapse in">
						<div class="panel-body">
							<p> شماره همراه ' . $cell . ' قبلا در سیستم ثبت شده است </p>
						</div>
					</div>
				</div>
		</div>';
		}
	}

	if ($_POST["sendcode"] == 2) {
		$cell = $_POST["membercell"];
		$member_GUID = $_POST["member"];
		$validcode = $_POST["validcode"];

		//Compare with DB

		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$data = $pdo->query("SELECT * FROM `Users_Cell_Temp` WHERE `MemberGUID` ='" . $member_GUID . "' and `CellPhoneNum` = " . $cell . " ORDER BY `Users_Cell_Temp`.`ID` DESC LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
		if (!empty($data[0]['Code']) && $data[0]['Code'] == $validcode) {
			$operator = 1; //Number is for Mobile phone
			$ElemanType = 30;
			$orgin = 1; //Orginal Number
			$add_tell = new Tell($_SESSION["Admin_GUID"]);
			$add_tell->AddTell($cell, $member_GUID, $operator, $orgin, $ElemanType);
			echo '<div class="panel panel-success card-view">
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<p>شماره همراه با موفقیت ثبت شد.</p>
								<input type="hidden" id="RegedMobileNum" name="RegedMobileNum" value="' . $cell . '">
							</div>
						</div>
					</div>';
		} else {
			echo '<div class="panel panel-info card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<p>کد دریافتی صحیح نمی باشد. لطفا مجددا تلاش فرمایید.</p>
									</div>
								</div>
							</div>
						</div>';
		}
	}
	if ($_POST["sendcode"] == 3) {
		$cell = $_POST["membercell"];
		$member_GUID = $_POST["member"];
		$validcode = $_POST["validcode"];

		//Compare with DB

		$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
		require("$rootDir/config/config_DB.php");
		$data = $pdo->query("SELECT * FROM `Users_Cell_Temp` WHERE `MemberGUID` ='" . $member_GUID . "' and `CellPhoneNum` = " . $cell . " ORDER BY `Users_Cell_Temp`.`ID` DESC LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
		if (!empty($data[0]['Code']) && $data[0]['Code'] == $validcode) {
			$operator = 1; //Number is for Mobile phone
			$Updatetell = new Tell($_SESSION["Admin_GUID"]);
			$Updatetell->UpdateTellOrgin($cell, $member_GUID, $operator);
			echo '<div class="panel panel-success card-view">
						<div class="panel-heading">
							<div class="pull-left">
								<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-wrapper collapse in">
							<div class="panel-body">
								<p>شماره همراه با موفقیت ثبت شد.</p>
								<input type="hidden" id="RegedMobileNum" name="RegedMobileNum" value="' . $cell . '">
							</div>
						</div>
					</div>';
		} else {
			echo '<div class="panel panel-info card-view">
								<div class="panel-heading">
									<div class="pull-left">
										<h6 class="panel-title txt-light"> پیام سیسیتم </h6>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="panel-wrapper collapse in">
									<div class="panel-body">
										<p>کد دریافتی صحیح نمی باشد. لطفا مجددا تلاش فرمایید.</p>
									</div>
								</div>
							</div>
						</div>';
		}
	}
}
