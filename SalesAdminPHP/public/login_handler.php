<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

// 加载配置
$config = require __DIR__ . '/../src/config/config.php';

try {
    // 获取请求数据
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('无效的请求数据格式');
    }

    // 连接数据库
    $db = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}",
        $config['db']['username'],
        $config['db']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 处理登录
    require_once __DIR__ . '/../src/auth/login.php';
    $auth = new Auth($db, $config);  
    $result = $auth->login(
        $data['username'] ?? '',
        $data['password'] ?? '',
        $data['csrf_token'] ?? ''
    );

    // 返回结果
    if (isset($result['error'])) {
        throw new Exception($result['error']);
    }
    echo json_encode(['success' => true, 'message' => '登录成功']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}