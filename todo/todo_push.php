<?php

function bot_push() {
	date_default_timezone_set('Japan');

	$lock=lock('todo/lock.txt');
	$file='todo/todo.txt';
	$todo=load($file);

	foreach ($todo as $id=>$list) {
		$out='';
		$rest=[];
		foreach ($list as $item) {
			if (strtotime(substr($item, 0, 5))<=time()) {
				$out.=$item."\n";
			} else {
				$rest[]=$item;
			}
		}
		$todo->{$id}=$rest;
		if (!empty($out)) {
			$out="予定の時刻です。\n".$out;
			push($id, $out);
		}
	}

	save($file, $todo);
	unlock($lock);
}
