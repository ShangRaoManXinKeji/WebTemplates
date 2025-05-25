<?php
return [
    // 基础信息配置
    'site_name' => '管理系统',
    'site_description' => '企业管理系统',
    
    // 数据库配置
    'db' => [
        'host' => 'localhost',
        'dbname' => 'sales_admin_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    
    // 安全配置
    'max_login_attempts' => 5,
    'lockout_time' => 1800,  // 30分钟
    'session_lifetime' => 3600,  // 1小时
    
    // 密码配置
    'password_min_length' => 3,
    'password_max_length' => 72
];
