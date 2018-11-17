<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);
	
$buyerid = $_GET['Company'];
$sourceid = $_GET['Id'];

if(isset($_POST['calculate'])){
	
	for($i=0;$i<count($_POST['id']);$i++){
		
		$id = $_POST['id'][$i];
		$qty = $_POST['qty'][$i];
		$unit = $_POST['unit'][$i];
		$price = $_POST['price'][$i];
		$total = $qty * $price;
		
		mysqli_query($con, "UPDATE tbl_rfq_items SET Qty = '$qty', Unit = '$unit', Price = '$price', Total = '$total' WHERE Id = '$id'")or die(mysqli_error($con));
	}
	
	mysqli_query($con, "UPDATE tbl_rfq_items SET Approved = '0' WHERE SourceId = '$sourceid'")or die(mysqli_error($con));
	
	for($i=0;$i<count($_POST['itemid']);$i++){
		
		$itemid = $_POST['itemid'][$i];
		
		mysqli_query($con, "UPDATE tbl_rfq_items SET Approved = '1' WHERE Id = '$itemid'")or die(mysqli_error($con));
	}
}

// Create Quote
if(isset($_POST['create'])){
	
	$query_rfq = mysqli_query($con, "SELECT * FROM tbl_rfq WHERE Id = '$sourceid'")or die(mysqli_error($con));
	$row_rfq = mysqli_fetch_array($query_rfq);
	
	$date = $row_rfq['Date'];
	$buyer = $row_rfq['CompanyId'];
	
	mysqli_query($con, "INSERT INTO tbl_qs (RFQId,CompanyId,Date,Status) VALUES ('$sourceid','$buyer','$date','1')")or die(mysqli_error($con));
	
	$query_quote_no = mysqli_query($con, "SELECT * FROM tbl_qs ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row_quote_no = mysqli_fetch_array($query_quote_no);
	
	$quoteno = $row_quote_no['Id'];

	$query_rfq = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SourceId = '$sourceid' AND Approved = '1'")or die(mysqli_error($con));
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
	mysqli_query($con, "UPDATE tbl_rfq SET Status = '3' WHERE Id = '$sourceid'")or die(mysqli_error($con));
	
	if(isset($_POST['itemid'])){
		
		header('Location: qs-calc.php?Id='. $quoteno);
	}
}

// Buyers Query
$query_buyer = "
SELECT
	tbl_rfq.Date,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.BuyerName,
	tbl_companies.BuyerEmail
FROM
	tbl_rfq
INNER JOIN tbl_companies ON tbl_rfq.CompanyId = tbl_companies.Id
WHERE
	tbl_rfq.CompanyId = '$buyerid'
ORDER BY tbl_rfq.Id DESC LIMIT 1";
	
$query_buyer = mysqli_query($con, $query_buyer)or die(mysqli_error($con));
$row_buyer = mysqli_fetch_array($query_buyer);

// Suppliers Query
$query_suppliers = "
SELECT
	tbl_rfq.Date,
	tbl_rfq.Id,
	tbl_rfq_items.Qty,
	tbl_rfq_items.ProductId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.RFQ,
	tbl_rfq_items.SourceId,
	tbl_products.`Name`,
	tbl_products.Grade,
	tbl_products.`Code`,
	tbl_companies.CompanyName,
	tbl_companies.Mobile,
	tbl_companies.SalesName,
	tbl_companies.SalesEmail,
	tbl_companies.Telephone
FROM
	tbl_rfq
INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
WHERE
	tbl_rfq.Id = '$sourceid'
GROUP BY 
    tbl_rfq_items.SupplierId";

$query_suppliers = mysqli_query($con, $query_suppliers)or die(mysqli_error($con));

$query_transporters = mysqli_query($con, "SELECT * FROM tbl_transport_companies ORDER BY Name ASC")or die(mysqli_error($con));

source_Reccomended($con, $sourceid);
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
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Awaiting Quotations</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="152" class="td-header-blue-dark">Enquired By</td>
          <td width="152" class="td-header-blue-dark">Company Name</td>
          <td width="152" class="td-header-blue-dark">Email</td>
          <td width="101" class="td-header-blue-dark">Telephone</td>
          <td width="101" class="td-header-blue-dark">Mobile</td>
          <td width="102" class="td-header-blue-dark">Date</td>
        </tr>
        <tr>
          <td class="td-grey"><?php echo $row_buyer['BuyerName']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['BuyerEmail']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['Telephone']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['Mobile']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['Date']; ?></td>
        </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        </table>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <?php while($row_suppliers = mysqli_fetch_array($query_suppliers)){ ?>
        <tr>
          <td colspan="10" class="td-header-blue-dark"><?php echo $row_suppliers['CompanyName']; ?></td>
          </tr>
        <tr>
          <td class="td-header-blue" width="200">Product</td>
          <td width="86" class="td-header-blue">Code</td>
          <td width="86" class="td-header-blue">Grade</td>
          <td width="86" class="td-header-blue">Pack Size</td>
          <td width="50" align="center" class="td-header-blue">Qty</td>
          <td width="50" align="center" class="td-header-blue">Unit</td>
          <td width="86" align="right" class="td-header-blue">Price</td>
          <td width="86" align="right" class="td-header-blue">Total</td>
          <td width="24" align="right" class="td-header-blue">&nbsp;</td>
        </tr>
        <?php
		
		$supplierid = $row_suppliers['SupplierId'];
		$query_products = "
		SELECT
		  tbl_rfq_items.SourceId,
		  tbl_rfq_items.Id,
		  tbl_rfq_items.ProductId,
		  tbl_rfq_items.SupplierId,
		  tbl_rfq_items.Qty,
		  tbl_rfq_items.Unit,
		  tbl_rfq_items.Price,
		  tbl_rfq_items.Total,
		  tbl_rfq_items.Reccomended,
		  tbl_rfq_items.Approved,
		  tbl_products.`Name`,
		  tbl_products.`Code`,
		  tbl_products.Grade,
		  tbl_products.PackSize
		FROM
		  tbl_rfq_items
		INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
		WHERE
		  tbl_rfq_items.SourceId = '$sourceid' AND
		  tbl_rfq_items.SupplierId = '$supplierid'";
		
		$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));
		while($row_products = mysqli_fetch_array($query_products)){
			
			if($row_products['Reccomended'] == 1){
				
				$style = 'style="color:#009933"';
				
			} else {
				
				$style = '';
			}
		?>
        <tr>
          <td class="td-grey" <?php echo $style; ?>>
          <label class="label" for="itemid_<?php echo $row_products['Id']; ?>">
          <input type="hidden" name="id[]" id="id[]" value="<?php echo $row_products['Id']; ?>" />
          <?php echo $row_products['Name']; ?>
          </label></td>
          <td <?php echo $style; ?> class="td-grey">
		  <label class="label" for="itemid_<?php echo $row_products['Id']; ?>">
		  <?php echo $row_products['Code']; ?>
          </label></td>
          <td <?php echo $style; ?> class="td-grey">
		  <label class="label" for="itemid_<?php echo $row_products['Id']; ?>">
		  <?php echo $row_products['Grade']; ?>
          </label></td>
          <td <?php echo $style; ?> class="td-grey">
		  <label class="label" for="itemid_<?php echo $row_products['Id']; ?>">
		  <?php echo $row_products['PackSize']; ?>
          </label></td>
          <td class="td-grey">
          <input <?php echo $style; ?> name="qty[]" type="text" class="tarea-qty" id="qty[]" value="<?php echo $row_products['Qty']; ?>" />
          </td><td class="td-grey">
          <input <?php echo $style; ?> name="unit[]" type="text" class="tarea-qty" id="unit[]" value="<?php echo $row_products['Unit']; ?>" />
          </td><td align="right" class="td-grey">
          <input <?php echo $style; ?> name="price[]" type="text" class="tarea-price" id="price[]" onfocus="if(this.value=='0.00'){this.value=''}" onblur="if(this.value==''){this.value='0.00'}" value="<?php echo $row_products['Price']; ?>" />
          </td><td align="right" class="td-grey">
          <input <?php echo $style; ?> name="total[]" type="text" class="tarea-price" id="total[]" value="<?php echo $row_products['Total']; ?>" />
          </td>
          <td align="right" class="td-grey">
          <input type="checkbox" name="itemid[]" id="itemid_<?php echo $row_products['Id']; ?>" value="<?php echo $row_products['Id']; ?>" <?php if($row_products['Approved'] == 1){ echo 'checked="checked"'; } ?> />
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="6" rowspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue-dark">Sub Total</td>
          <td colspan="2" align="right" class="td-header-blue-dark"><?php sourcing_subtotal($con, $sourceid, $supplierid); ?></td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue">VAT</td>
          <td colspan="2" align="right" class="td-header-blue"><?php sourcing_vat($con, $sourceid, $supplierid); ?></td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue-dark">Total </td>
          <td colspan="2" align="right" class="td-header-blue-dark"><?php sourcing_total($con, $sourceid, $supplierid); ?></td>
        </tr>
        <tr>
          <td colspan="10">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="10">&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="10" align="right">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" />  
          <?php if(isset($_POST['calculate']) && ISSET($_POST['itemid'])){ ?>          
          <input name="create" type="submit" class="btn-red" id="submit" value="Create Quote" />
          <?php } ?>
          <input name="calculate" type="submit" class="btn" id="calculate" value="Calculate" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>