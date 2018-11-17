<?php
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');
require_once('../../PHPMailer/PHPMailerAutoload.php');

email_content($con,2);

$quoteno = $_GET['Id'];
$companyid = $_GET['Company'];

if($_GET['Type'] == 2){

	$query = "
    SELECT
	    tbl_qs.Id,
	    tbl_qs.Date,
	    tbl_qs.DeliveryTerms,
	    tbl_qs.DeliveryDate,
	    tbl_qs.ExpiryDate,
	    tbl_qs.PaymentTerms,
	    tbl_qs.SpecialConditions,
	    tbl_companies.CompanyName,
	    tbl_companies.Telephone,
	    tbl_companies.Fax,
	    tbl_companies.Mobile,
	    tbl_companies.BuyerName,
	    tbl_companies.BuyerEmail
     FROM
	    tbl_qs
    INNER JOIN tbl_qs_items ON tbl_qs.Id = tbl_qs_items.QuoteNo
    INNER JOIN tbl_companies ON tbl_qs_items.SupplierId = tbl_companies.Id
    WHERE
	    tbl_qs.Id = '$quoteno'
    ORDER BY
	    tbl_qs.Id DESC
    LIMIT 1";

    $query = mysqli_query($con, $query);

} else {

	$query = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$companyid'")or die(mysqli_error($con));
}
$row = mysqli_fetch_array($query);

	$message = nl2br('Dear ' .$row['BuyerName'] .'<br><br>'.
	$_SESSION['content']
	.'<br><br>
	Mr S. Bissasser<br>
	<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />');

	$to = $row['BuyerEmail'];
	//$from = "Proton Chem <info@protonchem.co.za>";
	$from = "info@protonchem.co.za";
	$subject ="Proton Chem Quotation";
	$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333\">". $message ."</body>";
	$headers = "From: $from";

	$query = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);

	// preparing attachments
	$file = $row['PDF'];

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
	$mail->addAttachment($file);

	//send the message, check for errors
	if($mail->send()) {

		mysqli_query($con, "UPDATE tbl_qs SET Status = '2' WHERE Id = '$quoteno'")or die(mysqli_error($con));

		if(isset($_GET['Type'])){

			$query = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
			$row = mysqli_fetch_array($query);

			$sourceid = $row['RFQId'];

			mysqli_query($con, "UPDATE tbl_rfq SET Status = '3' WHERE Id = '$sourceid'")or die(mysqli_error($con));
			header('Location: ../../Supply/qued.php?Success='.$quoteno);

		} else {

			header('Location: ../../Source/qued.php?Success='.$quoteno);
		}

	} else {

		array_push($failed, $supplierid);

		header('Location: ../../Source/qued.php?Fail');

	}
?>
