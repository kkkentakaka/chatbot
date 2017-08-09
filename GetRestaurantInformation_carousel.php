<?php
require_once('functions.php');

function bot($event) {
	//ユーザーID(識別用ID)を取得
	$userId = $event->source->userId;

	if (empty($event->message->text))
	{
		return;
	}else{
		$input_text = $event->message->text;
		$query_luis = urlencode($input_text);
		$url_luis = 'https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/5c2e8e80-5e0f-4165-af22-2c8658275812?subscription-key=868d2f96eef54bc08f13455fc6032e96&timezoneOffset=540&verbose=true&q='.$query_luis;
		$json_luis = file_get_contents($url_luis);
		$json_object = mb_convert_encoding($json_luis, 'UTF-8', 'auto');
		$json_object = json_decode($json_object);
		$aaa = $json_object->intents[0]->intent;

		$json_object2 = mb_convert_encoding($json_luis, 'UTF-8', 'auto');
		$json_object2 = json_decode($json_object, false);
		debug('luis', $url_luis);

		if($json_object->intents[0]->intent == 'GetRestaurantInformation' && $json_object->intents[0]->score > 0.8)
		{
			if($json_object->entities[0]->type == 'timei' && $json_object->entities[0]->score > 0.5)
			{
				$areaname = $json_object->entities[0]->entity;
				$area = getAreaCode($areaname);
			}

			if($json_object->entities[1]->type == 'shurui' && $json_object->entities[0]->score > 0.5)
			{
				$shurui = $json_object->entities[1]->entity;
				$category = getCategory($shurui);
			}

			//エンドポイントのURIとフォーマットパラメータを変数に入れる
			$uri_gnavi   = "https://api.gnavi.co.jp/RestSearchAPI/20150630/";
			//APIアクセスキーを変数に入れる
			$acckey_gnavi= "c7e116108df9f2271086324e8c24c6f2";
			//返却値のフォーマットを変数に入れる
			$format= "json";
			//URL組み立て
			$url_gnavi = sprintf("%s%s%s%s%s", $uri_gnavi, "?format=", $format, "&keyid=", $acckey_gnavi);

			if(!empty($area))
			{
				$url_gnavi .= "&areacode_m=".$area;
			}

			if(!empty($category))
			{
				$url_gnavi .= "&category_s=".$category;
			}

			//API実行
			$json_gnavi = file_get_contents($url_gnavi);
			//取得した結果をオブジェクト化
			$object_gnavi = json_decode($json_gnavi);
			//$columnsのクリア
			$columns = '';
			unset($columns);
			//$part_of_columnsのクリア
			$part_of_columns = '';
			unset($part_of_columns);

			$type = 'uri';
			$label ='詳細を見る';

			foreach((array)$object_gnavi as $key => $val){
			   if(strcmp($key, "total_hit_count" ) == 0 ){
			       $total_hit_count = $val;
			   }

			   if(strcmp($key, "rest") == 0){
			       $n = 0;
			       foreach((array)$val as $restArray){
			         if($n >= 5){
                                    break;
                                 }else{
			            if(checkString($restArray->{'image_url'}->{'shop_image1'})) $thumbnailImageUrl = $restArray->{'image_url'}->{'shop_image1'};
			            if(checkString($restArray->{'name'})) $title = $restArray->{'name'};
			            if(checkString($restArray->{'pr'}->{'pr_short'})) $text = $restArray->{'pr'}->{'pr_short'};
			            if(checkString($restArray->{'url_mobile'})) $uri = $restArray->{'url_mobile'};

			            $action = array('type'=>$type, 'label'=>$label, 'uri'=>$uri);
			            $text = str_replace(array("\r\n", "\r", "\n"), ' ', $text);
			            $text = str_replace("<BR>", ' ', $text);
			            if(mb_strlen($title) >= 40) $title = mb_substr($title, 0, 39);
			            if(mb_strlen($text) >= 45) $text = mb_substr($text, 0, 45).'...';
			            $part_of_columns = array('thumbnailImageUrl'=>$thumbnailImageUrl, 'title'=>$title, 'text'=>$text, 'actions'=>array($action));
			            $columns[] = $part_of_columns;
			            $n++;
			         }
			       }
			   }
			}

			$messages = [
			    'type' => 'template',
			    'altText' => 'カルーセル',
			    'template' => [
			        'type' => 'carousel',
			        'columns' => $columns
			    ]
			];

//----1----が入っていた場所
			reply_carousel($event, $messages);

		}else{
		//レストラン検索にHitしない場合、雑談ボットで返事をする
			$connected = getConnection();
			$context = get_context($userId);
			debug('get_context', $context);
			$message = chat($input_text, $userId, $context);
			save_context($userId, $message->context);
			reply($event, $message->utt);
		}
	}
}


//----1-----
//    // カルーセルタイプ
//    $messages = [
//        'type' => 'template',
//        'altText' => 'カルーセル',
//        'template' => [
//            'type' => 'carousel',
//            'columns' => [
//                [
//                    'title' => 'カルーセル1',
//                    'text' => 'カルーセル1です',
//                    'actions' => [
//                        [
//                            'type' => 'postback',
//                            'label' => 'webhookにpost送信',
//                            'data' => 'value'
//                        ],
//                        [
//                            'type' => 'uri',
//                            'label' => '美容の口コミ広場を見る',
//                            'uri' => 'http://clinic.e-kuchikomi.info/'
//                        ]
//                    ]
//                ],
//                [
//                    'title' => 'カルーセル2',
//                    'text' => 'カルーセル2です',
//                    'actions' => [
//                        [
//                            'type' => 'postback',
//                            'label' => 'webhookにpost送信',
//                            'data' => 'value'
//                        ],
//                        [
//                            'type' => 'uri',
//                            'label' => '女美会を見る',
//                            'uri' => 'https://jobikai.com/'
//                        ]
//                    ]
//                ],
//            ]
//        ]
//    ];
