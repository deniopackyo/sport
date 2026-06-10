<?php
require_once 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Введите логин и пароль';
    } else {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - СпортПрокат</title>
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
                <a href="login.php" class="login-btn">Вход</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            </div>
        </nav>

        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 24px;"> Вход в аккаунт</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Имя пользователя или Email</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">Войти</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
        </div>
    </div>
</body>
</html>