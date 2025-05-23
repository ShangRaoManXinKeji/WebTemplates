<?php
/**
 * 系统核心配置文件
 * 
 * 包含以下配置分类：
 * 1. 基础信息配置
 * 2. 主题样式配置
 * 3. 客户模块配置
 * 4. 字体显示配置
 */

return [
    // 基础信息配置
    'site_name' => '灵儿智能管理系统',             // 系统显示名称 (最大长度50字符)
    
    // 主题样式配置
    'theme_color' => '#007bff',                  // 主色调 (使用Bootstrap标准色值)
    'theme_dark_color' => '#0056b3',             // 暗色模式主色调
    
    // 客户模块配置（新增）
    'customer' => [
        'pagination' => 15,                     // 客户列表每页显示数量
        'default_sort' => 'created_at',         // 默认排序字段: name|created_at|level
        'export_limit' => 1000,                 // 批量导出最大记录数
        'important_tags' => ['VIP', '战略客户'], // 重点客户标识标签
    ],
    
    // 字体显示配置
    'font_family' => "'Helvetica Neue', Helvetica, Arial, sans-serif", // 系统字体栈
    
    // 新增安全配置
    'security' => [
        'password_strength' => 2,               // 密码强度等级 1-弱 2-中 3-强
        'login_attempts' => 5                   // 允许的最大登录失败次数
    ]
];
