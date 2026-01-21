<?php

session_start();

$lid = $_POST['lid'];
$lpw = $_POST['lpw'];

require_once('config.php');
$pdo = db_conn();

// $stmt = $pdo->prepare('SELECT * FROM gs_user_table WHERE lid = :lid AND lpw = :lpw ');
$stmt = $pdo->prepare('SELECT * FROM gs_user_table WHERE lid = :lid');
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
// $stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status === false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();

if ($val['id'] !== '' && password_verify($lpw,$val['lpw'])) {
    //Login成功時 該当レコードがあればSESSIONに値を代入

    $_SESSION['chk_ssid'] = session_id();
    $_SESSION['kanri_flg'] = $val['kanri_flg'];
    header('Location: index.php');
} else {
    //Login失敗時(Logout経由)
    header('Location: login.php');
}

exit();