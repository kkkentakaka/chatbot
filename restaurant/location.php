<?php

function bot($event) {
	if ($event->message->type!='location') return;

	$text='';
	$text.='タイトル：'.$event->message->title."\n";
	$text.='住所：'.$event->message->address."\n";
	$text.='緯度：'.$event->message->latitude."\n";
	$text.='経度：'.$event->message->longitude."\n";

	reply($event, $text);
}
