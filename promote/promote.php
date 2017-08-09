<?php

function bot($event) {
	$text=load('promote/promote.txt');
	reply($event, $text[rand(0, count($text)-1)]);
}
