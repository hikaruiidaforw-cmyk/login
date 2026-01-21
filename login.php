<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Minimal Mono</title>
    <link rel="stylesheet" href="./login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="login-box">
            <div class="header">
                <h1>Sign In</h1>
                <p>ログイン</p>
            </div>

            <?php if (isset($_GET['registered'])): ?>
                <div style="color: #27ae60; text-align: center; margin-bottom: 15px; font-size: 14px;">
                    登録が完了しました。ログインしてください。
                </div>
            <?php endif; ?>

            <form name="form1" action="login_act.php" method="post">
                <div class="input-field">
                    <label for="text">Email</label>
                    <input type="text" id="email" placeholder="example@mail.com" name="lid">
                </div>

                <div class="input-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="••••••••" name="lpw">
                </div>

                <div class="sub-actions">
                    <label class="custom-checkbox">
                        <input type="checkbox">
                        <span class="checkmark"></span>
                        <span class="label-text">ログイン状態を保持</span>
                    </label>
                    <a href="#" class="forgot">パスワードをお忘れですか？</a>
                </div>

                <button type="submit" class="btn-login">ログイン</button>
            </form>

            <div class="footer">
                <p>アカウントをお持ちでない方は <a href="register.php">新規登録</a></p>
            </div>
        </div>
    </div>

</body>
</html>