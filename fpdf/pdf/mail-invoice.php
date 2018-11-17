<?php
$con = mysqli_connect('sql6.jnb1.host-h.net','chevron_db','kwd001','chevron_db');

$message = nl2br($_POST['message']);
$email = $_POST['email'];
	
$target_path = "";
	
$target_path = $target_path . basename( $_FILES['attach']['name']); 
	
if(move_uploaded_file($_FILES['attach']['tmp_name'], $target_path)) {
		
	$file_attachment = $_FILES['attach']['name'];
}
	
// array with filenames to be sent as attachment
$files = array();

for($i=0;$i<count($_POST['file']);$i++){
	
	array_push($files, 'Sealink Asset Management Invoice #'. $_POST['file'][$i] .'.pdf');
}

// Extra Attachment
if(!empty($_FILES['attach']['name'])){
		
	array_push($files, $file_attachment);
}
	
// email fields: to, from, subject, and so on
$to = $email;
$from = "info@protonchem.co.za"; 
$subject ="Seavest Asset Management Invoice"; 
$message = "<body style=\"font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333\"><img src=\"http://www.seavest.co.za/inv/fpdf16/mail_logo.jpg\"><br><br>". $message ."</body>";
$headers = "From: $from";
	
// boundary 
$semi_rand = md5(time()); 
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
// headers for attachment 
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	
// multipart boundary 
$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-type:text/html; charset=utf8\r\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
$message .= "--{$mime_boundary}\n";
	
// preparing attachments
	
for($x=0;$x<count($files);$x++){
	$file = fopen($files[$x],"rb");
	$data = fread($file,filesize($files[$x]));
	fclose($file);
	$data = chunk_split(base64_encode($data));
	$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
	    "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
	   $message .= "--{$mime_boundary}\n";
}
	
$ok = @mail($to, $subject, $message, $headers);

if($ok){
	
	for($i=0;$i<count($_POST['file']);$i++){
		
		$id = $_POST['file'][$i];
		$date = date('d M Y H:i:s');
		$file = $files[$x];
		
		mysqli_query($con, "UPDATE tbl_inv SET StatusDebtors = '149' WHERE Id = '$id'") or die(mysqli_error($con));
	}
	
	header('Location: ../../accounts/index.php?MailSuccess');
	
} else {
	
	header('Location: ../../accounts/index.php?MailError');
		
}
?>