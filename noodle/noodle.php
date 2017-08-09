<?php

function bot($event) {
	$limit=100;

	$id=$event->source->userId;
	if (empty($id)) return;

	$text=$event->message->text;
	if (empty($text)) return;

	$lock=lock('noodle/lock.txt');
	$file='noodle/noodle.txt';
	if (!file_exists($file)) {
		save($file, [$id=>[]]);
		chmod($file, 0600);
	}
	$noodle=load($file);
	$list=&$noodle->{$id};

	if (preg_match('/買う/', $text)) {
		if (count($list)<$limit) {
			$item=preg_replace('/買う/', '', $text);
			if (!in_array($item, $list)) {
				$list[]=$item;
				$out=$item.'を在庫リストに追加しました。';
			} else {
				$out=$item."は在庫しています。\n";
				$out.='買わないでおきましょう！';
			}
		} else {
			$out="在庫が一杯です。\n";
			$out.='在庫を食べましょう！';
		}
	} else

	if (preg_match('/食べた/', $text)) {
		$item=preg_replace('/食べた/', '', $text);
		if (in_array($item, $list)) {
			unset($list[array_search($item, $list)]);
			$list=array_values($list);
			$out=$item.'を在庫リストから削除しました。';
		} else {
			$out=$item.'は在庫リストに存在しません。';
		}
	} else

	if (preg_match('/在庫リスト/', $text)) {
		if (!empty($list)) {
			$out="在庫リストです。\n";
			foreach ($list as $item) {
				$out.=$item."\n";
			}
		} else {
			$out="在庫リストは空です。";
		}
	} else

	if (preg_match('/在庫クリア/', $text)) {
		$list=[];
		$out="在庫リストを削除しました。";
	} else
	
	if (preg_match('/何食べよう/', $text)) {
		if (!empty($list)) {
			$out=$list[rand(0, count($list)-1)].'はいかがでしょう？';
		} else {
			$out="在庫リストは空です。\n";
			$out.='買い物に行きましょう！';
		}
	}

	save($file, $noodle);
	unlock($lock);

	if (!empty($out)) reply($event, $out);
}
