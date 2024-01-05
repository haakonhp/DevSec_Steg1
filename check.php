<?php
session_start();

if (isset($_SESSION["s_bruker_id"])) {
    $mysqli = require __DIR__ . "/database.php";

    $sql = "CALL getUserFromID({$_SESSION["s_bruker_id"]})";

    $result = $mysqli->query($sql);

    $bruker = $result->fetch_assoc();

    $mysqli->next_result();

    $sql = 
        "CALL doesUserHaveRoleQuery({$_SESSION["s_bruker_id"]}, 4)";
    $row = $mysqli->query($sql);
    $is_admin = ($row->fetch_row()[0] == 1);

    $mysqli->next_result();
}
elseif (isset($_SESSION["s_pin_code"])) {
    $mysqli = require __DIR__ . "/database.php";
    $sql = "CALL getSubjectWithPIN({$_SESSION["s_pin_code"]})";

    $result = $mysqli->query($sql);

    $emne = $result->fetch_assoc();

    $result->free_result();
    $mysqli->next_result();
}