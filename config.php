<?php
session_start();

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $host = 'localhost';
        $dbname = 'sport_rental';
        $username = 'root';
        $password = '';
        
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}

// Функции для работы с пользователями
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUser($user_id = null) {
    if ($user_id === null) {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    if (!$user_id) return null;
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function getCartCount() {
    if (!isLoggedIn()) return 0;
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Функции для товаров
function getCategories() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

function getProduct($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.icon as category_icon 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProducts($category_id = null, $search = null, $sort = 'name_asc') {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT p.*, c.name as category_name, c.icon as category_icon 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    $params = [];
    
    if ($category_id && $category_id > 0) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    switch ($sort) {
        case 'name_asc':
            $sql .= " ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $sql .= " ORDER BY p.name DESC";
            break;
        case 'price_asc':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY p.price DESC";
            break;
        default:
            $sql .= " ORDER BY p.name ASC";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductReviews($product_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT r.*, u.username 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.product_id = ? 
                          ORDER BY r.created_at DESC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

// Функции для корзины
function getCartItems() {
    if (!isLoggedIn()) return [];
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT c.*, p.name, p.price, p.image 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetchAll();
}

function addToCart($product_id, $quantity = 1, $rental_days = 1) {
    if (!isLoggedIn()) return false;
    
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $stmt = $db->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        return $stmt->execute([$quantity, $existing['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity, rental_days) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$_SESSION['user_id'], $product_id, $quantity, $rental_days]);
    }
}

function removeFromCart($cart_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    return $stmt->execute([$cart_id, $_SESSION['user_id']]);
}

function clearCart() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$_SESSION['user_id']]);
}
?>