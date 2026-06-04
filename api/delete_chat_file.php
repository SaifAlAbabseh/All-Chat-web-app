<?php
header('Content-Type: application/json');
require_once '../env.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$secret = isset($_POST['secret']) ? $_POST['secret'] : '';
$filename = isset($_POST['filename']) ? $_POST['filename'] : '';

$expectedSecret = getVarFromEnv('API_SECRET');

if (empty($secret) || empty($expectedSecret) || $secret !== $expectedSecret) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (empty($filename)) {
    http_response_code(400);
    echo json_encode(['error' => 'Filename required']);
    exit;
}

// Security: Prevent path traversal by only taking the basename
$basename = basename($filename);
$targetPath = __DIR__ . '/../uploads/chat_files/' . $basename;

if (file_exists($targetPath)) {
    if (unlink($targetPath)) {
        echo json_encode(['success' => true, 'message' => 'File deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete file']);
    }
} else {
    // File already gone or doesn't exist, which is fine since the goal is deletion
    echo json_encode(['success' => true, 'message' => 'File does not exist or already deleted']);
}
?>
