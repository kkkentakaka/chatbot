<?php

function bot($event) {
	$limit=100;

	$id=$event->source->userId;
	if (empty($id)) return;

	$text=$event->message->text;
	if (empty($text)) return;
	
	$lock=lock('mimic/lock2.txt');
	$file='mimic/mimic2.txt';
	if (!file_exists($file)) {
		save($file, [$id=>[]]);
		chmod($file, 0600);
	}
	$mimic=load($file);
	$list=&$mimic->{$id};
	if (!in_array($text, $list)) $list[]=$text;
	if (count($list)>$limit) array_shift($list);
	save($file, $mimic);
	unlock($lock);

	reply($event, $list[rand(0, count($mimic->{$id})-1)]);
}
