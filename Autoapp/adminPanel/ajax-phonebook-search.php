<?php
session_start();
if (!isset($_SESSION["Admin_GUID"]) || !isset($_SESSION["Admin_ID"])) {
	session_start();
	session_unset();
	session_write_close();
	$url = "./sysAdmin.php";
	header("Location: $url");
}
$rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
require("$rootDir/config/public_conf.php");
require("$rootDir/Model/N_PhoneContactModel.php");

use fateh\Phonebook\MechanicContact as MechanicTell;
use fateh\Phonebook\GarageContact as GarageTell;
use fateh\Phonebook\IndependentContact as Independent;

$Mechanic_obj = new MechanicTell($_SESSION["Admin_GUID"]);
$Garage_obj = new GarageTell($_SESSION["Admin_GUID"]);
$Independent_obj = new Independent($_SESSION["Admin_GUID"]);

if (isset($_POST['searchval'])) {
	$Mechanic = $Mechanic_obj->Get_MechanicSerch_ByID($_POST['searchval']);
	$Garage = $Garage_obj->Get_GarageSerch_ByID($_POST['searchval']);
	$Independent = $Independent_obj->Get_IndependentSerch_ByID($_POST['searchval']);
	if (!empty($Mechanic)) {
		foreach ($Mechanic as $row) {
			echo '<div class="form-group">
						<div class="input-group">
						<div class="input-group-addon"><i class="icon-user"></i></div>
							<a href="#" onclick="findselect(this)" >' . $row['Number'] . ' </a><a href="#">' . $row['FName'] . " " . $row['LName'] . ' کد ملی ' . $row['UName'] . '</a><br />
						</div>
					</div>';
		}
	}

	if (!empty($Garage)) {
		foreach ($Garage as $row) {
			//if ($row['Mobile'] == 1) {
			echo '<div class="form-group">
					<div class="input-group">
					<div class="input-group-addon"><i class="icon-user"></i></div>
						<a href="#" onclick="findselect(this)" >' . $row['Number'] . ' </a><a href="#">' . $row['Name'] . " تگ " . $row['Tags'] . ' پلاک آبی ' . $row['UName'] . '</a><br />
					</div>
				</div>';
			//}
		}
	}
	if (!empty($Independent)) {
		foreach ($Independent as $row) {
			//if ($row['Mobile'] == 1) {
			echo '<div class="form-group">
					<div class="input-group">
					<div class="input-group-addon"><i class="icon-user"></i></div>
						<a href="#" onclick="findselect(this)" >' . $row['Number'] . ' </a><a href="#">' . $row['FirstName'] . " " . $row['LastName'] . " تگ " . $row['Tags'] . '</a><br />
					</div>
				</div>';
			//}
		}
	}
} else {
	echo '<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><i class="icon-user"></i></div>
					<p> موردی در دفترچه تلفن یافت نشد </p>
				</div>
			</div>';
}
