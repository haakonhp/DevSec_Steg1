<?php
$mysqli = require __DIR__ . "/database.php";
require("verifySubject.php");
require("check.php");

if($is_admin && isset($_POST['delete-comment'])){
    $sql = "CALL deleteCommentWithId({$_POST['comment_id']})";
    $mysqli->query($sql);
    header("Location: admin.php?room={$_POST['roomRedirect']}");
}

if($is_admin && isset($_POST['delete-report'])){
    $sql = "CALL deleteReportWithId({$_POST['report_id']})";
    $mysqli->query($sql);
    header("Location: admin.php?room={$_POST['roomRedirect']}");
}