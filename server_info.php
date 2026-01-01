<?php 

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];

function generateFullAbsolutePathForEmailUserImage($urlMainPath, $baseUrl, $username, $viewToken) {
    require_once "common.php";
    $urlAfterDomain = ($urlMainPath != "") ? "/" . $urlMainPath : "";
    $fullPath = $baseUrl . $urlAfterDomain . "/Main/View%20Image/?u=" . $username . "&token=" . $viewToken;
    return $fullPath;
}

?>