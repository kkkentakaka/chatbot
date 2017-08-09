<?php

function getConnection() {
    $server   = "mysql109.phy.lolipop.lan";              // 実際の接続値に置き換える
    $user     = "LAA0870025";                           // 実際の接続値に置き換える
    $pass     = "199247";                           // 実際の接続値に置き換える
    $database = "LAA0870025-bot";                      // 実際の接続値に置き換える
    //-------------------
    //DBに接続
    //-------------------
    $pdo = new PDO("mysql:host=" . $server . "; dbname=".$database, $user, $pass );
    return $pdo;
}

$connected = getConnection();

define('TOKEN', '2m7JkfSfUsPQGSkHzvDt6mgHcVK0p2Lsl2Lqx3Z4hTAhYnuQjc5otiQ46etbtCLKBvDaCcT9jJzCo+IZrn+56QIpV5xVHKHP3FcOPLhIb6H0wsO6G6rZsoEB1e2aT+ih6bJmc09QyTwmvRxjZDN8kAdB04t89/1O/w1cDnyilFU=');

//debug.phpに上書きをする為、コメントアウト
//if (file_exists(DEBUG)) unlink(DEBUG);

/**
* [Function debug]
* @author Kenta
* バグ情報をdebug.txtに書き込むためのファンクション
*/
function debug($title, $text) {
	$datetime_now = date("YmdHis", time());
	file_put_contents(DEBUG, $datetime_now."\n".'['.$title.']'."\n".$text."\n\n", FILE_APPEND);
}

/**
* [Function post]
* @author Kenta
* ラインで返事をするファンクション
*/
function post($url, $object) {
	debug('output_object', print_r($object, true));
	$json=json_encode($object);
	debug('output', $json);

	$curl=curl_init('https://api.line.me/v2/bot/message/'.$url);
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer '.TOKEN
	]);

	$result=curl_exec($curl);
	debug('result', $result);

	curl_close($curl);
}

/**
* [Function post_luis]
* @author Kenta
* LUISにPOSTするファンクション
*/
function post_luis($object) {
	$json=json_encode($object);

	$curl=curl_init('https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/5c2e8e80-5e0f-4165-af22-2c8658275812?subscription-key=868d2f96eef54bc08f13455fc6032e96&timezoneOffset=0&verbose=true&q=');
	curl_setopt($curl, CURLOPT_POST, TRUE);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer '
	]);

	$result_luis=curl_exec($curl);

	curl_close($curl);
}

/**
* [Function reply]
* @author Kenta
* 指定のメッセージを返すファンクション
*/
function reply($event, $text) {
	$object=[
		'replyToken'=>$event->replyToken, 
		'messages'=>[['type'=>'text', 'text'=>$text]]
	];
	post('reply', $object);
}

/**
* [Function reply_carousel]
* @author Kenta
* カルーセルで返事するファンクション
*/
function reply_carousel($event, $messages) {
	$object=[
		'replyToken'=>$event->replyToken, 
		'messages'=>[$messages]
	];
	post('reply', $object);
}

//function reply_carousel_sample($event, $columns) {
//	$object=[
//		'replyToken'=>$event->replyToken, 
//		'messages'=>[$columns]
//	];
//	post('reply', $object);
//}

/**
* [Function reply_image]
* @author Kenta
* 画像を返事に使うファンクション
*/
function reply_image($event, $original, $preview) {
	$object=[
		'replyToken'=>$event->replyToken, 
		'messages'=>[[
			'type'=>'image', 
			'originalContentUrl'=>$original, 
			'previewImageUrl'=>$preview
		]]
	];
	post('reply', $object);
}

/**
* [Function push]
* @author Kenta
* PUSHをするファンクション
*/
function push($to, $text) {
	$object=[
		'to'=>$to, 
		'messages'=>[['type'=>'text', 'text'=>$text]]
	];
	post('push', $object);
}

function load($file) {
	$json=file_get_contents($file);
	return json_decode($json);
}

