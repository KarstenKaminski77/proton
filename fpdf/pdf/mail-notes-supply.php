<?php 
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');

email_content($con,5);

$pono = $_GET['Id'];
$companyid = $_GET['Company'];

$query = "
SELECT
	tbl_notes.Id,
	tbl_notes.DeliveryNote,
	tbl_notes.PickUpSlip,
	tbl_notes.Date,
	tbl_transport_companies.ContactName,
	tbl_transport_companies.Email
FROM
	tbl_notes
INNER JOIN tbl_transport_companies ON tbl_notes.TranporterId = tbl_transport_companies.Id
WHERE
	tbl_notes.Id = '$pono'";
	
$query = mysqli_query($con, $query)or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$docs = array($row['PickUpSlip'],$row['DeliveryNote']);
	
	$message = nl2br('Dear ' .$row['ContactName'] .'<br><br>
	'. $_SESSION['content'] .'
	<br><br>
	Mr S. Bissasser<br><br>
	<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />');
	
// email fields: to, from, subject, and so on
	
$to = $row['Email'];
$from = "Proton Chem <info@protonchem.co.za>"; 
$subject ="Proton Chem Pick Up Slip | Delivery Note"; 
$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333\">". $message ."</body>";
$headers = "From: $from";
	
// boundary 
$semi_rand = md5(time()); 
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
// headers for attachment 
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	
// multipart boundary 
$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-type:text/html; charset=utf8\r\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
$message .= "--{$mime_boundary}\n";
	
$query = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);
	
// preparing attachments

for($x=0;$x<count($docs);$x++){
	
	$file = fopen($docs[$x],"rb");
	$data = fread($file,filesize($docs[$x]));
	fclose($file);
	$data = chunk_split(base64_encode($data));
	$pdf = $docs[$x];
	$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$pdf\"\n" . 
	    "Content-Disposition: attachment;\n" . " filename=\"$pdf\"\n" . 
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
    $message .= "--{$mime_boundary}\n";
}

$ok = @mail($to, $subject, $message, $headers);

header('Location: ../../Notes/pickup-delivery-supply.php?Success');
?>