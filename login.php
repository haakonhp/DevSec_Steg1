<?php
$title = "Login";
require("inc/header.php");?>

<?php
$is_invalid = false;

// Finner brukeren
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";
    $sql = sprintf("CALL selectUserFromEmail('%s')", $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);

    $bruker = $result->fetch_assoc();

    // Dersom bruker er funnet, verifiserer den passordet. Hvis riktig, så logger man seg inn med en session
    if ($bruker) {
        if (password_verify($_POST["password"], $bruker["password_hash"])) {
            //Starter session
            session_start();

            //Anti Session-fixation attack
            session_regenerate_id();

            // Lagrer user_id i session
            $_SESSION["s_bruker_id"] = $bruker["user_id"];

            // Blir redirected til index siden
            header("Location: index.php");
            exit;
        }
    }
    $is_invalid = true;
}

?>

<h1>Logg inn</h1>

<?php if ($is_invalid): ?>
    <em style="color:red;">Invalid login</em>
<?php endif; ?>

<?php if(isset($_GET["newpwd"])) {
    if ($_GET["newpwd"] == "passwordupdated") {
        echo '<p class="signupsuccess">Passord ble oppdatert!</p>';
    }
} ?>

<form method="post">

    <div>
    <label for="email">E-post</label>
    <!--Legger også til slik at email blir husket i formen dersom noe blir feil ved innlogging.. Lagt også til sikkerhet-->
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
    </div>

    <div>
    <label for="password">Passord</label>
    <input type="password" id="password" name="password">
    </div>

    <button>Logg inn</button>
    <br>
    <a href="signup_choose.php">Registrer en ny bruker</a><br>
    <a href="gjest_bruker_autentisering.php">Gjestbruker</a><br>
    <a href="reset-password.php">Glemt passord?</a><br>


</form>
<?php include 'inc/footer.php'; ?>