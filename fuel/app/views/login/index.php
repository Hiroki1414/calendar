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
    </style>
    <script>
        window.onload = function() {
            <?php if ($message = Session::get_flash('success')): ?>
                alert("<?php echo htmlspecialchars($message); ?>");
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (Session::get_flash('error')): ?>
            <p style="color:red;"><?php echo Session::get_flash('error'); ?></p>
        <?php endif; ?>
        <form method="post" action="login/login">
            <div>
                <label for="email">メールアドレス</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div>
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="spacer"></div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
        <a href="/register/index">新規登録はこちらから</a>
    </div>
</body>
</html>