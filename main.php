<?php
header("Content-Type: text/html; charset=UTF-8");

define('DEBUG', 'debug.txt');
//require_once('/sdk/src/LINEBot/MessageBuilder/TemplateBuilder/ButtonTemplateBuilder.php');
//require_once('/sdk/src/LINEBot/MessageBuilder/TemplateBuilder/CarouselColumnTemplateBuilder.php');
//require_once('/sdk/src/LINEBot/MessageBuilder/TemplateBuilder/ConfirmTemplateBuilder.php');
//require_once('/sdk/src/LINEBot/TemplateActionBuilder/MessageTemplateActionBuilder.php');

require_once('tool.php');
require_once('GetRestaurantInformation_carousel.php');

$input=file_get_contents('php://input');
debug('input', $input);

if (!empty($input)) {
	$events=json_decode($input)->events;
	foreach ($events as $event) {
		bot($event);
	}
}
