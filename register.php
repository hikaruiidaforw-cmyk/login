<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Minimal Mono</title>
    <link rel="stylesheet" href="./login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="login-box">
            <div class="header">
                <h1>Sign Up</h1>
                <p>新規登録</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div style="color: #e74c3c; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    <?php
                    $error = $_GET['error'];
                    if ($error === 'empty') echo '全ての項目を入力してください';
                    elseif ($error === 'password_mismatch') echo 'パスワードが一致しません';
                    elseif ($error === 'exists') echo 'このメールアドレスは既に登録されています';
                    elseif ($error === 'db') echo '登録に失敗しました。もう一度お試しください';
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div style="color: #27ae60; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    登録が完了しました。ログインしてください。
                </div>
            <?php endif; ?>

            <form name="form1" action="register_act.php" method="post">
                <div class="input-field">
                    <label for="name">Name</label>
                    <input type="text" id="name" placeholder="名前を入力" name="name" required>
                </div>

                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="text" id="email" placeholder="example@mail.com" name="lid" required>
                </div>

                <div class="input-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="••••••••" name="lpw" required>
                </div>

                <div class="input-field">
                    <label for="password_confirm">Password (確認)</label>
                    <input type="password" id="password_confirm" placeholder="••••••••" name="lpw_confirm" required>
                </div>

                <div class="sub-actions">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="kanri_flg" value="1">
                        <span class="checkmark"></span>
                        <span class="label-text">私は管理者です</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">登録する</button>
            </form>

            <div class="footer">
                <p>既にアカウントをお持ちの方は <a href="login.php">ログイン</a></p>
            </div>
        </div>
    </div>

</body>
</html>
