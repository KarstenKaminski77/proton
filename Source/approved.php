<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 
$quoteno = $_GET['Invoice'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(isset($_GET['Invoice']) && isset($_POST['create'])){
	
	$terms = $_POST['terms'];
	$due_date = $_POST['due-date'];
	
	$query_invoice = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
	$row_invoice = mysqli_fetch_array($query_invoice);
	
	$rfqno = $row_invoice['RFQId'];
    $company = $row_invoice['CompanyId'];
    $date = date('Y-m-d');
    $po = $row_invoice['PO'];
	$currency = $row_invoice['Currency'];
	
	mysqli_query($con, "INSERT INTO tbl_inv (RFQId,QuoteNo,CompanyId,PO,Date,DueDate,PaymentTerms,Type,Currency) 
	VALUES ('$rfqno','$quoteno','$company','$po','$date','$due_date','$terms','1','$currency')")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_inv ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$invoiceno = $row['Id'];
	
	mysqli_query($con, "UPDATE tbl_transport SET InvoiceNo = '$invoiceno' WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	mysqli_query($con, "UPDATE tbl_qs SET Status = '5' WHERE Id = '$quoteno'")or die(mysqli_error($con));
	
	$query_items = mysqli_query($con, "SELECT * FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	while($row_items = mysqli_fetch_array($query_items)){
		
		$productid = $row_items['ProductId'];
		$qty = $row_items['Qty'];
		$unit = $row_items['Unit'];
		$price = $row_items['Price'];
		$retail = $row_items['Retail'];
		$total = $row_items['Total'];
		
		mysqli_query($con, "INSERT INTO tbl_inv_items (InvoiceNo,QuoteNo,ProductId,Qty,Unit,Price,Retail,Total,Date) 
		VALUES ('$invoiceno','$quoteno','$productid','$qty','$unit','$price','$retail','$total','$date')")or die(mysqli_error($con));
		
	}
	
	header('Location: ../fpdf/pdf-inv.php?Id='. $invoiceno .'&Quote='. $quoteno);
}
	
// Suppliers Query
$status = $_GET['Status'];

$query_approved = "
SELECT
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.BuyerName,
	tbl_companies.BuyerEmail,
	tbl_qs.Date,
	tbl_qs.Id,
	tbl_qs.`Status`
FROM
	tbl_qs
INNER JOIN tbl_companies ON tbl_qs.CompanyId = tbl_companies.Id
WHERE
	tbl_qs. STATUS = '3'
ORDER BY
	tbl_qs.Date DESC, tbl_qs.Id DESC";
	
$query_approved = mysqli_query($con, $query_approved)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_approved);
	
$query_default = mysqli_query($con, "SELECT * FROM tbl_proforma WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
$row_default = mysqli_fetch_array($query_default);

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

<script type="text/javascript" src="../scripts/custom-form-elements.js"></script>

<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="jquery-ui-sliderAccess.js"></script>

<link href="date.css" rel="stylesheet" type="text/css" />

<link href="../css/form-elements.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="header">
  <div id="logo">
   <a class="close" href="<?php echo $_SERVER['../REQUEST_URI'] .'?Logout'; ?>"></a>
   <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
   <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../menu.php'); ?>
  <div id="right-container">
    <form id="form1" name="form1" method="post" action="">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Source Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Approved</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">Quotation No <?php echo $_GET['Success']; ?> successfully sent.....</div>
      <?php } ?>
      <?php if(isset($_GET['Invoice'])){ ?>
      <div style="margin-bottom:20px">
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td width="50%" class="td-header-blue-dark">Payment Terms</td>
            <td width="50%" class="td-header-blue-dark">Due Date</td>
          </tr>
          <tr>
            <td class="td-grey"><input name="terms" type="text" class="tarea-100" id="terms" value="<?php echo $row_default['PaymentTerms']; ?>" /></td>
            <td class="td-grey"><input name="due-date" type="text" class="tarea-100" id="due-date" value="<?php echo $row_default['DueDate']; ?>" /></td>
            <script type="text/javascript">
		    $('#due-date').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          </tr>
          <tr>
            <td colspan="4" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4" align="right"><input name="create" type="submit" class="btn" id="create" value="Send Invoice" /></td>
          </tr>
        </table>
      </div>
      <?php } ?>
      <?php if($numrows >= 1){ ?>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="51" class="td-header-blue-dark">Quote</td>
          <td class="td-header-blue-dark">Company Name</td>
          <td width="122" class="td-header-blue-dark">Email</td>
          <td width="101" class="td-header-blue-dark">Telephone</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php 
		
		while($row_approved = mysqli_fetch_array($query_approved)){ 
		
		$quoteno = $row_approved['Id'];
		
		$query_proforma = mysqli_query($con, "SELECT * FROM tbl_proforma WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
		$row_proforma = mysqli_fetch_array($query_proforma);
		$proforma_rows = mysqli_num_rows($query_proforma);
		
		$status = $row_proforma['Status'];
		
		if($proforma_rows == 1){
			
			if($status == 1){
				
				$link = '<a href="#" title="Awaiting Proforma Invoice"><img src="../images/icons/invoice-grey.png" width="20" height="20" /></a>';
				
			} else {
				
				$link = '<a href="approved.php?Invoice='. $row_approved['Id'] .'" title="Generate Invoice"><img src="../images/icons/contractor-invoice.png" width="20" height="20" /></a>';
			}
			
		} else {
			
			$link = '<a href="approved.php?Invoice='. $row_approved['Id'] .'" title="Generate Invoice"><img src="../images/icons/contractor-invoice.png" width="20" height="20" /></a>';
		}
		?>
        <tr>
          <td class="td-grey"><?php echo $row_approved['Id']; ?></td>
          <td class="td-grey"><?php echo $row_approved['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_approved['BuyerEmail']; ?></td>
          <td class="td-grey"><?php echo $row_approved['Telephone']; ?></td>
          <td class="td-grey"><?php echo $row_approved['Date']; ?></td>
          <td width="22" align="center" class="td-grey" title="View">
            <a href="qs-calc.php?Id=<?php echo $row_approved['Id']; ?>" class="edit"></a>
          </td>
          <td width="22" align="center" class="td-grey" title="View">
            <a href="../fpdf/pdf-qs.php?Id=<?php echo $row_approved['Id']; ?>&Preview" target="_blank" class="pdf"></a>
          </td><td width="22" align="center" class="td-grey"><?php echo $link; ?></td></tr>
        <?php } ?>
        <tr>
          <td colspan="8">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /></td>
        </tr>
      </table>
      <?php } else { ?>
      <div align="center" class="welcome">Currently no approved quotes...</div>
<?php } ?>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>