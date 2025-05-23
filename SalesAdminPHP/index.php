<?php
/**
 * 入口文件 - 负责初始化会话和用户认证
 * @author Your Sister
 * @version 1.0.0
 */

// 初始化安全会话
session_start();
session_regenerate_id(true); // 防止会话固定攻击

// 设置安全响应头
header('X-Frame-Options: DENY'); // 防止点击劫持
header('X-Content-Type-Options: nosniff'); // 防止MIME类型嗅探
header('X-XSS-Protection: 1; mode=block'); // 启用XSS过滤

// 安全检查流程
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_status'])) {
    // 用户未登录，记录访问日志
    error_log("未授权访问尝试: " . $_SERVER['REMOTE_ADDR']);
    
    // 重定向到登录页面
    header('Location: ./public/login.php');
    exit();
}

// 验证用户状态
if ($_SESSION['user_status'] !== 'active') {
    session_destroy();
    header('Location: ./public/login.php?error=inactive');
    exit();
}

// 继续加载应用程序
require_once __DIR__ . '/src/config/config.php';