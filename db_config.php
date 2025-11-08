<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'az_arch');

// Headers CORS pour l'API
header('Access-Control-Allow-Origin: *');  // En production, remplacer * par l'URL de votre app
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Connexion avec gestion d'erreurs JSON
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception($conn->connect_error);
        }
        
        // Force UTF-8
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode([
            'error' => true,
            'message' => 'Database connection error',
            'details' => $e->getMessage()
        ]);
        exit(1);
    }
}

// Obtenir la connexion
$conn = getConnection();

// Fonction utilitaire pour les réponses JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit();
}
?>