<?php

require("check.php");

if(isset($bruker)){
    $sql = 
        "CALL doesUserHaveRoleQuery({$_SESSION["s_bruker_id"]}, 4)";
    $row = $mysqli->query($sql);
    $valid = ($row->fetch_row()[0] == 1);
    
    $row->free_result();
    $mysqli->next_result();
}
if ($valid) {
    if (isset($_POST['godkjenn'])) {
       $sql = "CALL adminVerifyTeacher({$_POST['user_id']})";
       $result = $mysqli->query($sql);

    }elseif (isset($_POST['slett'])) {
        $sql = "CALL adminDeleteUser({$_POST['user_id']})";
        $result = $mysqli->query($sql);
    }
    header("Location: admin.php");
}