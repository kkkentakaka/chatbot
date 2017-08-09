<?php
$ermsg = "";
//---------------------------------------------------
// DBに接続する
//---------------------------------------------------
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
 
//---------------------------------------------------
// SQLを実行する
//---------------------------------------------------
function execute( $conn, $sql, $param = array() ) {
    //-------------------
    //クエリのセット
    //-------------------
    $stmt = $conn->prepare( $sql );
 
    //-----------------------
    // バインド変数のセット
    //-----------------------
    foreach( $param as $key => $value ) {
        //$stmt->bindValue( 1, "aaa" );   // ?でbindするとき(1 origin)
        $stmt->bindValue( $key, $value );
    }
 
    //-------------------
    //クエリの実行
    //-------------------
    $stmt->execute();
 
    return $stmt;
}
 
 
//---------------------------------------------------
// PDOテストメイン
//---------------------------------------------------
function testMain() {
    try {
        //----------------------------
        //DBへ接続する
        //----------------------------
        $conn = getConnection() ;
 
        //----------------------------
        // SQLの実行
        //----------------------------
        $sql  = <<< QUERY
            select table_name
            from   INFORMATION_SCHEMA.tables
            where  table_name like "C%"
QUERY;
        $stmt = execute( $conn, $sql );
 
 
        //----------------------------
        // 結果の出力
        //----------------------------
        echo "test case 1----------------------------------------------------\n";
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC ) ) { 
            echo $row[ "table_name" ] . "\n";
        }
 
    } catch ( PDOException $ex ) {
 
        $ermsg =  $ex->getMessage();
        return null;
    }
 
    $conn = null;
}

/**
* [Function test_test]
* @author Kenta
* @return int
*/
function test_test()
{
	global $debug, $connected;
	$result = 0;
	try
	{
		$stmt = $connected->prepare("SELECT * FROM `talk`");
		$stmt->execute();
		$result = $stmt->fetch();
		return $result;
	}
	catch(PDOException $e)
	{
		$result = 0;
		if ($debug)
		{
			echo 'ERROR(test_test): ' . $e->getMessage();
			exit;
		}
	}
	return $result;
}


?>
