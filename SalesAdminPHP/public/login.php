<?php
// login_view.php
// $error 和 $config 由控制器传入
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>登录 - <?=htmlspecialchars($config['site_name'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, <?= $config['theme_color'] ?>, #004085);
            font-family: <?= $config['font_family'] ?>;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            padding: 3rem 3.5rem;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            animation: fadeScaleIn 0.5s ease forwards;
        }
        @keyframes fadeScaleIn {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
        h2 {
            color: <?= $config['theme_color'] ?>;
            margin-bottom: 1.8rem;
            font-weight: 700;
            text-align: center;
            user-select: none;
        }
    </style>
</head>
<body>

<form class="login-box" method="post" novalidate>
    <h2><?=htmlspecialchars($config['site_name'])?></h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <div class="mb-3">
        <label for="username" class="form-label">用户名</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名" required autofocus />
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">密码</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" required />
    </div>

    <button type="submit" class="btn btn-primary w-100">登录</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
