CREATE DATABASE IF NOT EXISTS sales_admin_db DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;
USE sales_admin_db;
-- 用户表(系统账户管理)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '主键ID',
    username VARCHAR(100) NOT NULL UNIQUE COMMENT '登录账号(唯一)',
    password VARCHAR(255) NOT NULL COMMENT '加密后的密码',
    role ENUM('admin', 'manager', 'sales') DEFAULT 'sales' COMMENT '角色: admin-管理员, manager-经理, sales-销售',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) COMMENT = '系统用户表(存储登录账号和权限信息)';
-- 客户信息表
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '客户ID',
    name VARCHAR(255) NOT NULL COMMENT '客户公司全称',
    industry VARCHAR(100) COMMENT '所属行业',
    region VARCHAR(100) COMMENT '所在地区',
    contact VARCHAR(100) COMMENT '联系人姓名',
    phone VARCHAR(50) COMMENT '联系电话',
    email VARCHAR(100) COMMENT '联系邮箱',
    level ENUM('A', 'B', 'C') DEFAULT 'C' COMMENT '客户等级: A-重点客户, B-普通客户, C-潜在客户',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) COMMENT = '客户基本信息表';
-- 产品信息表
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '产品ID',
    name VARCHAR(255) NOT NULL COMMENT '产品名称',
    category VARCHAR(100) COMMENT '产品分类',
    price DECIMAL(10, 2) NOT NULL COMMENT '销售价格',
    cost DECIMAL(10, 2) COMMENT '成本价格',
    stock INT DEFAULT 0 COMMENT '当前库存',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) COMMENT = '产品信息及库存管理表';
-- 销售订单表（注意：这个表被重复定义了两次，建议删除其中一个）
CREATE TABLE IF NOT EXISTS sales_orders (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '订单ID',
    customer_id INT NOT NULL COMMENT '关联客户ID',
    user_id INT NOT NULL COMMENT '负责销售的员工ID',
    total_amount DECIMAL(12, 2) NOT NULL COMMENT '订单总金额',
    status ENUM(
        'pending',
        'paid',
        'shipped',
        'completed',
        'cancelled'
    ) DEFAULT 'pending' COMMENT '订单状态: 
        pending-待处理, 
        paid-已付款, 
        shipped-已发货, 
        completed-已完成, 
        cancelled-已取消',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '下单时间',
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) COMMENT = '销售订单主表';
-- 订单明细表
CREATE TABLE IF NOT EXISTS sales_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '明细ID',
    order_id INT NOT NULL COMMENT '关联订单ID',
    product_id INT NOT NULL COMMENT '产品ID',
    quantity INT NOT NULL COMMENT '购买数量',
    price DECIMAL(10, 2) NOT NULL COMMENT '成交单价',
    FOREIGN KEY (order_id) REFERENCES sales_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) COMMENT = '订单明细表(记录订单中的具体产品)';
-- KPI指标表
CREATE TABLE IF NOT EXISTS kpi_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '指标ID',
    metric_name VARCHAR(100) NOT NULL COMMENT '指标名称(如: 月销售额)',
    value DECIMAL(12, 2) NOT NULL COMMENT '指标数值',
    unit VARCHAR(20) COMMENT '计量单位(如: 元, 个)',
    collected_at DATE NOT NULL COMMENT '统计日期'
) COMMENT = '关键绩效指标记录表';