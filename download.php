<?php

// データベース接続情報追記
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_NAME', 'board');


// 変数の初期化
$csv_data = null;
$sql = null;
$res = null;
$message_array = array();
session_start();

if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) {
    //ここにファイル作成&出力する処理を入れる。3つのheader関数を追記。全てPHPの出力形式として、「Content-Type」「ファイル名」「エンコーディング」を順に指定してい
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=メッセージデータ.csv");
    header("Content-Transfer-Encoding: binary");

    // データベースに接続
    $mysqli = new mysqli( DB_HOST, DB_USER, '', DB_NAME);

    // 接続エラーの確認
    if( !$mysqli->connect_errno ) {

        $sql = "SELECT * FROM message ORDER BY post_date ASC";
        $res = $mysqli->query($sql);

        if( $res ) {
            $message_array = $res->fetch_all(MYSQLI_ASSOC);
        }

        $mysqli->close();
    }

    // csvデータを作成
    if( !empty($message_array) ) {

        // １行目のラベル作成
        $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

        foreach( $message_array as $value ) {

            // データを１行ずつcsvファイルに書き込む
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
        }
    }

    // ファイルを出力
    echo $csv_data;

} else {
    // ログインページへリダイレクト
    header("Location: ./admin.php");
}

return;

