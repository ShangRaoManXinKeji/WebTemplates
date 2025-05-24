<?php
session_start();
require_once '../src/config/config.php';


define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sales_admin_db');

// 检查登录状态
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 连接数据库
try {
    // 设置错误报告模式
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("数据库连接失败: " . $mysqli->connect_error);
    }
    
    // 设置字符集和连接选项
    $mysqli->set_charset("utf8mb4");
    $mysqli->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
    
    // 设置错误报告
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
} catch (Exception $e) {
    die("连接失败: " . $e->getMessage());
}

// 获取统计数据
$stats = array();

// 1. 总销售额
$result = $mysqli->query("SELECT SUM(total_amount) as total FROM sales_orders WHERE status != 'cancelled'");
$row = $result->fetch_assoc();
$stats['total_sales'] = number_format($row['total'], 2);
$result->free();

// 2. 客户总数
$result = $mysqli->query("SELECT COUNT(*) as total FROM customers");
$row = $result->fetch_assoc();
$stats['total_customers'] = $row['total'];
$result->free();

// 3. 产品总数
$result = $mysqli->query("SELECT COUNT(*) as total FROM products");
$row = $result->fetch_assoc();
$stats['total_products'] = $row['total'];
$result->free();

// 4. 最新客户满意度
$result = $mysqli->query("SELECT value FROM kpi_metrics WHERE metric_name = '客户满意度' ORDER BY collected_at DESC LIMIT 1");
$row = $result->fetch_assoc();
$stats['satisfaction'] = $row['value'];
$result->free();

// 5. 获取最近5个订单
$result = $mysqli->query("
    SELECT o.*, c.name as customer_name, u.username as sales_person
    FROM sales_orders o
    JOIN customers c ON o.customer_id = c.id
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recent_orders = array();
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}
$result->free();

// 6. 获取库存预警产品（库存小于100的产品）
$result = $mysqli->query("
    SELECT *
    FROM products
    WHERE stock < 100
    ORDER BY stock ASC
");
$low_stock = array();
while ($row = $result->fetch_assoc()) {
    $low_stock[] = $row;
}
$result->free();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>销售管理系统 - 仪表板</title>
    <!-- 使用国内CDN资源 -->
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1890ff;
            --success-color: #52c41a;
            --warning-color: #faad14;
            --danger-color: #ff4d4f;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }

        .dashboard-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .stat-card {
            padding: 24px;
            text-align: center;
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-title {
            color: #666;
            font-size: 1rem;
        }

        .table-responsive {
            margin-top: 2rem;
        }

        .status-badge {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-size: 0.85em;
        }

        .stock-warning {
            color: var(--warning-color);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>销售管理系统
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-home me-1"></i>仪表板</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-shopping-cart me-1"></i>订单管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-users me-1"></i>客户管理</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-box me-1"></i>产品管理</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-1"></i>设置</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>退出</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- 主要内容区 -->
    <div class="container-fluid py-4">
        <!-- 统计卡片 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-yen-sign text-primary"></i>
                    <div class="stat-value"><?php echo $stats['total_sales']; ?></div>
                    <div class="stat-title">总销售额（元）</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-users text-success"></i>
                    <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
                    <div class="stat-title">客户总数</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-box text-warning"></i>
                    <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-title">产品总数</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card stat-card">
                    <i class="fas fa-smile text-info"></i>
                    <div class="stat-value"><?php echo $stats['satisfaction']; ?>%</div>
                    <div class="stat-title">客户满意度</div>
                </div>
            </div>
        </div>

        <!-- 最近订单和库存预警 -->
        <div class="row g-4">
            <!-- 最近订单 -->
            <div class="col-lg-8">
                <div class="dashboard-card p-4">
                    <h4 class="mb-4"><i class="fas fa-clock me-2"></i>最近订单</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>订单号</th>
                                    <th>客户名称</th>
                                    <th>金额</th>
                                    <th>销售员</th>
                                    <th>状态</th>
                                    <th>时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>¥<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['sales_person']); ?></td>
                                    <td>
                                        <?php
                                        $status_class = array(
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'shipped' => 'info',
                                            'completed' => 'primary',
                                            'cancelled' => 'danger'
                                        )[$order['status']];
                                        $status_text = array(
                                            'paid' => '已支付',
                                            'pending' => '待处理',
                                            'shipped' => '已发货',
                                            'completed' => '已完成',
                                            'cancelled' => '已取消'
                                        )[$order['status']];
                                        ?>
                                        <span class="badge bg-<?php echo $status_class; ?> status-badge">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 库存预警 -->
            <div class="col-lg-4">
                <div class="dashboard-card p-4">
                    <h4 class="mb-4"><i class="fas fa-exclamation-triangle text-warning me-2"></i>库存预警</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>产品名称</th>
                                    <th>库存</th>
                                    <th>状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="stock-warning"><?php echo $product['stock']; ?></td>
                                    <td>
                                        <?php if ($product['stock'] < 50): ?>
                                        <span class="badge bg-danger">严重</span>
                                        <?php else: ?>
                                        <span class="badge bg-warning">警告</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 使用国内CDN资源 -->
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>