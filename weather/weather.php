<?php

function bot($event) {
	$weather=load('http://weather.livedoor.com/forecast/webservice/json/v1?city=130010');
	reply($event, $weather->description->text);
}
