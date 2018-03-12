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
$incidents = array_filter($incidents, function ($incident) {
    return !empty($incident->location->lat) && !empty($incident->location->lng);
});


function renderIncidentDays($offset, $count)
{
    global $incidents;

    $prevDateGroup = null;
    $entries = [];
    $daysRendered = 0;

    foreach ($incidents as $incident) {
        if (date('d M Y', $incident->timestamp) !== $prevDateGroup) {
            $daysRendered++;
            $entries[$daysRendered] = '
                <li class="timeline-item period">
                    <div class="timeline-info"></div>
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h2 class="timeline-title">' . date('j F Y', $incident->timestamp) . '</h2>
                    </div>
                </li>';
        }

        $classes = '';

        $searchString = $incident->title . ' ' . $incident->description;
        $classes .= stripos($searchString, 'false alarm') !== false ? 'false-alarm ' : '';
        $classes .= preg_match('/small vehicle|vehicle fire|Fire Vehicle|RTC|Road Traffic Collision|Car Fire/i', $searchString) === 1 ? 'vehicle-fire ' : '';
        $classes .= preg_match('/large vehicle|vehicle large|Lorry Fire/i', $searchString) === 1 ? 'vehicle-fire-large ' : '';
        $classes .= preg_match('/locked in|Shut In|Lift Release|Release (?:of )?person/i', $searchString) === 1 ? 'locked-in ' : '';
        $classes .= preg_match('/small animal|RSPCA|hamster/i', $searchString) === 1 ? 'small-animal ' : '';
        $classes .= preg_match('/aircraft/i', $searchString) === 1 ? 'aircraft ' : '';

        $entries[$daysRendered] .= '<li class="timeline-item">
            <div class="timeline-marker ' . $classes . '"></div>
            <div class="timeline-content">
                <h3 class="timeline-title">' . $incident->title . '</h3>

                <p>' . $incident->description . '</p>
            </div>
        </li>';
        $prevDateGroup = date('d M Y', $incident->timestamp);
    }

    $entries = array_slice($entries, $offset, $count);

    return implode('', $entries);
}
