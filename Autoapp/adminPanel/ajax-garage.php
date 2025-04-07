<?php
session_start();
if(!isset( $_SESSION["Admin_GUID"]) || !isset( $_SESSION["Admin_ID"])){
session_start();
session_unset();
session_write_close();
$url = "./sysAdmin.php";
header("Location: $url");
	
}

require('Model/GarageModel.php');
use fateh\AutoShop\AutoShop as garage;
$Garage_obj = new garage($_SESSION["Admin_GUID"]);
$x = $_POST["fan_id"];
if(!empty($x)){ 
	
	if ($x == 'NewCert'){
		
		$Issuer_type = $Garage_obj -> GetIssuerTopic();
		$Issuer_topic = $Garage_obj -> GetStatusTopic();

		echo '<div class="col-sm-4">
				<div class="form-group">
						<label class="control-label mb-10"> صادر کننده جواز :  </label>
					<select class="form-control select2" name="CertIssuer[]" id="CertIssuer[]">';
					foreach ($Issuer_type as $key ){
						echo "<option value='".$key[0]."'>". $key[1] ."</option>";
							}
		echo		 '</select>
			</div>
			<div class="form-group">
				<label class="control-label mb-10" > وضعیت پروانه کسب :  </label>
				<select class="form-control select2" name="CertStatus[]" id="CertStatus[]">';
						foreach ($Issuer_topic as $key ){
							echo "<option value='".$key["ID"]."'>". $key["PersianName"] ."</option>";
							}
		echo '</select>
		</div>
		<div class="form-group">
		<label class="control-label mb-10" for="Cert_Num"> شماره جواز : </label>
		<input type="text" class="form-control" name="Cert_Num[]" id="Cert_Num[]" placeholder="شماره جواز">
		</div>
		<div class="form-group">
		<br /><a href="#" class="remove-cert btn btn-info btn-outline fancy-button btn-0">حذف کردن</a>
		</div>
		</div>';
	}

}
