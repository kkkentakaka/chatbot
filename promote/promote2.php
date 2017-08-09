<?php

function bot($event) {
	if (rand(0, 100)>10) return;

	$text=load('promote/promote.txt');
	reply($event, $text[rand(0, count($text)-1)]);
}
