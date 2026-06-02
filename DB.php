<?php
    require_once 'env.php';

    $db_hostName = getVarFromEnv("DB_HOST_NAME");
    $db_username = getVarFromEnv("DB_USERNAME");
    $db_password = getVarFromEnv("DB_PASSWORD");
    $db_name = getVarFromEnv("DB_NAME");

    $conn = mysqli_connect($db_hostName, $db_username, $db_password, $db_name);
?>