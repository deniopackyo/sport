<?php
require_once 'config.php';

// Проверка прав администратора
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$db = Database::getInstance()->getConnection();
$message = '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// Создаем папку для изображений если её нет
if (!file_exists('uploads/products')) {
    mkdir('uploads/products', 0777, true);
}

// ========== ОБРАБОТКА ЗАКАЗОВ ==========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Изменение статуса заказа
    if (isset($_POST['update_order_status'])) {
        $order_id = (int)$_POST['order_id'];
        $status = $_POST['status'];
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $message = '<div class="alert alert-success">Статус заказа обновлен!</div>';
    }
    
    // Добавление товара с фото
    elseif (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category_id = (int)$_POST['category_id'];
        $specs = $_POST['specs'];
        
        $image_path = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['product_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = 'uploads/products/' . $new_filename;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                }
            }
        }
        
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock, category_id, specs, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $stock, $category_id, $specs, $image_path])) {
            $message = '<div class="alert alert-success">Товар добавлен!</div>';
        }
    }
    
    // Редактирование товара
    elseif (isset($_POST['edit_product'])) {
        $id = (int)$_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category_id = (int)$_POST['category_id'];
        $specs = $_POST['specs'];
        
        $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $current_image = $stmt->fetchColumn();
        
        $image_path = $current_image;
        
        if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
            if ($current_image && file_exists($current_image)) {
                unlink($current_image);
            }
            $image_path = '';
        }
        
        if (isset($_FILES['edit_product_image']) && $_FILES['edit_product_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['edit_product_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                if ($current_image && file_exists($current_image) && $image_path != '') {
                    unlink($current_image);
                }
                
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = 'uploads/products/' . $new_filename;
                
                if (move_uploaded_file($_FILES['edit_product_image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                }
            }
        }
        
        $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, specs = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $price, $stock, $category_id, $specs, $image_path, $id])) {
            $message = '<div class="alert alert-success">Товар обновлен!</div>';
        }
    }
    
    // Удаление товара
    elseif (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['product_id'];
        
        $stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $image = $stmt->fetchColumn();
        
        if ($image && file_exists($image)) {
            unlink($image);
        }
        
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$product_id])) {
            $message = '<div class="alert alert-success">Товар удален!</div>';
        }
    }
    
    // Добавление категории
    elseif (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $icon = $_POST['icon'];
        $description = $_POST['description'];
        
        $stmt = $db->prepare("INSERT INTO categories (name, icon, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $icon, $description])) {
            $message = '<div class="alert alert-success">Категория добавлена!</div>';
        }
    }
    
    // Удаление категории
    elseif (isset($_POST['delete_category'])) {
        $id = (int)$_POST['category_id'];
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div class="alert alert-success">Категория удалена!</div>';
    }
    
    // Изменение роли пользователя
    elseif (isset($_POST['change_user_role'])) {
        $user_id = (int)$_POST['user_id'];
        $role = $_POST['role'];
        
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$role, $user_id]);
            $message = '<div class="alert alert-success">Роль пользователя изменена!</div>';
        } else {
            $message = '<div class="alert alert-error">Нельзя изменить свою роль!</div>';
        }
    }
    
    // Удаление пользователя
    elseif (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = '<div class="alert alert-success">Пользователь удален!</div>';
        } else {
            $message = '<div class="alert alert-error">Нельзя удалить самого себя!</div>';
        }
    }
    
    // Удаление отзыва
    elseif (isset($_POST['delete_review'])) {
        $review_id = (int)$_POST['review_id'];
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$review_id]);
        $message = '<div class="alert alert-success">Отзыв удален!</div>';
    }
}

// ========== ПОЛУЧЕНИЕ ДАННЫХ ==========

