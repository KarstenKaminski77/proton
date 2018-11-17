<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

if(isset($_POST['submit']) && $_POST['qty'] != 0){
	
	$id = $_GET['Id'];
	$qty = $_POST['qty'];
	$date = date('Y-m-d');
	$buyer = $_POST['supplierid'];
	
	mysqli_query($con, "UPDATE tbl_offers SET Closed = '1' WHERE RFQId = '$id'")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE Id = '$id'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$rfqid = $row['SourceId'];
	
	$query = "
	SELECT
		tbl_rfq.Id,
		tbl_offers.Closed
	FROM
		tbl_rfq
	INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
	INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
	WHERE
		tbl_rfq.Id = '$rfqid' AND tbl_offers.Closed = '0'";
		
	$query = mysqli_query($con, $query)or die(mysqli_query($con));
	$rows = mysqli_num_rows($query);
	
	if($rows == 0){
		
		mysqli_query($con, "UPDATE tbl_rfq SET Status = '3' WHERE Id = '$rfqid'")or die(mysqli_error($con));
	}
	
	mysqli_query($con, "UPDATE tbl_rfq_items SET Qty = '$qty', Approved = '1' WHERE Id = '$id'")or die(mysqli_error($con));
	
	mysqli_query($con, "INSERT INTO tbl_rfq (CompanyId,Date,Status,Type) VALUES ('4','$date','2','2')")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_rfq ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$rfqid = $row['Id'];
	
	$query_details = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE Id = '$id'")or die(mysqli_error($con));
	$row_details = mysqli_fetch_array($query_details);
	
	
	$productid  = $row_details['ProductId'];
	$date = date('Y-m-d');
	
	mysqli_query($con, "INSERT INTO tbl_rfq_items (SourceId,SupplierId,ProductId,Qty,Approved) VALUES ('$rfqid','$buyer','$productid','$qty','1')")or die(mysqli_error($con));
	
	mysqli_query($con, "INSERT INTO tbl_qs (RFQId,OldRFQId,CompanyId,Date,Status,Type) VALUES ('$rfqid','$id','4','$date','1','2')")or die(mysqli_error($con));
	
	$query_quote_no = mysqli_query($con, "SELECT * FROM tbl_qs ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row_quote_no = mysqli_fetch_array($query_quote_no);
	
	$quoteno = $row_quote_no['Id'];

	$query_rfq = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SourceId = '$rfqid' AND Approved = '1'")or die(mysqli_error($con));
	while($row_rfq = mysqli_fetch_array($query_rfq)){
		
		$supplier = $row_rfq['SupplierId'];
		$product = $row_rfq['ProductId'];
		$qty = $row_rfq['Qty'];
		$unit = $row_rfq['Unit'];
		$price = $row_rfq['Price'];
		$total = $row_rfq['Total'];
		$date = date('Y-m-d');
		
		mysqli_query($con, "INSERT INTO tbl_qs_items (QuoteNo,SupplierId,ProductId,Qty,Unit,Price,Total,Date) VALUES 
	    ('$quoteno','$supplier','$product','$qty','$unit','$price','$total','$date')")or die(mysqli_error($con));
	}
	
	mysqli_query($con, "INSERT INTO tbl_transport (QuoteNo) VALUES ('$quoteno')")or die(mysqli_error($con));
	
		
	header('Location: rfq-confirm.php?Id='. $quoteno);
}

if(isset($_POST['submit']) && $_POST['qty'] == 0){
	
	$class = 'style="border: solid 2px #FF0000; color: #FF0000"';
}

	
// Suppliers Query

$id = $_GET['Id'];
			
$query_rfq = "
SELECT
tbl_rfq_items.SourceId,
tbl_rfq_items.ProductId,
tbl_rfq_items.SupplierId,
tbl_rfq_items.Qty,
tbl_rfq_items.Unit,
tbl_products.`Name`,
tbl_products.`Code`,
tbl_products.Grade,
 tbl_products.PackSize,
 tbl_offers.Notes,
 tbl_offers.RFQId,
 tbl_companies.CompanyName
FROM
	tbl_rfq_items
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_offers ON tbl_offers.RFQId = tbl_rfq_items.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
WHERE
	tbl_offers.RFQId = '$id'";
	
$query_rfq = mysqli_query($con, $query_rfq)or die(mysqli_error($con));
$row_rfq = mysqli_fetch_array($query_rfq);
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

<script type="text/javascript" src="../scripts/custom-form-elements2.js"></script>

<link href="../css/form-elements2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="header">
  <div id="logo"></div>
</div>
<div id="container">
  <div id="right-container-closed">
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
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Supply New Product</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
        <table width="1000" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td colspan="3" class="td-header-blue-dark"><?php echo $row_rfq['CompanyName']; ?></td>
          </tr>
        <tr>
          <td class="td-header-blue">Product</td>
          <td width="100" class="td-header-blue">Qty</td>
          <td width="100" class="td-header-blue">Unit</td>
          </tr>
        <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
          <td class="td-grey"><?php echo $row_rfq['Name']; ?></td>
          <td class="td-grey"><input name="qty" type="text" class="tarea-100" <?php echo $class; ?> id="qty" onfocus="if(this.value=='0'){this.value=''}" onblur="if(this.value==''){this.value='0'}" value="0" /></td>
          <td class="td-grey"><?php echo $row_rfq['Unit']; ?></td>
          </tr>
        <tr>
          <td colspan="3" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" align="right">
          <input type="hidden" name="supplierid" id="supplierid" value="<?php echo $row_rfq['SupplierId']; ?>" />
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" />            
          <input name="submit" type="submit" class="btn" id="submit" value="Submit Request For Quote" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>