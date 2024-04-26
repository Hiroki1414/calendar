<?php

return array(
    'driver' => 'Simpleauth',  // 使用する認証ドライバ
    'verify_multiple_logins' => false,  // 複数のログインを許可するか
    'salt' => 'eYGh3MQp4O',  // パスワードハッシュの際に使用するソルト
    'iterations' => 10000,  // ハッシュ化の繰り返し回数
);
