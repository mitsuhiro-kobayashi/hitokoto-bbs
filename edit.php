<?php
// データベース接続情報追記
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();

session_start();

// 管理者としてログインしているか確認
if (empty($_SESSION['admin_login']) && $_SESSION['admin_login'] !== true) {
    // ログイン情報なければ、adminへ戻る
    header("Location: ./admin.php");
}
if (!empty($_GET['message_id']) && empty($_POST['message_id'])) {

    $message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);

    $mysqli = new mysqli(DB_HOST, DB_USER, '', DB_NAME);

    if ($mysqli->connect_errno) {
        $error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    } else {
        $sql = "SELECT * FROM message WHERE id = $message_id";
        // * で全部指定
        $res = $mysqli->query($sql);

        if ($res) {
            $message_data = $res->fetch_assoc();
        } else {

            // データが読み込めなかったら一覧に戻る
            header("Location: ./admin.php");
        }

        $mysqli->close();
    }
} elseif (!empty($_POST['message_id'])) {
    // ここに編集内容を保存する処理を入れる
    $message_id = (int)htmlspecialchars($_POST['message_id'], ENT_QUOTES);

    if (empty($_POST['view_name'])) {
        $error_message[] = '表示名を入力してください。';
    } else {
        $message_data['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
    }

    if (empty($_POST['message'])) {
        $error_message[] = 'メッセージを入力してください。';
    } else {
        $message_data['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
    }
    if (empty($error_message)) {

        // ここにデータベースに保存する処理が入る
        // データベースに接続
        $mysqli = new mysqli(DB_HOST, DB_USER, '', DB_NAME);

        // 接続エラーの確認
        if ($mysqli->connect_errno) {
            $error_message[] = 'データベースの接続に失敗しました。 エラー番号 ' . $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
        } else {
            $sql = "UPDATE message set view_name = '$message_data[view_name]', message= '$message_data[message]' WHERE id =  $message_id";
            $res = $mysqli->query($sql);
            // UPDATE テーブル名 SET カラム名 = 値 WHERE 条件;
        }

        $mysqli->close();

        // 更新に成功したら一覧に戻る
        if ($res) {
            header("Location: ./admin.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title>ひと言掲示板 管理ページ（投稿の編集）</title>
</head>


<body>
    <h1>ひと言掲示板 管理ページ（投稿の編集）</h1>
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
            <input id="view_name" type="text" name="view_name" value="<?php if (!empty($message_data['view_name'])) {
                                                                            echo $message_data['view_name'];
                                                                        } ?>">
        </div>
        <div>
            <label for="message" name="message">ひと言メッセージ</label>
            <textarea id="message" name="message"><?php if (!empty($message_data['message'])) {
                                                        echo $message_data['message'];
                                                    } ?>
        </textarea>
            <!-- textarea要素でもPHPで入力データを判別するためのname属性 -->
        </div>
        <a class="btn_cancel" href="admin.php">キャンセル</a>
        <input type="submit" name="btn_submit" value="更新">
        <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
        </label>

    </form>
</body>

</html>