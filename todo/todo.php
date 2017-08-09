<?php

function bot($event) {
	$limit=100;

	$id=$event->source->userId;
	if (empty($id)) return;

	$text=$event->message->text;
	if (empty($text)) return;
	
	$lock=lock('todo/lock.txt');
	$file='todo/todo.txt';
	if (!file_exists($file)) {
		save($file, [$id=>[]]);
		chmod($file, 0600);
	}
	$todo=load($file);
	$list=&$todo->{$id};

	if (preg_match('/([0-9]+):([0-9]+) *(.*)/', $text, $capture)) {
		if (count($list)<$limit) {
			$hour=substr('0'.$capture[1], -2);
			$minute=substr('0'.$capture[2], -2);
			$list[]=$hour.':'.$minute.' '.$capture[3];
			$out='予定に追加しました。';
		} else {
			$out='予定が一杯です。';
		}
	} else

	if (preg_match('/予定リスト/', $text)) {
		if (!empty($list)) {
			$out="予定の一覧です。\n";
			sort($list);
			foreach ($list as $item) {
				$out.=$item."\n";
			}
		} else {
			$out='予定はありません。';
		}
	} else

	if (preg_match('/予定クリア/', $text)) {
		$list=[];
		$out='予定を削除しました。';
	}

	save($file, $todo);
	unlock($lock);

	if (!empty($out)) reply($event, $out);
}
