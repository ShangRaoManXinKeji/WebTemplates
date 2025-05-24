<?php
declare(strict_types=1);

namespace App\Auth;

use PDO;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * 认证服务类
 */
class AuthenticationService {
    private PDO $db;
    private array $config;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 1800; // 30分钟

    public function __construct(PDO $db, array $config) {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * 处理登录请求
     * @throws InvalidArgumentException 参数验证失败
     * @throws RuntimeException 登录失败
     */
    public function login(string $username, string $password, string $csrf_token): array {
        try {
            $this->validateLoginRequest($username, $password, $csrf_token);
            $user = $this->authenticateUser($username, $password);
            $this->updateLoginStatus($user['id']);

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            return $user;
        } catch (Exception $e) {
            $this->logFailedAttempt($username);
            throw $e;
        }
    }

    /**
     * 验证登录请求参数
     */
    private function validateLoginRequest(string $username, string $password, string $csrf_token): void {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            throw new InvalidArgumentException('无效的请求，请刷新页面重试');
        }
        if (empty($username) || mb_strlen($username) > 50) {
            throw new InvalidArgumentException('用户名格式不正确');
        }
        if (empty($password) || mb_strlen($password) > 72) {
            throw new InvalidArgumentException('密码格式不正确');
        }
    }

    /**
     * 检查登录频率限制
     */
    private function checkLoginAttempts(string $username): void {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as attempts, MAX(attempt_time) as last_attempt FROM login_attempts WHERE username = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)'
        );
        $stmt->execute([$username, self::LOCKOUT_TIME]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            $waitTime = self::LOCKOUT_TIME - (time() - strtotime($result['last_attempt']));
            throw new RuntimeException(sprintf('登录尝试次数过多，请在%d分钟后重试', ceil($waitTime / 60)));
        }
    }

    /**
     * 验证用户凭据
     */
    private function authenticateUser(string $username, string $password): array {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? AND status = 1 LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new RuntimeException('用户名或密码错误');
        }
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $this->updatePassword($user['id'], $password);
        }
        return $user;
    }

    /**
     * 更新密码哈希
     */
    private function updatePassword(int $userId, string $password): void {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
    }

    /**
     * 更新登录状态
     */
    private function updateLoginStatus(int $userId): void {
        $stmt = $this->db->prepare('UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?');
        $stmt->execute([$userId]);
    }

    /**
     * 记录失败的登录尝试
     */
    private function logFailedAttempt(string $username): void {
        $stmt = $this->db->prepare('INSERT INTO login_attempts (username, attempt_time, ip_address) VALUES (?, NOW(), ?)');
        $stmt->execute([$username, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    }



    /**
     * 注销登录
     */
    public function logout(): void {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        session_destroy();
    }
}
