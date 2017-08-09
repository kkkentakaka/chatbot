<?php

function bot($event) {
	$original='https://.../mybot/image/original.jpg';
	$preview='https://.../mybot/image/preview.jpg';
	reply_image($event, $original, $preview);
}
