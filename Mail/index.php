<?php
// Connect To The Database
require_once('../functions/db-connect.php');
require_once('../functions/functions.php');
require_once('../../PHPMailer/PHPMailerAutoload.php');

restrict();

logout($con);

$userid = $_COOKIE['userid'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_industries = mysqli_query($con, "SELECT * FROM tbl_industries ORDER BY Industry ASC")or die(mysqli_error($con));

$query_product = mysqli_query($con, "SELECT * FROM tbl_products ORDER BY Name ASC")or die(mysqli_error($con));

if(isset($_POST['send'])){

	$date = date('Y-m-d H:i:s');
	$industryid = $_POST['industry'];
	$productid = $_POST['product'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];


	mysqli_query($con, "INSERT INTO tbl_mail (Date,Industry,Subject,Body) VALUES ('$date','$industryid','$subject','$body')")or die(mysqli_error($con));

	$query = mysqli_query($con, "SELECT * FROM tbl_mail ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);

	$mailid = $row['Id'];

	if(!empty($_POST['industry'])){

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

	}

	if(!empty($_POST['product'])){

		$query_mail = "
		SELECT
			tbl_companies.CompanyName,
			tbl_companies.BuyerEmail,
			tbl_companies.SalesEmail
		FROM
			tbl_companies
		INNER JOIN tbl_product_company_relation ON tbl_companies.Id = tbl_product_company_relation.CompanyId
		INNER JOIN tbl_products ON tbl_product_company_relation.ProductId = tbl_products.Id
		WHERE
			tbl_product_company_relation.ProductId = '$productid'";

	}

	$query_mail = mysqli_query($con, $query_mail)or die(mysqli_error($con));
	while($row_mail = mysqli_fetch_array($query_mail)){

		if($_POST['to'] == 'sales'){

			$to = $row_mail['SalesEmail'];

		} else {

			$to  = $row_mail['BuyerEmail'];
		}

		$from = 'info@protonchem.co.za';
		$company = $row_mail['CompanyName'];

		$message = '
		<body style="font-family:Arial; font-size:12px; margin: 20px; line-height:18px; color:#333333">'.
		$body
		.'<br><br>
		Mr S. Bissasser
		<br><br>
		<img src="http://www.kwd.co.za/proton/images/sig.jpg" width="600" height="68" />
		</body>';


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
		$mail->Host = "mail.protonchem.co.za";
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = 587;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = "info@protonchem.co.za";
		//Password to use for SMTP authentication
		$mail->Password = "0832591098";
		//Set who the message is to be sent from
		$mail->setFrom($from, 'Proton Chem');
		//Set an alternative reply-to address
		$mail->addReplyTo($from, 'Proton Chem');
		//Set who the message is to be sent to
		$mail->addAddress($to);
		//$mail->addBcc($bcc);
		//$mail->addCC('marcus.abrahams@seavest.co.za', 'Seavest Africa');
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($html = $message);
		//Replace the plain text body with one created manually
		$mail->AltBody = '';
		//Attach an image file
		//$mail->addAttachment($pdf);

		//send the message, check for errors
		if($mail->send()) {

			mysqli_query($con, "INSERT INTO tbl_mail_details (MailId,Company,`To`,Bcc) VALUES ('$mailid','$company','$to','$bcc')")or die(mysqli_error($con));

		} else {

			array_push($failed, $supplierid);

			header('Location: ../../Source/index.php?Status=1&Failed');

		}
	}

	header('Location: sent.php?Success');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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

$("#product").on("click", function()
{
		$("#product").focusin(function() { $("#category").prop("disabled", "disabled"); });
});
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
      <table width="100%" border="0" cellpadding="4" cellspacing="1">
        <tr>
          <td class="td-right">
          <select name="industry" class="tarea-100 toggle" id="industry">
           <option value="">Select An Industry...</option>
           <?php while($row_industries = mysqli_fetch_array($query_industries)){ ?>
           <option value="<?php echo $row_industries['Id']; ?>"><?php echo $row_industries['Industry']; ?></option>
           <?php } ?>
          </select></td>
        </tr>
				<tr>
          <td class="td-right">
          <select name="to" class="tarea-100" id="to">
           <option value="">Send To...</option>
					 <option value="sales">Sales</option>
					 <option value="buyer">Buyer</option>
          </select></td>
        </tr>
        <tr>
          <td class="td-right"><select name="product" class="tarea-100 toggle" id="product" disabled>
            <option value="">Select A Product...</option>
            <?php while($row_product = mysqli_fetch_array($query_product)){ ?>
            <option value="<?php echo $row_product['Id']; ?>"><?php echo $row_product['Name']; ?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr>
          <td class="td-right"><input name="subject" type="text" class="tarea-100" id="subject" onfocus="if(this.value=='Subject'){this.value=''}" onblur="if(this.value==''){this.value='Subject'}" value="Subject" /></td>
        </tr>
        <tr>
          <td class="td-right"><textarea name="body" rows="18" class="tarea-100" id="body"></textarea></td>
        </tr>
        <tr>
          <td colspan="2" align="right"><input name="send" type="submit" class="btn" id="send" value="Send Email" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
