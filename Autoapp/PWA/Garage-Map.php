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
require("$rootDir/Model/GarageModel.php");

use fateh\AutoShop\AutoShop as garage;


if (isset($_SESSION["Garage-ID"])) {
    $Garage_obj = new garage($_SESSION["Mechanic_GUID"]);
    $map = $Garage_obj->Get_LocationMap_ByID($_SESSION['Garage-ID']);
} else {
    session_unset();
    session_write_close();
    $url = "/";
    header("Location: $url");
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Mapp styles -->
  <script src="./config/config/OSM/leaflet.js"></script>
  <link rel="stylesheet" href="./config/config/OSM/leaflet.css" />
  <link rel="stylesheet" href="./dist/css/fa/style.css" data-locale="true">
  <!-- Your styles -->
  <link rel="stylesheet" href="./app/css/app.css">
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
  <div id="map" style="font-family: 'Fatehchehr'; height : 480px;"></div>
  <script>
    var GarageMarker = L.icon({
            iconUrl: 'images/point.png',
            iconSize: [28, 32], // size of the icon
            iconAnchor: [1, 1], // point of the icon which will correspond to marker's location
            popupAnchor: [-1, -1] // point from which the popup should open relative to the iconAnchor
        });


    <?php
    if (!empty($map)) {
      // initialize Leaflet
      echo '    var map = L.map("map").setView({
      lon: ' . $map[0]['Longitude'] . ',
      lat: ' . $map[0]['latitude'] . ' 
      }, 13);';
    }
    ?>

    // add the OpenStreetMap tiles
    L.tileLayer('https://map.autoapp.ir/tile/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '<img src="images/logo-autoapp.png" alt="logo" style="height: 75%;width: 75%;padding: 0px 5px 0px 0px;">'
    }).addTo(map);

    // show the scale bar on the lower left corner
    L.control.scale({
      imperial: true,
      metric: true
    }).addTo(map);

    <?php
    if (!empty($map)) {
      echo 'L.marker([' . $map[0]['latitude'] . ', ' . $map[0]['Longitude'] . '], {
                    icon: GarageMarker
                }).addTo(map).bindPopup(popup);';
    }
    ?>
  </script>
  <br />
  <br />
</body>

</html>