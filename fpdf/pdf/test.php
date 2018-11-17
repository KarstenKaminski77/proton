<?php
session_start();
// Connect To The Database
require_once('../../PHPMailer/PHPMailerAutoload.php');

	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 2;
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';
	//Set the hostname of the mail server
	$mail->Host = "www506.jnb1.host-h.net";
	//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = 587;
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	//Username to use for SMTP authentication
	$mail->Username = "snap@nerdw.com";
	//Password to use for SMTP authentication
	$mail->Password = "mztw38dj9RutNRkg";
	//Set who the message is to be sent from
	$mail->setFrom('snap@nerdw.com', 'Snap');
	//Set an alternative reply-to address
	$mail->addReplyTo('snap@nerdw.com', 'snap');
	//Set who the message is to be sent to
	$mail->addAddress('karsten@nerdw.com');
	//$mail->addCC('marcus.abrahams@seavest.co.za', 'Seavest Africa');
	//Set the subject line
	$mail->Subject = 'Test';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($html = 'test message');
	//Replace the plain text body with one created manually
	$mail->AltBody = '';
	//Attach an image file
	//$mail->addAttachment($pdf);

	//send the message, check for errors
	$mail->send()
?>
