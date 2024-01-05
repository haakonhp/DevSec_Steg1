<?php
require(__DIR__ . "/../verifySubject.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/../database.php";
    $sql = sprintf("CALL getUserFromToken('%s')", $mysqli->real_escape_string($_POST["auth_token"]));
    $result = $mysqli->query($sql);
    $bruker = $result->fetch_assoc();
    $result->free_result();
    $mysqli->next_result();

    if (doesCurrentUserHaveSubject($_POST['room_id'], $bruker['user_id'], null)) {
        $text = htmlspecialchars($_POST['text']);
        if (empty($_POST['reply_id'])) {
            $sql = "CALL createComment('{$text}', {$bruker['user_id']}, {$_POST['room_id']})";
        } else {
            $sql = "CALL reply({$text}, {$bruker['user_id']}, {$_POST['reply_id']})";
        }
        $result = $mysqli->query($sql);
        echo "Succsessfully posted";
    }
}
?>