function save($file, $object) {
	$json=json_encode($object);
	file_put_contents($file, $json);
}

function lock($file) {
	$fp=fopen($file, 'c');
	flock($fp, LOCK_EX);
	return $fp;
}

function unlock($fp) {
	flock($fp, LOCK_UN);
	fclose($fp);
}


/**
* [Function get_displayName]
* @author Kenta
* ユーザー名を取得するファンクション
*/
function get_displayName($mid){
	$url = "https://api.line.me/v2/bot/profile/".$mid;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Authorization: Bearer '.TOKEN
	]);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($curl);
	$receive = json_decode($output);
	debug('userInformation', $output);

	return $receive->displayName;
}

/**
* [Function reply_displayName]
* @author Kenta
* ユーザー名を返事するファンクション
*/
function reply_displayName($event){

	$userId = $event->source->userId;
	$messages = get_displayName($userId);
	reply($event, $messages);
}



/**
* [Function get_context]
* @author Kenta
* @return int
*/
function get_context($userId)
{
	global $debug, $connected;
	$result = '';
	try
	{
		$stmt = $connected->prepare("SELECT PREVIOUS_TALK as PREVIOUS_TALK, COUNT(*) as total FROM `talk` WHERE FROM_ID = :userId");
		$stmt->bindValue(':userId', (string)$userId, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch();
		$context = $result['PREVIOUS_TALK'];
		$total = (int)$result['total'];
		if($total == 0){
			$context = '';
		}
		return $context;
	}
	catch(PDOException $e)
	{
		$result = 0;
		if ($debug)
		{
			echo 'ERROR(get_context): ' . $e->getMessage();
			exit;
		}
	}
	return $result;
}

/**
* [Function save_context]
* @author Kenta
* @return int
*/
function save_context($userId, $context)
{
	global $debug, $connected;
	$result = 0;
	try
	{
		$stmt = $connected->prepare("SELECT COUNT(*) as total FROM `talk` WHERE FROM_ID = :userId");
		$stmt->bindValue(':userId', (string)$userId, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch();
		$total = (int)$result['total'];
		debug('total', $total);
		if($total == 0){
			$stm = $connected->prepare("INSERT INTO `talk` (FROM_ID, PREVIOUS_TALK) VALUES (:userId, :context)");
			$stm->bindValue(':userId', (string)$userId, PDO::PARAM_STR);
			$stm->bindValue(':context', (string)$context, PDO::PARAM_STR);
			$stm->execute();
		}else{
			$stmt1 = $connected->prepare("UPDATE `talk` SET PREVIOUS_TALK = :context WHERE FROM_ID = :userId");
			$stmt1->bindValue(':userId', (string)$userId, PDO::PARAM_STR);
			$stmt1->bindValue(':context', (string)$context, PDO::PARAM_STR);
			$stmt1->execute();
		}
	}
	catch(PDOException $e)
	{
		$result = 0;
		if ($debug)
		{
			echo 'ERROR(save_context): ' . $e->getMessage();
			exit;
		}
	}
	return $result;
}


/**
* [Function chat]
* @author Kenta
* おしゃべりできるファンクション
*/
function chat($message, $userId, $context){

	$api_key = '645132456a75567450573378362f58746d343464345253776f7473346a65594f746b384e6c716b4e6c2f41';
	$api_url = sprintf('https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=%s', $api_key);
	$req_body = array(
		'utt' => $message,
		'context' => $context,
		'mode' => "dialog"
	);

	$headers = array(
		'Content-Type: application/json; charset=UTF-8',
	);
	$options = array(
		'http'=>array(
			'method'  => 'POST',
			'header'  => implode("\r\n", $headers),
			'content' => json_encode($req_body)
			)
		);
	$stream = stream_context_create($options);
	$response = file_get_contents($api_url, false, $stream);
	$res = json_decode($response);

	$context_for_save = $res->context;

	debug('zatudan', print_r($response, true));

	return $res;
}