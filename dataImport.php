<?php

$html = file_get_contents('https://www.dwfire.org.uk/');

preg_match('/var WP = ({.+})/', $html, $matches);

$json = json_decode($matches[1]);

$rawIncidents = $json->context->incidentsBar;

$incidents = json_decode(file_get_contents(__DIR__ . '/incidents.json'), true);

foreach ($rawIncidents as $incident) {
    $incidentData = new stdClass();
    $incidentData->id = $incident->id;
    $incidentData->title = $incident->post_title;
    $incidentData->description = $incident->description;
    $incidentData->timestamp = $incident->timestamp;
    $incidentData->location = new stdClass();
    $incidentData->location->address = $incident->location->address;
    $incidentData->location->lat = $incident->location->lat;
    $incidentData->location->lng = $incident->location->lng;
    $incidentData->stations = $incident->attending;

    $incidents[$incidentData->id] = $incidentData;
}

file_put_contents(__DIR__ . '/incidents.json', json_encode($incidents));
