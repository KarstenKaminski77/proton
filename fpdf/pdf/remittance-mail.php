<?php
session_start();

require_once('../../functions/functions.php');

$con = mysqli_connect('dedi133.jnb2.host-h.net','seavest001','nFT4nwk8','seavest');

select_db();

$remittanceid = $_POST['remittanceid'];
$to = $_POST['email'];
$text = nl2br($_POST['message']);

		
	$from = "control@seavest.co.za"; 
	$subject ="Seavest Remittance Advice ". $_POST['remittanceid']; 
	$message = "<body style=\"font-family:tahoma; font-size:12px; margin: 20px; line-height:18px; color:#333366\"><img src=\"http://www.seavest.co.za/inv/fpdf16/mail_logo.jpg\"><br><br>". $text ."</body>";
	$headers = "From: $from";
	
	// boundary 
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
	// headers for attachment 
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	
	// multipart boundary 
	$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-type:text/html; charset=utf8\r\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
	$message .= "--{$mime_boundary}\n";
	
	$files = 'Seavest Remittance '. $_POST['remittanceid'] .'.pdf';
	
	// preparing attachments
		$file = fopen($files,"rb");
		$data = fread($file,filesize($files));
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files\"\n" . 
	    "Content-Disposition: attachment;\n" . " filename=\"$files\"\n" . 
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
	    $message .= "--{$mime_boundary}\n";
	
	$ok = @mail($to, $subject, $message, $headers);
	
		
		//mysql_query("UPDATE tbl_jc SET SearchDate = '$searchdate', Days = '$today', InvoiceNo = '$invoice_no', Status = '12' WHERE JobId = '$jobid'") or die(mysql_error());
		  
?>
<?php require_once('../../Connections/seavest.php'); ?>
<?php
//MX Widgets3 include
require_once('../../includes/wdg/WDG.php');

require_once('../../functions/functions.php');

mysql_select_db($database_seavest, $seavest);
$query_Recordset1 = "SELECT * FROM tbl_companies";
$Recordset1 = mysql_query($query_Recordset1, $seavest) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_seavest, $seavest);
$query_Recordset2 = "SELECT * FROM tbl_sites";
$Recordset2 = mysql_query($query_Recordset2, $seavest) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:wdg="http://www.interaktonline.com/MXWidgets">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SEAVEST AFRICA TRADING CC</title>
<link href="../../styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../styles/fonts.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial;
}
a {
	font-family: Arial;
	font-size: 11px;
	color: #FFFFFF;
	font-weight: bold;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFFFF;
}
a:hover {
	text-decoration: none;
	color: #CCCCCC;
}
a:active {
	text-decoration: none;
	color: #FFFFFF;
}
-->
</style>
<link href="../../styles/fonts.css" rel="stylesheet" type="text/css">
<link href="../../styles/layout.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="823" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" valign="top">
        <?php
include('../../menu.php'); ?>
      <p>&nbsp;</p>
    <p>&nbsp;</p></td>
    <td valign="top"><table width="823" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center"><img src="../../images/banner.jpg" width="823" height="213"></td>
      </tr>
      <tr>
        <td align="center"><p>&nbsp;</p>
          <p>
		  <?php
		  $ok = @mail($to, $subject, $message, $headers); 
if ($ok) { 
	echo "<p>mail sent to $to!</p>"; 
} else { 
	echo "<p>mail could not be sent!</p>"; 
} 
?>
		  </p></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($Recordset2);
?>
