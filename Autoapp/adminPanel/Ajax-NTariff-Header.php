<?php

function Get_AutoTip_ByID($tipid)
{
    $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    require("$rootDir/config/config_DB.php");
    $data = $pdo->query('SELECT * FROM `Automobile_Tip` WHERE `Visible` = 1 and `ID`=' . $tipid)->fetchAll();
    return "[ تیپ " . $data[0]['ModelName'] . " ]";
}

if (!empty($_POST["Auto_ID"])) {
    $autoID = $_POST["Auto_ID"];

    $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
    require("$rootDir/config/config_DB.php");

    $data = $pdo->query('SELECT * FROM `Automobile_Name` WHERE `Visible` = 1 and ID =' . $autoID)->fetchAll();
    $internal = $data[0]['internalproduct'];
    $auto_name = $data[0]['Name'];
    //show tip 
    $modelid = $data[0]['ModelID'];
    $modelname = Get_AutoTip_ByID($modelid);
    echo '<div class="row">
			<div class="panel panel-default card-view pa-0">
				<div class="panel-wrapper collapse in">
					<div class="panel-body pa-0">
						<div class="sm-data-box bg-green">
							<div class="container-fluid">
								<div class="text-center">
									<span class="txt-light block counter"> ثبت قیمت برای سرویسهای تعرفه خودرو ' . $auto_name . ' تیپ ' . $modelname . '</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />';
}else{
    echo '<div class="row">
    <div class="panel panel-default card-view pa-0">
        <div class="panel-wrapper collapse in">
            <div class="panel-body pa-0">
                <div class="sm-data-box bg-red">
                    <div class="container-fluid">
                        <div class="text-center">
                            <span class="txt-light block counter"> ابتدا خودروساز و سپس خودور را مشخص نمایید </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br />';


}
