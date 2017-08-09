<?php

function getAreaCode($areaname)
{
	if($areaname == '天神'){
	$area = AREAM5012;
	}
	if($areaname == '親富孝'){
	$area = AREAM5032;
	}
	if($areaname == '大名'){
	$area = AREAM5014;
	}
	if($areaname == '赤坂'){
	$area = AREAM5016;
	}
	if($areaname == '今泉'){
	$area = AREAM5022;
	}
	if($areaname == '警固'){
	$area = AREAM5024;
	}
	if($areaname == '薬院'){
	$area = AREAM5026;
	}
	if($areaname == '平尾・清川'){
	$area = AREAM5076;
	}
	if($areaname == '大濠'){
	$area = AREAM5034;
	}
	if($areaname == '六本松'){
	$area = AREAM5036;
	}
	if($areaname == '桜坂・小笹'){
	$area = AREAM5038;
	}
	if($areaname == '博多'){
	$area = AREAM5042;
	}
	if($areaname == '博多'){
	$area = AREAM5042;
	}
	if($areaname == '博多'){
	$area = AREAM5042;
	}
	if($areaname == '中洲'){
	$area = AREAM5052;
	}
	if($areaname == '西中洲・春吉'){
	$area = AREAM5054;
	}
	if($areaname == '川端・祇園'){
	$area = AREAM5062;
	}
	if($areaname == '大橋'){
	$area = AREAM5074;
	}
	if($areaname == '高宮'){
	$area = AREAM5072;
	}
	if($areaname == 'その他南区'){
	$area = AREAM5086;
	}
	if($areaname == '百道・藤崎'){
	$area = AREAM5092;
	}
	if($areaname == 'ヤフードーム周辺'){
	$area = AREAM5094;
	}
	if($areaname == '西新'){
	$area = AREAM5096;
	}
	if($areaname == 'その他早良区'){
	$area = AREAM5098;
	}
	if($areaname == '姪浜・小戸'){
	$area = AREAM5102;
	}
	if($areaname == 'その他西区・糸島'){
	$area = AREAM5106;
	}
	if($areaname == '香椎'){
	$area = AREAM5112;
	}
	if($areaname == '箱崎'){
	$area = AREAM5114;
	}
	if($areaname == 'その他東区'){
	$area = AREAM5116;
	}
	if($areaname == '別府'){
	$area = AREAM5122;
	}
	if($areaname == 'その他城南区'){
	$area = AREAM5124;
	}
return $area;
};

//文字列であるかをチェック
function checkString($input)
{
 
    if(isset($input) && is_string($input)) {
        return true;
    }else{
        return false;
    }
 
};

function getCategory($shurui)
{
  /*****************************************************************************************
   　ぐるなびWebサービスのマスタ検索APIをパースするプログラム
   　注意：アクセスキーはユーザ登録後に発行されるキーを指定してください。
  *****************************************************************************************/

  //エンドポイントのURIとフォーマットパラメータを変数に入れる
  $uri   = "https://api.gnavi.co.jp/master/CategorySmallSearchAPI/20150630/";
  //APIアクセスキーを変数に入れる
  $acckey= "c7e116108df9f2271086324e8c24c6f2";
  //返却値のフォーマットを指定
  $format= "json";

  //URI組み立て
  $url  = sprintf("%s%s%s%s%s", $uri, "?format=", $format,"&keyid=", $acckey);
  //API実行
  $json = file_get_contents($url);
  //取得した結果をオブジェクト化
  $obj  = json_decode($json);
  //結果をパース
  foreach((array)$obj as $key => $val){
     if(strcmp($key,"category_s") == 0){
         foreach($val as $k =>$v){
            if(preg_match('/'.$v->{'category_s_name'}.'/', $shurui)) return $v->{'category_s_code'};
         }
     }
  }
};