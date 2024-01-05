<?php
require(__DIR__ . "/../verifySubject.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/../database.php";
    $sql = sprintf("CALL getUserFromToken('%s')", $mysqli->real_escape_string($_POST["auth_token"]));
    $result = $mysqli->query($sql);
    $bruker = $result->fetch_assoc();
    $result->free_result();
    $mysqli->next_result();

    if ($bruker) {
        if (doesCurrentUserHaveSubject($_POST['room'], $bruker['user_id'], null)) {
            $sql = "CALL getConversationInChatRoomAnonymous({$_POST['room']})";
            $result = $mysqli->query($sql);
            $conversations = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($conversations);
        }
    }
}
?>