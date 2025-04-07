<?php
    if(!empty($_POST["Tariff_GUID"])){
        $rootDir = realpath($_SERVER["DOCUMENT_ROOT"]);
        require("$rootDir/config/config_DB.php");
        $TariffType = $_POST["Tariff_GUID"];

        $data = $pdo->query("SELECT * FROM `N_Tariff_Services` WHERE `Visible` = 1 and `TariffTypeGUID`= '$TariffType'")->fetchAll();
        if(!empty($data)){
            foreach ($data as $row ){
            echo '<label class="control-label mb-10" for="serviceprice">'.$row['NameFa'].'</label>
            <div class="input-group">
                <div class="input-group-addon"><i class="pe-7s-cash"></i></div>
                <input type="number" class="form-control" onkeypress="return onlyNumberKey(event)" id="serviceprice[]" name="serviceprice[]" placeholder="قیمت به ریال">
                <input type="hidden" id="ServiceGUID[]" name="ServiceGUID[]" value="'.$row['GUID'].'">
            </div>';
            echo '<br />';
            }
        }else{
            echo '<label class="control-label mb-10" for="serviceprice"> سرویسی برای این نوع تعرفه یافت نشد. </label>';
        }
    }else{
        echo '<label class="control-label mb-10" for="serviceprice"> ID ست نشده </label>';
    }
