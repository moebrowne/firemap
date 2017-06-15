<?php

$incidents = json_decode(file_get_contents('fire.json'))->context->incidentsBar;

var_dump(count($incidents));

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Fire Map</title>
	<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<style type="text/css">
		body {
			color: #768390;
			background: #FFF;
			font-family: "Effra", Helvetica, sans-serif;
			padding: 0;
			-webkit-font-smoothing: antialiased;
		}

		h1, h2, h3, h4, h5, h6 {
			color: #3D4351;
			margin-top: 0;
		}

		a {
			color: #FF6B6B;
		}
		a:hover {
			color: #ff9a9a;
			text-decoration: none;
		}

		.example-header {
			background: #3D4351;
			color: #FFF;
			font-weight: 300;
			padding: 3em 1em;
			text-align: center;
		}
		.example-header h1 {
			color: #FFF;
			font-weight: 300;
			margin-bottom: 20px;
		}
		.example-header p {
			font-size: 12px;
			text-transform: uppercase;
			letter-spacing: 3px;
			font-weight: 700;
		}

		.container-fluid .row {
			padding: 0 0 4em 0;
		}
		.container-fluid .row:nth-child(even) {
			background: #F1F4F5;
		}

		.example-title {
			text-align: center;
			margin-bottom: 60px;
			padding: 3em 0;
			border-bottom: 1px solid #E4EAEC;
		}
		.example-title p {
			margin: 0 auto;
			font-size: 16px;
			max-width: 400px;
		}

		/*==================================
			TIMELINE
		==================================*/
		/*-- GENERAL STYLES
		------------------------------*/
		.timeline {
			line-height: 1.4em;
			list-style: none;
			margin: 0;
			padding: 0;
			width: 100%;
		}
		.timeline h1, .timeline h2, .timeline h3, .timeline h4, .timeline h5, .timeline h6 {
			line-height: inherit;
		}

		/*----- TIMELINE ITEM -----*/
		.timeline-item {
			padding-left: 40px;
			position: relative;
		}
		.timeline-item:last-child {
			padding-bottom: 0;
		}

		/*----- TIMELINE INFO -----*/
		.timeline-info {
			font-size: 12px;
			font-weight: 700;
			letter-spacing: 3px;
			margin: 0 0 .5em 0;
			text-transform: uppercase;
			white-space: nowrap;
		}

		/*----- TIMELINE MARKER -----*/
		.timeline-marker {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			width: 15px;
		}
		.timeline-marker:before {
			background: #FF6B6B;
			border: 3px solid transparent;
			border-radius: 100%;
			content: "";
			display: block;
			height: 15px;
			position: absolute;
			top: 4px;
			left: 0;
			width: 15px;
			transition: background 0.3s ease-in-out, border 0.3s ease-in-out;
		}
		.timeline-marker:after {
			content: "";
			width: 3px;
			background: #CCD5DB;
			display: block;
			position: absolute;
			top: 24px;
			bottom: 0;
			left: 6px;
		}
		.timeline-item:last-child .timeline-marker:after {
			content: none;
		}

		.timeline-item:not(.period):hover .timeline-marker:before {
			background: transparent;
			border: 3px solid #FF6B6B;
		}

		/*----- TIMELINE CONTENT -----*/
		.timeline-content {
			padding-bottom: 40px;
		}
		.timeline-content p:last-child {
			margin-bottom: 0;
		}

		/*----- TIMELINE PERIOD -----*/
		.period {
			padding: 0;
		}
		.period .timeline-info {
			display: none;
		}
		.period .timeline-marker:before {
			background: transparent;
			content: "";
			width: 15px;
			height: auto;
			border: none;
			border-radius: 0;
			top: 0;
			bottom: 30px;
			position: absolute;
			border-top: 3px solid #CCD5DB;
			border-bottom: 3px solid #CCD5DB;
		}
		.period .timeline-marker:after {
			content: "";
			height: 32px;
			top: auto;
		}
		.period .timeline-content {
			padding: 40px 0 70px;
		}
		.period .timeline-title {
			margin: 0;
		}

		/*----------------------------------------------
			MOD: TIMELINE SPLIT
		----------------------------------------------*/
		@media (min-width: 768px) {
			.timeline-split .timeline, .timeline-centered .timeline {
				display: table;
			}
			.timeline-split .timeline-item, .timeline-centered .timeline-item {
				display: table-row;
				padding: 0;
			}
			.timeline-split .timeline-info, .timeline-centered .timeline-info,
			.timeline-split .timeline-marker,
			.timeline-centered .timeline-marker,
			.timeline-split .timeline-content,
			.timeline-centered .timeline-content,
			.timeline-split .period .timeline-info,
			.timeline-centered .period .timeline-info {
				display: table-cell;
				vertical-align: top;
			}
			.timeline-split .timeline-marker, .timeline-centered .timeline-marker {
				position: relative;
			}
			.timeline-split .timeline-content, .timeline-centered .timeline-content {
				padding-left: 30px;
			}
			.timeline-split .timeline-info, .timeline-centered .timeline-info {
				padding-right: 30px;
			}
			.timeline-split .period .timeline-title, .timeline-centered .period .timeline-title {
				position: relative;
				left: -45px;
			}
		}

		/*----------------------------------------------
			MOD: TIMELINE CENTERED
		----------------------------------------------*/
		@media (min-width: 992px) {
			.timeline-centered,
			.timeline-centered .timeline-item,
			.timeline-centered .timeline-info,
			.timeline-centered .timeline-marker,
			.timeline-centered .timeline-content {
				display: block;
				margin: 0;
				padding: 0;
			}
			.timeline-centered .timeline-item {
				padding-bottom: 40px;
				overflow: hidden;
			}
			.timeline-centered .timeline-marker {
				position: absolute;
				left: 50%;
				margin-left: -7.5px;
			}
			.timeline-centered .timeline-info,
			.timeline-centered .timeline-content {
				width: 50%;
			}
			.timeline-centered > .timeline-item:nth-child(odd) .timeline-info {
				float: left;
				text-align: right;
				padding-right: 30px;
			}
			.timeline-centered > .timeline-item:nth-child(odd) .timeline-content {
				float: right;
				text-align: left;
				padding-left: 30px;
			}
			.timeline-centered > .timeline-item:nth-child(even) .timeline-info {
				float: right;
				text-align: left;
				padding-left: 30px;
			}
			.timeline-centered > .timeline-item:nth-child(even) .timeline-content {
				float: left;
				text-align: right;
				padding-right: 30px;
			}
			.timeline-centered > .timeline-item.period .timeline-content {
				float: none;
				padding: 0;
				width: 100%;
				text-align: center;
			}
			.timeline-centered .timeline-item.period {
				padding: 50px 0 90px;
			}
			.timeline-centered .period .timeline-marker:after {
				height: 30px;
				bottom: 0;
				top: auto;
			}
			.timeline-centered .period .timeline-title {
				left: auto;
			}
		}

		/*----------------------------------------------
			MOD: MARKER OUTLINE
		----------------------------------------------*/
		.marker-outline .timeline-marker:before {
			background: transparent;
			border-color: #FF6B6B;
		}
		.marker-outline .timeline-item:hover .timeline-marker:before {
			background: #FF6B6B;
		}

	</style>
</head>
<body>
<div class="container-fluid">
	<div class="row example-basic">
		<div class="col-md-12 example-title">
			<h2>Basic Timeline</h2>
			<p>Extra small devices (phones, less than 768px)</p>
		</div>
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2">
			<ul class="timeline">
                <?php foreach($incidents as $incident) : ?>

				<li class="timeline-item">
					<div class="timeline-info">
						<span><?= date('M d, Y', $incident->timestamp); ?></span>
					</div>
					<div class="timeline-marker"></div>
					<div class="timeline-content">
						<h3 class="timeline-title"><?= $incident->post_title; ?></h3>
						<p><?= $incident->description; ?></p>
					</div>
				</li>

                <?php endforeach; ?>
				<li class="timeline-item period">
					<div class="timeline-info"></div>
					<div class="timeline-marker"></div>
					<div class="timeline-content">
						<h2 class="timeline-title">April 2016</h2>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
</body>
</html>