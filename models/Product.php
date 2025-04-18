<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $product_name;
    public $product_type;
    public $product_img;
    public $product_info;
    public $product_price;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получение всех продуктов
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получение продуктов по категории
    public function readByCategory($category) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category);
        $stmt->execute();
        return $stmt;
    }

    // Получение продуктов по диапазону цен
    public function readByPriceRange($minPrice, $maxPrice) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_price BETWEEN ? AND ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $minPrice);
        $stmt->bindParam(2, $maxPrice);
        $stmt->execute();
        return $stmt;
    }

    // Получение всех категорий
    public function getCategories() {
        $query = "SELECT DISTINCT product_type FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readById($id) {
        $query = "SELECT * FROM products WHERE id = :id LIMIT 1";  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt;
    }

    public function create($name, $type, $img, $info, $price) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET product_name=:name, product_type=:type, product_img=:img, product_info=:info, product_price=:price, created_at=:created_at";
        
        $stmt = $this->conn->prepare($query);
        
        $this->product_name = htmlspecialchars(strip_tags($name));
        $this->product_type = htmlspecialchars(strip_tags($type));
        $this->product_img = htmlspecialchars(strip_tags($img));
        $this->product_info = htmlspecialchars(strip_tags($info));
        $this->product_price = htmlspecialchars(strip_tags($price));
        $this->created_at = date('Y-m-d H:i:s');
        
        $stmt->bindParam(":name", $this->product_name);
        $stmt->bindParam(":type", $this->product_type);
        $stmt->bindParam(":img", $this->product_img);
        $stmt->bindParam(":info", $this->product_info);
        $stmt->bindParam(":price", $this->product_price);
        $stmt->bindParam(":created_at", $this->created_at);
        
        return $stmt->execute();
    }
}
?>