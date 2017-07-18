<?php

$incidents = json_decode(file_get_contents(__DIR__ . '/incidents.json'));
$incidents = (array)$incidents;

usort($incidents, function ($a, $b) {
    if ($a->timestamp == $b->timestamp) {
        return 0;
    }
    return ($a->timestamp < $b->timestamp) ? 1 : -1;
});

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
$latCentre = $minLat + ($latDiff/2);

$lngDiff = abs($maxLng-$minLng);
$lngCentre = $minLng + ($lngDiff/8);

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
            zoom: 8,
            center: {
                lat: <?= $latCentre; ?>,
                lng: <?= $lngCentre; ?>
            },
            mapTypeId: 'roadmap'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
            data: [
                <?php foreach ($incidents as $incident) : ?>
                new google.maps.LatLng(<?= $incident->location->lat; ?>, <?= $incident->location->lng; ?>),
                <?php endforeach; ?>
            ],
            map: map
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDGZBYP2Xhoq3ZqfM3sOXAx2t_3-iyMaJE&libraries=visualization&callback=initMap"></script>

<div id="map" style="width: 49vw; height: 100vh; float: left;"></div>

<div class="container-fluid" style="width: 49vw; height: 100vh; overflow-y: scroll; float: right;">
    <div class="row example-basic">
        <div class="col-md-12 example-title">
            <h1>Dorset and Wiltshire Fire Service</h1>
        </div>
        <div class="col-md-12">
            <ul class="timeline">
                <?php $prevMonth = null; ?>
                <?php foreach ($incidents as $incident) : ?>

                    <?php if (date('M', $incident->timestamp) !== $prevMonth) : ?>
                    <li class="timeline-item period">
                        <div class="timeline-info"></div>
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h2 class="timeline-title"><?= date('F Y', $incident->timestamp); ?></h2>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php $falseAlarm = strpos($incident->description, 'false alarm') !== false; ?>

                    <li class="timeline-item">
                        <div class="timeline-info">
                            <span><?= date('M d', $incident->timestamp); ?></span>
                        </div>
                        <div class="timeline-marker <?= ($falseAlarm) ? 'false-alarm':''; ?>"></div>
                        <div class="timeline-content">
                            <h3 class="timeline-title"><?= $incident->title; ?></h3>

                            <p><?= $incident->description; ?></p>
                        </div>
                    </li>
                    <?php $prevMonth = date('M', $incident->timestamp); ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>