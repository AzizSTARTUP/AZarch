<?php
header("Content-Type: application/json; charset=UTF-8");require 'db_config.php';

// Utiliser des backticks (`) pour les noms de colonnes est une bonne pratique
$sql = "SELECT `id`, `image_url`, `description`, `price` FROM `plans` ORDER BY `id` DESC";
$result = $conn->query($sql);

$plans = array();

// Vérifier si la requête a réussi et s'il y a des résultats
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Forcer la conversion des types pour un JSON propre et fiable
        $plan_item = array(
            "id" => (int)$row['id'],
            "image_url" => $row['image_url'],
            "description" => $row['description'],
            "price" => (float)$row['price']
        );
        $plans[] = $plan_item;
    }
}

echo json_encode($plans);

$conn->close();
?>