<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

$today = date('Y-m-d');

$query_follow_up = "SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.Id,
	tbl_products.`Name`,
	tbl_products.Id AS Product,
	tbl_rfq.Date,
	tbl_companies.BuyerName,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.BuyerEmail,
	tbl_offers.RFQId,
	tbl_offers.Notes,
	tbl_offers.Interested,
	tbl_offers.FollowUpDate,
	tbl_offers.Sent,
	tbl_products.CAO,
	tbl_products.DataSheet
FROM
	tbl_rfq_items
INNER JOIN tbl_rfq ON tbl_rfq_items.SourceId = tbl_rfq.Id
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
WHERE
	tbl_rfq.Type = '2' AND tbl_rfq.`Status` = '1' AND tbl_offers.Closed = '0' AND tbl_offers.FollowUpDate = '$today'
ORDER BY
	tbl_rfq.Date DESC";
	
$query_follow_up = mysqli_query($con, $query_follow_up)or die(mysqli_error($con));
while($row_follow_up = mysqli_fetch_array($query_follow_up)){
	
	email_content($con,7);
	
	$sourceid = $row_follow_up['SourceId'];
	$supplierid = $row_follow_up['SupplierId'];
	$productid = $row_follow_up['Product'];
	$rfq_item = $row_follow_up['Id'];
	
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
	
	$to = $row_follow_up['BuyerEmail'];
	$from = "Proton Chem <info@protonchem.co.za>"; 
	$subject ="Proton Chem Sales Offer"; 
	$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; width:760px; color:#333333\">". $message ."</body>";
	$headers = "From: $from";
	
	// boundary 
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
	// headers for attachment 
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	
	// multipart boundary 
	$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-type:text/html; charset=utf8\r\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
	$message .= "--{$mime_boundary}\n";
	
	$query = mysqli_query($con, "SELECT * FROM tbl_offers WHERE RFQId = '$rfq_item'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$files = array($row['PDF'],'../data/'.$row_follow_up['DataSheet'],'../cao/'.$row_follow_up['CAO']);
	
	for($i=0;$i<count($files);$i++){
		
		// preparing attachments'$itemid'
		$file = fopen($files[$i],"rb");
		$data = fread($file,filesize($files[$i]));
		
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$pdf = $files[$i];
		$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$pdf\"\n" . 
	    "Content-Disposition: attachment;\n" . " filename=\"$pdf\"\n" . 
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message .= "--{$mime_boundary}\n";
	}
	
	$ok = @mail($to, $subject, $message, $headers);
	
	$itemid = $row_follow_up['Id'];
	
	$query = mysqli_query($con, "SELECT * FROM tbl_offers WHERE RFQId = '$itemid'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$sent = $row['Sent'] + 1;
	
	mysqli_query($con, "UPDATE tbl_offers SET Sent = '$sent' WHERE RFQId = '$itemid'")or die(mysqli_error($con));
}
	
?>