<?php 

header('Content-Type: application/json');

// Load .env
require_once 'env.php';

// Only expose PUBLIC_ variables
$response = [];
$response[$_REQUEST["var"]] = getVarFromEnv($_REQUEST["var"]);

// Return JSON
echo json_encode($response);
?>