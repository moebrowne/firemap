<?php

$jsonData = file_get_contents('WP.json');

$json = json_decode($jsonData);

$rawIncidents = $json->context->incidentsBar;

$incidents = [];

foreach ($rawIncidents as $incident) {
    $incidentData = new stdClass();
    $incidentData->id = $incident->id;
    $incidentData->description = $incident->description;
    $incidentData->timestamp = $incident->timestamp;
    $incidentData->location = new stdClass();
    $incidentData->location->address = $incident->location->address;
    $incidentData->location->lat = $incident->location->lat;
    $incidentData->location->lng = $incident->location->lng;
    $incidentData->stations = $incident->attending;

    array_push($incidents, $incidentData);
}

file_put_contents('incidents.json', json_encode($incidents));
