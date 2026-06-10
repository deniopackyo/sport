<?php
require_once 'config.php';

$category_id = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

$products = getProducts($category_id, $search, $sort);
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>СпортПрокат - Аренда спортивного инвентаря</title>
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
        .admin-link { background: linear-gradient(135deg, #ef4444, #dc2626); padding: 8px 20px; border-radius: 25px; color: white !important; }
        
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; transition: transform 0.3s, box-shadow 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102,126,234,0.4); }
        .btn-secondary { background: #f0f0f0; color: #333; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-secondary:hover { background: #e0e0e0; transform: translateY(-2px); }
        
        .filters { background: white; padding: 20px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .filter-form { display: flex; gap: 15px; flex-wrap: wrap; }
        .filter-group { flex: 1; min-width: 150px; }
        .filter-group input, .filter-group select { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; transition: border-color 0.3s; }
        .filter-group input:focus, .filter-group select:focus { outline: none; border-color: #667eea; }
        
        /* Виджет инфографики */
        .infographics-widget {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            transition: all 0.3s;
        }
        .infographics-widget:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .infographics-widget-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .infographics-widget-icon {
            font-size: 48px;
        }
        .infographics-widget-text h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 20px;
        }
        .infographics-widget-text p {
            color: #666;
        }
        
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin: 30px 0; }
        .product-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
        
        .product-image { height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #eef2ff 100%); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .product-card:hover .product-image img { transform: scale(1.05); }
        .product-image .no-image { font-size: 80px; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        
        .product-badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); color: white; padding: 5px 15px; border-radius: 25px; font-size: 12px; font-weight: 600; z-index: 1; }
        
        .product-info { padding: 20px; }
        .product-title { font-size: 18px; font-weight: 700; margin-bottom: 5px; color: #333; }
        .product-category { color: #667eea; font-size: 13px; margin-bottom: 10px; }
        .product-description { color: #666; font-size: 14px; margin-bottom: 15px; line-height: 1.4; min-height: 60px; }
        .product-price { font-size: 26px; font-weight: 800; color: #667eea; margin-bottom: 10px; }
        .product-price span { font-size: 14px; color: #999; font-weight: normal; }
        .product-stock { font-size: 14px; margin-bottom: 15px; padding: 4px 12px; border-radius: 20px; display: inline-block; }
        .in-stock { background: #d1fae5; color: #10b981; }
        .out-of-stock { background: #fee2e2; color: #ef4444; }
        
        .product-actions { display: flex; gap: 10px; padding-top: 15px; border-top: 1px solid #eee; }
        .product-actions .btn-secondary, .product-actions .btn-primary { flex: 1; text-align: center; font-size: 14px; padding: 10px; }
        
        .footer { text-align: center; padding: 30px; color: #666; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 30px; background: white; border-radius: 16px; }
        .footer-links { margin-top: 10px; }
        .footer-links a { color: #667eea; text-decoration: none; margin: 0 10px; transition: color 0.3s; }
        .footer-links a:hover { color: #764ba2; text-decoration: underline; }
        
        .empty-state { text-align: center; padding: 60px; background: white; border-radius: 20px; color: #666; }
        h2 { text-align: center; margin-bottom: 25px; font-size: 32px; color: #333; position: relative; }
        h2:after { content: ''; display: block; width: 60px; height: 4px; background: linear-gradient(135deg, #667eea, #764ba2); margin: 15px auto 0; border-radius: 2px; }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .filter-form { flex-direction: column; }
            .products-grid { grid-template-columns: 1fr; }
            .infographics-widget { text-align: center; justify-content: center; }
            .infographics-widget-left { justify-content: center; text-align: center; }
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
                
                <?php if (isLoggedIn() && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin_panel.php" class="admin-link">⚡ Админ-панель</a>
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

        <div class="filters">
            <form method="get" class="filter-form">
                <div class="filter-group">
                    <input type="text" name="search" placeholder="🔍 Поиск товара..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="filter-group">
                    <select name="cat">
                        <option value="0">Все категории</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['icon']; ?> <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="sort">
                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>По названию (А-Я)</option>
                        <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>По названию (Я-А)</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Цена: сначала дешевле</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Цена: сначала дороже</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Применить</button>
                <a href="index.php" class="btn-secondary">Сбросить</a>
            </form>
        </div>

        <!-- ВИДЖЕТ ИНФОГРАФИКИ -->
        <div class="infographics-widget">
            <div class="infographics-widget-left">
                <div class="infographics-widget-icon">📊</div>
                <div class="infographics-widget-text">
                    <h3>СпортПрокат в цифрах</h3>
                    <p>Узнайте статистику сервиса: количество клиентов, популярные товары и динамику заказов</p>
                </div>
            </div>
            <a href="infographics.php" class="btn-primary" style="padding: 12px 28px;">📈 Смотреть инфографику →</a>
        </div>

        <h2>🏆 Наш инвентарь</h2>

        <?php if (empty($products)): ?>
            <div class="empty-state">🔍 Товары не найдены</div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <?php echo $product['category_icon'] ?? ''; ?>
                                </div>
                            <?php endif; ?>
                            <div class="product-badge"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category"><?php echo $product['category_icon'] ?? ''; ?> <?php echo htmlspecialchars($product['category_name']); ?></div>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 80)); ?></p>
                            <div class="product-price"><?php echo number_format($product['price'], 0, '.', ' '); ?> ₽<span>/день</span></div>
                            <div class="product-stock <?php echo ($product['stock'] ?? 0) > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                <?php echo ($product['stock'] ?? 0) > 0 ? "✓ В наличии ({$product['stock']} шт.)" : "✗ Нет в наличии"; ?>
                            </div>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Подробнее</a>
                                <?php if (isLoggedIn() && ($product['stock'] ?? 0) > 0): ?>
                                    <form method="post" action="cart.php" style="flex: 1;">
                                        <input type="hidden" name="add_to_cart" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn-primary">В корзину</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря с доставкой по всей России</p>
            <div class="footer-links">
                <a href="about.php">О нас</a> |
                <a href="infographics.php">📊 Инфографика</a> |
                <a href="categories.php">Категории</a> |
                <a href="index.php">Главная</a>
            </div>
        </div>
    </div>
</body>
</html>