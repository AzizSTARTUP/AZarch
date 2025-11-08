<?php
header("Content-Type: application/json; charset=UTF-8");
require 'db_config.php';

$response = array('error' => true, 'message' => 'ID not provided or invalid request method.');

// Le script doit accepter une requête POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    // --- Logique pour supprimer l'image physique ---
    $stmt_select = $conn->prepare("SELECT `image_url` FROM `plans` WHERE `id` = ?");
    if ($stmt_select) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $base_url_path = '/App/';
            // Remplacer l'URL par un chemin de fichier local
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $base_url_path . basename($row['image_url']);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $stmt_select->close();
    }
    
    // --- Supprimer de la base de données ---
    $stmt_delete = $conn->prepare("DELETE FROM `plans` WHERE `id` = ?");
    if ($stmt_delete) {
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            if ($stmt_delete->affected_rows > 0) {
                 $response = array('error' => false, 'message' => 'Plan deleted successfully.');
            } else {
                 $response = array('error' => true, 'message' => 'Error: Plan with ID ' . $id . ' not found.');
            }
        } else {
            $response = array('error' => true, 'message' => 'Database Execute Error: ' . $stmt_delete->error);
        }
        $stmt_delete->close();
    } else {
        $response = array('error' => true, 'message' => 'Database Prepare Error: ' . $conn->error);
    }
}

echo json_encode($response);
$conn->close();
?>