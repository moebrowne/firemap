<?php

require 'incidents.php';

if (!is_numeric($_GET['offset']) || !is_numeric($_GET['count'])) {
    die();
}

echo renderIncidentDays($_GET['offset'], $_GET['count']);
