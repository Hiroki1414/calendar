<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7f7f7;
        }
        .login-container {
            padding: 40px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            text-align: center;
            width: 350px;
        }
        label {
            margin-top: 15px;
            display: block;
            width: 100%;
            font-size: 16px;
            text-align: left;
        }
        input, button, a {
            margin-top: 15px;
            display: block;
            width: 100%;
            font-size: 16px;
        }
        input {
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: calc(100% - 24px);
        }
        .spacer {
            margin-top: 40px; 
        }
        button {
            padding: 12px; 
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #007BFF; 
            color: white;
            border: none;
            cursor: pointer; 
        }
        button:hover {
            background-color: #0056b3; 
        }
        a {
            color: #007BFF; 
            text-decoration: none;
            font-size: 16px; 
        }
        a:hover {
            text-decoration: underline;
        }
        .notification {
            background-color: #4CAF50; 
            color: white;
            padding: 20px;
            position: fixed;
            top: 20px; 
            right: 20px; 
            z-index: 1001;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 18px;
            display: none; 
            text-align: center;
            opacity: 1; 
            transition: opacity 1s ease-in-out;
        }
    </style>
    <script>
        window.onload = function() {
            // ページロード時に通知を表示
            var notification = document.getElementById('notification');
            if (notification && notification.innerText.trim() !== '') {
                notification.style.display = 'block'; // 通知を表示
                setTimeout(function() {
                    notification.style.opacity = '0'; 
                    setTimeout(function() {
                        notification.style.display = 'none'; 
                    }, 1000);
                }, 5000);
            }
        };
    </script>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <div class="notification" id="notification" style="display: none;">
            <?php if ($message = Session::get_flash('success')): ?>
                <?php echo htmlspecialchars($message); ?>
            <?php endif; ?>
        </div>
        <?php if (Session::get_flash('error')): ?>
            <p style="color:red;"><?php echo Session::get_flash('error'); ?></p>
        <?php endif; ?>
        <?php echo Form::open('login/login'); ?>
            <div>
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php 
            $token_key = \Config::get('security.csrf_token_key');
            $token = \Security::fetch_token();
            echo Form::hidden($token_key, $token);
            ?>
            <div class="spacer"></div>
            <button type="submit">Login</button>
        <?php echo Form::close(); ?>
        <a href="/register">新規登録はこちらから</a>
    </div>
</body>
</html>