<?php

require("check.php");

if ($is_admin) {
    $user_id = $_POST['user_id'];
    if (isset($_POST['c_name']) && $_POST['i_name']) {
        $i_name = $_POST['i_name'];

        $sql = "CALL adminChangeName($user_id, '$i_name')";
        $result = $mysqli->query($sql);

    }elseif (isset($_POST['c_email']) && ($_POST['i_email'])) {
        $i_email = $_POST['i_email'];

        $sql = "CALL adminChangeEmail($user_id, '$i_email')";
        $result = $mysqli->query($sql);

    }elseif (isset($_POST['c_password']) && ($_POST['i_password'])) {
        $i_password = $_POST['i_password'];

        $password_hash = password_hash($i_password, PASSWORD_DEFAULT);

        $sql = "CALL adminChangePassword($user_id, '$password_hash')";
        $result = $mysqli->query($sql);
    }elseif(isset($_POST['c_semester']) && ($_POST['i_semester'])) {
        $semester = $_POST['i_semester'];

        $sql = "CALL adminChangeSemester($user_id, $semester)";
        $result = $mysqli->query($sql);

    }elseif(isset($_POST['c_study_field']) && ($_POST['i_study_field'])) {
        $study_field = $_POST['i_study_field'];
        
        $sql = "CALL adminChangeField($user_id, $study_field)";
        $result = $mysqli->query($sql);
    }
    header("Location: admin.php");
}