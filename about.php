<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - СпортПрокат | Челябинск</title>
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

        .about-content {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .about-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .about-text {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .feature {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: transform 0.3s;
        }

        .feature:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .feature-desc {
            font-size: 14px;
            color: #666;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 40px 0;
            text-align: center;
        }

        .stat-item {
            background: linear-gradient(135deg, #667eea15, #764ba215);
            padding: 20px;
            border-radius: 12px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .reviews {
            margin: 40px 0;
        }

        .reviews-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .review-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            font-style: italic;
        }

        .review-text {
            font-size: 14px;
            line-height: 1.5;
            color: #555;
            margin-bottom: 10px;
        }

        .review-author {
            font-weight: bold;
            color: #667eea;
        }

        .contact-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-top: 30px;
        }

        .contact-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #555;
        }

        .map-container {
            margin: 30px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .map-title {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }

        .shops {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .shop-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .shop-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .shop-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .shop-hours {
            font-size: 12px;
            color: #888;
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
            .about-content {
                padding: 20px;
            }
        }
    </style>
    <!-- Подключаем Яндекс.Карты API -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=ваш_ключ_api&lang=ru_RU" type="text/javascript"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <h1>⚡ СпортПрокат</h1>
                <p>Качественный инвентарь для активного отдыха в Челябинске</p>
            </div>
            <div class="nav-links">
                <a href="index.php">Главная</a>
                <a href="about.php">О нас</a>
                <a href="categories.php">Категории</a>
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <a href="profile.php">Личный кабинет</a>
                    <a href="orders.php">Мои заказы</a>
                    <a href="logout.php">Выйти</a>
                    <a href="cart.php" class="cart-icon">🛒 Корзина (<?php echo function_exists('getCartCount') ? getCartCount() : '0'; ?>)</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Вход</a>
                    <a href="register.php" class="btn-primary">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="about-content">
            <h1 class="about-title">О нас</h1>
            
            <div class="about-text">
                <p> <strong>СпортПрокат</strong> — это современный сервис аренды спортивного инвентаря в Челябинске, созданный для тех, кто ценит активный образ жизни и качественное оборудование. Мы делаем спорт доступным для каждого жителя и гостя нашего города.</p>
            </div>
            
            <div class="about-text">
                <p> Мы работаем с 2020 года и за это время помогли тысячам челябинцев насладиться любимыми видами спорта без необходимости покупать дорогостоящее оборудование. Наша миссия — вдохновлять людей на активный отдых и здоровый образ жизни, предоставляя надёжный инвентарь по доступным ценам.</p>
            </div>

            <div class="about-text">
                <p> Почему выбирают нас? Мы не просто сдаём инвентарь в аренду — мы заботимся о вашем комфорте. Каждое оборудование проходит тщательную проверку и дезинфекцию перед выдачей. Наши консультанты всегда готовы помочь с выбором подходящего инвентаря и дать полезные советы.</p>
            </div>

            <!-- Статистика компании -->
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">5+</div>
                    <div class="stat-label">Лет на рынке</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3000+</div>
                    <div class="stat-label">Довольных клиентов</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">80+</div>
                    <div class="stat-label">Единиц инвентаря</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.8★</div>
                    <div class="stat-label">Средний рейтинг</div>
                </div>
            </div>

            <!-- Преимущества -->
            <div class="features">
                <div class="feature">
                    <div class="feature-icon"></div>
                    <div class="feature-title">Широкий выбор</div>
                    <div class="feature-desc">Более 80 единиц качественного инвентаря: лыжи, сноуборды, велосипеды, ролики, коньки и многое другое</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"></div>
                    <div class="feature-title">Доступные цены</div>
                    <div class="feature-desc">Гибкая система скидок до 20% для постоянных клиентов и специальные предложения на длительную аренду</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"></div>
                    <div class="feature-title">Доставка по Челябинску</div>
                    <div class="feature-desc">Бесплатная доставка при заказе от 3000 ₽. Быстрая курьерская доставка в день заказа</div>
                </div>
                <div class="feature">
                    <div class="feature-icon"></div>
                    <div class="feature-title">Качество и безопасность</div>
                    <div class="feature-desc">Весь инвентарь проходит регулярное техническое обслуживание и дезинфекцию</div>
                </div>
            </div>

            <!-- Отзывы клиентов -->
            <div class="reviews">
                <h2 class="reviews-title"> Что говорят наши клиенты</h2>
                <div class="reviews-grid">
                    <div class="review-card">
                        <div class="review-text">"Отличный сервис! Арендовал сноуборд на выходные на ГЛК «Солнечная долина». Всё в идеале, инвентарь подготовленный. Доставка вовремя, респект ребятам!"</div>
                        <div class="review-author">— Максим, Курчатовский район</div>
                    </div>
                    <div class="review-card">
                        <div class="review-text">"Брали велосипеды для покатушек по парку Гагарина. Всё чётко, дети довольны. Отдельное спасибо за шлемы. Рекомендую!"</div>
                        <div class="review-author">— Елена, Калининский район</div>
                    </div>
                    <div class="review-card">
                        <div class="review-text">"Удобно, что пункт выдачи рядом с остановкой. Забрал лыжи, покатался на Шершнях — кайф. Буду брать ещё коньки зимой."</div>
                        <div class="review-author">— Дмитрий, Челябинск</div>
                    </div>
                </div>
            </div>

            <!-- Карта магазинов Яндекс -->
            <div class="map-title">📍 Наши магазины на карте Челябинска</div>
            <div class="map-container">
                <div id="map"></div>
            </div>

            <!-- Список магазинов в Челябинске -->
            <div class="shops">
                <div class="shop-card">
                    <div class="shop-name"> Главный офис | Центральный район</div>
                    <div class="shop-address">📍 г. Челябинск, ул. Кирова, д. 112</div>
                    <div class="shop-hours"> Пн-Пт: 9:00 - 21:00, Сб-Вс: 10:00 - 19:00</div>
                </div>
                <div class="shop-card">
                    <div class="shop-name"> Филиал "Северо-Запад"</div>
                    <div class="shop-address">📍 г. Челябинск, Комсомольский проспект, д. 47</div>
                    <div class="shop-hours"> Ежедневно: 10:00 - 20:00</div>
                </div>
                <div class="shop-card">
                    <div class="shop-name"> Филиал "Парковый" (Парк Гагарина)</div>
                    <div class="shop-address">📍 г. Челябинск, ул. Лесопарковая, 6</div>
                    <div class="shop-hours"> Пн-Пт: 11:00 - 21:00, Сб-Вс: 10:00 - 18:00</div>
                </div>
                <div class="shop-card">
                    <div class="shop-name"> Пункт выдачи | ГЛК "Солнечная долина"</div>
                    <div class="shop-address">📍 г. Челябинск, пос. Красное Поле, 1</div>
                    <div class="shop-hours"> Сезонный режим: декабрь-март, 10:00 - 19:00</div>
                </div>
            </div>

            <div class="contact-info">
                <h2 class="contact-title"> Наши контакты</h2>
                <div class="contact-item">
                    <span></span>
                    <span>+7 (351) 123-45-67</span>
                </div>
                <div class="contact-item">
                    <span></span>
                    <span>info@sportrental174.ru</span>
                </div>
                <div class="contact-item">
                    <span></span>
                    <span>support@sportrental174.ru (для вопросов по заказам)</span>
                </div>
                <div class="contact-item">
                    <span></span>
                    <span>Мы в Telegram: @sportrentalchel_bot</span>
                </div>
                <div class="contact-item">
                    <span></span>
                    <span>Круглосуточная поддержка через чат на сайте</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>© 2025 СпортПрокат — Аренда спортивного инвентаря в Челябинске с доставкой по всему городу</p>
            <p style="font-size: 12px; margin-top: 10px;">Следите за нами: VK | Telegram | Instagram</p>
        </div>
    </div>

    <script>
        // Ждём загрузки Яндекс.Карт
        ymaps.ready(init);
        
        function init() {
            // Создаём карту с центром в Челябинске
            var map = new ymaps.Map("map", {
                center: [55.164441, 61.436843],
                zoom: 13,
                controls: ["zoomControl", "fullscreenControl"]
            });
            
            // Магазины
            var shops = [
                { coords: [55.160242, 61.402256], name: "Главный офис", address: "ул. Кирова, 112", hours: "Пн-Пт 9:00-21:00, Сб-Вс 10:00-19:00" },
                { coords: [55.185625, 61.414890], name: "Филиал 'Северо-Запад'", address: "Комсомольский проспект, 47", hours: "Ежедневно 10:00-20:00" },
                { coords: [55.113124, 61.433048], name: "Парк Гагарина", address: "ул. Лесопарковая, 6", hours: "Пн-Пт 11:00-21:00, Сб-Вс 10:00-18:00" },
                { coords: [55.024850, 61.466300], name: "ГЛК 'Солнечная долина'", address: "пос. Красное Поле, 1", hours: "Сезонный: декабрь-март 10:00-19:00" }
            ];
            
            // Добавляем метки
            shops.forEach(function(shop) {
                var placemark = new ymaps.Placemark(shop.coords, {
                    balloonContentHeader: '<b>' + shop.name + '</b>',
                    balloonContentBody: '<span> ' + shop.address + '</span><br/><span> ' + shop.hours + '</span>',
                    balloonContentFooter: ' +7 (351) 123-45-67',
                    hintContent: shop.name
                }, {
                    preset: 'islands#blueCircleDotIcon',
                    balloonLayout: 'default#imageWithContent'
                });
                
                map.geoObjects.add(placemark);
            });
            
            // Масштабируем карту, чтобы показать все метки
            if (shops.length > 0) {
                var bounds = map.geoObjects.getBounds();
                if (bounds) {
                    map.setBounds(bounds, {
                        checkZoomRange: true,
                        zoomMargin: 50
                    });
                }
            }
        }
    </script>
</body>
</html>