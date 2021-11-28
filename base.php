<?php
// メッセージを保存するファイルのパス設定
// 「message.txt」へのパスを「定数」として宣言
// 定数名は通常の変数と見分けやすいように、アルファベットの大文字で指定する慣習があります
// define( 'FILENAME', './message.txt');

// データベース接続情報追記
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
// 今回はファイルを開くコードの他に、上の方で「 変数の初期化」を追加しました。
// 変数の初期化とは、変数をあらかじめ「null」など空の値で宣言しておくことで存在しない変数を参照するエラーを防いだり、型をあらかじめ設定しておくことで意図しない動作を防ぐことができます。
// また、複数のエンジニア間でコードを共有する際にも使用する変数を共有し、コード全体の動きや意図をトレースしやすくする役割を持ちます。
// PHPでは変数の型を動的に変更する機能があるため必須ではありませんが、出来るだけ宣言しておくと後々メンテナンスが楽になるため強くオススメしたい慣習です
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();
session_start();


if (!empty($_POST['btn_submit'])) {
    // 表示名の入力チェック：バリデーションを実装
    if (empty($_POST['view_name'])) {
        $error_message[] = '表示名を入力してください。';
    } else {
        $clean['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);

        // セッションに表示名を保存
        $_SESSION['view_name'] = $clean['view_name'];
    }

    if (empty($_POST['message'])) {
        $error_message[] = 'ひと言メッセージを入力してください';
    } else {
        $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
    }
    if (empty($error_message)) {

        // データベースに接続
        $mysqli = new mysqli(DB_HOST, DB_USER, '', DB_NAME);
        /* mysqliクラスのオブジェクトには、データベースへの接続情報として「ホスト名」「ユーザー名」「パスワード」「データベース名」を順に指定します。
        それぞれの情報は環境によって異なりますが、最後の「データベース名」については前回作成したデータベースを使用するので「board」を指定してください
        */

        // 接続エラーの確認
        if ($mysqli->connect_errno) {
            $error_message[] = '書き込みに失敗しました。　エラー番号　' . $mysqli->connect_errno . ' ： ' . $mysqli->connect_error;
        } else {
            //　文字コード設定
            $mysqli->set_charset('utf8');

            // 書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");

            // データを登録するsql作成
            $sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]','$clean[message]','$now_date')";

            // データを登録
            $res = $mysqli->query($sql);

            if ($res) {
                $_SESSION['$success_message'] = 'メッセージを書き込みました。';
            } else {
                $error_message[] = '書き込みに失敗しました。';
            }
            // データベースの接続を閉じる
            $mysqli->close();
        }
        header('Location: ./');
    }
}

// データベースに接続
$mysqli = new mysqli(DB_HOST, DB_USER, '', DB_NAME);
// 接続エラーの確認
if ($mysqli->connect_errno) {
    $error_message[] = 'データの読み込みに失敗しました。　エラー番号　' . $mysqli->connect_errno . '：' . $mysqli->connect_error;
} else {
    $sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
    $res = $mysqli->query($sql);

    if ($res) {
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title>ひと言掲示板</title>
</head>

<body>
    <h1>ひと言掲示板</h1>
    <?php if (empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])) : ?>
        <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($error_message)) : ?>
        <ul class="error_message">
            <?php foreach ($error_message as $value) : ?>
                <li>・<?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <!-- ここにメッセージの入力フォームを設置 -->
    <form method="post">
        <!-- <label> を <input> 要素と関連付けると、いくらかの利点が発生 -->
        <!-- <label> を <input> 要素に関連付けるには、 <input> に id 属性を設定しなければなりません。そして <label> に for 属性を設定して、値を input の id と同じにします -->
        <div>
            <label for="view_name">表示名</label>
            <input id="view_name" type="text" name="view_name" value="<?php if (!empty($_SESSION['view_name'])) {
                                                                            echo $_SESSION['view_name'];
                                                                        } ?>">
        </div>
        <div>
            <label for="message" name="message">ひと言メッセージ</label>
            <textarea id="message" name="message"></textarea>
            <!-- textarea要素でもPHPで入力データを判別するためのname属性 -->
        </div>
        <input type="submit" name="btn_submit" value="書き込む">
        </label>

    </form>
    <hr>
    <section>
        <!-- ここに投稿されたメッセージを表示 -->
        <?php if (!empty($message_array)) { ?>
            <?php foreach ($message_array as $value) { ?>
                <article>
                    <div class="info">
                        <h2><?php echo $value['view_name']; ?></h2>
                        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                    </div>
                    <p><?php echo nl2br($value['message']); ?></p>
                </article>
            <?php } ?>
        <?php } ?>
    </section>
</body>

</html>