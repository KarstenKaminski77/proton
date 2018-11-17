<?php
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');
require_once('../../PHPMailer/PHPMailerAutoload.php');

email_content($con,7);

$sourceid = $_GET['Id'];
$supplierid = $_GET['Supplier'];
$productid = $_GET['Product'];
$rfq_item = $_GET['Item'];

$query = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$message = "Dear " .$row['BuyerName'] ."<br><br>
    ". 	$_SESSION['content'] ."
	<br><br><br>
	<a href='http://www.kwd.co.za/proton/Supply/rfq.php?Id=". $rfq_item ."'>Yes I am interested.</a>
	<br>
	<a href='http://www.kwd.co.za/proton/Supply/close.php?Id=". $rfq_item ."'>No thanks, I'm not interested.</a>
	<br><br>
	Mr S. Bissasser<br><br>
	<img src='http://www.kwd.co.za/proton/images/sig.jpg' />";

$to = $row['BuyerEmail'];
$from = "info@protonchem.co.za";
$subject ="Proton Chem Sales Offer";
$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; width:760px; color:#333333\">". $message ."</body>";

$query = mysqli_query($con, "SELECT * FROM tbl_offers WHERE RFQId = '$rfq_item'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$file1 = $row['PDF'];
$file2 = '../../data/'.$_SESSION['datasheet'];
$file3 = '../../cao/'.$_SESSION['cao'];



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
$mail->addBcc($bcc);
//$mail->addCC('marcus.abrahams@seavest.co.za', 'Seavest Africa');
//Set the subject line
$mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($html = $message);
//Replace the plain text body with one created manually
$mail->AltBody = '';
//Attach an image file
$mail->addAttachment($file1);
$mail->addAttachment($file2);
$mail->addAttachment($file3);

//send the message, check for errors
if($mail->send()) {

	$itemid = $_GET['Item'];

	$query = mysqli_query($con, "SELECT * FROM tbl_offers WHERE RFQId = '$itemid'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);

	$sent = $row['Sent'] + 1;

	mysqli_query($con, "UPDATE tbl_offers SET Sent = '$sent' WHERE RFQId = '$itemid'")or die(mysqli_error($con));

	header('Location: ../../Supply/qued-details.php?Id='. $sourceid .'&Success');

} else {

	die('Could not send email...');

}

?>
