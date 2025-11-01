<?php

declare(strict_types=1);

ini_set('display_errors', 0);

$incidents = (array)json_decode(file_get_contents(__DIR__ . '/incidents.json'));

// Remove incidents without a position
$incidents = array_filter(
    $incidents,
    fn (stdClass $incident): bool => empty($incident->location->lat) === false && empty($incident->location->lng) === false,
);

// Remove incidents without a date
$incidents = array_filter(
    $incidents,
    fn (stdClass $incident): bool => empty($incident->timestamp) === false,
);

usort($incidents, fn (stdClass $a, stdClass $b): int => $b->timestamp <=> $a->timestamp);

return $incidents;