// Статистика
$stats = [];
$stmt = $db->query("SELECT COUNT(*) as count FROM users");
$stats['users'] = $stmt->fetch()['count'];
$stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'confirmed', 'active')");
$stats['active_orders'] = $stmt->fetch()['count'];
$stmt = $db->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;
$stmt = $db->query("SELECT SUM(stock) as count FROM products");
$stats['products_stock'] = $stmt->fetch()['count'] ?? 0;

// Заказы
$orders = $db->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

// Товары
$products = $db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Категории
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Пользователи
$users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

// Отзывы
$reviews = $db->query("SELECT r.*, u.username, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC")->fetchAll();

$statuses = [
    'pending' => 'Ожидает',
    'confirmed' => 'Подтвержден',
    'active' => 'Активен',
    'completed' => 'Завершен',
    'cancelled' => 'Отменен'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - СпортПрокат</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .admin-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            background: white;
            padding: 15px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-tab {
            padding: 12px 24px;
            background: #f3f4f6;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .admin-tab:hover {
            background: #667eea;
            color: white;
        }
        
        .admin-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .tab-content {
            display: none;
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px;
            border-radius: 16px;
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .product-image-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product-image-cell {
            width: 80px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
            display: block;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        th {
            background: #f3f4f6;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-confirmed { background: #dbeafe; color: #2563eb; }
        .status-active { background: #d1fae5; color: #059669; }
        .status-completed { background: #e5e7eb; color: #4b5563; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        
        .role-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .role-admin { background: #dbeafe; color: #2563eb; }
        .role-user { background: #e5e7eb; color: #4b5563; }
        
        .btn-sm {
            padding: 4px 12px;
            font-size: 12px;
            margin: 2px;
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
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 24px;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .review-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        
        .rating {
            color: #f59e0b;
        }
        
        .current-image {
            margin: 10px 0;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .current-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .image-upload-info {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .header-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            th, td {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <div>
                <h1>⚡ Панель администратора</h1>
                <p>Управление сайтом СпортПрокат</p>
            </div>
            <div class="header-buttons">
                <a href="infographics.php" class="btn-secondary" style="background: white; color: #10b981;">📊 Инфографика</a>
                <a href="index.php" class="btn-secondary" style="background: white; color: #667eea;">На сайт</a>
                <a href="logout.php" class="btn-primary" style="background: white; color: #667eea;">Выйти</a>
            </div>
        </div>
        
        <?php echo $message; ?>
        
        <div class="admin-tabs">
            <button class="admin-tab <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>" onclick="showTab('dashboard')">📊 Главная</button>
            <button class="admin-tab <?php echo $active_tab == 'orders' ? 'active' : ''; ?>" onclick="showTab('orders')">📦 Заказы</button>
            <button class="admin-tab <?php echo $active_tab == 'products' ? 'active' : ''; ?>" onclick="showTab('products')">🏷️ Товары</button>
            <button class="admin-tab <?php echo $active_tab == 'categories' ? 'active' : ''; ?>" onclick="showTab('categories')">📁 Категории</button>
            <button class="admin-tab <?php echo $active_tab == 'users' ? 'active' : ''; ?>" onclick="showTab('users')">👥 Пользователи</button>
            <button class="admin-tab <?php echo $active_tab == 'reviews' ? 'active' : ''; ?>" onclick="showTab('reviews')">⭐ Отзывы</button>
        </div>
        
        <!-- ГЛАВНАЯ -->
        <div id="dashboard" class="tab-content <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
            <h2>📈 Статистика</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Всего пользователей</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <h3><?php echo $stats['active_orders']; ?></h3>
                    <p>Активных заказов</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <h3><?php echo number_format($stats['revenue'], 0, '.', ' '); ?> ₽</h3>
                    <p>Общая выручка</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <h3><?php echo $stats['products_stock']; ?></h3>
                    <p>Товаров в наличии</p>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 16px; text-align: center;">
                <p style="margin-bottom: 15px;">📊 Хотите увидеть подробную статистику в виде графиков и диаграмм?</p>
                <a href="infographics.php" class="btn-primary">Перейти к инфографике →</a>
            </div>
            
            <h3 style="margin-top: 30px;"> Последние заказы</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr><th>№</th><th>Пользователь</th><th>Сумма</th><th>Статус</th><th>Дата</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_number']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</td>
                            <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $statuses[$order['status']]; ?></span></td>
                            <td><?php echo date('d.m.Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- ЗАКАЗЫ -->
        <div id="orders" class="tab-content <?php echo $active_tab == 'orders' ? 'active' : ''; ?>">
            <h2> Управление заказами</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr><th>№</th><th>Пользователь</th><th>Сумма</th><th>Статус</th><th>Дата</th><th>Действия</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_number']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</td>
                            <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $statuses[$order['status']]; ?></span></td>
                            <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Ожидает</option>
                                        <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                                        <option value="active" <?php echo $order['status'] == 'active' ? 'selected' : ''; ?>>Активен</option>
                                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Завершен</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                    </select>
                                    <input type="hidden" name="update_order_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- ТОВАРЫ -->
        <div id="products" class="tab-content <?php echo $active_tab == 'products' ? 'active' : ''; ?>">
            <h2> Управление товарами</h2>
            
            <div style="background: #f3f4f6; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                <h3> Добавить товар</h3>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-grid">
                        <input type="text" name="name" placeholder="Название товара" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <input type="number" step="0.01" name="price" placeholder="Цена за день" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <input type="number" name="stock" placeholder="Количество" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <select name="category_id" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea name="description" placeholder="Описание" rows="2" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                        <textarea name="specs" placeholder="Характеристики" rows="2" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                        <div>
                            <input type="file" name="product_image" accept="image/*">
                            <div class="image-upload-info">Поддерживаются: JPG, PNG, GIF, WEBP</div>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn-primary">➕ Добавить товар</button>
                </form>
            </div>
            
            <h3> Список товаров</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>В наличии</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td class="product-image-cell">
                                <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                    <img src="<?php echo $product['image']; ?>" class="product-image-preview" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <span style="font-size: 30px;"></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['category_name'] ?? '-'; ?></td>
                            <td><?php echo number_format($product['price'], 0, '.', ' '); ?> ₽</td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <button onclick="editProduct(<?php echo $product['id']; ?>)" class="btn-secondary btn-sm"> Ред.</button>
                                <form method="post" style="display: inline;" onsubmit="return confirm('Удалить товар?')">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn-danger btn-sm"></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- КАТЕГОРИИ -->
        <div id="categories" class="tab-content <?php echo $active_tab == 'categories' ? 'active' : ''; ?>">
            <h2> Управление категориями</h2>
            
            <div style="background: #f3f4f6; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                <h3> Добавить категорию</h3>
                <form method="post">
                    <div class="form-grid">
                        <input type="text" name="name" placeholder="Название категории" required style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <input type="text" name="icon" placeholder="Иконка (эмодзи) например: 🎿" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <textarea name="description" placeholder="Описание" rows="2" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                    </div>
                    <button type="submit" name="add_category" class="btn-primary"> Добавить категорию</button>
                </form>
            </div>
            
            <div class="stats-grid">
                <?php foreach ($categories as $cat): ?>
                <div style="background: #f3f4f6; padding: 20px; border-radius: 16px; text-align: center; position: relative;">
                    <form method="post" onsubmit="return confirm('Удалить категорию?')" style="position: absolute; top: 10px; right: 10px;">
                        <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                        <button type="submit" name="delete_category" class="btn-danger btn-sm">✖</button>
                    </form>
                    <div style="font-size: 48px;"><?php echo $cat['icon'] ?? ''; ?></div>
                    <h4><?php echo htmlspecialchars($cat['name']); ?></h4>
                    <p style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($cat['description'] ?? ''); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- ПОЛЬЗОВАТЕЛИ -->
        <div id="users" class="tab-content <?php echo $active_tab == 'users' ? 'active' : ''; ?>">
            <h2> Управление пользователями</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Логин</th><th>Email</th><th>Имя</th><th>Телефон</th><th>Роль</th><th>Действия</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo $user['role'] == 'admin' ? 'Админ' : 'Пользователь'; ?></span></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="role" onchange="this.form.submit()" style="padding: 4px; border-radius: 6px;">
                                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Пользователь</option>
                                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Администратор</option>
                                        </select>
                                        <input type="hidden" name="change_user_role" value="1">
                                    </form>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Удалить пользователя?')">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn-danger btn-sm">🗑️</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #666;">Вы</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- ОТЗЫВЫ -->
        <div id="reviews" class="tab-content <?php echo $active_tab == 'reviews' ? 'active' : ''; ?>">
            <h2>⭐ Управление отзывами</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <strong><?php echo htmlspecialchars($review['username']); ?></strong> на 
                            <strong><?php echo htmlspecialchars($review['product_name']); ?></strong>
                        </div>
                        <div class="rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                    </div>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                    <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                        <small style="color: #666;"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></small>
                        <form method="post" onsubmit="return confirm('Удалить отзыв?')">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit" name="delete_review" class="btn-danger btn-sm">Удалить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($reviews)): ?>
                <p style="text-align: center; padding: 40px;"> Отзывов пока нет</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Модальное окно для редактирования товара -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <h3>✏️ Редактировать товар</h3>
            <form method="post" id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="edit_id">
                <div class="form-group">
                    <label>Название:</label>
                    <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div class="form-group">
                    <label>Описание:</label>
                    <textarea name="description" id="edit_description" rows="3" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                <div class="form-group">
                    <label>Цена (₽/день):</label>
                    <input type="number" step="0.01" name="price" id="edit_price" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div class="form-group">
                    <label>Количество в наличии:</label>
                    <input type="number" name="stock" id="edit_stock" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div class="form-group">
                    <label>Категория:</label>
                    <select name="category_id" id="edit_category_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Характеристики:</label>
                    <textarea name="specs" id="edit_specs" rows="3" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                
                <div id="current_image_container" class="current-image" style="display: none;">
                    <label>Текущее фото:</label>
                    <img id="current_image_preview" src="" alt="Текущее фото">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="delete_image" value="1"> Удалить фото
                    </label>
                </div>
                
                <div class="form-group">
                    <label>Новое фото (оставьте пустым, чтобы не менять):</label>
                    <input type="file" name="edit_product_image" accept="image/*">
                    <div class="image-upload-info">Поддерживаются: JPG, PNG, GIF, WEBP</div>
                </div>
                
                <button type="submit" name="edit_product" class="btn-primary"> Сохранить</button>
                <button type="button" onclick="closeModal()" class="btn-secondary">Отмена</button>
            </form>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            var tabs = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            document.getElementById(tabName).classList.add('active');
            
            var btns = document.getElementsByClassName('admin-tab');
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove('active');
            }
            event.target.classList.add('active');
            
            history.pushState(null, '', '?tab=' + tabName);
        }
        
        function editProduct(id) {
            fetch('get_product.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_name').value = data.name;
                    document.getElementById('edit_description').value = data.description || '';
                    document.getElementById('edit_price').value = data.price;
                    document.getElementById('edit_stock').value = data.stock;
                    document.getElementById('edit_category_id').value = data.category_id;
                    document.getElementById('edit_specs').value = data.specs || '';
                    
                    if (data.image && data.image !== '') {
                        document.getElementById('current_image_preview').src = data.image + '?t=' + new Date().getTime();
                        document.getElementById('current_image_container').style.display = 'flex';
                    } else {
                        document.getElementById('current_image_container').style.display = 'none';
                    }
                    
                    document.getElementById('editProductModal').style.display = 'flex';
                });
        }
        
        function closeModal() {
            document.getElementById('editProductModal').style.display = 'none';
            document.getElementById('editProductForm').reset();
        }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('editProductModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>