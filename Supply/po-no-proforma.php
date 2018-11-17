<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$quoteno = $_GET['Id'];

$query = "
SELECT
	tbl_qs.Id,
	tbl_qs.CompanyId,
	tbl_companies.Account
FROM
	tbl_companies
INNER JOIN tbl_qs ON tbl_qs.CompanyId = tbl_companies.Id
WHERE
	tbl_qs.Id = '$quoteno'";
	
$query = mysqli_query($con, $query)or die(mysqli_error($con));

if(isset($_POST['next'])){
	
	$quoteno = $_GET['Id'];
	$po = $_POST['po-no'];
	
	mysqli_query($con, "UPDATE tbl_qs SET PO = '$po' WHERE Id = '$quoteno'")or die(mysqli_error($con));
	
	$query_invoice = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
	$row_invoice = mysqli_fetch_array($query_invoice);
	
	$rfqno = $row_invoice['RFQId'];
    $company = $row_invoice['CompanyId'];
    $date = date('Y-m-d');
	$duedate = $_POST['due-date'];
    $po = $row_invoice['PO'];
	$currency = $row_invoice['Currency'];
	$terms = $row_invoice['PaymentTerms'];
	
	mysqli_query($con, "INSERT INTO tbl_proforma (RFQId,QuoteNo,CompanyId,PO,Date,DueDate,Type,Currency,PaymentTerms) 
	VALUES ('$rfqno','$quoteno','$company','$po','$date','$duedate','2','$currency','$terms')")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_proforma ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$proformano = $row['Id'];
	
	mysqli_query($con, "UPDATE tbl_transport SET ProformaNo = '$proformano' WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	
	$query_items = mysqli_query($con, "SELECT * FROM tbl_qs_items WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	while($row_items = mysqli_fetch_array($query_items)){
		
		$productid = $row_items['ProductId'];
		$qty = $row_items['Qty'];
		$unit = $row_items['Unit'];
		$price = $row_items['Price'];
		$retail = $row_items['Retail'];
		$total = $row_items['Total'];
		
		mysqli_query($con, "INSERT INTO tbl_proforma_items (ProformaNo,QuoteNo,ProductId,Qty,Unit,Price,Retail,Total,Date) 
		VALUES ('$proformano','$quoteno','$productid','$qty','$unit','$price','$retail','$total','$date')")or die(mysqli_error($con));
		
	}
	
	header('Location: ../fpdf/pdf-proforma.php?Id='. $proformano .'&Type=2');
}


$quoteno = $_GET['Id'];
// Suppliers Query
$query_qued = "
SELECT
	tbl_qs.ExpiryDate
FROM
	tbl_qs
WHERE
	tbl_qs.Id = '$quoteno'";
	
$query_qued = mysqli_query($con, $query_qued)or die(mysqli_error($con));
$row_qued = mysqli_fetch_array($query_qued);

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
//-->
</script>

<script type="text/javascript" src="../scripts/custom-form-elements.js"></script>

<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="jquery-ui-sliderAccess.js"></script>

<link href="date.css" rel="stylesheet" type="text/css" />
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
                  <td>Supply Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Submitted</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="379" class="td-header-blue-dark">PO Number</td>
          <td width="379" class="td-header-blue-dark">Due Date</td>
        </tr>
        <tr>
          <td class="td-grey"><input name="po-no" type="text" class="tarea-100" id="po-no" /></td>
          <td class="td-grey">
          <input name="due-date" type="text" class="tarea-100" id="due-date" value="<?php echo $row_qued['ExpiryDate']; ?>" /></td>
          <script type="text/javascript">
		    $('#due-date').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          </td>
        </tr>
        <?php 
		$i = 0;
		while($row_qued = mysqli_fetch_array($query_qued)){ 
		$i++;
		?>
        <?php } ?>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="right"><input name="next" type="submit" class="btn" id="next" value="Generate Proforma Invoice" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>