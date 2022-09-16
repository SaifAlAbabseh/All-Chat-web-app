<?php
if (isset($_SESSION) && isset($_SESSION["getDB"])) {
    $conn = mysqli_connect("localhost", "root", "12345678", "allchat");
}
?>