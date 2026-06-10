<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';

// Оформление заказа из корзины
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $cart_items = getCartItems();
    
    if (empty($cart_items)) {
        $message = '<div class="alert alert-error">Корзина пуста!</div>';
    } else {
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'] * $item['rental_days'];
        }
        
        $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
        
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("INSERT INTO orders (order_number, user_id, total_amount, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
            $stmt->execute([$order_number, $_SESSION['user_id'], $total]);
            $order_id = $db->lastInsertId();
            
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_day, rental_days) VALUES (?, ?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['rental_days']]);
            }
            
            clearCart();
            
            $db->commit();
            $message = '<div class="alert alert-success">✅ Заказ #' . $order_number . ' успешно оформлен!</div>';
        } catch (Exception $e) {
            $db->rollBack();
            $message = '<div class="alert alert-error">Ошибка оформления заказа: ' . $e->getMessage() . '</div>';
        }
    }
}

// Получаем заказы пользователя
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

function getOrderItems($order_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы - СпортПрокат</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-pending { background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .status-confirmed { background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .status-active { background: #d1fae5; color: #059669; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .status-completed { background: #e5e7eb; color: #4b5563; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        .status-cancelled { background: #fee2e2; color: #dc2626; padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; font-weight: 600; }
        
        .order-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            transition: box-shadow 0.3s;
        }
        .order-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        .order-number {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }
        .order-date {
            color: #666;
            font-size: 14px;
        }
        .order-total {
            font-size: 20px;
            font-weight: 800;
            color: #667eea;
        }
        .order-items {
            margin-top: 16px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item-name {
            flex: 2;
        }
        .order-item-qty, .order-item-price {
            flex: 1;
            text-align: center;
        }
        .order-item-total {
            flex: 1;
            text-align: right;
            font-weight: 600;
            color: #667eea;
        }
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .empty-cart {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
        }
        .cart-summary {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            text-align: right;
        }
        
        /* Баннер инфографики */
        .infographics-banner {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 16px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            transition: transform 0.3s;
        }
        .infographics-banner:hover {
            transform: translateY(-2px);
        }
        .infographics-banner-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .infographics-banner-icon {
            font-size: 32px;
        }
        .infographics-banner-text {
            font-weight: 500;
        }
        .infographics-banner-text strong {
            color: #333;
        }
        .infographics-banner-text span {
            color: #666;
        }
        .infographics-banner-link {
            background: #667eea;
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .infographics-banner-link:hover {
            background: #764ba2;
            transform: scale(1.05);
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .navbar { background: white; border-radius: 16px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 40px rgba(0,0,0,0.1); flex-wrap: wrap; gap: 15px; }
        .logo h1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; background-clip: text; color: transparent; font-size: 24px; }
        .logo p { color: #666; font-size: 12px; }
        .nav-links { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .nav-links a:hover { color: #667eea; transform: translateY(-2px); }
        .cart-icon, .login-btn { background: linear-gradient(135deg, #667eea, #764ba2); padding: 8px 20px; border-radius: 25px; color: white !important; }
        .footer { text-align: center; padding: 30px; color: #666; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 30px; background: white; border-radius: 16px; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-secondary { background: #f0f0f0; color: #333; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .infographics-banner { text-align: center; justify-content: center; }
            .infographics-banner-left { justify-content: center; text-align: center; }
        }
    </style>
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
                <a href="infographics.php">📊 Инфографика</a>
                <a href="profile.php">Личный кабинет</a>
                <a href="orders.php">Мои заказы</a>
                <a href="logout.php">Выйти</a>
                <a href="cart.php" class="cart-icon">🛒 Корзина (<?php echo getCartCount(); ?>)</a>
            </div>
        </nav>

        <!-- БАННЕР ИНФОГРАФИКИ -->
        <div class="infographics-banner">
            <div class="infographics-banner-left">
                <div class="infographics-banner-icon">📊</div>
                <div class="infographics-banner-text">
                    <strong>Интересная статистика?</strong>
                    <span>Посмотрите инфографику нашего сервиса</span>
                </div>
            </div>
            <a href="infographics.php" class="infographics-banner-link">Узнать →</a>
        </div>

        <div style="background: white; border-radius: 16px; padding: 32px;">
            <h2 style="margin-bottom: 24px;"> Мои заказы</h2>
            
            <?php echo $message; ?>
            
            <?php 
            $cart_items_for_checkout = getCartItems();
            if (!empty($cart_items_for_checkout)): 
                $cart_total = 0;
                foreach ($cart_items_for_checkout as $item) {
                    $cart_total += $item['price'] * $item['quantity'] * $item['rental_days'];
                }
            ?>
                <div class="cart-summary">
                    <h3>🛒 Текущая корзина</h3>
                    <p>Товаров: <?php echo count($cart_items_for_checkout); ?> | Сумма: <?php echo number_format($cart_total, 0, '.', ' '); ?> ₽</p>
                    <form method="post">
                        <button type="submit" name="checkout" class="btn-primary" style="margin-top: 10px;"> Оформить заказ из корзины</button>
                    </form>
                </div>
            <?php endif; ?>
            
            <?php if (empty($orders)): ?>
                <div class="empty-cart">
                    <p style="font-size: 48px; margin-bottom: 16px;"></p>
                    <p>У вас пока нет заказов</p>
                    <a href="index.php" class="btn-primary" style="margin-top: 16px; display: inline-block;">Перейти к покупкам</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php $order_items = getOrderItems($order['id']); ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-number"> Заказ #<?php echo htmlspecialchars($order['order_number']); ?></div>
                                <div class="order-date"> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div>
                                <span class="status-<?php echo $order['status']; ?>">
                                    <?php 
                                        $statuses = [
                                            'pending' => ' Ожидает подтверждения',
                                            'confirmed' => ' Подтвержден',
                                            'active' => ' Активен',
                                            'completed' => ' Завершен',
                                            'cancelled' => ' Отменен'
                                        ];
                                        echo $statuses[$order['status']];
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($order_items as $item): ?>
                                <div class="order-item">
                                    <div class="order-item-name">
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </div>
                                    <div class="order-item-qty">
                                        <?php echo $item['quantity']; ?> шт × <?php echo $item['rental_days']; ?> дн.
                                    </div>
                                    <div class="order-item-price">
                                        <?php echo number_format($item['price_per_day'], 0, '.', ' '); ?> ₽/день
                                    </div>
                                    <div class="order-item-total">
                                        <?php echo number_format($item['price_per_day'] * $item['quantity'] * $item['rental_days'], 0, '.', ' '); ?> ₽
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f0f0f0; text-align: right;">
                            <span style="font-size: 18px; font-weight: 600;">Итого: </span>
                            <span class="order-total"><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
            <p style="margin-top: 10px;">
                <a href="about.php" style="color: #667eea; text-decoration: none;">О нас</a> |
                <a href="infographics.php" style="color: #667eea; text-decoration: none;">📊 Инфографика</a> |
                <a href="categories.php" style="color: #667eea; text-decoration: none;">Категории</a>
            </p>
        </div>
    </div>
</body>
</html>