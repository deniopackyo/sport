<?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProduct($product_id);

if (!$product) {
    header('Location: index.php');
    exit();
}

$reviews = getProductReviews($product_id);
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_review']) && isLoggedIn()) {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        
        if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $comment]);
            $message = '<div class="alert alert-success">Отзыв добавлен!</div>';
            header('refresh:2');
        }
    } elseif (isset($_POST['add_to_cart'])) {
        if (addToCart($product_id, 1, 1)) {
            $message = '<div class="alert alert-success">Товар добавлен в корзину!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - СпортПрокат</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .navbar { background: white; border-radius: 16px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 40px rgba(0,0,0,0.1); flex-wrap: wrap; gap: 15px; }
        .logo h1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; background-clip: text; color: transparent; font-size: 24px; }
        .logo p { color: #666; font-size: 12px; }
        .nav-links { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .nav-links a:hover { color: #667eea; transform: translateY(-2px); }
        .cart-icon, .login-btn { background: linear-gradient(135deg, #667eea, #764ba2); padding: 8px 20px; border-radius: 25px; color: white !important; }
        
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 12px 28px; border-radius: 12px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102,126,234,0.4); }
        .btn-secondary { background: #f0f0f0; color: #333; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        
        .product-detail { background: white; border-radius: 24px; padding: 32px; margin-bottom: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .product-detail-image { background: linear-gradient(135deg, #f5f7fa, #eef2ff); border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; min-height: 400px; }
        .product-detail-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-detail-image .no-image { font-size: 120px; }
        .product-detail-info h1 { font-size: 32px; margin-bottom: 16px; color: #333; }
        .product-detail-price { font-size: 42px; font-weight: 800; color: #667eea; margin: 20px 0; }
        .product-detail-price span { font-size: 18px; color: #999; font-weight: normal; }
        
        .reviews-section { background: white; border-radius: 24px; padding: 32px; margin-bottom: 32px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .review-card { border-bottom: 1px solid #f0f0f0; padding: 20px 0; }
        .rating { color: #f59e0b; font-size: 20px; }
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .footer { text-align: center; padding: 30px; color: #666; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 30px; background: white; border-radius: 16px; }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .product-detail { grid-template-columns: 1fr; }
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
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php">Личный кабинет</a>
                    <a href="orders.php">Мои заказы</a>
                    <a href="logout.php">Выйти</a>
                    <a href="cart.php" class="cart-icon"> Корзина (<?php echo getCartCount(); ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Вход</a>
                    <a href="register.php" class="btn-primary">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>

        <?php echo $message; ?>

        <div class="product-detail">
            <div class="product-detail-image">
                <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="no-image"><?php echo $product['category_icon'] ?? ''; ?></div>
                <?php endif; ?>
            </div>
            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p style="color: #666; line-height: 1.6; margin: 16px 0;">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
                
                <?php if ($product['specs']): ?>
                    <p><strong>Характеристики:</strong> <?php echo htmlspecialchars($product['specs']); ?></p>
                <?php endif; ?>
                
                <div class="product-detail-price">
                    <?php echo number_format($product['price'], 0, '.', ' '); ?> ₽ <span>/ день</span>
                </div>
                
                <p><strong>Наличие:</strong> <?php echo $product['stock'] > 0 ? "✓ В наличии ({$product['stock']} шт.)" : "✗ Нет в наличии"; ?></p>
                
                <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                    <form method="post" style="margin-top: 24px;">
                        <button type="submit" name="add_to_cart" class="btn-primary" style="font-size: 18px; padding: 15px 35px;"> Добавить в корзину</button>
                    </form>
                <?php elseif (!isLoggedIn()): ?>
                    <p><a href="login.php">Войдите</a> чтобы добавить товар в корзину</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="reviews-section">
            <h3> Отзывы клиентов (<?php echo count($reviews); ?>)</h3>
            
            <?php if (isLoggedIn()): ?>
                <form method="post" style="margin: 24px 0; padding: 20px; background: #f9fafb; border-radius: 16px;">
                    <h4> Оставить отзыв</h4>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Оценка</label>
                        <select name="rating" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            <option value="5">5 - Отлично</option>
                            <option value="4">4 - Хорошо</option>
                            <option value="3">3 - Средне</option>
                            <option value="2">2 - Плохо</option>
                            <option value="1">1 - Ужасно</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Комментарий</label>
                        <textarea name="comment" rows="3" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                    </div>
                    <button type="submit" name="add_review" class="btn-primary"> Отправить отзыв</button>
                </form>
            <?php endif; ?>
            
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                    <p style="margin: 8px 0;"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <small style="color: #666;">👤 <?php echo htmlspecialchars($review['username']); ?> • <?php echo date('d.m.Y', strtotime($review['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($reviews)): ?>
                <p style="text-align: center; padding: 40px;"> Пока нет отзывов. Будьте первым!</p>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
        </div>
    </div>
</body>
</html>