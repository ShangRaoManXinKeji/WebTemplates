USE sales_admin_db;
INSERT INTO users (username, password, role, status, login_count)
VALUES -- 原密码: 123456
    (
        'xiaoli',
        '$2y$10$HKOKPALMfkeUng5VxUkTyOqhkqV0qW9hX5tK6HdXXhH.RJNW2Iq6O',
        'admin',
        1,
        0
    ),
    -- 原密码: abc123
    (
        'wangwu',
        '$2y$10$LMNQRSTuvwxyzABCDefGhijklmNOpqRsTuVwXyZaBcDeFgHiJk',
        'manager',
        1,
        0
    ),
    -- 原密码: password
    (
        'zhangsan',
        '$2y$10$abcdefGHIjklMNOpqRSTuvWXyZaBCdeFghiJKLmnoPQRstUVw',
        'sales',
        1,
        0
    ),
    -- 原密码: qwerty
    (
        'lisi',
        '$2y$10$YourSaltHere1234567890uT0zX1jK2vZD8K1WqP2q2K8XYwgN',
        'sales',
        1,
        0
    ),
    -- 原密码: letmein
    (
        'hanmeimei',
        '$2y$10$YourSaltHere1234567890uU1zX2jK3vZD8K1WqP2q2K8XYwgO',
        'manager',
        1,
        0
    ),
    -- 原密码: tom123
    (
        'tom',
        '$2y$10$YourSaltHere1234567890uV2zX3jK4vZD8K1WqP2q2K8XYwgP',
        'sales',
        1,
        0
    ),
    -- 原密码: jerry321
    (
        'jerry',
        '$2y$10$YourSaltHere1234567890uW3zX4jK5vZD8K1WqP2q2K8XYwgQ',
        'admin',
        1,
        0
    ),
    -- 原密码: iloveyou
    (
        'lucy',
        '$2y$10$YourSaltHere1234567890uX4zX5jK6vZD8K1WqP2q2K8XYwgR',
        'sales',
        1,
        0
    ),
    -- 原密码: welcome
    (
        'jack',
        '$2y$10$YourSaltHere1234567890uY5zX6jK7vZD8K1WqP2q2K8XYwgS',
        'manager',
        1,
        0
    ),
    -- 原密码: admin123
    (
        'rose',
        '$2y$10$YourSaltHere1234567890uZ6zX7jK8vZD8K1WqP2q2K8XYwgT',
        'sales',
        1,
        0
    ),
    -- 原密码: leo456
    (
        'leo',
        '$2y$10$YourSaltHere1234567890ua7zX8jK9vZD8K1WqP2q2K8XYwgU',
        'sales',
        1,
        0
    );
-- 其他表的数据保持不变
INSERT INTO customers (
        name,
        industry,
        region,
        contact,
        phone,
        email,
        level
    )
VALUES (
        '华夏科技有限公司',
        '信息技术',
        '北京',
        '李明',
        '13800138000',
        'liming@huaxia-tech.com',
        'A'
    ),
    (
        '东方食品集团',
        '食品制造',
        '上海',
        '王芳',
        '13900139000',
        'wangfang@dongfangfood.cn',
        'B'
    ),
    (
        '盛世房地产开发',
        '房地产',
        '广州',
        '张强',
        '13700137000',
        'zhangqiang@shengshi-estate.com',
        'A'
    ),
    (
        '金桥进出口贸易有限公司',
        '贸易',
        '深圳',
        '刘洋',
        '13600136000',
        'liuyang@jinqiao-trade.com',
        'C'
    ),
    (
        '未来能源股份有限公司',
        '新能源',
        '天津',
        '赵倩',
        '13500135000',
        'zhaoqian@weilai-energy.cn',
        'B'
    ),
    (
        '星光教育培训中心',
        '教育培训',
        '杭州',
        '孙丽',
        '13400134000',
        'sunli@xingguang-edu.com',
        'C'
    ),
    (
        '盛世医疗器械有限公司',
        '医疗器械',
        '重庆',
        '周杰',
        '13300133000',
        'zhoujie@shengshi-med.com',
        'A'
    ),
    (
        '蓝海传媒集团',
        '传媒',
        '南京',
        '郑娜',
        '13200132000',
        'zhengna@lanhai-media.cn',
        'B'
    ),
    (
        '金盾安防技术有限公司',
        '安防技术',
        '苏州',
        '吴涛',
        '13100131000',
        'wutao@jindun-security.com',
        'C'
    ),
    (
        '东方航空股份有限公司',
        '航空运输',
        '成都',
        '陈红',
        '13000130000',
        'chenhong@dongfang-air.cn',
        'A'
    );
