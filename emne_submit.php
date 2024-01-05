<?php

$mysqli = require __DIR__ . "/database.php";
require("verifySubject.php");

if (doesCurrentUserHaveSubject($_POST['roomRedirect'], $_SESSION["s_bruker_id"], $emne["subject_id"])) {
    $user_id = (!empty($_SESSION["s_bruker_id"])) ? $_SESSION["s_bruker_id"] : 1;
    $text = htmlspecialchars($_POST['text']);
    switch ($_POST['submit']) {
        case 'report':
        {
             $sql = "CALL report('{$text}', $user_id, {$_POST['reply_id']})";
            break;
        }
        case 'Reply':
        {
            $sql = "CALL reply('{$text}', $user_id, {$_POST['reply_id']})";
            break;
        }
        case 'Add top comment':
        {
            $sql = "CALL createComment('{$text}', $user_id, {$_POST['roomRedirect']})";
            break;
        }
        default:
        {
            die();
        }
    }
    $result = $mysqli->query($sql);
    header("Location: emne.php?room={$_POST['roomRedirect']}");
} else {
    print_r($_POST['roomRedirect']);
}
?>