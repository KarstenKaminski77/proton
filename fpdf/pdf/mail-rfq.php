<?php
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');
require_once('../../PHPMailer/PHPMailerAutoload.php');

$sourceid = $_GET['Id'];
$sent = array();
$failed = array();
email_content($con,1);

for($i=0;$i<count($_SESSION['supplierid']);$i++){

	$supplierid = $_SESSION['supplierid'][$i];

	$query_products = "
	SELECT
		tbl_rfq.Date,
		tbl_rfq.Id,
		tbl_rfq_items.Qty,
		tbl_rfq_items.ProductId,
		tbl_rfq_items.SupplierId,
		tbl_rfq_items.Unit,
		tbl_rfq_items.RFQ,
		tbl_rfq_items.SourceId,
		tbl_products.`Name`,
		tbl_products.Grade,
		tbl_products.`Code`,
		tbl_products.`PackSize`,
		tbl_companies.CompanyName,
		tbl_companies.Mobile,
		tbl_companies.SalesName,
		tbl_companies.SalesEmail,
		tbl_companies.Telephone
	FROM
		tbl_rfq
	INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
	INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
	INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
	WHERE
		tbl_rfq.Id = '$sourceid' AND
		tbl_rfq_items.SupplierId = '$supplierid'";

	$table  = '<table border="0" cellpadding="5" cellspacing="0" style="border: solid 1px #000">';
	$table .= '	<tr>';
	$table .= '		<td style="border: solid 1px #000"><b>Product</b></td>';
	$table .= '		<td style="border: solid 1px #000"><b>Qty</b></td>';
	$table .= '		<td style="border: solid 1px #000"><b>Unit</b></td>';
	$table .= '		<td style="border: solid 1px #000"><b>Pack Size</b></td>';
	$table .= '		<td style="border: solid 1px #000"><b>Grade</b></td>';
	$table .= '	</tr>';

	$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));
	while($row_products = mysqli_fetch_array($query_products)){

		$table .= '<tr>';
		$table .= '	<td style="border: solid 1px #000">'. $row_products['Name'] .'</td>';
		$table .= '	<td style="border: solid 1px #000">'. $row_products['Qty'] .'</td>';
		$table .= '	<td style="border: solid 1px #000">'. $row_products['Unit'] .'</td>';
		$table .= '	<td style="border: solid 1px #000">'. $row_products['PackSize'] .'</td>';
		$table .= '	<td style="border: solid 1px #000">'. $row_products['Grade'] .'</td>';
		$table .= '</tr>';
	}

	$table .= '</table>';

	$query = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$supplierid'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);

	$message = nl2br('Dear ' .$row['SalesName']
	.'<br><br>'.
    $_SESSION['content']
	.'<br><br>'.
		$table
	.'<br><br>
	Mr S. Bissasser<br>
	<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />');

	$to = $row['SalesEmail'];
	//$from = "Proton Chem <info@protonchem.co.za>";
	$from = "info@protonchem.co.za";
	$subject ="Proton Chem Request For Quotation";
	$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; width:760px; color:#333333\">". $message ."</body>";
	$headers = "From: $from";

	$query = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SupplierId = '$supplierid'")or die(mysqli_error($con));
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

		array_push($sent, $supplierid);

		mysqli_query($con, "UPDATE tbl_rfq SET Status = '2' WHERE Id = '$sourceid'")or die(mysqli_error($con));

		header('Location: ../../Source/index.php?Status=1&Success');

	} else {

		array_push($failed, $supplierid);

		header('Location: ../../Source/index.php?Status=1&Failed');

	}
}
?>
