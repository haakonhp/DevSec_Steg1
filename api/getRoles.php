<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/../database.php";
    $sql = sprintf("CALL getUserFromToken('%s')", $mysqli->real_escape_string($_POST["auth_token"]));
    $result = $mysqli->query($sql);
    $bruker = $result->fetch_assoc();
    $result->free_result();
    $mysqli->next_result();
    if ($bruker) {
        $sql = "CALL getUserRoles({$bruker["user_id"]})";
        $result = $mysqli->query($sql);
        $roles = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($roles);
    }
}
?>