<?php
// エラー表示を有効化（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$url = trim($_POST['url'] ?? '');
$imageUrl = trim($_POST['image_url'] ?? '');
$sourceName = trim($_POST['source_name'] ?? '');
$publishedAt = $_POST['published_at'] ?? null;
if ($publishedAt) {
    $dt = new DateTime($publishedAt);
    $publishedAt = $dt->format('Y-m-d H:i:s');
}
$memo = trim($_POST['memo'] ?? '');

if (empty($title)) {
    header('Location: index.php?error=empty_title');
    exit;
}

try {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare('
        INSERT INTO saved_news (title, description, url, image_url, source_name, published_at, memo)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $title,
        $description ?: null,
        $url ?: null,
        $imageUrl ?: null,
        $sourceName ?: null,
        $publishedAt ?: null,
        $memo ?: null
    ]);

    header('Location: select.php?message=saved');
    exit;
} catch (PDOException $e) {
    // エラー詳細を表示（デバッグ用）
    die('保存エラー: ' . $e->getMessage());
}
