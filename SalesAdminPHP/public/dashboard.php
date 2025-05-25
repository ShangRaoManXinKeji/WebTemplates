<?php
session_start();

// 检查登录状态
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 加载配置
$config = require __DIR__ . '/../src/config/config.php';

// 连接数据库
try {
    $db = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}",
        $config['db']['username'],
        $config['db']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 获取统计数据
    $stats = [
        'total_sales' => $db->query("SELECT SUM(total_amount) FROM sales_orders WHERE status != 'cancelled'")->fetchColumn() ?: 0,
        'total_customers' => $db->query("SELECT COUNT(*) FROM customers")->fetchColumn(),
        'total_products' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'satisfaction' => $db->query("SELECT value FROM kpi_metrics WHERE metric_name = '客户满意度' ORDER BY collected_at DESC LIMIT 1")->fetchColumn() ?: 0
    ];

    // 获取最近订单
    $recent_orders = $db->query(
        "SELECT o.*, c.name as customer_name, u.username as sales_person 
         FROM sales_orders o 
         JOIN customers c ON o.customer_id = c.id 
         JOIN users u ON o.user_id = u.id 
         ORDER BY o.created_at DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

    // 获取库存预警
    $low_stock = $db->query(
        "SELECT * FROM products 
         WHERE stock < 100 
         ORDER BY stock ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    // 计算总成本（以库存产品为基础估算整体投入）
    $total_cost = -abs($db->query("SELECT SUM(cost * stock) FROM products")->fetchColumn() ?: 0);

    // 粗略毛利润计算：总销售额 - 当前库存总成本（注意：非实际售出成本，仅估算）
    $gross_profit = $stats['total_sales'] - $total_cost;

    // 当前利润暂定等于毛利润（后续可引入运营成本、营销费用等进行扣除）
    $profit = $total_cost + $gross_profit;
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>仪表板 - <?= htmlspecialchars($config['site_name']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@unocss/reset/tailwind.css">
  <link rel="stylesheet" href="./css/style.css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-relaxed">
  <header class="bg-gray-900 text-white py-6 px-10 shadow-md rounded-b-xl">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold tracking-wider">
        <?= htmlspecialchars($config['site_name']) ?>
      </h1>
      <nav class="flex gap-4 items-center">
        <a href="#" class="bg-blue-500 hover:bg-blue-600 transition px-4 py-2 rounded-md font-semibold">仪表板</a>

        <div class="relative group">
          <a href="#" class="px-4 py-2">订单管理</a>
          <div class="absolute hidden group-hover:block bg-white shadow-md rounded-md mt-2 w-40 z-20">
            <a href="order_create.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">新增订单</a>
            <a href="order_list.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">订单列表</a>
            <a href="order_edit.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">编辑订单</a>
            <a href="order_delete.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">删除订单</a>
          </div>
        </div>

        <div class="relative group">
          <a href="#" class="px-4 py-2">客户管理</a>
          <div class="absolute hidden group-hover:block bg-white shadow-md rounded-md mt-2 w-40 z-20">
            <a href="customer_create.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">新增客户</a>
            <a href="customer_list.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">客户列表</a>
            <a href="customer_edit.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">编辑客户</a>
            <a href="customer_delete.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">删除客户</a>
          </div>
        </div>

        <div class="relative group">
          <a href="#" class="px-4 py-2">产品管理</a>
          <div class="absolute hidden group-hover:block bg-white shadow-md rounded-md mt-2 w-40 z-20">
            <a href="product_create.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">新增产品</a>
            <a href="product_list.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">产品列表</a>
            <a href="product_edit.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">编辑产品</a>
            <a href="product_delete.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">删除产品</a>
          </div>
        </div>

        <a href="logout.php" class="text-red-400 hover:text-red-500 font-semibold">退出</a>
      </nav>
    </div>
  </header>

  <main class="max-w-screen-xl mx-auto px-6 py-8">
    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <?php
        $cards = [
          ['title' => '总销售额', 'value' => '¥' . number_format($stats['total_sales'], 2)],
          ['title' => '总成本', 'value' => '¥' . number_format($total_cost, 2)],
        //   ['title' => '毛利润', 'value' => '¥' . number_format($gross_profit, 2)],
          ['title' => '利润', 'value' => '¥' . number_format($profit, 2)],
          ['title' => '客户总数', 'value' => $stats['total_customers']],
          ['title' => '产品总数', 'value' => $stats['total_products']],
          ['title' => '客户满意度', 'value' => $stats['satisfaction'] . '%'],
        ];
        foreach ($cards as $card):
      ?>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all">
          <h3 class="text-gray-500 text-sm font-semibold mb-2"><?= $card['title'] ?></h3>
          <p class="text-2xl font-bold text-blue-600"><?= $card['value'] ?></p>
        </div>
      <?php endforeach; ?>
    </section>

    <div class="text-right mb-6">
      <a href="export.php" class="bg-gradient-to-r from-teal-400 to-blue-500 text-white px-6 py-2 rounded-lg shadow hover:opacity-90 font-semibold">导出数据</a>
    </div>

    <section class="mb-10">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">最近订单</h2>
      <div class="overflow-x-auto">
        <table class="w-full bg-white shadow rounded-xl">
          <thead class="bg-gray-800 text-white">
            <tr>
              <th class="p-3 text-left">订单号</th>
              <th class="p-3 text-left">客户名称</th>
              <th class="p-3 text-left">金额</th>
              <th class="p-3 text-left">销售员</th>
              <th class="p-3 text-left">状态</th>
              <th class="p-3 text-left">时间</th>
              <th class="p-3 text-left">操作</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_orders as $order): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-3">#<?= $order['id'] ?></td>
                <td class="p-3"><?= htmlspecialchars($order['customer_name']) ?></td>
                <td class="p-3">¥<?= number_format($order['total_amount'], 2) ?></td>
                <td class="p-3"><?= htmlspecialchars($order['sales_person']) ?></td>
                <td class="p-3"><?= htmlspecialchars($order['status']) ?></td>
                <td class="p-3"><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                <td class="p-3 space-x-2">
                  <a href="edit_order.php?id=<?= $order['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">编辑</a>
                  <a href="delete_order.php?id=<?= $order['id'] ?>" 
                     onclick="return confirm('确定要删除订单 #<?= $order['id'] ?> 吗？该操作不可逆！')"
                     class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">删除</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section>
      <h2 class="text-xl font-semibold text-gray-800 mb-4">库存预警</h2>
      <div class="overflow-x-auto">
        <table class="w-full bg-white shadow rounded-xl">
          <thead class="bg-red-100 text-red-800">
            <tr>
              <th class="p-3 text-left">产品名称</th>
              <th class="p-3 text-left">库存</th>
              <th class="p-3 text-left">状态</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($low_stock as $product): ?>
              <tr class="hover:bg-red-50">
                <td class="p-3"><?= htmlspecialchars($product['name']) ?></td>
                <td class="p-3"><?= $product['stock'] ?></td>
                <td class="p-3 font-semibold <?= $product['stock'] < 50 ? 'text-red-600' : 'text-yellow-500' ?>">
                  <?= $product['stock'] < 50 ? '严重' : '警告' ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>