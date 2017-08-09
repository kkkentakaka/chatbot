<?php

function bot($event) {
	if (preg_match('/おみくじ/', $event->message->text)) {
		$object=[
			'replyToken'=>$event->replyToken, 
			'messages'=>[[
				'type'=>'template', 
				'altText'=>'ボタンが表示できません。', 
				'template'=>[
					'type'=>'buttons', 
					'text'=>'どのおみくじを引きますか？', 
					'actions'=>[
						[
							'type'=>'postback', 
							'label'=>'上段', 
							'data'=>'0'
						], 
						[
							'type'=>'postback', 
							'label'=>'中段', 
							'data'=>'1'
						], 
						[
							'type'=>'postback', 
							'label'=>'下段', 
							'data'=>'2'
						]
					]
				]
			]]
		];
		post('reply', $object);
	}
	if ($event->type=='postback') {
		$fortune=['大吉', '小吉', '凶'];
		$out='おみくじの結果は「';
		$count=count($fortune);
		$out.=$fortune[(rand(0, $count-1)+$event->postback->data)%$count];
		$out.='」でした';
		reply($event, $out);
	}
}
