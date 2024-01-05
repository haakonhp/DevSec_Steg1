<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
$title = "Emne";
require("inc/header.php");
require("check.php");?>
<link rel="stylesheet" href="index_style.css">

    <link rel="stylesheet" href="styles/emnestyles.css">
    <!-- Dersom brukeren er funnet og er set med session beskrevet ovenfor, får vi tilgang til innholdet under.-->
<?php

// User blir definert senere dersom bruker blir registrert som gjest eller ordentlig bruker.
$user = "";

$valid = FALSE;
if (isset($bruker)) {
    // Utfører SQL spørring for å se etter brukerens emner
    $sql =
        "CALL doesUserHaveSubject({$_SESSION["s_bruker_id"]}, {$_GET["room"]})";
    $row = $mysqli->query($sql);
    $valid = ($row->fetch_row()[0] == 1);

    $row->free_result();
    $mysqli->next_result();

    $user = $bruker;
} elseif (isset($emne)) { // For gjest_bruker med pin_kode. Henter allerede subject_id fra SESSION.
    if ($emne["subject_id"] == $_GET["room"]) {
        $valid = TRUE;
        $user = array(
            "name" => "Gjest"
        );
    }
} else {
    header("Location: login.php");
}

// Dersom brukeren har emnet som tilsvarer denne siden, vil de få tilgang til innholdet som skrives under.
if ($valid === TRUE) {
    $sql = "CALL getSubjectDataFromID({$_GET["room"]})";
    $row = $mysqli->query($sql);
    $emne_detaljer = ($row->fetch_assoc());
    $mysqli->next_result();

    // Henter Subject name?>
    <h1><?= $emne_detaljer["subject_id"] ?> - <?= $emne_detaljer["subject_name"]; ?></h1>

    <section class="top_grid">
    <p class="one">Hei <?= htmlspecialchars($user["name"]) ?>!</p>
    <p class="two"><a href='index.php'>Hjem</a></p>
    <p class="three"><a href="logout.php">Logg ut</a></p>
    </section>

    <?php
    echo "<h2>Kommentarer:</h2>";
    // Mulighet for å skrive en topp nivå kommentar.
    echo "
        <button id='createNewButton' onclick='
        var hiddenValue = document.getElementById(\"createNew\").hidden.valueOf();
        document.getElementById(\"createNew\").hidden = !hiddenValue;'>Create new comment</button>

        <form hidden method='post' id='createNew' action='emne_submit.php'>
        <input type='text' name='text'>
        <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
        <input type='submit' name='submit' value='Add top comment'>
        </form>
        ";

    $user_id = (!empty($_SESSION["s_bruker_id"])) ? $_SESSION["s_bruker_id"] : 1;
    $role_sql = "CALL getUserRoles({$user_id})";
    $role = $mysqli->query($role_sql)->fetch_assoc()["role_name"];
    $mysqli->next_result();

    if ($role == "Administrator") {
        $sql = "CALL getCommentChainAsAdmin({$_GET["room"]})";
    } else {
        $sql = "CALL getConversationInChatRoomAnonymous({$_GET["room"]})";
    }
    $rows = $mysqli->query($sql);
    $study_field = mysqli_fetch_all($rows, MYSQLI_ASSOC);
    foreach ($study_field as $key => $value) {
        $comment_name = htmlspecialchars($value['name']);
        $comment_text = htmlspecialchars($value['text']);
        $img_html = !empty($value["photo_path"]) ? "<img src='img/{$value["photo_path"]}' alt='profilbilde' width = '80' height = '80'>" : "";

        echo "<article style='margin-left: calc({$value['depth']} * 50px);'>
            $img_html
            <p>{$comment_name}: {$comment_text}</p>
            
            <button class='replyButton' id='replyButton{$value['id']}' onclick='
            var hiddenValue = document.getElementById(\"replyform{$value['id']}\").hidden.valueOf();
            document.getElementById(\"replyform{$value['id']}\").hidden = !hiddenValue;
            document.getElementById(\"reportform{$value['id']}\").hidden = true;
            '>Reply</button>
            
            <button class='reportButton' id='reportButton{$value['id']}' onclick='
            var hiddenValue = document.getElementById(\"reportform{$value['id']}\").hidden.valueOf();
            document.getElementById(\"reportform{$value['id']}\").hidden = !hiddenValue;
            document.getElementById(\"replyform{$value['id']}\").hidden = true;
            '>Report</button>
            ";

        echo "<form hidden method='post' class='inputForm' id='replyform{$value['id']}' action='emne_submit.php'>
                <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
                <input type='hidden' name='reply_id' value='{$value['id']}'>
                <input type='text' name='text'>
                <input type='submit' name='submit' value='Reply'>
            </form>
            
            <form hidden method='post' class='inputForm'  id='reportform{$value['id']}' action='emne_submit.php'>
                <input type='hidden' name='roomRedirect' value='{$_GET['room']}'>
                <input type='hidden' name='reply_id' value='{$value['id']}'>
                <input type='text' name='text'>
                <input type='submit' name='submit' value='report'>
            </form>";
        echo "</article>";
    }

    echo "<br>Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
} // Dersom brukeren ikke har emnet som tilsvar denne nettsiden, vil de bli nektet adgang
else {
    echo "<h1> Nektet tilgang </h1>";
    echo "<strong>Du har ikke tilgang til dette emnet</strong><br><br>";
    echo "Trykk <a href='index.php'>her </a> for å gå tilbake til hovedsiden";
}
?>

<?php include 'inc/footer.php'; ?>