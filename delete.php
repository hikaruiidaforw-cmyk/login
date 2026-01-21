<?php
session_start();

// if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()  ){
//  exit('LOGIN ERROR');
// }

// session_regenerate_id(true);
// $_SESSION['chk_ssid'] =session_id();

require_once 'config.php';

loginCheck();



$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: select.php');
    exit;
}

$pdo = getDbConnection();

// IDが存在するか確認
$stmt = $pdo->prepare('SELECT id FROM saved_news WHERE id = ?');
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    header('Location: select.php');
    exit;
}

// 削除実行
$stmt = $pdo->prepare('DELETE FROM saved_news WHERE id = ?');
$stmt->execute([$id]);

header('Location: select.php?message=deleted');
exit;
