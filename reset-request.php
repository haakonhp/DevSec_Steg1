<?php
include 'inc/header.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


$mysqli = require __DIR__ . "/database.php";


//få med input fra form
if(isset($_POST["reset-request-submit"])) {

//lage 2 tokens
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);
//inkludere select token og validator token i link
    $url = "localhost/Steg1/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);
//dagens dato + 30min
    $userEmail = $_POST["email"];

    $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);



//delete existing token of user inside db.
    $sql = "CALL deletePWDToken(?)";
    $stmt = mysqli_stmt_init($mysqli);
//if fail - error
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "ERRRORR";
        exit();
        //if no fail -> exw
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }


    $sql = "CALL createResetToken(?, ?, ?)";
    $stmt = mysqli_stmt_init($mysqli);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "ERRRORR";
        exit();


    } else {
        //hash before transfer using default
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sss", $userEmail, $selector, $hashedToken);
        mysqli_stmt_execute($stmt);
    }
    //closing all
    mysqli_stmt_close($stmt);
    //mysqli_close();



    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'datasikkerhetgr1@gmail.com';                     //SMTP username
        $mail->Password = 'bkhzwctgjerzexgu';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('datasikkerhetgr1@gmail.com', 'DXD');
        $mail->addAddress($userEmail);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset password ';
        $mail->Body = '<br>We have received a password reset request. The link to reset your password is below.</br>';
        $mail->Body .= '<a href="' . $url . '">' . $url . '</a></p>';
        //   $mail->Body .= '<a href=localhost/Steg1/create-new-password.php?"' . $url . '">' . $url . '</a></p>';

        $mail->send();
        header("Location: reset-password.php?reset=success");

        mysqli_close($mysqli);
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    /*
 $to = $userEmail;
 $subject = 'Reset password for DXD';
 $message = '<p>We have recieved a password reset request. The link to reset your password is below. If you did not make this request please ignore this.</p>';
 $message .= '<a href=127.01.01/Steg1/create-new-password.php?"' . $url . '">' . $url . '</a></p>';

 $headers = "From: gxd <gxd <grpgxd@gmail.com>\r\n";
 $headers .= "Reply to: <gxd <grpgxd@gmail.com>\r\n";
 $headers .= "Content-Type: text/html\r\n";

 //sending mail with all
 mail($to, $subject, $message, $headers);

 header("reset-password.php?reset=success");*/
}




