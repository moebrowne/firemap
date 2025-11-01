<?php
ini_set('display_errors', 0);

require 'incidents.php';

$maxLat = 0;
$maxLng = 0;
$minLat = INF;
$minLng = INF;
$meanLat = 0;
$meanLng = 0;

foreach ($incidents as $incident) {
    $maxLat = max($maxLat, $incident->location->lat);
    $maxLng = max($maxLng, $incident->location->lng);
    $minLat = min($minLat, $incident->location->lat);
    $minLng = min($minLng, $incident->location->lng);

    $meanLat += $incident->location->lat;
    $meanLng += $incident->location->lng;
}

$meanLat /= count($incidents);
$meanLng /= count($incidents);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fire Map</title>
    <link type="text/css" rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="/css/leaflet.css" />
    <script src="/js/leaflet.js"></script>
    <script src="/js/leaflet-heat.js"></script>
</head>
<body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L
            .map('map', {minZoom: 9})
            .setView([<?= $meanLat; ?>, <?= $meanLng; ?>], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            opacity: 0.8,
        })
        .addTo(map);

        L.heatLayer(
            [
                <?php foreach ($incidents as $incident) : ?>
                [<?= $incident->location->lat; ?>, <?= $incident->location->lng; ?>, 1],
                <?php endforeach; ?>
            ],
            {
                radius: 5,
                blur: 4,
                maxZoom: 14,
            }
        )
        .addTo(map);
    });
</script>

<div id="map" style="width: 49vw; height: 100vh; float: left; background-color: #000"></div>

<div id="incident-list" style="width: 49vw; height: 100vh; overflow-y: scroll; float: right;">
    <div>
        <h1>Dorset and Wiltshire Fire Service</h1>
        <div>
            <ul class="timeline">
                <?= renderIncidentDays(0, 4); ?>
            </ul>
        </div>
    </div>
    <div class="spinner">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
        <div class="rect4"></div>
        <div class="rect5"></div>
    </div>
</div>
<script>
    var incidentListContainer = document.getElementById('incident-list');
    var incidentList = document.querySelector('#incident-list .timeline');
    var currentlyRenderedDays = 3;
    var oppPending = false;

    incidentListContainer.addEventListener('scroll', function () {

        if (oppPending) {
            return;
        }

        if (incidentListContainer.offsetHeight + incidentListContainer.scrollTop >= incidentListContainer.scrollHeight) {
            var xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                    if (xmlhttp.status == 200) {
                        incidentList.innerHTML = incidentList.innerHTML + xmlhttp.responseText;
                        currentlyRenderedDays += 1;
                        oppPending = false;
                        hideSpinner();
                    }
                }
            };

            xmlhttp.open("GET", "fetchIncidents.php?offset=" + (currentlyRenderedDays+1) + "&count=1", true);
            xmlhttp.send();
            oppPending = true;
            showSpinner();
        }
    });

    function showSpinner() {
        document.querySelector('.spinner').style.visibility = 'visible';
    }

    function hideSpinner() {
        document.querySelector('.spinner').style.visibility = 'hidden';
    }
</script>
</body>
</html>