<?php
session_start();

if (!isset($_SESSION["who"])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if (!isset($_GET['f']) || empty($_GET['f'])) {
    http_response_code(400);
    echo "Missing file parameter";
    exit();
}

$filename = basename($_GET['f']); // Prevent directory traversal
$path = 'uploads/chat_files/' . $filename;

if (!file_exists($path)) {
    http_response_code(404);
    echo "File not found";
    exit();
}

$mime = mime_content_type($path);
if ($mime === false) {
    $mime = 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));

// Suggest original filename if passed (optional, for downloads)
if (isset($_GET['n']) && !empty($_GET['n'])) {
    $originalName = basename($_GET['n']);
    header('Content-Disposition: inline; filename="' . $originalName . '"');
} else {
    header('Content-Disposition: inline; filename="' . $filename . '"');
}

// Clean output buffer before reading the file
if (ob_get_level()) {
    ob_end_clean();
}

readfile($path);
?>
