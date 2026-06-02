<?php
session_start();

if (!isset($_SESSION["who"])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['chat_file'])) {
    $file = $_FILES['chat_file'];
    
    // File details
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    // Validate upload error
    if ($fileError !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["error" => "File upload error code: " . $fileError]);
        exit();
    }
    
    // Validate size (e.g. 20MB limit)
    if ($fileSize > 20 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(["error" => "File is too large (max 20MB)."]);
        exit();
    }
    
    // Validate extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'doc', 'docx', 'zip', 'rar', 'mp3', 'mp4'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(["error" => "File type not allowed."]);
        exit();
    }
    
    // Generate unique name
    $newFileName = uniqid('', true) . '.' . $fileExt;
    $uploadDir = 'uploads/chat_files/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $destination = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTmpName, $destination)) {
        // Return JSON with path and original name
        echo json_encode([
            "success" => true,
            "path" => $newFileName,
            "original_name" => $fileName
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to move uploaded file."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "No file uploaded."]);
}
?>
