<?php
// メッセージを保存するファイルのパス設定
// 「message.txt」へのパスを「定数」として宣言
// 定数名は通常の変数と見分けやすいように、アルファベットの大文字で指定する慣習があります
define( 'FILENAME', './message.txt');

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


if( !empty($_POST['btn_submit'])){
    if( $file_handle = fopen( FILENAME, "a")){
        //ファイルを無事に開くことができると、$file_handleに「ファイルポインターリソース」と呼ばれるファイルへのアクセス情報が代入されます。
        // ファイルからデータを読み込んだり書き込むときに、このポインターリソースが必ず必要になります。
        // もしファイルを開くことができなかった場合は$file_handleにfalseが代入され、if文の中は実行されません。if文の中にあるfclose関数はファイルを安全に閉じるための関数です。
        // こちらの関数はfopen関数とセットで使用し、先ほど取得したファイルポインターリソースを渡して閉じるファイルを特定します。ここまででファイルを開いて、閉じることができるようになりました。
        
        // 書き込み日時を取得
        $now_date = date("Y-m-d H:i:s");
        // 書き込むデータを作成
        $data = "'". $_POST['view_name']. "','". $_POST['message']. "','". $now_date. "'\n";
        // 書き込み
        fwrite($file_handle, $data);
        // ファイルを閉じる
        fclose( $file_handle);
    }
}

if($file_handle = fopen(FILENAME, 'r')){
    while($data = fgets($file_handle)){
        $split_data = preg_split('/\'/', $data);

        $message = array(
            'view_name' => $split_data[1],
            'message' => $split_data[3],
            'post_data' => $split_data[5]
        );
        array_unshift($message_array, $message);
    }
    // ファイルを閉じる
    fclose($file_handle);
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板</title>
<style>

/*------------------------------

 Reset Style
 
------------------------------*/
html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
abbr, address, cite, code,
del, dfn, em, img, ins, kbd, q, samp,
small, strong, sub, sup, var,
b, i,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, figcaption, figure,
footer, header, hgroup, menu, nav, section, summary,
time, mark, audio, video {
    margin:0;
    padding:0;
    border:0;
    outline:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

body {
    line-height:1;
}

article,aside,details,figcaption,figure,
footer,header,hgroup,menu,nav,section {
    display:block;
}

nav ul {
    list-style:none;
}

blockquote, q {
    quotes:none;
}

blockquote:before, blockquote:after,
q:before, q:after {
    content:'';
    content:none;
}

a {
    margin:0;
    padding:0;
    font-size:100%;
    vertical-align:baseline;
    background:transparent;
}

/* change colours to suit your needs */
ins {
    background-color:#ff9;
    color:#000;
    text-decoration:none;
}

/* change colours to suit your needs */
mark {
    background-color:#ff9;
    color:#000;
    font-style:italic;
    font-weight:bold;
}

del {
    text-decoration: line-through;
}

abbr[title], dfn[title] {
    border-bottom:1px dotted;
    cursor:help;
}

table {
    border-collapse:collapse;
    border-spacing:0;
}

hr {
    display:block;
    height:1px;
    border:0;
    border-top:1px solid #cccccc;
    margin:1em 0;
    padding:0;
}

input, select {
    vertical-align:middle;
}

/*------------------------------

Common Style

------------------------------*/
body {
	padding: 50px;
	font-size: 100%;
	font-family:'ヒラギノ角ゴ Pro W3','Hiragino Kaku Gothic Pro','メイリオ',Meiryo,'ＭＳ Ｐゴシック',sans-serif;
	color: #222;
	background: #f7f7f7;
}

a {
    color: #007edf;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

h1 {
	margin-bottom: 30px;
    font-size: 100%;
    color: #222;
    text-align: center;
}


/*-----------------------------------
入力エリア
-----------------------------------*/

label {
    display: block;
    margin-bottom: 7px;
    font-size: 86%;
}

input[type="text"],
textarea {
	margin-bottom: 20px;
	padding: 10px;
	font-size: 86%;
    border: 1px solid #ddd;
    border-radius: 3px;
    background: #fff;
}

input[type="text"] {
	width: 200px;
}
textarea {
	width: 50%;
	max-width: 50%;
	height: 70px;
}
input[type="submit"] {
	appearance: none;
    -webkit-appearance: none;
    padding: 10px 20px;
    color: #fff;
    font-size: 86%;
    line-height: 1.0em;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #37a1e5;
}
input[type=submit]:hover,
button:hover {
    background-color: #2392d8;
}

hr {
	margin: 20px 0;
	padding: 0;
}

.success_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #48b400;
    border-radius: 10px;
    border: 1px solid #4dc100;
}

.error_message {
    margin-bottom: 20px;
    padding: 10px;
    color: #ef072d;
    list-style-type: none;
    border-radius: 10px;
    border: 1px solid #ff5f79;
}

.success_message,
.error_message li {
    font-size: 86%;
    line-height: 1.6em;
}


/*-----------------------------------
掲示板エリア
-----------------------------------*/

article {
	margin-top: 20px;
	padding: 20px;
	border-radius: 10px;
	background: #fff;
}
article.reply {
    position: relative;
    margin-top: 15px;
    margin-left: 30px;
}
article.reply::before {
    position: absolute;
    top: -10px;
    left: 20px;
    display: block;
    content: "";
    border-top: none;
    border-left: 7px solid #f7f7f7;
    border-right: 7px solid #f7f7f7;
    border-bottom: 10px solid #fff;
}
	.info {
		margin-bottom: 10px;
	}
	.info h2 {
		display: inline-block;
		margin-right: 10px;
		color: #222;
		line-height: 1.6em;
		font-size: 86%;
	}
	.info time {
		color: #999;
		line-height: 1.6em;
		font-size: 72%;
	}
    article p {
        color: #555;
        font-size: 86%;
        line-height: 1.6em;
    }

@media only screen and (max-width: 1000px) {

    body {
        padding: 30px 5%;
    }

    input[type="text"] {
        width: 100%;
    }
    textarea {
        width: 100%;
        max-width: 100%;
        height: 70px;
    }
}
</style>
</head>
<body>
<h1>ひと言掲示板</h1>
<!-- ここにメッセージの入力フォームを設置 -->
<form action="" method="POST">
<!-- <label> を <input> 要素と関連付けると、いくらかの利点が発生 -->
<!-- <label> を <input> 要素に関連付けるには、 <input> に id 属性を設定しなければなりません。そして <label> に for 属性を設定して、値を input の id と同じにします -->
    <div>
        <label for="view_name">表示名</label>
        <input id="view_name" type="text" name="view_name" value="">
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
<?php if( !empty($message_array)): ?>
<?php foreach($message_array as $value): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
</body>
</html>