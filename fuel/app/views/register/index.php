<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 350px; /* フォームの最大幅をさらに大きくする */
            padding: 40px; /* フォームのパディングをさらに大きくする */
            background: white;
            border-radius: 8px;
            box-shadow: 0 0px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 28px; /* タイトルのフォントサイズを大きくする */
            font-weight: 700; /* タイトルのフォントを太字にする */
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        input, button {
            padding: 12px; /* インプットとボタンのパディングをさらに大きくする */
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            font-size: 16px; /* フォントサイズをさらに大きくする */
        }
        input:focus, button:focus {
            border-color: #5d647b;
            outline: none;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            width: 100%;
            font-size: 16px;
            color: #007BFF; /* リンクの色を青色に変更 */
            text-align: center; /* リンクを中央に配置 */
            text-decoration: none;
            font-weight: normal; /* リンクのフォントは太字ではなくする */
        }
        a:hover {
            text-decoration: underline;
        }
        small {
            text-align: center;
            color: red; /* エラーメッセージの色を赤にする */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>新規登録</h1>
        <?php if (Session::get_flash('error')): ?>
            <p style="color: red;"><?php echo Session::get_flash('error'); ?></p>
        <?php endif; ?>
        <?php if (Session::get_flash('success')): ?>
            <p style="color: green;"><?php echo Session::get_flash('success'); ?></p>
        <?php endif; ?>
        <form action="register/register" method="post">
            <div>
                <label for="username">ユーザー名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required minlength="8">
                <small>パスワードは8桁以上で入力してください。</small>
            </div>
            <div>
                <button type="submit">登録</button>
            </div>
            <a href="/auth/login/index">ログインページに戻る</a>
        </form>
    </div>
</body>
</html>