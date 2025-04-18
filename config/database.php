<?php
class Database {
    private $host = "localhost";
    private $port = "3306";
    private $db_name = "shop";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // error log("attemting db connect");
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            
            // Проверяем существует ли база данных
            $stmt = $this->conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->db_name}'");
            
            if ($stmt->rowCount() == 0) {
                // error log("initializing db");
                $this->initializeDatabase();
            } else {
                $this->conn->exec("use " . $this->db_name);
            }
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }

    private function initializeDatabase() {
        try {
            // Создаем базу данных
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->conn->exec("use " . $this->db_name);
            
            // Создаем таблицу пользователей
            $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Создаем таблицу продуктов
            $this->conn->exec("CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_name VARCHAR(255) NOT NULL,
                product_type VARCHAR(100) NOT NULL,
                product_img VARCHAR(255),
                product_info VARCHAR(255) NOT NULL,
                product_price DECIMAL(10,2) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Добавляем тестового пользователя
            $this->addInitialUser();
            
            // Добавляем тестовые продукты
            $this->addInitialProducts();
            
        } catch(PDOException $exception) {
            echo "Initialization error: " . $exception->getMessage();
        }
    }

    private function addInitialUser() {
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);
        
        $username = "admin";
        $email = "admin@example.com";
        $password = password_hash("admin123", PASSWORD_BCRYPT);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        
        $stmt->execute();
    }

    private function addInitialProducts() {
        // Путь к файлу products.json в корне проекта
        $jsonFile = realpath(__DIR__ . '/products.json');
        error_log("Request path: " . print_r($jsonFile, true));
    

        // Проверяем существование файла
        if (!file_exists($jsonFile)) {
            error_log("Products JSON file not found: " . $jsonFile);
            return;
        }

        // Читаем содержимое файла
        $jsonContent = file_get_contents($jsonFile);
        if ($jsonContent === false) {
            error_log("Failed to read products JSON file");
            return;
        }

        // Декодируем JSON
        $products = json_decode($jsonContent, true);
        if ($products === null) {
            error_log("Failed to decode products JSON: " . json_last_error_msg());
            return;
        }

        // Проверяем, что мы получили массив продуктов
        if (!is_array($products)) {
            error_log("Invalid products data format");
            return;
        }

        $productModel = new Product($this->conn);

        foreach ($products as $product) {
            try {
                // Проверяем наличие всех обязательных полей
                $requiredFields = ['product_name', 'product_type', 'product_img', 'product_info', 'product_price'];
                foreach ($requiredFields as $field) {
                    if (!isset($product[$field])) {
                        error_log("Missing required field '$field' in product data");
                        continue 2; // Переходим к следующему продукту
                    }
                }

                // Преобразуем цену к float
                $product['product_price'] = (float)$product['product_price'];

                // Добавляем продукт в базу данных
                $productModel->create(
                    $product['product_name'],
                    $product['product_type'],
                    $product['product_img'],
                    $product['product_info'],
                    $product['product_price']
                );

            } catch (Exception $e) {
                error_log("Failed to add product: " . $e->getMessage());
                continue; // Продолжаем со следующим продуктом при ошибке
            }
        }

        error_log("Successfully added " . count($products) . " products to database");
    }
}
?>