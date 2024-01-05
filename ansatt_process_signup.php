<?php

session_start();

if (isset($_POST['signup'])) {

    $_SESSION['form_data'] = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'subjects' => $_POST['subjects'],
    ];

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    $subjects = $_POST['subjects'];

    $errors = [];

    if (empty($name)) {
        $errors['name_error'] = 'Navn må fylles ut';
    }

    if (strlen($name) < 3) {
        $errors['name_error'] = 'Navn må være minst 3 bokstaver';
    }

    if (empty($email)) {
        $errors['email_error'] = 'E-post må fylles ut';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email_error'] = 'E-post må være en gyldig e-post';
    }

    if (empty($password)) {
        $errors['password_error'] = 'Passord må fylles ut';
    }

    if (strlen($password) < 8) {
        $errors['password_error'] = 'Passord må inneholde minst 8 tegn';
    }

    if (!preg_match("/[a-zA-Z]/", $password)) {
        $errors['password_error'] = 'Passord må inneholde minst 1 bokstav';
    }

    if (!preg_match("/[0-9]/", $password)) {
        $errors['password_error'] = 'Passord må inneholde minst 1 tall';
    }

    if ($password !== $password_confirmation) {
        $errors['password_confirmation_error'] = 'Passordene må være like';
    }

    if (empty($subjects)) {
        $errors['subjects_error'] = 'Emne må fylles ut';
    }

    // Bilde implementering i dir folder og setter path navn
    $accepted_types = ['jpg', 'jpeg', 'png'];
    $bilde = $_FILES["bilde"];
    $picture_extension = pathinfo($bilde['name'], PATHINFO_EXTENSION);
    if (in_array($picture_extension, $accepted_types)) {
        $picture_name = time().uniqid(rand());
        $path_to_be_written =  __DIR__ . "/img/" . $picture_name . '.' . $picture_extension;
        move_uploaded_file(
            $bilde["tmp_name"],
            $path_to_be_written
        );
        $photo_path = $picture_name . '.' . $picture_extension;
    } else {
        $photo_path = "default_img.png";
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $mysqli = require __DIR__ . '/database.php';

    $emailCheckQuery = "Call DoesEmailExist('$email');";
    $emailstmt = mysqli_prepare($mysqli, $emailCheckQuery);
    mysqli_stmt_execute($emailstmt);
    mysqli_stmt_bind_result($emailstmt, $rowValue);
    mysqli_stmt_fetch($emailstmt);
    mysqli_stmt_close($emailstmt);

    if ($rowValue > 0) {
        $errors['email_error'] = 'Eposten er allerede registrert.';
        $errors['error'] = 'E-posten er allerede registrert. Vennligst prøv en annen e-post, eller <a href="login.php">logg inn</a>.';
    }

    if (!empty($errors)) {
        $query = http_build_query($errors);
        header("Location: ansatt_signup.php?$query");
    } else {

        $sql = "CALL createTeacher(?, ?, ?, ?, ?);";

        $stmt = $mysqli->stmt_init();
        if (!$stmt->prepare($sql)) {
            die('SQL error: (' . $mysqli->errno . ') ' . $mysqli->error);
        }

        $stmt->bind_param("sssss", $name, $email, $subjects, $password_hash, $photo_path);

        $stmt_result = $stmt->execute();

        if ($stmt_result) {
            header("Location: signup_success.php");
            session_destroy();
        } else {
            header("Location: ansatt_signup.php?error=Noe gikk galt. Vennligst prøv igjen.");
        }

        $stmt->close();
        $mysqli->close();
        exit();
    }
}