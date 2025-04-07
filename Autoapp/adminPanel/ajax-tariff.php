<?php
	function showtip($tipid){
		require('config/config_DB.php');
		$data = $pdo->query('SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ID`='.$tipid)->fetchAll();
			foreach ($data as $row ){
				return $row['ModelName'];
			}
	}
	
	function showitem($internal){
	require('config/config_DB.php');
	$data = $pdo->query('SELECT * FROM `Tariff_Item` WHERE `Visible` = 1 and `Foreign`='.$internal)->fetchAll();
		foreach ($data as $row ){
		echo '<label class="control-label mb-10" for="serviceprice">'.$row['Name'].'</label>
		<div class="input-group">
			<div class="input-group-addon"><i class="pe-7s-cash"></i></div>
			<input type="number" class="form-control" onkeypress="return onlyNumberKey(event)" id="serviceprice[]" name="serviceprice[]" placeholder="قیمت به ریال">
			<input type="hidden" id="ItemID[]" name="ItemID[]" value="'.$row['ID'].'">
		</div>';
		echo '<br />';

		}
	}
	
if(!empty($_POST["man_id"])){
	if ($_POST["man_id"] !== 0){

	require('config/config_DB.php');
	$autoID = $_POST["man_id"];
	$data = $pdo->query('SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID ='.$autoID)->fetchAll();
	$internal = $data[0]['internalproduct'];
	$auto_name = $data[0]['Name'];
	//show tip 
	$modelid = $data[0]['ModelID'];
	$modelname = showtip($modelid);
	if ( $modelname == 0){
			echo '<div class="row">
						<div class="panel panel-default card-view pa-0">
							<div class="panel-wrapper collapse in">
								<div class="panel-body pa-0">
									<div class="sm-data-box bg-blue">
										<div class="container-fluid">
												<div class="text-center" style =" line-height: 500%;">
													<span class="txt-light block counter"> ثبت قیمت برای سرویسهای تعرفه خودرو '. $auto_name .'</span>
												</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br />';
	showitem($internal);

		}else{
		echo '<div class="row">
							<div class="panel panel-default card-view pa-0">
								<div class="panel-wrapper collapse in">
									<div class="panel-body pa-0">
										<div class="sm-data-box bg-blue">
											<div class="container-fluid">
													<div class="text-center" style =" line-height: 500%;">
														<span class="txt-light block counter"> ثبت قیمت برای سرویسهای تعرفه خودرو '.$auto_name .' تیپ '. $modelname .'</span>
													</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<br />';
		showitem($internal);
		}
	}
}

?>