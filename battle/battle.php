<?php

function image($keyword) {
	$cx='...';
	$key='...';

	$url='https://www.googleapis.com/customsearch/v1';
	$url.='?cx='.$cx;
	$url.='&key='.$key;
	$url.='&q='.urlencode($keyword);
	$url.='&searchType=image';

	$json=load($url);
	$items=$json->items;
	$item=$items[rand(0, count($items)-1)];
	
	$original=$item->link;
	$original=preg_replace('/^http:/', 'https:', $original);

	$preview=$item->image->thumbnailLink;
	$preview=preg_replace('/^http:/', 'https:', $preview);
	
	$result->message=[
		'type'=>'image', 
		'originalContentUrl'=>$original, 
		'previewImageUrl'=>$preview
	];
	$result->total=$json->searchInformation->totalResults;
	return $result;
}

function bot($event) {
	$id=$event->source->userId;
	if (empty($id)) return;

	$text=$event->message->text;
	if (empty($text)) return;

	$lock=lock('battle/lock.txt');
	$file='battle/battle.txt';
	if (!file_exists($file)) {
		save($file, [$id=>[]]);
		chmod($file, 0600);
	}
	$battle=load($file);
	$state=&$battle->{$id};

	if (preg_match('/とたたかう/', $text)) {
		$name=preg_replace('/とたたかう/', '', $text);
		$state->name=$name;
		$state->image=image($name);
		$state->life=$state->image->total;
		$out=$state->name."があらわれた！\n";
		$out.='たいりょく'.$state->life;
	} else

	if ($state->life>0) {
		$damage=0;
		$total=$state->image->total;
		if (preg_match('/こうげき/', $text)) {
			$damage=(int)rand($total*0.3, $total*0.7);
		} else
		if (preg_match('/じゅもん/', $text)) {
			$damage=rand(0, $total);
		}

		if ($damage>0) {
			$out=$state->name.'に'.$damage."のダメージをあたえた！\n";
			$state->life-=$damage;
		}
		if ($state->life<=0) {
			$out.=$state->name.'をたおした！';
		} else {
			if (rand(0, 9)<8) {
				$out.=$state->name."がおそいかかってきた！\n";
				if (rand(0, 9)<4) {
					$out.='かわした！';
				} else
				if (rand(0, 9)<7) {
					$out.='ふせいだ！';
				} else {
					$out.='たおされてしまった！';
					$state->life=0;
				}
			}
		}
	}

	save($file, $battle);
	unlock($lock);

	if (!empty($out)) {
		$object=[
			'replyToken'=>$event->replyToken, 
			'messages'=>[
				$state->image->message, 
				[
					'type'=>'text', 
					'text'=>$out
				]
			]
		];
		post('reply', $object);
	}
}
