<?php
/**
 * Created by PhpStorm.
 * User: charles
 * Date: 03/11/2015
 * Time: 10:08
 */

error_reporting(E_STRICT);
include 'phpmailer/PHPMailerAutoload.php';

$sBody=$_POST['message'];
$sSubject=$_POST['subject'];
$sFrom=$_POST['from'];
$sTo=$_POST['from'];
$aBCC = preg_split("\r\n?|\n",$_POST['BCC']);
$iMaxBCC = 20; // max number of bcc [er mail

//$sSig = file_get_contents("");

$aBatches = array_chunk($aBCC,$iMaxBCC,false); // split mail into batches

foreach ($aBatches as $aBatch) {
    $mail = new PHPMailer();
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPDebug  = 1; // enables SMTP debug information
    $mail->SMTPAuth = false;
    $mail->SMTPSecure = 'none';
    $mail->Host = "127.0.0.1";
    $mail->Port = 587;
    $mail->CharSet = "utf-8"; //?
    $mail->addAddress($sTo,'');
    $mail->addReplyTo($sFrom,'');
    $mail->SetFrom($sFrom, '');

    foreach ($aBatch as $sBCC) {
        $mail->addBCC($sBCC);
    }

    $mail->Subject = ($sSubject); // subject
    $mail->MsgHTML("$sBody"); // message

    if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        die();
    }
    else {
        echo "Message sent!";
    }
}
