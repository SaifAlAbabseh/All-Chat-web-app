<?php 

function getVarFromEnv($var) {
    $filePath = __DIR__ . "/.env";

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        if (trim($line)[0] === '#' || !strpos($line, '=')) continue;

        // Split key=value
        [$key, $value] = explode('=', $line, 2);

        // Remove quotes if present
        $value = trim($value);
        $value = trim($value, "\"'");

        if($key == $var) 
            return $value;
    }
    return;
}

?>