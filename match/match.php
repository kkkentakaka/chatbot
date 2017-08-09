<?php

function bot($event) {
	if (empty($event->message->text)) return;
	if (preg_match('/おはよう/', $event->message->text)) {
		reply($event, 'おはようございます！');
	}
}
