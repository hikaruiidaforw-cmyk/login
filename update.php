<?php
session_start();

// if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()  ){
//  exit('LOGIN ERROR');
// }

// session_regenerate_id(true);
// $_SESSION['chk_ssid'] =session_id();

require_once 'config.php';

loginCheck();


$pdo = getDbConnection();
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$error = '';

if (!$id) {
    header('Location: select.php');
    exit;
}

// POST処理（更新）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $memo = trim($_POST['memo'] ?? '');

    if (empty($title)) {
        $error = 'タイトルは必須です。';
    } else {
        $stmt = $pdo->prepare('UPDATE saved_news SET title = ?, description = ?, memo = ? WHERE id = ?');
        $stmt->execute([$title, $description, $memo, $id]);

        header('Location: select.php?message=updated');
        exit;
    }
}

// 既存データを取得
$stmt = $pdo->prepare('SELECT * FROM saved_news WHERE id = ?');
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    header('Location: select.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ニュースを編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>ニュースを編集</h1>

        <div class="nav-links">
            <a href="select.php" class="btn btn-back">← 一覧に戻る</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($news['id']) ?>">

            <div class="form-group">
                <label for="title">タイトル <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($news['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">説明</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($news['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="memo">メモ</label>
                <textarea id="memo" name="memo" rows="4" placeholder="このニュースに関するメモを追加..."><?= htmlspecialchars($news['memo'] ?? '') ?></textarea>
            </div>

            <?php if ($news['image_url']): ?>
                <div class="form-group">
                    <label>画像プレビュー</label>
                    <img src="<?= htmlspecialchars($news['image_url']) ?>" alt="News Image" class="preview-image">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>元の記事URL</label>
                <?php if ($news['url']): ?>
                    <a href="<?= htmlspecialchars($news['url']) ?>" target="_blank" class="article-link"><?= htmlspecialchars($news['url']) ?></a>
                <?php else: ?>
                    <span class="text-muted">URLなし</span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-save">保存する</button>
                <a href="select.php" class="btn btn-cancel">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>
