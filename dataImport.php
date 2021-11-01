<?php

$html = file_get_contents('https://www.dwfire.org.uk/');

preg_match('/var WP = ({.+})/', $html, $matches);

$json = json_decode($matches[1]);

$rawIncidents = $json->context->incidentsBar;

$incidents = json_decode(file_get_contents(__DIR__ . '/incidents.json'), true);

foreach ($rawIncidents as $incident) {
    $time = trim($incident->custom->time);

    if (strpos($time, '.') !== false) {
        $timeFragments = explode('.', $time, 2);
    } else if (strpos($time, ':') !== false) {
        $timeFragments = explode(':', $time, 2);
    } else {
        echo 'ERROR: unable to decode time' . PHP_EOL;
        continue;
    }

    $timeHours = $timeFragments[0];
    $timeMinutes = $timeFragments[1];

    if (strpos($timeMinutes, 'pm') !== false) {
        $timeHours += 12;
    }

    $timeMinutes = str_replace(['am', 'pm'], '', $timeMinutes);

    $timeHours = str_pad($timeHours, 2, '0', STR_PAD_LEFT);
    $timeMinutes = str_pad($timeMinutes, 2, '0', STR_PAD_LEFT);

    $incidentData = new stdClass();
    $incidentData->id = $incident->id;
    $incidentData->title = $incident->post_title;
    $incidentData->description = $incident->description;
    $incidentData->timestamp = (DateTimeImmutable::createFromFormat('Ymd Hi', $incident->custom->date . ' ' . $timeHours . $timeMinutes))->getTimestamp();
    $incidentData->location = new stdClass();
    $incidentData->location->address = $incident->location->address;
    $incidentData->location->lat = $incident->location->lat;
    $incidentData->location->lng = $incident->location->lng;
    $incidentData->stations = $incident->attending;

    $incidents[$incidentData->id] = $incidentData;
}

file_put_contents(__DIR__ . '/incidents.json', json_encode($incidents));
