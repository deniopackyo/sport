<?php
// file: infographics.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Инфографика - Статистика сервиса | СпортПрокат</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Навигация */
        .navbar {
            background: white;
            border-radius: 16px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 24px;
        }

        .logo p {
            color: #666;
            font-size: 12px;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
            transform: translateY(-2px);
        }

        .cart-icon, .login-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 8px 20px;
            border-radius: 25px;
            color: white !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        /* Хлебные крошки */
        .breadcrumb {
            background: white;
            padding: 12px 24px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        /* Заголовок */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .page-header h1 {
            font-size: 42px;
            background: linear-gradient(135deg, #1e293b, #334155);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
        }
        .page-header p {
            color: #64748b;
            font-size: 18px;
        }

        /* Главная сетка статистики */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: white;
            border-radius: 28px;
            padding: 28px 20px;
            text-align: center;
            box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 40px -12px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .stat-number {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 16px;
            color: #475569;
            font-weight: 500;
        }
        .stat-trend {
            margin-top: 12px;
            font-size: 14px;
            color: #10b981;
            font-weight: 600;
        }

        /* Блоки с графиками/диаграммами */
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 48px;
        }
        .chart-card {
            background: white;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .chart-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-left: 12px;
            border-left: 5px solid #667eea;
            color: #1e293b;
        }
        .chart-container {
            position: relative;
            height: 280px;
        }
        canvas {
            max-height: 260px;
            width: 100%;
        }

        /* Таблица отзывов */
        .top-table {
            background: white;
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 48px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .top-table h3 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            font-weight: 600;
            color: #334155;
        }
        .rating-stars {
            color: #f59e0b;
            letter-spacing: 2px;
        }
        .badge {
            background: #e2e8f0;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Категории популярности */
        .popular-cats {
            background: white;
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 48px;
        }
        .cat-bar {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .cat-icon {
            font-size: 28px;
            width: 50px;
            text-align: center;
        }
        .cat-name {
            width: 130px;
            font-weight: 600;
        }
        .progress {
            flex: 1;
            background: #e2e8f0;
            border-radius: 30px;
            height: 12px;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #667eea, #764ba2);
            height: 100%;
            border-radius: 30px;
            width: 0%;
        }
        .cat-percent {
            width: 60px;
            font-weight: 700;
            color: #475569;
        }

        .footer {
            text-align: center;
            padding: 30px;
            color: #666;
            border-top: 1px solid rgba(0,0,0,0.1);
            margin-top: 30px;
            background: white;
            border-radius: 16px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .charts-section {
                grid-template-columns: 1fr;
            }
            .stat-number {
                font-size: 36px;
            }
        }
    </style>
    <!-- Подключаем Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="container">
    <nav class="navbar">
        <div class="logo">
            <h1>⚡ СпортПрокат</h1>
            <p>Качественный инвентарь для активного отдыха</p>
        </div>
        <div class="nav-links">
            <a href="index.php">Главная</a>
            <a href="about.php">О нас</a>
            <a href="categories.php">Категории</a>
            <a href="infographics.php" style="color:#667eea; font-weight:700;">📊 Инфографика</a>
            <?php if (isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin_panel.php" style="background: #ef4444; padding: 8px 20px; border-radius: 25px; color: white;">⚡ Админ-панель</a>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <a href="profile.php">Личный кабинет</a>
                <a href="orders.php">Мои заказы</a>
                <a href="logout.php">Выйти</a>
                <a href="cart.php" class="cart-icon">🛒 Корзина (<?php echo getCartCount(); ?>)</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Вход</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="breadcrumb">
        <a href="index.php">Главная</a> / <span style="color:#334155;">Инфографика сервиса</span>
    </div>

    <div class="page-header">
        <h1>📈 Статистика и достижения</h1>
        <p>СпортПрокат в цифрах: динамика роста, популярные категории и настроение клиентов</p>
    </div>

    <?php
    // ---- Функции для подсчёта реальной статистики из БД ----
    $db = Database::getInstance()->getConnection();
    
    // 1. Общее количество пользователей
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // 2. Количество активных заказов (не завершённые и не отменённые)
    $stmt = $db->query("SELECT COUNT(*) as active FROM orders WHERE status NOT IN ('completed','cancelled')");
    $activeOrders = $stmt->fetch()['active'];
    
    // 3. Общая выручка (завершённые заказы)
    $stmt = $db->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
    $revenue = $stmt->fetch()['revenue'] ?? 0;
    
    // 4. Количество единиц инвентаря в аренде (сумма quantity в активных заказах)
    $stmt = $db->prepare("SELECT SUM(oi.quantity) as total_rented 
                          FROM order_items oi 
                          JOIN orders o ON oi.order_id = o.id 
                          WHERE o.status IN ('confirmed','active')");
    $stmt->execute();
    $rentedNow = $stmt->fetch()['total_rented'] ?? 0;
    
    // 5. Топ-3 популярных товара по количеству заказов (order_items)
    $stmt = $db->query("SELECT p.name, SUM(oi.quantity) as total_ordered 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        GROUP BY p.id 
                        ORDER BY total_ordered DESC LIMIT 3");
    $topProducts = $stmt->fetchAll();
    
    // 6. Статистика по категориям (сколько раз товары категории заказывали)
    $stmt = $db->query("SELECT c.name, c.icon, COUNT(oi.id) as order_count
                        FROM categories c
                        LEFT JOIN products p ON p.category_id = c.id
                        LEFT JOIN order_items oi ON oi.product_id = p.id
                        GROUP BY c.id
                        ORDER BY order_count DESC");
    $categoriesStats = $stmt->fetchAll();
    $maxCount = $categoriesStats ? max(array_column($categoriesStats, 'order_count')) : 1;
    
    // 7. Рейтинг товаров по отзывам (топ-5 средний балл)
    $stmt = $db->query("SELECT p.name, AVG(r.rating) as avg_rating, COUNT(r.id) as reviews_count
                        FROM products p
                        LEFT JOIN reviews r ON r.product_id = p.id
                        GROUP BY p.id
                        HAVING reviews_count > 0
                        ORDER BY avg_rating DESC LIMIT 5");
    $topRated = $stmt->fetchAll();
    
    // 8. Динамика заказов по месяцам (последние 6 месяцев)
    $stmt = $db->query("SELECT 
                            DATE_FORMAT(created_at, '%Y-%m') as month,
                            COUNT(*) as orders_count,
                            SUM(total_amount) as month_total
                        FROM orders
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 MONTH)
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                        ORDER BY month ASC");
    $monthlyData = $stmt->fetchAll();
    
    $months = [];
    $ordersCounts = [];
    $revenueByMonth = [];
    foreach ($monthlyData as $row) {
        $months[] = date('M Y', strtotime($row['month'] . '-01'));
        $ordersCounts[] = (int)$row['orders_count'];
        $revenueByMonth[] = (float)$row['month_total'];
    }
    if (empty($months)) {
        $months = ['Нет данных'];
        $ordersCounts = [0];
        $revenueByMonth = [0];
    }
    
    // 9. Распределение оценок отзывов (1-5 звёзд)
    $stmt = $db->query("SELECT rating, COUNT(*) as cnt FROM reviews GROUP BY rating ORDER BY rating");
    $ratingsDistribution = [];
    for ($i = 1; $i <= 5; $i++) $ratingsDistribution[$i] = 0;
    while ($row = $stmt->fetch()) {
        $ratingsDistribution[$row['rating']] = (int)$row['cnt'];
    }
    ?>

    <!-- Статистические карточки -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
            <div class="stat-label">довольных клиентов</div>
            <div class="stat-trend">↑ +18% за месяц</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-number"><?php echo number_format($activeOrders); ?></div>
            <div class="stat-label">активных заказов</div>
            <div class="stat-trend">⚡ в обработке / аренде</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number"><?php echo number_format($revenue, 0, '.', ' '); ?> ₽</div>
            <div class="stat-label">общая выручка</div>
            <div class="stat-trend">🏆 завершённые заказы</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⛷️</div>
            <div class="stat-number"><?php echo number_format($rentedNow); ?></div>
            <div class="stat-label">единиц инвентаря в аренде</div>
            <div class="stat-trend"> прямо сейчас</div>
        </div>
    </div>

    <!-- Графики + диаграммы -->
    <div class="charts-section">
        <div class="chart-card">
            <div class="chart-title"> Динамика заказов (последние 6 мес)</div>
            <div class="chart-container">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-title"> Распределение оценок</div>
            <div class="chart-container">
                <canvas id="ratingsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="charts-section">
        <div class="chart-card">
            <div class="chart-title"> Топ товаров по популярности</div>
            <div style="padding: 10px;">
                <?php if (empty($topProducts)): ?>
                    <p style="color:#999;">Пока нет данных о заказах...</p>
                <?php else: ?>
                    <ul style="list-style: none;">
                    <?php foreach ($topProducts as $index => $product): ?>
                        <li style="display:flex; align-items:center; gap:12px; margin-bottom: 16px;">
                            <span style="font-size:26px;"><?php echo $index === 0 ? '' : ($index === 1 ? '' : ''); ?></span>
                            <span style="flex:1; font-weight:500;"><?php echo htmlspecialchars($product['name']); ?></span>
                            <span style="background:#f1f5f9; padding:6px 12px; border-radius:40px; font-weight:700;"><?php echo (int)$product['total_ordered']; ?> шт</span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">📈 Выручка по месяцам (₽)</div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Популярные категории (прогресс-бары) -->
    <div class="popular-cats">
        <div class="chart-title" style="margin-bottom: 20px;"> Популярность категорий</div>
        <?php if (!empty($categoriesStats)): ?>
            <?php foreach ($categoriesStats as $cat): ?>
                <?php 
                    $percent = $maxCount > 0 ? round(($cat['order_count'] / $maxCount) * 100) : 0;
                ?>
                <div class="cat-bar">
                    <div class="cat-icon"><?php echo $cat['icon'] ?? ''; ?></div>
                    <div class="cat-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                    <div class="progress"><div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div></div>
                    <div class="cat-percent"><?php echo $cat['order_count']; ?> заказов</div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Статистика по категориям появится после первых заказов.</p>
        <?php endif; ?>
    </div>

    <!-- Топ-5 лучших по отзывам -->
    <div class="top-table">
        <h3>🌟 Топ-5 товаров с лучшим рейтингом</h3>
        <?php if (!empty($topRated)): ?>
            <table>
                <thead>
                <tr><th>Товар</th><th>Средний рейтинг</th><th>Кол-во отзывов</th><th>Оценка</th></tr>
                </thead>
                <tbody>
                <?php foreach ($topRated as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td><?php echo number_format($item['avg_rating'], 1); ?></td>
                        <td><?php echo $item['reviews_count']; ?></td>
                        <td class="rating-stars"><?php echo str_repeat('★', round($item['avg_rating'])) . str_repeat('☆', 5 - round($item['avg_rating'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="padding: 20px; text-align: center;">Пока нет отзывов для рейтинга.</p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>© 2025 СпортПрокат — Инфографика на основе реальных данных из системы аренды</p>
        <p style="font-size:12px; margin-top:8px;">Данные обновляются автоматически</p>
    </div>
</div>

<script>
    // Отрисовка графиков
    const months = <?php echo json_encode($months); ?>;
    const ordersCounts = <?php echo json_encode($ordersCounts); ?>;
    const revenueData = <?php echo json_encode($revenueByMonth); ?>;
    
    // График заказов
    const ctxOrders = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctxOrders, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Количество заказов',
                data: ordersCounts,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102,126,234,0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#764ba2',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} заказов` } }
            }
        }
    });

    // График выручки
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Выручка (₽)',
                data: revenueData,
                backgroundColor: 'linear-gradient(135deg, #667eea, #764ba2)',
                backgroundColor: '#a78bfa',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${Number(ctx.raw).toLocaleString()} ₽` } }
            }
        }
    });

    // Распределение оценок (кольцевая/бар) - для наглядности используем столбчатую
    const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
    new Chart(ratingsCtx, {
        type: 'bar',
        data: {
            labels: ['⭐ 1', '⭐⭐ 2', '⭐⭐⭐ 3', '⭐⭐⭐⭐ 4', '⭐⭐⭐⭐⭐ 5'],
            datasets: [{
                label: 'Количество отзывов',
                data: <?php echo json_encode(array_values($ratingsDistribution)); ?>,
                backgroundColor: ['#f97316', '#facc15', '#84cc16', '#22c55e', '#10b981'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true, stepSize: 1, title: { display: true, text: 'Отзывы' } } }
        }
    });
</script>
</body>
</html>