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

// 検索キーワード
$search = trim($_GET['search'] ?? '');

// 保存されたニュースを取得
if ($search !== '') {
    $stmt = $pdo->prepare('
        SELECT * FROM saved_news
        WHERE title LIKE ? OR description LIKE ? OR memo LIKE ?
        ORDER BY created_at DESC
    ');
    $searchParam = '%' . $search . '%';
    $stmt->execute([$searchParam, $searchParam, $searchParam]);
} else {
    $stmt = $pdo->query('SELECT * FROM saved_news ORDER BY created_at DESC');
}
$savedNews = $stmt->fetchAll();

// メッセージ処理
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>保存したニュース一覧</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>保存したニュース一覧</h1>

        <div class="nav-links">
            <a href="index.php" class="btn btn-back">← ニュース検索に戻る</a>
            <a href="logout.php" class="btn btn-logout">ログアウト</a>
        </div>

        <!-- 検索フォーム -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="キーワードで検索..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">検索</button>
            <?php if ($search !== ''): ?>
                <a href="select.php" class="btn btn-clear">クリア</a>
            <?php endif; ?>
        </form>

        <?php if ($search !== ''): ?>
            <p class="search-result">「<?= htmlspecialchars($search) ?>」の検索結果: <?= count($savedNews) ?>件</p>
        <?php endif; ?>

        <?php if ($message === 'deleted'): ?>
            <div class="alert alert-success">ニュースを削除しました。</div>
        <?php elseif ($message === 'updated'): ?>
            <div class="alert alert-success">ニュースを更新しました。</div>
        <?php elseif ($message === 'saved'): ?>
            <div class="alert alert-success">ニュースを保存しました。</div>
        <?php endif; ?>

        <div class="news-list">
            <?php if (empty($savedNews)): ?>
                <p class="no-news">保存されたニュースはありません。</p>
            <?php else: ?>
                <?php foreach ($savedNews as $index => $news): ?>
                    <div class="news-card">
                        <?php if ($news['image_url']): ?>
                            <img src="<?= htmlspecialchars($news['image_url']) ?>" alt="News Image" class="news-image">
                        <?php endif; ?>

                        <div class="news-content">
                            <h2 class="news-title" id="title-<?= $index ?>">
                                <?= htmlspecialchars($news['title']) ?>
                            </h2>

                            <p class="news-description" id="description-<?= $index ?>">
                                <?= htmlspecialchars($news['description'] ?? '') ?>
                            </p>

                            <?php if ($news['memo']): ?>
                                <div class="news-memo">
                                    <strong>メモ:</strong> <?= nl2br(htmlspecialchars($news['memo'])) ?>
                                </div>
                            <?php endif; ?>

                            <div class="news-meta">
                                <span class="news-source"><?= htmlspecialchars($news['source_name'] ?? 'Unknown') ?></span>
                                <?php if ($news['published_at']): ?>
                                    <span class="news-date"><?= date('Y/m/d H:i', strtotime($news['published_at'])) ?></span>
                                <?php endif; ?>
                                <span class="news-saved">保存: <?= date('Y/m/d H:i', strtotime($news['created_at'])) ?></span>
                            </div>

                            <div class="news-actions">
                                <?php if ($news['url']): ?>
                                    <a href="<?= htmlspecialchars($news['url']) ?>" target="_blank" class="btn btn-link">記事を読む</a>
                                <?php endif; ?>
                                <button class="btn btn-translate" onclick="translateArticle(<?= $index ?>, '<?= addslashes($news['title'] ?? '') ?>', '<?= addslashes($news['description'] ?? '') ?>')">
                                    日本語に翻訳
                                </button>
                                <?php if ($_SESSION['kanri_flg'] == 1): ?>
                                <a href="update.php?id=<?= $news['id'] ?>" class="btn btn-edit">編集</a>
                                <a href="delete.php?id=<?= $news['id'] ?>" class="btn btn-delete" onclick="return confirm('本当に削除しますか？')">削除</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        async function translateArticle(index, title, description) {
            const titleElement = document.getElementById('title-' + index);
            const descriptionElement = document.getElementById('description-' + index);
            const button = event.target;

            button.disabled = true;
            button.textContent = '翻訳中...';

            try {
                // タイトルを翻訳
                if (title) {
                    const titleResponse = await fetch('translate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'text=' + encodeURIComponent(title)
                    });
                    const titleData = await titleResponse.json();
                    if (titleData.success) {
                        titleElement.textContent = titleData.translated;
                    }
                }

                // 説明を翻訳
                if (description) {
                    const descResponse = await fetch('translate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'text=' + encodeURIComponent(description)
                    });
                    const descData = await descResponse.json();
                    if (descData.success) {
                        descriptionElement.textContent = descData.translated;
                    }
                }

                button.textContent = '翻訳完了';
            } catch (error) {
                console.error('Translation error:', error);
                button.textContent = '翻訳エラー';
            }
        }
    </script>
</body>

</html>