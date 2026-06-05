<?php
session_start();
header('Content-Type: application/json');

if (!(isset($_SESSION) && isset($_SESSION["who"]) && isset($_REQUEST) && isset($_REQUEST["group_id"]))) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$group_id = $_REQUEST["group_id"];
$username = $_SESSION["who"];

require_once("DB.php");
$group_table_name = "g" . $group_id . "_users";

// First, check if the user is part of the group
$check_query = "SELECT username FROM $group_table_name WHERE BINARY username=?";
$stmt = mysqli_prepare($conn, $check_query);
if ($stmt === false) {
    echo json_encode(["error" => "Database error"]);
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$check_result = mysqli_stmt_get_result($stmt);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    echo json_encode(["error" => "Not a member"]);
    exit();
}

// Fetch all members
$query = "SELECT username FROM $group_table_name";
$stmt2 = mysqli_prepare($conn, $query);
if ($stmt2 === false) {
    echo json_encode(["error" => "Database error"]);
    exit();
}
mysqli_stmt_execute($stmt2);
$result = mysqli_stmt_get_result($stmt2);

$members = [];
if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        $members[] = $row[0];
    }
}

echo json_encode(["members" => $members]);
?>
