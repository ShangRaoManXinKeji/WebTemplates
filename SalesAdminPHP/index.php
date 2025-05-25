<?php
session_start();

// 设置安全响应头
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// 检查登录状态
if (!isset($_SESSION['user_id'])) {
    header('Location: ./public/login.php');
    exit();
} else {
    header('Location: ./public/dashboard.php');
}

// 加载配置
$config = require __DIR__ . '/src/config/config.php';

// 检查会话超时
if (time() - $_SESSION['last_activity'] > $config['session_lifetime']) {
    session_destroy();
    header('Location: ./public/login.php?error=timeout');
    exit();
}

// 更新最后活动时间
$_SESSION['last_activity'] = time();

// 继续加载应用程序
require_once __DIR__ . '/src/config/config.php';