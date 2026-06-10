-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 10 2026 г., 06:48
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sport_rental`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `rental_days` int(11) DEFAULT 1,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'Лыжи', 'Горные и беговые лыжи для профессионалов и любителей', '', '2026-05-28 05:29:05'),
(2, 'Сноуборды', 'Сноуборды для фристайла и карвинга', '', '2026-05-28 05:29:05'),
(3, 'Велосипеды', 'Горные, городские и электро-велосипеды', '', '2026-05-28 05:29:05'),
(4, 'Коньки', 'Фигурные и хоккейные коньки', '', '2026-05-28 05:29:05'),
(5, 'Теннис', 'Ракетки и аксессуары для тенниса', '', '2026-05-28 05:29:05'),
(6, 'Ролики', 'Роликовые коньки и защита', '', '2026-05-28 05:29:05'),
(7, 'Туризм', 'Палатки, рюкзаки и снаряжение', '', '2026-05-28 05:29:05');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','active','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `payment_status`, `delivery_address`, `delivery_date`, `return_date`, `note`, `created_at`) VALUES
(1, 3, 'ORD-20260529-9511', 1600.00, 'confirmed', 'pending', NULL, NULL, NULL, NULL, '2026-05-29 05:57:05'),
(2, 5, 'ORD-20260529-6165', 800.00, 'pending', 'pending', NULL, NULL, NULL, NULL, '2026-05-29 06:00:36'),
(3, 3, 'ORD-20260529-6711', 800.00, 'pending', 'pending', NULL, NULL, NULL, NULL, '2026-05-29 06:40:43'),
(4, 3, 'ORD-20260529-6796', 300.00, 'pending', 'pending', NULL, NULL, NULL, NULL, '2026-05-29 06:41:19'),
(5, 4, 'ORD-20260601-2859', 4000.00, 'pending', 'pending', NULL, NULL, NULL, NULL, '2026-06-01 06:31:22'),
(6, 3, 'ORD-20260601-2684', 3200.00, 'pending', 'pending', NULL, NULL, NULL, NULL, '2026-06-01 06:34:51');

-- --------------------------------------------------------

--
-- Структура таблицы `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_day_fixed` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `rental_days` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_per_day`, `rental_days`, `total_price`) VALUES
(1, 1, 10, 2, 800.00, 1, NULL),
(2, 2, 3, 1, 800.00, 1, NULL),
(3, 3, 3, 1, 800.00, 1, NULL),
(4, 4, 5, 1, 300.00, 1, NULL),
(5, 5, 10, 1, 800.00, 1, NULL),
(6, 5, 8, 1, 900.00, 1, NULL),
(7, 5, 1, 1, 1500.00, 1, NULL),
(8, 5, 3, 1, 800.00, 1, NULL),
(9, 6, 1, 1, 1500.00, 1, NULL),
(10, 6, 8, 1, 900.00, 1, NULL),
(11, 6, 3, 1, 800.00, 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_week` decimal(10,2) DEFAULT NULL,
  `price_month` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `specs` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `price_week`, `price_month`, `stock`, `description`, `image`, `specs`, `created_at`) VALUES
(1, 'Горные лыжи Atomic', 1, 1500.00, 9000.00, 30000.00, 8, 'Профессиональные горные лыжи для опытных спортсменов', 'uploads/products/6a1926a17a43e.jpeg', 'Длина: 170см, Радиус: 16м', '2026-05-28 05:29:06'),
(2, 'Сноуборд Burton', 2, 1200.00, 7200.00, 24000.00, 5, 'Отличный сноуборд для фристайла и карвинга', 'uploads/products/6a192699c4423.jpeg', 'Размеры: 155см, 158см, 162см', '2026-05-28 05:29:06'),
(3, 'Горный велосипед', 3, 800.00, 4800.00, 16000.00, 4, 'Надежный горный велосипед с амортизацией', 'uploads/products/6a19267b90a88.webp', '24 скорости, дисковые тормоза', '2026-05-28 05:29:06'),
(4, 'Фигурные коньки', 4, 500.00, 3000.00, 10000.00, 12, 'Комфортные фигурные коньки для катания', 'uploads/products/6a1926545e972.jpg', 'Размеры: 36-42', '2026-05-28 05:29:06'),
(5, 'Теннисная ракетка', 5, 300.00, 1800.00, 6000.00, 20, 'Профессиональная теннисная ракетка', 'uploads/products/6a19262a9286b.png', 'Вес: 280г, Баланс: нейтральный', '2026-05-28 05:29:06'),
(6, 'Роликовые коньки', 6, 600.00, 3600.00, 12000.00, 10, 'Ролики для активного катания', 'uploads/products/6a19260d920d0.webp', 'Размеры: 37-44, подшипники ABEC-7', '2026-05-28 05:29:06'),
(7, 'Туристическая палатка', 7, 1000.00, 6000.00, 20000.00, 6, 'Трехсезонная палатка на 4 человека', 'uploads/products/6a191fe39320f.webp', 'Вес: 3.5кг, Водостойкость: 3000мм', '2026-05-28 05:29:06'),
(8, 'Беговые лыжи Fischer', 1, 900.00, 5400.00, 18000.00, 7, 'Легкие беговые лыжи для классического хода', 'uploads/products/6a191df3610d5.jpg', 'Длина: 190см, 195см, 200см', '2026-05-28 05:29:06'),
(9, 'Электросамокат Ninebot', 3, 2000.00, 12000.00, 40000.00, 3, 'Мощный электросамокат с запасом хода 40км аорпоаопоапо', 'uploads/products/6a19259d0ca02.webp', 'Макс скорость: 25км/ч, Мощность: 350W', '2026-05-28 05:29:06'),
(10, 'Хоккейные коньки Bauer', 4, 800.00, 4800.00, 16000.00, 8, 'Профессиональные хоккейные коньки', 'uploads/products/6a19291fe05b3.webp', 'Термоформуемая посадка', '2026-05-28 05:29:06');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 5, 'Отличные лыжи! Катался весь сезон', '2026-05-28 05:29:06'),
(2, 3, 1, 4, 'Хороший велосипед, но тяжеловат', '2026-05-28 05:29:06');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `avatar`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sportrental.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', NULL, NULL, NULL, 'admin', '2026-05-28 05:29:06', '2026-05-28 05:29:06'),
(2, 'денима', 'joks22323@mail.ru', '$2y$10$nkMUuF6qtPyOQxXYjbBJnuJ.mct4N0.Scz7VaNUYgGcA/D7RXIOEK', 'Антонов Антон', '+79324395476', NULL, NULL, 'user', '2026-05-28 05:51:20', '2026-05-28 05:51:20'),
(3, 'dens', 'davidoganesan592@gmail.com', '$2y$10$J.bXp2O2EQjcvLjTiL4PQuFTpAy7hWzwCMj3rm7fOGGNYdSYIsfTG', 'fkgkfgkfkgk', '+79324395476', NULL, NULL, 'user', '2026-05-28 06:29:37', '2026-05-28 06:29:37'),
(4, 'admin1', 'admin@test.ru', '$2y$10$5AbNjyJNc7xQhk/9B/Dxd./jCp/eAmRhtvpwpcmrlCC8t9gXEN1fm', 'Антонов Антон', '+79324395470', NULL, NULL, 'admin', '2026-05-29 04:30:29', '2026-05-29 04:30:43'),
(5, 'Лев Исламов', 'levislamov0506@icloud.com', '$2y$10$Wa4oVMKKjtWV/1ftfXPOMuH.D4OpHLB5E2uDP7fa3N7mvjk5xQxJG', 'Львумба', '79000636892', 'Абразивная 48 Челябинск', NULL, 'user', '2026-05-29 05:59:37', '2026-05-29 06:01:02');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitems_order` (`order_id`),
  ADD KEY `fk_orderitems_product` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_product` (`product_id`),
  ADD KEY `fk_reviews_user` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
