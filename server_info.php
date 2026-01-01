<?php 

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];

function generateHostPath($urlMainPath, $baseUrl) {
    $urlAfterDomain = ($urlMainPath != "") ? "/" . $urlMainPath : "";
    return $baseUrl . $urlAfterDomain;
}

?>