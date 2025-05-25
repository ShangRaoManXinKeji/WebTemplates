# 销售管理系统（SalesAdminPHP）技术部署与使用说明

---

## 一、系统简介

本系统为企业级销售管理平台，采用 PHP 8.4.7 与 MySQL 8.0 构建，支持多用户、多角色权限、订单与客户管理、数据分析、KPI 指标、库存预警等功能。前后端分离，前端基于 HTML5/CSS3/JavaScript，后端采用高安全标准开发，适合中小企业销售业务数字化管理。

---

## 二、环境要求

- **操作系统**：推荐 Linux/macOS，支持 Windows
- **Web 服务器**：Apache 2.4+/Nginx 1.18+
- **PHP**：8.4.7 及以上，需启用 PDO、mysqli、openssl、mbstring、json、session 等扩展
- **MySQL**：8.0 及以上，建议开启 InnoDB 引擎
- **浏览器**：Chrome/Edge/Firefox（最新版）

---

## 三、目录结构说明

```
SalesAdminPHP/
├── public/           # Web 入口目录（对外暴露）
│   ├── dashboard.php # 主控制台页面
│   ├── login.php     # 登录页面
│   ├── login_handler.php # 登录处理
│   └── css/          # 前端样式
├── scripts/          # 数据库脚本
│   ├── database.sql  # 数据库结构定义
│   ├── sample_data.sql # 示例数据
│   └── drop.sql      # 删除表结构脚本
├── src/              # 核心后端代码
│   ├── auth/         # 认证与权限
│   └── config/       # 配置文件
├── README.md         # 本说明文档
└── demo/             # 截图演示
```

---

## 四、数据库初始化与配置

### 1. 创建数据库及表结构

请确保 MySQL 服务已启动，执行：

```bash
mysql -u root -p < scripts/database.sql
```

### 2. 导入示例数据

```bash
mysql -u root -p < scripts/sample_data.sql
```

### 3. 数据库连接配置

编辑 `src/config/config.php`，根据实际环境填写：

```php
'db' => [
    'host' => 'localhost',
    'dbname' => 'sales_admin_db',
    'username' => 'root',
    'password' => '你的数据库密码',
    'charset' => 'utf8mb4'
],
```

---

## 五、Web 服务器部署

### 1. 目录权限

确保 PHP 进程有读写权限：

```bash
chmod -R 755 /path/to/SalesAdminPHP
chown -R www-data:www-data /path/to/SalesAdminPHP
```

### 2. 虚拟主机配置（以 Apache 为例）

```
<VirtualHost *:80>
    DocumentRoot "/path/to/SalesAdminPHP/public"
    <Directory "/path/to/SalesAdminPHP/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

重启 Apache：

```bash
sudo systemctl restart apache2
```

---

## 六、系统安全与配置

### 1. 配置文件安全

- `src/config/config.php` 不应暴露于公网
- 建议配置 `.htaccess` 禁止访问 `src/` 目录

### 2. 会话与 CSRF 防护

- 系统自动生成 CSRF Token，防止跨站请求伪造
- 登录失败次数超限自动锁定账户
- 密码加密采用 bcrypt

### 3. 日志与错误追踪

- PHP 错误日志建议开启，便于排查问题
- 日志路径可在 `php.ini` 配置

---

## 七、功能模块说明

### 1. 用户与权限

- 支持多角色（admin/manager/sales）
- 登录、登出、密码加密、登录限制

### 2. 客户与产品管理

- 客户信息增删改查
- 产品信息、库存管理

### 3. 订单与明细

- 订单录入、编辑、删除
- 订单明细自动关联产品与价格

### 4. 数据分析与 KPI

- 销售额、成本、利润统计
- KPI 指标每日/每月自动汇总
- 支持自定义报表扩展

### 5. 操作日志与安全

- 登录尝试记录
- 重要操作建议接入审计日志

---

## 八、常见问题与排查

### 1. 登录失败

- 检查数据库用户表密码字段是否为 bcrypt 加密
- 检查 `config.php` 配置项
- 查看 PHP 错误日志

### 2. 数据库连接异常

- 检查 MySQL 服务状态
- 检查数据库连接配置
- 使用命令行测试连接

### 3. 页面样式错乱

- 检查 `public/css/style.css` 是否加载
- 浏览器缓存清理后刷新

### 4. 权限不足

- 检查当前用户角色与权限配置
- 检查 session 是否正常

---

## 九、扩展开发建议

### 1. 新功能开发流程

- 设计数据库表结构，更新 `scripts/database.sql`
- 编写后端 PHP 逻辑，放于 `src/`
- 前端页面建议使用 Tailwind CSS 或自定义样式
- 测试功能完整性与安全性

### 2. 性能优化

- 关键表添加索引（如订单、客户、产品主键及外键）
- 统计类查询建议使用视图或存储过程
- 可选接入 Redis/Memcached 缓存热点数据

### 3. 安全加固

- 所有表单操作建议二次确认
- 重要操作建议接入操作日志
- 定期备份数据库与配置文件

---

## 十、维护与升级

- 定期备份数据库（mysqldump）
- 监控 PHP 错误日志与访问日志
- 系统升级建议先在测试环境验证
- 及时修复安全漏洞，保持依赖组件最新

---

## 十一、技术支持

- 技术支持：上饶满星科技有限公司技术部
- 联系电话：0793-1234567
- 邮箱：support@manxing.com

如需定制开发或遇到技术难题，请联系技术支持团队。
