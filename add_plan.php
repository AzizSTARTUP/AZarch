<?php
header("Content-Type: application/json; charset=UTF-8");
require 'db_config.php';

$upload_path = 'uploads/';
if (!is_dir($upload_path)) {
    mkdir($upload_path, 0777, true);
}

$base_image_url = 'http://' . $_SERVER['SERVER_NAME'] . '/App/' . $upload_path;

$response = array('error' => true, 'message' => 'Invalid Request: Missing parameters.');

if (isset($_FILES['image']) && isset($_POST['description']) && isset($_POST['price'])) {
    
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $response = array('error' => true, 'message' => 'Image Upload Error: Code ' . $_FILES['image']['error']);
        echo json_encode($response);
        exit();
    }

    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    
    $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
    $target_file = $upload_path . $file_name;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_url = $base_image_url . $file_name;
        
        $stmt = $conn->prepare("INSERT INTO `plans` (`image_url`, `description`, `price`) VALUES (?, ?, ?)");
        
        if ($stmt === false) {
             $response = array('error' => true, 'message' => 'Database Prepare Error: ' . $conn->error);
        } else {
            $stmt->bind_param("ssd", $image_url, $description, $price);
            
            if ($stmt->execute()) {
                $response = array('error' => false, 'message' => 'Plan added successfully.');
            } else {
                $response = array('error' => true, 'message' => 'Database Execute Error: ' . $stmt->error);
            }
            $stmt->close();
        }
    } else {
        $response = array('error' => true, 'message' => 'Failed to move uploaded image.');
    }
}

echo json_encode($response);
$conn->close();
?>