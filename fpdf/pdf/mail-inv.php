<?php
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');
require_once('../../PHPMailer/PHPMailerAutoload.php');

email_content($con,6);

$invoiceno = $_GET['Id'];
$companyid = $_GET['Company'];

$query = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$companyid'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$message = 'Dear ' .$row['AccountsName'] .'<br><br>
'. $_SESSION['content'] .'
<br><br>
Mr S. Bissasser<br><br>
<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />';

$to = $row['AccountsEmail'];
$from = "info@protonchem.co.za";
$subject ="Proton Chem Invoice";
$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333\">". $message ."</body>";

$query = mysqli_query($con, "SELECT * FROM tbl_inv WHERE Id = '$invoiceno'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$pdf = $row['PDF'];

//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = "www27.jnb1.host-h.net";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 587;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = "test@kwd.co.za";
//Password to use for SMTP authentication
$mail->Password = "K4rsten001!";
//Set who the message is to be sent from
$mail->setFrom($from, 'Proton Chem');
//Set an alternative reply-to address
$mail->addReplyTo($from, 'Proton Chem');
//Set who the message is to be sent to
$mail->addAddress($to);
//$mail->addCC('marcus.abrahams@seavest.co.za', 'Seavest Africa');
//Set the subject line
$mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($html = $message);
//Replace the plain text body with one created manually
$mail->AltBody = '';
//Attach an image file
$mail->addAttachment($pdf);

//send the message, check for errors
if($mail->send()) {

	mysqli_query($con, "UPDATE tbl_inv SET Status = '2' WHERE Id = '$invoiceno'")or die(mysqli_error($con));

	header('Location: ../../Source/archives.php?Success');

} else {

	die('Could not send mail...');
}

?>
