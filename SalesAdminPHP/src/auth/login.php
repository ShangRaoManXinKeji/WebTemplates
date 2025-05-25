<?php
class Auth {
    private $db;
    private $config;

    public function __construct($db, $config) {
        $this->db = $db;
        $this->config = $config;
    }

    public function login($username, $password, $csrf_token) {
        // 验证CSRF令牌
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrf_token) {
            return ['error' => '无效的请求，请刷新页面重试'];
        }

        // 验证输入
        if (empty($username) || strlen($username) > 50) {
            return ['error' => '用户名格式不正确'];
        }
        if (empty($password) || strlen($password) > $this->config['password_max_length']) {
            return ['error' => '密码格式不正确'];
        }

        // 检查登录尝试次数
        if ($this->isLockedOut($username)) {
            return ['error' => '登录尝试次数过多，请稍后再试'];
        }

        // 验证用户
        $sql = "SELECT * FROM users WHERE username = ? AND status = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user) {
            error_log('找不到用户: ' . $username);
        }
        if ($user && !password_verify($password, $user['password'])) {
            error_log('密码不匹配: ' . $password . ' vs ' . $user['password']);
        }

      
        // 更新登录信息
        $this->updateLoginStatus($user['id']);

        // 设置会话
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();

        return ['success' => true, 'user' => $user];
    }

    private function isLockedOut($username) {
        $sql = "SELECT COUNT(*) FROM login_attempts 
               WHERE username = ? 
               AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $this->config['lockout_time']]);
        return $stmt->fetchColumn() >= $this->config['max_login_attempts'];
    }

    private function logFailedAttempt($username) {
        $sql = "INSERT INTO login_attempts (username, attempt_time, ip_address) 
               VALUES (?, NOW(), ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    }

    private function updateLoginStatus($userId) {
        $sql = "UPDATE users SET 
               last_login = NOW(), 
               login_count = login_count + 1 
               WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }

    public function logout() {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        session_destroy();
    }
}
