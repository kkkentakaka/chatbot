<?php

function bot_push() {
	$to='...';
	date_default_timezone_set('Japan');
	push($to, date('ただいまG時i分s秒です。'));
}
