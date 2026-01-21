<?php
session_start();

// if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()  ){
//  exit('LOGIN ERROR');
// }

// session_regenerate_id(true);
// $_SESSION['chk_ssid'] =session_id();

require_once 'config.php';

loginCheck();

// NewsAPIからニュースを取得
function getNews($country = 'us', $category = 'general') {
    $url = NEWSAPI_URL . '?' . http_build_query([
        'country' => $country,
        'category' => $category,
        'apiKey' => NEWSAPI_KEY
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: NewsViewerApp/1.0'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// カテゴリの選択
$category = $_GET['category'] ?? 'general';
$country = 'us';

// ニュース取得
$newsData = getNews($country, $category);
$articles = $newsData['articles'] ?? [];
$apiError = $newsData['message'] ?? null;
$apiStatus = $newsData['status'] ?? null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Viewer with Translation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>News Viewer</h1>

        <div class="nav-links">
            <a href="select.php" class="btn btn-saved">保存したニュース一覧</a>
            <a href="logout.php" class="btn btn-logout">ログアウト</a>
        </div>

        <!-- フィルター -->
        <form method="GET" class="filter-form">
            <label for="category">カテゴリ:</label>
            <select name="category" id="category">
                <option value="general" <?= $category === 'general' ? 'selected' : '' ?>>一般</option>
                <option value="business" <?= $category === 'business' ? 'selected' : '' ?>>ビジネス</option>
                <option value="technology" <?= $category === 'technology' ? 'selected' : '' ?>>テクノロジー</option>
                <option value="sports" <?= $category === 'sports' ? 'selected' : '' ?>>スポーツ</option>
                <option value="entertainment" <?= $category === 'entertainment' ? 'selected' : '' ?>>エンタメ</option>
                <option value="health" <?= $category === 'health' ? 'selected' : '' ?>>健康</option>
                <option value="science" <?= $category === 'science' ? 'selected' : '' ?>>科学</option>
            </select>

            <button type="submit">検索</button>
        </form>

        <!-- エラー表示 -->
        <?php if ($apiError): ?>
            <div class="alert alert-error">
                <strong>APIエラー:</strong> <?= htmlspecialchars($apiError) ?>
            </div>
        <?php endif; ?>

        <!-- ニュース一覧 -->
        <div class="news-list">
            <?php if (empty($articles)): ?>
                <p class="no-news">ニュースが見つかりませんでした。</p>
            <?php else: ?>
                <?php foreach ($articles as $index => $article): ?>
                    <div class="news-card">
                        <?php if ($article['urlToImage']): ?>
                            <img src="<?= htmlspecialchars($article['urlToImage']) ?>" alt="News Image" class="news-image">
                        <?php endif; ?>

                        <div class="news-content">
                            <h2 class="news-title" id="title-<?= $index ?>">
                                <?= htmlspecialchars($article['title'] ?? 'No Title') ?>
                            </h2>

                            <p class="news-description" id="description-<?= $index ?>">
                                <?= htmlspecialchars($article['description'] ?? 'No Description') ?>
                            </p>

                            <div class="news-meta">
                                <span class="news-source"><?= htmlspecialchars($article['source']['name'] ?? 'Unknown') ?></span>
                                <span class="news-date"><?= date('Y/m/d H:i', strtotime($article['publishedAt'])) ?></span>
                            </div>

                            <div class="news-actions">
                                <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" class="btn btn-link">記事を読む</a>
                                <button class="btn btn-translate" onclick="translateArticle(<?= $index ?>, '<?= addslashes($article['title'] ?? '') ?>', '<?= addslashes($article['description'] ?? '') ?>')">
                                    日本語に翻訳
                                </button>
                                <button type="button" class="btn btn-save" onclick="openSaveModal(<?= $index ?>)">保存</button>
                                <form id="save-form-<?= $index ?>" method="POST" action="save.php" style="display: none;">
                                    <input type="hidden" name="title" value="<?= htmlspecialchars($article['title'] ?? '') ?>">
                                    <input type="hidden" name="description" value="<?= htmlspecialchars($article['description'] ?? '') ?>">
                                    <input type="hidden" name="url" value="<?= htmlspecialchars($article['url'] ?? '') ?>">
                                    <input type="hidden" name="image_url" value="<?= htmlspecialchars($article['urlToImage'] ?? '') ?>">
                                    <input type="hidden" name="source_name" value="<?= htmlspecialchars($article['source']['name'] ?? '') ?>">
                                    <input type="hidden" name="published_at" value="<?= htmlspecialchars($article['publishedAt'] ?? '') ?>">
                                    <input type="hidden" name="memo" id="memo-input-<?= $index ?>" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- メモ入力モーダル -->
    <div id="save-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>ニュースを保存</h3>
            <p id="modal-title" style="font-weight: bold; margin-bottom: 10px;"></p>
            <label for="modal-memo">メモ（任意）:</label>
            <textarea id="modal-memo" rows="4" placeholder="メモを入力してください..."></textarea>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeSaveModal()">キャンセル</button>
                <button type="button" class="btn btn-save" onclick="submitSave()">保存する</button>
            </div>
        </div>
    </div>

    <script>
        let currentSaveIndex = null;

        function openSaveModal(index) {
            currentSaveIndex = index;
            const form = document.getElementById('save-form-' + index);
            const title = form.querySelector('input[name="title"]').value;
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-memo').value = '';
            document.getElementById('save-modal').style.display = 'flex';
        }

        function closeSaveModal() {
            document.getElementById('save-modal').style.display = 'none';
            currentSaveIndex = null;
        }

        function submitSave() {
            if (currentSaveIndex === null) return;
            const memo = document.getElementById('modal-memo').value;
            document.getElementById('memo-input-' + currentSaveIndex).value = memo;
            document.getElementById('save-form-' + currentSaveIndex).submit();
        }

        // モーダル外クリックで閉じる
        document.getElementById('save-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSaveModal();
            }
        });

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
