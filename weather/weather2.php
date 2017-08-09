<?php

function bot($event) {
	$weather=load('http://weather.livedoor.com/forecast/webservice/json/v1?city=130010');
	$text=$weather->location->prefecture."の天気は\n";
	$text.=$weather->forecasts[0]->dateLabel;
	foreach ($weather->forecasts as $forecast) {
		$text.=$forecast->dateLabel.' '.$forecast->telop."\n";
	}
	$text.='です。';
	reply($event, $text);
}
