<?php

    //ヘッダー情報の設定
    header("Content-Type: application/json; charset=utf-8");
    
    $data = array();
    
    // DB接続情報
    $dsn = 'mysql:host=localhost;dbname=maps';
    $db_username = 'root';
    $db_password = '';
    
    // DB接続情報設定・SQL準備・接続
    
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // 失敗したら例外を投げる
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,   //デフォルトのフェッチモードはクラス
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',   //MySQL サーバーへの接続時に実行するコマンド
    ); 
    
    $dbh = new PDO($dsn, $db_username, $db_password, $options);
    
    $sql = "select exam_name, address, year, month, day, url from exams";
    $sth = $dbh -> prepare($sql);
    $sth -> execute();
    
    //データを取得する
    $data = $sth -> fetchAll(PDO::FETCH_ASSOC);

    //jsonオブジェクト化
    $json_code = json_encode($data);

    echo $json_code;

?>