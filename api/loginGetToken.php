<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/../database.php";
    $sql = sprintf("CALL selectUserFromEmail('%s')", $mysqli->real_escape_string($_POST["email"]));
    $result = $mysqli->query($sql);
    $bruker = $result->fetch_assoc();
    $result -> free_result();
    $mysqli -> next_result();

    if ($bruker) {
        if (password_verify($_POST["password"], $bruker["password_hash"])) {
            $sql = "CALL createAuthToken({$bruker["user_id"]})";
            $result = $mysqli->query($sql);
            $UUID = $result->fetch_row()[0];
            echo json_encode($UUID);
        }
    }
}
?>