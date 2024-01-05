<?php
$title = "Angi kode - Gjest";
include 'inc/header.php';?>

<?php
$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" and strlen($_POST["pin"]) > 0) {
    //finner emne med pin-koden
    $mysqli = require __DIR__ . "/database.php";
    $sql = sprintf("CALL getSubjectWithPIN('%s')", $mysqli->real_escape_string($_POST["pin"]));

    $result = $mysqli->query($sql);

    $emne = $result->fetch_assoc();

    //hvis emne med pin-koden finnes
    if($emne)
    {
        session_start();
        
        // Lagrer pin for videre validering når bruker ankommer rommet.
        $_SESSION["s_pin_code"] = $_POST["pin"];

        // Send til emne-siden
        header(sprintf("Location: emne.php?room=%d",$emne["subject_id"]));
        exit;
    }
    $is_invalid = true;
}
?>

<h1>Angi emne PIN-koden din:</h1>

<?php if ($is_invalid): ?>
    <em style="color:red;">Invalid PIN</em>
<?php endif; ?>

<form method="post">
    <label for="pin">PIN-kode</label>
    <input type="number" id="pin" name="pin">
    <button>Valider</button>
</form>
<p>Trykk <a href="index.php"> her</a> for å gå tilbake til hovedsiden.</p>
<?php include 'inc/footer.php'; ?>
