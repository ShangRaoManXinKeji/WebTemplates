<?php
/**
 * 企业管理系统登录页面
 * @package SalesAdminPHP
 * @author 满星科技开发团队
 * @version 1.0.0
 * 
 * @var array $config 系统配置
 * @var string $error 错误信息
 */

// 初始化会话
session_start();

// 加载配置
if (!isset($config)) {
    $config = require_once __DIR__ . '/../src/config/config.php';
}

// 初始化变量
if (!isset($error)) {
    $error = '';
}

// 生成CSRF令牌
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 获取系统名称
$siteName = htmlspecialchars($config['site_name'] ?? '企业管理系统');
$siteDescription = htmlspecialchars($config['site_description'] ?? '管理系统登录');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - <?php echo htmlspecialchars($config['site_name'] ?? '满星科技销售管理系统'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 使用国内CDN资源 -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.loli.net/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" />
    
    <style>
        :root {
            /* 主色调 */
            --primary-color: #2C3E50;
            --primary-light: #34495E;
            --primary-dark: #1A252F;
            --accent-color: #3498DB;
            --accent-hover: #2980B9;
            
            /* 文本颜色 */
            --text-primary: #2C3E50;
            --text-secondary: #7F8C8D;
            --text-light: #ECF0F1;
            
            /* 背景颜色 */
            --bg-light: #ECF0F1;
            --bg-white: #FFFFFF;
            --bg-gradient-start: #2C3E50;
            --bg-gradient-end: #3498DB;
            
            /* 边框和阴影 */
            --border-color: #BDC3C7;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --box-shadow-hover: 0 15px 35px rgba(0, 0, 0, 0.15);
            
            /* 动画时间 */
            --transition-fast: 0.2s;
            --transition-normal: 0.3s;
            --transition-slow: 0.5s;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Noto Sans SC', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-primary);
            line-height: 1.6;
        }
        
        .login-container {
            background-color: var(--bg-white);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            transition: transform var(--transition-normal) ease, box-shadow var(--transition-normal) ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header .logo {
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .login-header .logo i {
            font-size: 40px;
        }
        
        .login-header .title {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            display: inline-block;
        }
        
        .login-header .title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            width: 40px;
            height: 3px;
            background-color: var(--accent-color);
            transform: translateX(-50%);
            transition: width var(--transition-normal) ease;
        }
        
        .login-container:hover .login-header .title::after {
            width: 60px;
        }
        
        .login-header .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 15px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 24px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px 12px 45px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            color: var(--text-primary);
            background-color: var(--bg-white);
            transition: all var(--transition-fast) ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
        }
        
        .form-group .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: all var(--transition-fast) ease;
        }
        
        .form-control:focus + .icon {
            color: var(--accent-color);
        }
        
        .form-label {
            position: absolute;
            left: 45px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 15px;
            pointer-events: none;
            transition: all var(--transition-fast) ease;
            background-color: var(--bg-white);
            padding: 0 5px;
        }
        
        .form-control:focus ~ .form-label,
        .form-control:not(:placeholder-shown) ~ .form-label {
            top: 0;
            left: 15px;
            transform: translateY(-50%) scale(0.85);
            color: var(--accent-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: var(--text-light);
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast) ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-login:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login .button-text {
            transition: opacity var(--transition-fast) ease;
        }
        
        .btn-login .loading {
            position: absolute;
            display: none;
        }
        
        .alert {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .alert::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .captcha-container {
            margin-bottom: 24px;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .form-footer a {
            color: var(--accent-color);
            text-decoration: none;
            transition: color var(--transition-fast) ease;
        }
        
        .form-footer a:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }
        
        /* 响应式调整 */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header .title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="title"><?= $siteName ?></h1>
            <p class="subtitle">请输入您的账号和密码登录系统</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="post" id="loginForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
            
            <div class="form-group">
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       placeholder=" "
                       required 
                       autocomplete="username"
                       autofocus />
                <i class="icon fas fa-user"></i>
                <label for="username" class="form-label">用户名</label>
            </div>
            
            <div class="form-group">
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       placeholder=" "
                       autocomplete="current-password"
                       required />
                <i class="icon fas fa-lock"></i>
                <label for="password" class="form-label">密码</label>
            </div>
            
            <?php if ($config['enable_captcha'] ?? false): ?>
            <div class="captcha-container">
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($config['recaptcha_site_key']) ?>"></div>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100" id="loginBtn">登录</button>
            </div>
            
            <div class="form-footer">
                <?php if ($config['show_forgot_password'] ?? false): ?>
                <a href="<?= htmlspecialchars($config['forgot_password_url'] ?? '#') ?>">忘记密码?</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 在其他脚本之前添加 SweetAlert2 -->
    <script src="https://cdn.staticfile.org/sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.staticfile.org/sweetalert2/11.7.32/sweetalert2.min.css">
    
    <!-- 添加 axios CDN -->
    <script src="https://cdn.staticfile.org/axios/1.5.0/axios.min.js"></script>
    
    <!-- 使用国内CDN资源 -->
    <script src="https://cdn.staticfile.org/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <?php if ($config['enable_captcha'] ?? false): ?>
    <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 表单提交处理
        const form = document.getElementById('loginForm');
        const button = form.querySelector('.btn-login');
        const buttonText = button.querySelector('.button-text');
        const loading = button.querySelector('.loading');
        
        // 初始化
        loading.style.display = 'none';
        
        // 表单验证
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            buttonText.style.opacity = '0';
            loading.style.display = 'block';
            button.disabled = true;
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            let isValid = true;
            if (!username.value.trim()) {
                isValid = false;
                showError(username, '请输入用户名');
            }
            if (!password.value.trim()) {
                isValid = false;
                showError(password, '请输入密码');
            }
            // 已移除验证码相关代码
            if (!isValid) {
                buttonText.style.opacity = '1';
                loading.style.display = 'none';
                button.disabled = false;
                return;
            }
            // 发送登录请求
            const formData = new URLSearchParams({
                'username': username.value.trim(),
                'password': password.value.trim(),
                'csrf_token': document.querySelector('[name=csrf_token]').value
            });
            
            // 如果启用了验证码，添加验证码响应
            if (typeof grecaptcha !== 'undefined') {
                const captchaResponse = grecaptcha.getResponse();
                if (!captchaResponse) {
                    Swal.fire({
                        icon: 'error',
                        title: '验证失败',
                        text: '请完成人机验证'
                    });
                    buttonText.style.opacity = '1';
                    loading.style.display = 'none';
                    button.disabled = false;
                    return;
                }
                formData.append('g-recaptcha-response', captchaResponse);
            }
            
            try {
                const response = await fetch('login_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username.value.trim(),
                        password: password.value.trim(),
                        csrf_token: document.querySelector('[name=csrf_token]').value
                    })
                });

                const data = await response.json();
                
                if (!(data instanceof Object)) {
                    throw new Error('无效的服务器响应格式');
                }

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '登录成功',
                        text: '欢迎回来，正在为您跳转...',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.href = 'dashboard.php';
                } else {
                    throw new Error(data.message || '登录失败，请重试');
                }
            } catch (error) {
                const errorMessage = error.message || '网络连接异常，请检查网络后重试';
                console.log('完整错误对象:', error);
                await Swal.fire({
                    icon: 'error',
                    title: '登录失败',
                    html: `${errorMessage}<br><small>请求ID: ${Date.now()}</small>`
                });
                buttonText.style.opacity = '1';
                loading.style.display = 'none';
                button.disabled = false;
            }
        });
        
        // 错误提示函数
        function showError(input, message) {
            const formGroup = input.closest('.form-group');
            input.classList.add('is-invalid');
            
            // 移除已有的错误消息
            const existingError = formGroup.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
            
            // 添加新的错误消息
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            formGroup.appendChild(errorDiv);
            
            // 聚焦到第一个错误输入
            input.focus();
        }
        
        // 输入时移除错误状态
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorMsg = this.closest('.form-group').querySelector('.invalid-feedback');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
    });
    </script>
</body>
</html>
