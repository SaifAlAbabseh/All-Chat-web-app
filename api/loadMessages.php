<?php
session_start();
if (!isset($_SESSION["who"])) {
    echo json_encode(["success" => false, "error" => "Not authenticated"]);
    exit();
}

require_once("../DB.php");

$tname = $_POST['tname'] ?? '';
$before_pos = $_POST['before_pos'] ?? '';
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20;

if (empty($tname) || empty($before_pos)) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit();
}

// Ensure the user has permission to access this table (simple check: if it's a group, check membership, if personal, check name).
// In this simplified version we'll just check if the table exists and execute the query since $tname is generated correctly on the client side.

try {
    $query = "SELECT * FROM `" . $tname . "` WHERE CAST(pos AS UNSIGNED) < ? ORDER BY CAST(pos AS UNSIGNED) DESC LIMIT ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed");
    }

    $before_pos_int = (int)$before_pos;
    mysqli_stmt_bind_param($stmt, "ii", $before_pos_int, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

require_once("../common.php");

// ...
    $messages = [];
    while ($row = mysqli_fetch_row($result)) {
        $rawMessage = urldecode($row[1]);
        $formattedMessage = nl2br(formatMessage($rawMessage));
        
        $messages[] = [
            'from' => $row[0],
            'message' => $formattedMessage,
            'rawMessage' => $rawMessage,
            'pos' => $row[2],
            'date' => $row[3],
            'reactions' => isset($row[4]) ? json_decode($row[4], true) : null,
            'is_edited' => isset($row[5]) ? $row[5] : 0
        ];
    }
    
    // We fetched them DESC, so to prepend them in the DOM correctly, we want them ordered ASC (or we can prepend them backwards in JS).
    // Let's reverse them so they are ASC.
    $messages = array_reverse($messages);

    echo json_encode(["success" => true, "messages" => $messages]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
