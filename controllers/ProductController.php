<?php
class ProductController {
    private $db;
    private $product;

    public function __construct($db) {
        $this->db = $db;
        $this->product = new Product($db);
    }

    // Получение всех продуктов
    public function getAllProducts() {
        $stmt = $this->product->read();
        $this->respondWithProducts($stmt);
    }

    // Получение продуктов по категории
    public function getProductsByCategory($category) {
        $stmt = $this->product->readByCategory($category);
        $this->respondWithProducts($stmt);
    }

    // Получение всех категорий
    public function getCategories() {
        $stmt = $this->product->getCategories();
        $categories = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($categories, $row['product_type']);
        }

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $categories
        ));
    }

    public function getProductById($productId) {
        $query = "SELECT * FROM products WHERE id = :product_id";
        $stmt = $this->db->prepare($query);
        

        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
    }
    
    
    // Общий метод для ответа с продуктами
    private function respondWithProducts($stmt) {
        $num = $stmt->rowCount();

        if ($num > 0) {
            $products_arr = array();
            $products_arr["data"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $product_item = array(
                    "id" => $id,
                    "product_name" => $product_name,
                    "product_type" => $product_type,
                    "product_img" => $product_img,
                    "product_info" => $product_info,
                    "product_price" => $product_price,
                    "created_at" => $created_at
                );

                array_push($products_arr["data"], $product_item);
            }

            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "data" => $products_arr["data"]
            ));
        } else {
            http_response_code(404);
            echo json_encode(array(
                "success" => false,
                "message" => "No products found."
            ));
        }
    }
}
?>