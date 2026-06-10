<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_cart'])) {
        addToCart($_POST['add_to_cart']);
        header('Location: cart.php');
        exit();
    } elseif (isset($_POST['remove'])) {
        removeFromCart($_POST['remove']);
        header('Location: cart.php');
        exit();
    } elseif (isset($_POST['checkout'])) {
        header('Location: orders.php');
        exit();
    }
}

$cart_items = getCartItems();
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'] * $item['rental_days'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - СпортПрокат</title>
    <link rel="stylesheet" href="style.css">
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
                <a href="profile.php">Личный кабинет</a>
                <a href="orders.php">Мои заказы</a>
                <a href="logout.php">Выйти</a>
                <a href="cart.php" class="cart-icon"> Корзина (<?php echo getCartCount(); ?>)</a>
            </div>
        </nav>

        <div style="background: white; border-radius: var(--radius); padding: 32px;">
            <h2 style="margin-bottom: 24px;"> Корзина</h2>
            
            <?php if (empty($cart_items)): ?>
                <p>Ваша корзина пуста</p>
                <a href="index.php" class="btn-primary">Перейти к покупкам</a>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light);">
                            <th style="padding: 12px; text-align: left;">Товар</th>
                            <th style="padding: 12px; text-align: left;">Цена/день</th>
                            <th style="padding: 12px; text-align: left;">Кол-во</th>
                            <th style="padding: 12px; text-align: left;">Дней</th>
                            <th style="padding: 12px; text-align: left;">Сумма</th>
                            <th style="padding: 12px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <?php echo number_format($item['price'], 0, '.', ' '); ?> ₽
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <?php echo $item['rental_days']; ?>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <?php echo number_format($item['price'] * $item['quantity'] * $item['rental_days'], 0, '.', ' '); ?> ₽
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                                    <form method="post">
                                        <button type="submit" name="remove" value="<?php echo $item['id']; ?>" class="btn-danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="padding: 16px; text-align: right; font-size: 18px; font-weight: 700;">Итого:</td>
                            <td colspan="2" style="padding: 16px; font-size: 24px; font-weight: 800; color: var(--secondary);">
                                <?php echo number_format($total, 0, '.', ' '); ?> ₽
                            </td>
                        </tr>
                    </tfoot>
                </table>
                
                <div style="display: flex; gap: 16px; margin-top: 24px; justify-content: flex-end;">
                    <a href="index.php" class="btn-secondary">Продолжить покупки</a>
                    <form method="post">
                        <button type="submit" name="checkout" class="btn-primary">Оформить заказ</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
        </div>
    </div>
</body>
</html>