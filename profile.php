<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getUser();
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    
    if ($stmt->execute([$full_name, $phone, $address, $_SESSION['user_id']])) {
        $message = '<div class="alert alert-success"> Профиль обновлен!</div>';
        $user = getUser();
    } else {
        $message = '<div class="alert alert-error"> Ошибка обновления</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - СпортПрокат</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .navbar { background: white; border-radius: 16px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 40px rgba(0,0,0,0.1); flex-wrap: wrap; gap: 15px; }
        .logo h1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; background-clip: text; color: transparent; font-size: 24px; }
        .logo p { color: #666; font-size: 12px; }
        .nav-links { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: all 0.3s; }
        .nav-links a:hover { color: #667eea; transform: translateY(-2px); }
        .cart-icon, .login-btn { background: linear-gradient(135deg, #667eea, #764ba2); padding: 8px 20px; border-radius: 25px; color: white !important; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .form-container { background: white; border-radius: 24px; padding: 32px; max-width: 600px; margin: 0 auto; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 14px; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #667eea; }
        .form-group input:disabled { background: #f3f4f6; cursor: not-allowed; }
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .footer { text-align: center; padding: 30px; color: #666; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 30px; background: white; border-radius: 16px; }
        
        /* Блок ссылки на инфографику */
        .infographics-link-block {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #eef2ff);
            border-radius: 16px;
        }
        .infographics-link-block p {
            margin-bottom: 15px;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .form-container { padding: 20px; margin: 0 10px; }
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

        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 24px;">👤 Личный кабинет</h2>
            
            <?php echo $message; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Имя пользователя</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Полное имя</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Адрес доставки</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;"> Сохранить изменения</button>
            </form>
            
            <!-- Блок ссылки на инфографику -->
            <div class="infographics-link-block">
                <p>📊 Интересует статистика нашего сервиса?</p>
                <a href="infographics.php" class="btn-primary" style="display: inline-block;">Перейти к инфографике →</a>
            </div>
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