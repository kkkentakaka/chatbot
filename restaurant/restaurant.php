<?php

function bot($event) {
	$keyid='...';
	if ($event->message->type!='location') return;
	
	debug('latitude', $event->message->latitude);
	debug('longitude', $event->message->longitude);

	$url='https://api.gnavi.co.jp/RestSearchAPI/20150630/';
	$url.='?format=json&hit_per_page=5';
	$url.='&keyid='.$keyid;
	$url.='&latitude='.$event->message->latitude;
	$url.='&longitude='.$event->message->longitude;
	debug('url', $url);
	
	$result=load($url);
	$text='近くのレストランをお知らせします。';
	foreach ($result->rest as $rest) {
		$text.="\n\n";
		$text.=$rest->name;
		$text.='（'.$rest->category."）\n";
		$text.=$rest->url;
	}
	reply($event, $text);
}
