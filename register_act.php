<?php
// デバッグ用エラー表示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$name = $_POST['name'] ?? '';
$lid = $_POST['lid'] ?? '';
$lpw = $_POST['lpw'] ?? '';
$lpw_confirm = $_POST['lpw_confirm'] ?? '';
$kanri_flg = isset($_POST['kanri_flg']) ? 1 : 0;

// 入力チェック
if (empty($name) || empty($lid) || empty($lpw) || empty($lpw_confirm)) {
    header('Location: register.php?error=empty');
    exit();
}

// パスワード一致チェック
if ($lpw !== $lpw_confirm) {
    header('Location: register.php?error=password_mismatch');
    exit();
}

try {
    require_once('config.php');
    $pdo = db_conn();

    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 既存ユーザーチェック
    $stmt = $pdo->prepare('SELECT * FROM gs_user_table WHERE lid = :lid');
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->execute();
    $existing = $stmt->fetch();

    if ($existing) {
        header('Location: register.php?error=exists');
        exit();
    }

    // パスワードをハッシュ化
    $hashed_password = password_hash($lpw, PASSWORD_DEFAULT);

    // ユーザー登録
    $stmt = $pdo->prepare('INSERT INTO gs_user_table (name, lid, lpw, kanri_flg, life_flg) VALUES (:name, :lid, :lpw, :kanri_flg, :life_flg)');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $stmt->bindValue(':lpw', $hashed_password, PDO::PARAM_STR);
    $stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
    $stmt->bindValue(':life_flg', 1, PDO::PARAM_INT);
    $stmt->execute();

    // 登録成功
    header('Location: login.php?registered=1');
    exit();

} catch (PDOException $e) {
    // エラー詳細を表示
    die('データベースエラー: ' . $e->getMessage());
}
