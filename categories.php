<?php
require_once 'config.php';
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории - СпортПрокат</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .navbar {
            background: white;
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
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

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .category-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .category-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        
        .category-desc {
            color: #666;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            padding: 30px;
            color: #666;
            border-top: 1px solid #ddd;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
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
                    <a href="cart.php" class="cart-icon">🛒 Корзина (<?php echo getCartCount(); ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Вход</a>
                    <a href="register.php" class="btn-primary">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>

        <h2 style="text-align: center; margin-bottom: 32px;"> Категории товаров</h2>
        
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <a href="index.php?cat=<?php echo $category['id']; ?>" class="category-card">
                    <div class="category-icon"><?php echo $category['icon'] ?? ''; ?></div>
                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                    <div class="category-desc"><?php echo htmlspecialchars($category['description'] ?? 'Описание категории'); ?></div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
        </div>
    </div>
</body>
</html>