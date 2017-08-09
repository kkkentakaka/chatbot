<?php

function bot($event) {
	if ($event->type=='message') {
		$object=[
			'replyToken'=>$event->replyToken, 
			'messages'=>[[
				'type'=>'template', 
				'altText'=>'ボタンが表示できません。', 
				'template'=>[
					'type'=>'buttons', 
					'text'=>'ボタンの使用例です。', 
					'actions'=>[
						[
							'type'=>'postback', 
							'label'=>'クマ（Postback）', 
							'data'=>'bear'
						], 
						[
							'type'=>'message', 
							'label'=>'ペンギン（Message）', 
							'text'=>'penguin'
						], 
						[
							'type'=>'uri', 
							'label'=>'ひぐぺん工房（URI）', 
							'uri'=>'http://cgi1.plala.or.jp/~higpen/'
						]
					]
				]
			]]
		];
		post('reply', $object);
	}
	if ($event->type=='postback') {
		reply($event, $event->postback->data);
	}
}
