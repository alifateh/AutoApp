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
require('Model/GarageModel.php');

use fateh\AutoShop\AutoShop as garage;

$Garage_obj = new garage($_SESSION["Mechanic_GUID"]);

$data = $Garage_obj->V_GarageMapsAll();

?>


<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./config/config/OSM/leaflet.js"></script>
    <link rel="stylesheet" href="./config/config/OSM/leaflet.css" />
    <style>
        html,
        body {
            height: 100%;
            padding: 0;
            margin: 0;
        }

        #map {
            /* configure the size of the map */
            width: 100%;
            height: 100%;
        }

        button {
            background: none;
            color: inherit;
            border: none;
            padding: 0;
            font: inherit;
            cursor: pointer;
            outline: inherit;
        }
    </style>
</head>

<body>
    <div id="map"></div>
    <script>
        // initialize Leaflet
        var map = L.map('map').setView({
            lon: 51.33633521935895,
            lat: 35.70120803925202
        }, 11);
        
        var GarageMarker = L.icon({
            iconUrl: 'images/point.png',
            iconSize: [28, 32], // size of the icon
            iconAnchor: [1, 1], // point of the icon which will correspond to marker's location
            popupAnchor: [-1, -1] // point from which the popup should open relative to the iconAnchor
        });

        // add the OpenStreetMap tiles
        L.tileLayer('https://map.autoapp.ir/tile/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '<img src="images/logo-AutoApp_MAP.png" alt="logo" style="height: 75%;width: 75%;padding: 0px 5px 0px 0px;">'
        }).addTo(map);

        // show the scale bar on the lower left corner
        L.control.scale({
            imperial: true,
            metric: true
        }).addTo(map);

        <?php
        if (!empty($data)) {
            foreach ($data as $row) {
                $str =  $row['Name'];
                echo "var popup = L.popup().setContent('" . $str . "');";
                echo 'L.marker([' . $row['latitude'] . ', ' . $row['Longitude'] . '], {
                    icon: GarageMarker
                }).addTo(map).bindPopup(popup);';
            }
        }
        ?>
    </script>
</body>

</html>