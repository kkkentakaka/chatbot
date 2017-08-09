<?php
require_once('functions.php');

function bot($event) {
	if (empty($event->message->text))
	{
		return;
	}else{
		$query_luis = $event->message->text;
		$query_luis = urlencode($query_luis);
		$url_luis = 'https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/5c2e8e80-5e0f-4165-af22-2c8658275812?subscription-key=868d2f96eef54bc08f13455fc6032e96&timezoneOffset=540&verbose=true&q='.$query_luis;
		$json_luis = file_get_contents($url_luis);
		$json_object = mb_convert_encoding($json_luis, 'UTF-8', 'auto');
		$json_object = json_decode($json_object);
		$aaa = $json_object->intents[0]->intent;

		$json_object2 = mb_convert_encoding($json_luis, 'UTF-8', 'auto');
		$json_object2 = json_decode($json_object, false);
		debug('luis', $url_luis);

		if($json_object->intents[0]->intent == 'GetRestaurantInformation' && $json_object->intents[0]->score > 0.5)
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

			$info = 'お店の情報'."\n";

			foreach((array)$object_gnavi as $key => $val){
			   if(strcmp($key, "total_hit_count" ) == 0 ){
			       echo "total:".$val."\n";
			   }

			   if(strcmp($key, "rest") == 0){
			       foreach((array)$val as $restArray){
			            if(checkString($restArray->{'id'}))   $info = $info.'店舗ID：'.$restArray->{'id'}."\n";
			            if(checkString($restArray->{'name'})) $info = $info.'店舗名：'.$restArray->{'name'}."\n";
			            if(checkString($restArray->{'url_mobile'})) $info = $info.'URL：'.$restArray->{'url_mobile'}."\n";
			            if(checkString($restArray->{'access'}->{'line'}))    $info = $info.'路線：'.(string)$restArray->{'access'}->{'line'}."\n";
			            if(checkString($restArray->{'access'}->{'station'})) $info = $info.'駅：'.(string)$restArray->{'access'}->{'station'}."\n";
			            if(checkString($restArray->{'access'}->{'walk'}))    $info = $info.'徒歩：'.(string)$restArray->{'access'}->{'walk'}."分\n";
			 
			            foreach((array)$restArray->{'code'}->{'category_name_s'} as $v){
			                if(checkString($v)) $info = $info.'カテゴリ：'.$v."\n";
			            }
			            $info = $info."\n";
			       }
			 
			   }
			}
			 
			reply($event, $info);

		}
	}
}