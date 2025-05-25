<?php
session_start();
$config = require __DIR__ . '/../src/config/config.php';

// 生成CSRF令牌
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - <?= htmlspecialchars($config['site_name']) ?></title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="login-container">
        <h1><?= htmlspecialchars($config['site_name']) ?></h1>
        <div id="loginError" class="login-error" style="display:none;"></div>
        <form id="loginForm" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit">登录</button>
        </form>
    </div>
    <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const data = {
            username: form.username.value,
            password: form.password.value,
            csrf_token: form.csrf_token.value
        };
        const errorDiv = document.getElementById('loginError');
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
        try {
            const response = await fetch('login_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                window.location.href = 'dashboard.php';
            } else {
                errorDiv.textContent = result.message || '用户名或密码错误';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.textContent = '登录失败，请重试';
            errorDiv.style.display = 'block';
        }
    });
    </script>
</body>
</html>
