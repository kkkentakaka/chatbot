<?php

function bot($event) {
	$limit=100;

	$id=$event->source->userId;
	if (empty($id)) return;

	$text=$event->message->text;
	if (empty($text)) return;

	$lock=lock('shopping/lock.txt');
	$file='shopping/shopping.txt';
	if (!file_exists($file)) {
		save($file, [$id=>[]]);
		chmod($file, 0600);
	}
	$shopping=load($file);
	$list=&$shopping->{$id};

	if (preg_match('/買う/', $text)) {
		if (count($list)<$limit) {
			$item=preg_replace('/買う/', '', $text);
			if (!in_array($item, $list)) {
				$list[]=$item;
				$out=$item.'をリストに追加しました。';
			} else {
				$out=$item.'はリストに追加済みです。';
			}
		} else {
			$out='買い物リストが一杯です。';
		}
	} else

	if (preg_match('/買った/', $text)) {
		$item=preg_replace('/買った/', '', $text);
		if (in_array($item, $list)) {
			unset($list[array_search($item, $list)]);
			$list=array_values($list);
			$out=$item.'をリストから削除しました。';
		} else {
			$out=$item.'はリストに存在しません。';
		}
	} else

	if (preg_match('/買い物リスト/', $text)) {
		if (!empty($list)) {
			$out="買い物リストです。\n";
			foreach ($list as $item) {
				$out.=$item."\n";
			}
		} else {
			$out='買い物リストは空です。';
		}
	} else

	if (preg_match('/買い物クリア/', $text)) {
		$list=[];
		$out='買い物リストを削除しました。';
	}

	save($file, $shopping);
	unlock($lock);

	if (!empty($out)) reply($event, $out);
}
