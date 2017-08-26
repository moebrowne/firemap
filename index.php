<?php

$incidents = json_decode(file_get_contents(__DIR__ . '/incidents.json'));
$incidents = (array)$incidents;

usort($incidents, function ($a, $b) {
    if ($a->timestamp == $b->timestamp) {
        return 0;
    }
    return ($a->timestamp < $b->timestamp) ? 1 : -1;
});

// Remove any incidents without a position
$incidents = array_filter($incidents, function($incident) {
    return !empty($incident->location->lat) && !empty($incident->location->lng);
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

<div class="container-fluid" style="width: 49vw; height: 100vh; overflow-y: scroll; float: right;">
    <div class="row example-basic">
        <div class="col-md-12 example-title">
            <h1>Dorset and Wiltshire Fire Service</h1>
        </div>
        <div class="col-md-12">
            <ul class="timeline">
                <?php
                $prevDateGroup = null;
                $limit = (int)max(15, $_GET['limit']);
                $i = 0;
                ?>
                <?php foreach ($incidents as $incident) : ?>

                    <?php if (date('d M Y', $incident->timestamp) !== $prevDateGroup) : ?>
                    <li class="timeline-item period">
                        <div class="timeline-info"></div>
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <h2 class="timeline-title"><?= date('j F Y', $incident->timestamp); ?></h2>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php $falseAlarm = strpos($incident->description, 'false alarm') !== false; ?>
                    <?php $vehicleFire = preg_match('/small vehicle|vehicle fire|Fire Vehicle|RTC|Road Traffic Collision|Car Fire/i', $incident->title . $incident->description) === 1; ?>
                    <?php $vehicleFireLarge = preg_match('/large vehicle|vehicle large|Lorry Fire/i', $incident->title . $incident->description) === 1; ?>
                    <?php $lockedIn = preg_match('/locked in|Shut In/i', $incident->title . $incident->description) === 1; ?>
                    <?php $smallAnimal = preg_match('/small animal|RSPCA|hamster/i', $incident->title . $incident->description) === 1; ?>
                    <?php $aircraft = preg_match('/aircraft/i', $incident->title . $incident->description) === 1; ?>

                    <li class="timeline-item">
                        <div class="timeline-marker <?= ($falseAlarm) ? 'false-alarm':''; ?> <?= ($vehicleFire) ? 'vehicle-fire':''; ?> <?= ($vehicleFireLarge) ? 'vehicle-fire-large':''; ?> <?= ($lockedIn) ? 'locked-in':''; ?> <?= ($smallAnimal) ? 'small-animal':''; ?> <?= ($aircraft) ? 'aircraft':''; ?>"></div>
                        <div class="timeline-content">
                            <h3 class="timeline-title"><?= $incident->title; ?></h3>

                            <p><?= $incident->description; ?></p>
                        </div>
                    </li>
                    <?php $prevDateGroup = date('d M Y', $incident->timestamp); ?>
                    <?php if ($i++ > $limit) { break; } ?>
                <?php endforeach; ?>
                <?php if (count($incidents) > $limit) : ?>
                    <li style="margin: 15px 0 40px; text-align: center;">
                        <a href="?limit=<?= ($limit + 15) ?>">Show More</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>