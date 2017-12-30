<?php

require 'incidents.php';

$maxLat = 0;
$maxLng = 0;
$minLat = INF;
$minLng = INF;

foreach ($incidents as $incident) {
    $maxLat = max($maxLat, $incident->location->lat);
    $maxLng = max($maxLng, $incident->location->lng);
    $minLat = min($minLat, $incident->location->lat);
    $minLng = min($minLng, $incident->location->lng);
}

$latDiff = abs($maxLat-$minLat);
$latCentre = $minLat + ($latDiff/5);

$lngDiff = abs($maxLng-$minLng);
$lngCentre = $minLng + ($lngDiff/5);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fire Map</title>
    <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="css/styles.css">
</head>
<body>

<script>
    var map, heatmap;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 9,
            center: {
                lat: <?= $latCentre; ?>,
                lng: <?= $lngCentre; ?>
            },
            mapTypeId: 'hybrid'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: [
                <?php foreach ($incidents as $incident) : ?>
                new google.maps.LatLng(<?= $incident->location->lat; ?>, <?= $incident->location->lng; ?>),
                <?php endforeach; ?>
            ],
            radius: 8,
            map: map
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGZBYP2Xhoq3ZqfM3sOXAx2t_3-iyMaJE&libraries=visualization&callback=initMap"></script>

<div id="map" style="width: 49vw; height: 100vh; float: left;"></div>

<div id="incident-list" class="container-fluid" style="width: 49vw; height: 100vh; overflow-y: scroll; float: right;">
    <div class="row example-basic">
        <div class="col-md-12 example-title">
            <h1>Dorset and Wiltshire Fire Service</h1>
        </div>
        <div class="col-md-12">
            <ul class="timeline">
                <?= renderIncidentDays(0, 4); ?>
            </ul>
        </div>
    </div>
</div>
<script>
    var incidentListContainer = document.getElementById('incident-list');
    var incidentList = document.querySelector('#incident-list .timeline');
    var currentlyRenderedDays = 3;

    incidentListContainer.addEventListener('scroll', function () {
        if (incidentListContainer.offsetHeight + incidentListContainer.scrollTop >= incidentListContainer.scrollHeight) {
            var xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                    if (xmlhttp.status == 200) {
                        incidentList.innerHTML = incidentList.innerHTML + xmlhttp.responseText;
                        currentlyRenderedDays += 1;
                    }
                }
            };

            xmlhttp.open("GET", "fetchIncidents.php?offset=" + (currentlyRenderedDays+1) + "&count=1", true);
            xmlhttp.send();
        }
    })
</script>
</body>
</html>