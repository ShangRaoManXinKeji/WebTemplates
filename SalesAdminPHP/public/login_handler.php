<?php
/**
 * 登录请求处理器
 */

header('Content-Type: application/json; charset=utf-8');

// 开启会话
session_start();

// 加载配置和依赖
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/auth/login.php';

try {
    // 获取并解析请求数据
    $requestData = json_decode(file_get_contents('php://input'), true);
    if (!$requestData) {
        throw new InvalidArgumentException('无效的请求数据格式');
    }

    // 实例化认证服务
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4",
        $config['db']['username'],
        $config['db']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    $auth = new \App\Auth\AuthenticationService($pdo, $config);
    
    // 处理登录请求
    $user = $auth->login(
        $requestData['username'],
        $requestData['password'],
        $requestData['csrf_token']
    );
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'message' => '登录成功',
        'user' => [
            'username' => $user['username'],
            'role' => $user['role']
        ]
    ]);
    
} catch (Exception $e) {
    // 返回错误响应
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}