INSERT INTO products (name, category, price, cost, stock)
VALUES ('华为Mate 50 Pro', '智能手机', 5999.00, 4200.00, 150),
    ('联想拯救者游戏本', '笔记本电脑', 8999.00, 7000.00, 80),
    ('小米智能手环7', '智能穿戴', 299.00, 150.00, 500),
    ('苹果AirPods Pro', '耳机', 1799.00, 1100.00, 200),
    ('佳能EOS R6', '数码相机', 15800.00, 12000.00, 40),
    ('罗技MX Master 3', '电脑配件', 699.00, 400.00, 300),
    ('戴尔U2723QE显示器', '显示设备', 3499.00, 2800.00, 70),
    ('飞利浦空气净化器', '家用电器', 2299.00, 1700.00, 120),
    ('索尼PlayStation 5', '游戏设备', 4499.00, 3800.00, 50),
    ('三星T7移动硬盘1TB', '存储设备', 999.00, 750.00, 250);
INSERT INTO sales_orders (
        customer_id,
        user_id,
        total_amount,
        status,
        created_at
    )
VALUES (1, 3, 12000.00, 'paid', '2025-05-01 10:20:30'),
    (2, 5, 8999.00, 'pending', '2025-05-03 11:15:00'),
    (3, 7, 4500.50, 'shipped', '2025-05-05 14:05:20'),
    (4, 2, 299.00, 'completed', '2025-05-07 09:30:00'),
    (
        5,
        4,
        15800.00,
        'cancelled',
        '2025-05-10 16:45:00'
    ),
    (6, 8, 699.00, 'paid', '2025-05-12 13:00:00'),
    (
        7,
        1,
        3499.00,
        'completed',
        '2025-05-15 08:25:40'
    ),
    (8, 9, 2299.00, 'paid', '2025-05-18 17:50:00'),
    (9, 3, 4499.00, 'shipped', '2025-05-20 19:00:00'),
    (10, 6, 999.00, 'pending', '2025-05-22 20:30:15');
INSERT INTO sales_order_items (order_id, product_id, quantity, price)
VALUES (1, 1, 2, 5999.00),
    -- 华为Mate 50 Pro *2
    (2, 2, 1, 8999.00),
    -- 联想拯救者游戏本 *1
    (3, 3, 3, 299.00),
    -- 小米智能手环7 *3
    (4, 4, 1, 1799.00),
    -- 苹果AirPods Pro *1
    (5, 5, 1, 15800.00),
    -- 佳能EOS R6 *1
    (6, 6, 2, 699.00),
    -- 罗技MX Master 3 *2
    (7, 7, 1, 3499.00),
    -- 戴尔U2723QE显示器 *1
    (8, 8, 1, 2299.00),
    -- 飞利浦空气净化器 *1
    (9, 9, 1, 4499.00),
    -- 索尼PlayStation 5 *1
    (10, 10, 4, 999.00);
-- 三星T7移动硬盘1TB *4
INSERT INTO kpi_metrics (metric_name, value, unit, collected_at)
VALUES ('月销售额', 1200000.00, '元', '2025-04-30'),
    ('月销售额', 1350000.00, '元', '2025-05-31'),
    ('新增客户数', 50, '个', '2025-04-30'),
    ('新增客户数', 65, '个', '2025-05-31'),
    ('客户满意度', 92.5, '%', '2025-04-30'),
    ('客户满意度', 94.1, '%', '2025-05-31'),
    ('月退货率', 1.2, '%', '2025-04-30'),
    ('月退货率', 1.0, '%', '2025-05-31'),
    ('库存周转率', 4.5, '次', '2025-04-30'),
    ('库存周转率', 4.8, '次', '2025-05-31');