<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_industries = mysqli_query($con, "SELECT * FROM tbl_industries ORDER BY Industry ASC")or die(mysqli_error($con));

if(isset($_POST['send'])){

$date = date('Y-m-d H:i:s');
$industryid = $_POST['industry'];
$subject = $_POST['subject'];
$body = $_POST['body'];


mysqli_query($con, "INSERT INTO tbl_mail (Date,Industry,Subject,Body) VALUES ('$date','$industry','$subject','$body')")or die(mysqli_error($con));

$query = mysqli_query($con, "SELECT * FROM tbl_mail ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
$row = mysqli_fetch_array($query);

$mailid = $row['Id'];

$query_mail = "
SELECT
	tbl_companies.CompanyName,
	tbl_companies.BuyerEmail,
	tbl_companies.SalesEmail,
	tbl_industries.Industry
FROM
	tbl_companies
INNER JOIN tbl_company_industry_relation ON tbl_companies.Id = tbl_company_industry_relation.CompanyId
INNER JOIN tbl_industries ON tbl_company_industry_relation.IndustryId = tbl_industries.Id
WHERE
	tbl_company_industry_relation.IndustryId = '$industryid'";

$query_mail = mysqli_query($con, $query_mail)or die(mysqli_error($con));
while($row_mail = mysqli_fetch_array($query_mail)){
	
$to  = $row_mail['BuyerEmail'];
$bcc = $row_mail['SalesEmail']; 
$from = 'Proton Chem <info@protonchem.co.za>';
$company = $row_mail['CompanyName'];

$message = '
<body style="font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333">'.
$body
.'<br><br>
Mr S. Bissasser
<br><br>
<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />
</body>';

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'FROM: '.$from . "\r\n";
$headers .= 'Bcc: '.$bcc . "\r\n";

mysqli_query($con, "INSERT INTO tbl_mail_details (MailId,Company,`To`,Bcc) VALUES ('$mailid','$company','$to','$bcc')")or die(mysqli_error($con));

mail($to, $subject, $message, $headers);
}
header('Location: sent.php?Success');
}

$mailid = $_GET['Id'];

$query_sent = "
SELECT
	tbl_mail.Id,
	tbl_mail.Date,
	tbl_mail.`Subject`,
	tbl_industries.Industry,
	tbl_mail.Body,
	tbl_mail_details.`To`,
	tbl_mail_details.Bcc
FROM
	tbl_mail
INNER JOIN tbl_industries ON tbl_mail.Industry = tbl_industries.Id
INNER JOIN tbl_mail_details ON tbl_mail.Id = tbl_mail_details.MailId
WHERE
    tbl_mail.Id = '$mailid'";

$query_sent = mysqli_query($con, $query_sent)or die(mysqli_error($con));
$row_sent = mysqli_fetch_array($query_sent);

$query_to = mysqli_query($con, "SELECT * FROM tbl_mail_details WHERE MailId = '$mailid'")or die(mysqli_error($con));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="../sdmenu/sdmenu.js"></script>
<script type="text/javascript">
<!--
var myMenu;
	window.onload = function() {
		myMenu = new SDMenu("my_menu");
		myMenu.init();
	};

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
    selector: "textarea",
    theme: "modern",
    plugins: [
        ["autoresize advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker"],
        ["searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking"],
        ["save table contextmenu directionality emoticons template paste"]
    ],
    add_unload_trigger: true,
    schema: "html5",
    inline: false,
    toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    statusbar: false,
	relative_urls:false,
    external_filemanager_path:"/proton/Mail/filemanager/",
    filemanager_title:"Responsive Filemanager" ,
    external_plugins: { "filemanager" : "/proton/Mail/filemanager/plugin.min.js"},
});

</script> 

</head>

<body>
<div id="header">
  <div id="logo"> <a class="close" href="<?php echo $_SERVER['file:///C|/REQUEST_URI'] .'?Logout'; ?>"></a>
    <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
    <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../menu.php'); ?>
  <div align="center" class="welcome" id="right-container">
    <form id="form1" name="form1" method="post" action="">
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">RFQ successfully sent.....</div>
      <?php } ?>
      <table width="100%" border="0" cellpadding="4" cellspacing="1">
        <tr>
          <td width="9%" class="td-left">Industry</td>
          <td width="91%" class="td-right"><?php echo $row_sent['Industry']; ?></td>
        </tr>
        <tr>
          <td class="td-left">Subject</td>
          <td class="td-right"><?php echo $row_sent['Subject']; ?></td>
        </tr>
        <tr>
          <td class="td-left">To</td>
          <td class="td-right" style="text-transform:lowercase"><?php 
		  while($row_to = mysqli_fetch_array($query_to)){
			  
			  echo strtolower($row_to['To'] .'; '. $row_to['Bcc'] .'; ');
		  }
		  ?></td>
        </tr>
        <tr>
          <td colspan="2" valign="top" class="td-right"><?php echo stripslashes($row_sent['Body']); ?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer">Proton Chemicals | Developed By <a href="http://www.kwd.co.za" class="footer-link">KWD</a></div>
</body>
</html>