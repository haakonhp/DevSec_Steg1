<?php
include 'inc/header.php';
$mysqli = require __DIR__ . "/database.php";

session_start();

if (isset($_POST['new_password'], $_POST['confirm_password'])) {
    if (!$mysqli) {
        echo "\n";
        die('Could not connect to the db: ' . mysqli_connect_error());
    }

    $user_id = $_SESSION['s_bruker_id'];

    $query = "CALL getPasswordHashFromUser(?)";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $password_hash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!password_verify($_POST['currentpwd'], $password_hash)) {
        die("\n Gammelt passord er ugyldig");
    } else {
        $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $query = "CALL updatePasswordByID(?,?)";
        $stmt = mysqli_prepare($mysqli, $query);
        mysqli_stmt_bind_param($stmt, "si", $new_password_hash, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            echo 'passord er oppdatert!';
        } else {
            echo 'passord ble ikke forandret. ' . mysqli_error($mysqli);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
    <h1> Bytt passord </h1>
    <form action="" method="post">
        <label for="currentpwd"> Passord:</label>
        <input type="password" id="currentpwd" name="currentpwd" placeholder="Skriv inn gammelt passord..">

        <label for="new_password">Nytt passord:</label>
        <input type="password" id="new_password" name="new_password" placeholder="Skriv inn nytt passord..">

        <label for="confirm_password">Gjenta nytt passord:</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Skriv inn nytt passord..">

        <input type="submit" value="Bytt passord">

        <a href="index.php">Tilbake til forrige side.</a><br>
    </form>


<?php include 'inc/footer.php'; ?>