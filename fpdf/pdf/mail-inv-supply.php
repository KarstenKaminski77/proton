<?php 
session_start();
// Connect To The Database
require_once('../../functions/db-connect.php');
require_once('../../functions/functions.php');

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
	
// email fields: to, from, subject, and so on
	
$to = $row['AccountsEmail'];
$from = "Proton Chem <info@protonchem.co.za>"; 
$subject ="Proton Chem Invoice"; 
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
	
$query = mysqli_query($con, "SELECT * FROM tbl_inv WHERE Id = '$invoiceno'")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);
	
// preparing attachments
$file = fopen($row['PDF'],"rb");
$data = fread($file,filesize($row['PDF']));
fclose($file);
$data = chunk_split(base64_encode($data));
$pdf = $row['PDF'];
$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$pdf\"\n" . 
	    "Content-Disposition: attachment;\n" . " filename=\"$pdf\"\n" . 
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
    $message .= "--{$mime_boundary}\n";
	
	$ok = @mail($to, $subject, $message, $headers);

mysqli_query($con, "UPDATE tbl_inv SET Status = '2' WHERE Id = '$invoiceno'")or die(mysqli_error($con));

header('Location: ../../Supply/approved.php?InvSuccess');
?>