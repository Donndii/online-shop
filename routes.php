<?php

require_once "config/database.php";
require_once "models/User.php";
require_once "models/Product.php";
require_once "controllers/AuthController.php";
require_once "controllers/ProductController.php";

$database = new Database();
$db = $database->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = trim($path, '/');
$path_parts = explode('/', $path);

// Удаляем пустые элементы из массива пути
$path_parts = array_values(array_filter($path_parts));

// Debug output
error_log("Request path: " . print_r($path_parts, true));
error_log("Request method: " . $request_method);

// Базовый путь API (например, 'api' если ваш API находится по пути /api/)
$base_path = isset($path_parts[0]) ? $path_parts[0] : '';

// Определяем маршрут
$route = isset($path_parts[1]) ? $path_parts[1] : '';

switch ($base_path) {
    case 'api': // Если у вас API находится по пути /api/
        handleApiRoutes($db, $request_method, $path_parts);
        break;

    default: // Если API находится в корне
        handleApiRoutes($db, $request_method, $path_parts);
        break;
}

function handleApiRoutes($db, $request_method, $path_parts) {
    // Check if you have the correct path parts here
    $route = isset($path_parts[1]) ? $path_parts[1] : '';

    switch ($route) {
        case 'auth':  // Match /api/auth
            handleAuthRoutes($db, $request_method, $path_parts);
            break;

        case 'products':
            handleProductRoutes($db, $request_method, $path_parts);
            break;

        default:
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Endpoint not found"
            ]);
            break;
    }
}


function handleAuthRoutes($db, $request_method, $path_parts) {
    $action = isset($path_parts[2]) ? $path_parts[2] : '';
    $authController = new AuthController($db);

    switch ($action) {
        case 'register':
            if ($request_method == 'POST') {
                $authController->register();
            } else {
                sendMethodNotAllowed();
            }
            break;

        case 'login':
            if ($request_method == 'POST') {
                $authController->login();
            } else {
                sendMethodNotAllowed();
            }
            break;

        default:
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Auth endpoint not found"
            ]);
            break;
    }
}

function handleProductRoutes($db, $request_method, $path_parts) {
    $productController = new ProductController($db);
    $action = isset($path_parts[2]) ? $path_parts[2] : '';

    if ($request_method == 'GET') {
        switch ($action) {
            case 'categories':
                // GET /products/categories
                $productController->getCategories();
                break;

            case '':
                // GET /products
                $productController->getAllProducts();
                break;

            case 'product':
                if (isset($path_parts[2]) && is_numeric($path_parts[2])) {
                        $productController->getProductById($path_parts[2]);
                } else {
                        echo json_encode(["success" => false, "message" => "Product ID is required."]);
                    }
                break;


            default:
                // GET /products/product_type
                if (is_numeric($action)) {
                    // GET /products/{id} - Get product by ID
                    $productController->getProductById($action);
                } else {
                    // GET /products/product_type - Get products by category
                    $productController->getProductsByCategory($action);
                }
                break;
        }
    } else {
        sendMethodNotAllowed();
    }
}

function sendMethodNotAllowed() {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method Not Allowed"
    ]);
}