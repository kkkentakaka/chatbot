<?php

function bot($event) {
	$limit=100;

	$text=$event->message->text;
	if (empty($text)) return;
	
	$lock=lock('mimic/lock.txt');
	$file='mimic/mimic.txt';
	$mimic=load($file);
	if (!in_array($text, $mimic)) $mimic[]=$text;
	if (count($mimic)>$limit) array_shift($mimic);
	save($file, $mimic);
	unlock($lock);

	reply($event, $mimic[rand(0, count($mimic)-1)]);
}
