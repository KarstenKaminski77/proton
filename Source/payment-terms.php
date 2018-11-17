<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(isset($_POST['po-no'])){
	
	$po = $_POST['po-no'];
	$delivery_date = $_POST['delivery-date'];
	$terms_d = $_POST['terms-d'];
	$terms_p = $_POST['terms-p'];
	$quoteno = $_GET['Id'];
	
	mysqli_query($con, "UPDATE tbl_qs SET PO = '$po', Status = '3' WHERE Id = '$quoteno'")or die(mysqli_error($con));
	
	$query_rfq = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
	$row_rfq = mysqli_fetch_array($query_rfq);
	
	$rfqid = $row_rfq['RFQId'];																						 
	
	$query_suppliers = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SourceId = '$rfqid' AND Approved = '1' GROUP BY SupplierId")or die(mysqli_error($con));
	while($row_suppliers = mysqli_fetch_array($query_suppliers)){
		
		$companyid = $row_suppliers['SupplierId'];
		$date = date('Y-m-d');
		
		mysqli_query($con, "INSERT INTO tbl_po (QuoteNo,RFQNo,CompanyId,Date,DeliveryDate,DeliveryTerms,PaymentTerms,Status) 
		VALUES ('$quoteno','$rfqid','$companyid','$date','$delivery_date','$terms_d','$terms_p','1')")or die(mysqli_error($con));
		
		$query_po = mysqli_query($con, "SELECT * FROM tbl_po ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
		$row_po = mysqli_fetch_array($query_po);
		
		$pono = $row_po['Id'];
		
		$query_items = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SupplierId = '$companyid' AND Approved = '1'")or die(mysqli_error($con));
		while($row_query_items = mysqli_fetch_array($query_items)){
			
			$productid = $row_query_items['ProductId'];
			$qty = $row_query_items['Qty'];
			$unit = $row_query_items['Unit'];
			$price = $row_query_items['Price'];
			$total = $row_query_items['Total'];
			
			mysqli_query($con, "INSERT INTO tbl_po_items (SupplierId,ProductId,Qty,Unit,Price,Total) 
			VALUES ('$companyid','$productid','$qty','$unit','$price','$total')")or die(mysqli_error($con));
		}
	}
	
	header('Location: ../fpdf/pdf-po.php?Id='. $rfqid);
}

if(isset($_GET['Reject'])){
	
	$quoteno = $_GET['Reject'];
	
	mysqli_query($con, "UPDATE tbl_qs SET Status = '4' WHERE Id = '$qouteno'")or die(mysqli_error($con));
}

// Suppliers Query
$query_qued = "
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
	tbl_qs. STATUS = '2'
ORDER BY
	tbl_qs.Date DESC";
	
$query_qued = mysqli_query($con, $query_qued)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_qued);

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
                  <td>Source Products</td>
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
          <td width="50%" class="td-header-blue-dark">Payment Terms</td>
          <td width="50%" class="td-header-blue-dark">Due Date</td>
        </tr>
        <tr>
          <td class="td-grey"><input name="terms" type="text" class="tarea-100" id="terms" /></td>
          <td class="td-grey"><input name="due-date" type="text" class="tarea-100" id="due-date" value="<?php echo $row_buyer['ExpiryDate']; ?>" /></td>
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
          <td colspan="4" align="right"><input name="next" type="submit" class="btn" id="next" value="Next" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>