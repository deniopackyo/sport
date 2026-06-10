<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    // Валидация
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Заполните обязательные поля';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        $db = Database::getInstance()->getConnection();
        
        // Проверка существования пользователя
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким именем или email уже существует';
        } else {
            // Создание пользователя
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                $success = 'Регистрация успешна! Теперь вы можете войти.';
                header('refresh:2;url=login.php');
            } else {
                $error = 'Ошибка регистрации';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - СпортПрокат</title>
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
                <a href="login.php">Вход</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            </div>
        </nav>

        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 24px;"> Регистрация</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label>Имя пользователя *</label>
                    <input type="text" name="username" required value="<?php echo $_POST['username'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Полное имя</label>
                    <input type="text" name="full_name" value="<?php echo $_POST['full_name'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Пароль *</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Подтверждение пароля *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">Зарегистрироваться</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Уже есть аккаунт? <a href="login.php">Войдите</a>
            </p>
        </div>
        
        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря</p>
        </div>
    </div>
</body>
</html>