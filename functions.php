<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Sending email
function sendEmail($to, $subject, $body)
{
  $mail = new PHPMailer(true);

  try {
    //Server settings
    $mail->SMTPDebug  = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = SMTP_USER;                     //SMTP username
    $mail->Password   = SMTP_PASS;                               //SMTP password
    $mail->SMTPSecure = SMTP_PORT === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom(SMTP_USER, 'Pure Coding');
    $mail->addAddress($to);               //Name is optional

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    return 1;
  } catch (Exception $e) {
    return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

// Check if user is authenticated
function isAuthenticated()
{
  global $conn;

  if (!isset($_COOKIE['token'])) {
    return false;
  }
  $token = $_COOKIE['token'];

  // Check token
  $sql = "SELECT `id` FROM `users` WHERE `password` = '$token'";
  $result = mysqli_query($conn, $sql);
  // Check if email is already exists
  if (mysqli_num_rows($result) > 0) {
    return $token;
  } else {
    return false;
  }
}

// Function to return user data
function getLoggedInUser()
{
  global $conn;

  $token = isAuthenticated();

  // Checking token
  if ($token === false) {
    return false;
  }

  $sql = "SELECT * FROM `users` WHERE `password` = '$token'";
  $result = mysqli_query($conn, $sql);
  return mysqli_fetch_assoc($result);
}
