<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 
$quoteno = $_GET['Id'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);
	
$quoteno = $_GET['Id'];

if(isset($_POST['calculate'])){
	
	for($i=0;$i<count($_POST['id']);$i++){
		
		$id = $_POST['id'][$i];
		$qty = $_POST['qty'][$i];
		$unit = $_POST['unit'][$i];
		$price = $_POST['price'][$i];
		$currency = $_POST['currency'];
		$retail = $price + $_POST['transport'];
		$total = $qty * $retail;
		$transport_cost = $_POST['transport'];
		
		mysqli_query($con, "UPDATE tbl_qs_items SET Qty = '$qty', Unit = '$unit', Price = '$price', Total = '$total', Retail = '$retail' WHERE Id = '$id'")or die(mysqli_error($con));
		
	}
	
	$transporterid = $_POST['transporterid'];
	$transport_cost = $_POST['transport'];
		
	mysqli_query($con, "UPDATE tbl_transport SET TransporterId = '$transporterid', Price = '$transport_cost' WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$old_rfq = $row['OldRFQId'];
	
	mysqli_query($con, "UPDATE tbl_offers SET Closed = '1' WHERE RFQId = '$old_rfq'")or die(mysqli_error($con));
	
	$terms_d = $_POST['terms-d'];
	$expire = $_POST['expire'];
	$delivery_date = $_POST['delivery-date'];
	$terms_p = $_POST['terms-p'];
	$conditions = $_POST['conditions'];
	
	mysqli_query($con, "UPDATE tbl_qs SET DeliveryTerms = '$terms_d', DeliveryDate = '$delivery_date', ExpiryDate = '$expire', PaymentTerms = '$terms_p', SpecialConditions = '$conditions', Currency = '$currency' 
	WHERE Id = '$quoteno'")or die(mysqli_error($con));
}

//Create PDF
if(isset($_POST['create'])){
	
	header('Location: ../fpdf/pdf-qs.php?Id='. $quoteno .'&Type=2');
}


// Buyers Query
$query_buyer = "
SELECT
	tbl_qs.Id,
	tbl_qs.Date,
	tbl_qs.DeliveryTerms,
	tbl_qs.DeliveryDate,
	tbl_qs.ExpiryDate,
	tbl_qs.PaymentTerms,
	tbl_qs.SpecialConditions,
	tbl_qs.Currency,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Fax,
	tbl_companies.Mobile,
	tbl_companies.BuyerName,
	tbl_companies.BuyerEmail
FROM
	tbl_qs
INNER JOIN tbl_qs_items ON tbl_qs.Id = tbl_qs_items.QuoteNo
INNER JOIN tbl_companies ON tbl_qs_items.SupplierId = tbl_companies.Id
WHERE
	tbl_qs.Id = '$quoteno'
ORDER BY tbl_qs.Id DESC LIMIT 1";
	
$query_buyer = mysqli_query($con, $query_buyer)or die(mysqli_error($con));
$row_buyer = mysqli_fetch_array($query_buyer);

$query_products = "
SELECT
	tbl_products.`Name`,
	tbl_qs_items.Id,
	tbl_qs_items.Qty,
	tbl_qs_items.Unit,
	tbl_qs_items.Price,
	tbl_qs_items.Total
FROM
	tbl_qs_items
INNER JOIN tbl_products ON tbl_qs_items.ProductId = tbl_products.Id
WHERE
	tbl_qs_items.QuoteNo = '$quoteno'";
	
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

$query_transporters = mysqli_query($con, "SELECT * FROM tbl_transport_companies ORDER BY Name ASC")or die(mysqli_error($con));

$query_transport = mysqli_query($con, "SELECT * FROM tbl_transport WHERE QuoteNo = '$quoteno'")or die(mysqli_error($con));
$row_transport = mysqli_fetch_array($query_transport);

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
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Qued</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Ref No: <?php echo $_GET['Id']; ?></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="152" class="td-header-blue">Quotation To</td>
          <td width="152" class="td-header-blue">Attrention</td>
          <td width="152" class="td-header-blue">Email</td>
          <td width="101" class="td-header-blue">Telephone</td>
          <td width="101" class="td-header-blue">Mobile</td>
          <td width="102" class="td-header-blue">Date</td>
        </tr>
        <tr>
          <td class="td-grey"><?php echo $row_buyer['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_buyer['BuyerName']; ?></td>
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
      <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="190" class="td-header-blue">Delivery Terms</td>
          <td width="190" class="td-header-blue">Offer Expires</td>
          <td width="190" class="td-header-blue">Delivery Date</td>
          <td width="190" class="td-header-blue">Payment Terms</td>
        </tr>
        <tr>
          <td class="td-grey"><input name="terms-d" type="text" class="tarea-100" id="terms-d" value="<?php echo $row_buyer['DeliveryTerms']; ?>" /></td>
          <td class="td-grey"><input name="expire" type="text" class="tarea-100" id="expire" value="<?php echo $row_buyer['ExpiryDate']; ?>" /></td>
          <script type="text/javascript">
		    $('#expire').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          <td class="td-grey"><input name="delivery-date" type="text" class="tarea-100" id="delivery-date" value="<?php echo $row_buyer['DeliveryDate']; ?>" /></td>
          <script type="text/javascript">
		    $('#delivery-date').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          <td class="td-grey"><input name="terms-p" type="text" class="tarea-100" id="terms-p" value="<?php echo $row_buyer['PaymentTerms']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td class="td-header-blue">Currency</td>
          </tr>
          <tr>
            <td class="td-grey"><input name="currency" type="text" class="tarea-100" id="currency" value="<?php echo $row_buyer['Currency']; ?>" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td colspan="2" class="td-header-blue">Special Conditions</td>
          </tr>
        <tr>
          <td class="td-grey"><textarea name="conditions" rows="3" class="tarea-100" id="conditions"><?php echo $row_buyer['SpecialConditions']; ?></textarea></td>
          </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td colspan="2" class="td-header-blue">Transport Company</td>
          <td colspan="4" class="td-header-blue">Transport Cost</td>
          </tr>
        <tr>
          <td colspan="2" class="td-grey">
          <select name="transporterid" class="tarea-100" id="transporterid">
            <? while($row_transporters = mysqli_fetch_array($query_transporters)){ ?>
            <option value="<?php echo $row_transporters['Id']; ?>" <?php if($row_transport['TransporterId'] == $row_transporters['Id']){ echo 'selected="selected"'; } ?>><?php echo $row_transporters['Name']; ?></option>
            <?php } ?>
          </select></td>
          <td colspan="4" class="td-grey"><input name="transport" type="text" class="tarea-100" id="transport" onfocus="if(this.value=='0.00'){this.value=''}" onblur="if(this.value==''){this.value='0.00'}" value="<?php echo $row_transport['Price']; ?>" /></td>
          </tr>
        <tr>
          <td colspan="7" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="7">&nbsp;</td>
        </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td class="td-header-blue">Product</td>
          <td width="50" align="center" class="td-header-blue">Qty</td>
          <td width="50" align="center" class="td-header-blue">Unit</td>
          <td width="86" align="right" class="td-header-blue">Price</td>
          <td width="86" align="right" class="td-header-blue">Total</td>
          </tr>
        <?php while($row_products = mysqli_fetch_array($query_products)){ ?>
        <tr>
          <td class="td-grey" <?php echo $style; ?>>
            <label class="label" for="itemid_<?php echo $row_products['Id']; ?>">
              <input type="hidden" name="id[]" id="id[]" value="<?php echo $row_products['Id']; ?>" />
              <?php echo $row_products['Name']; ?>
          </label></td>
          <td class="td-grey">
            <input <?php echo $style; ?> name="qty[]" type="text" class="tarea-qty" id="qty[]" value="<?php echo $row_products['Qty']; ?>" />
          </td><td class="td-grey">
          <input <?php echo $style; ?> name="unit[]" type="text" class="tarea-qty" id="unit[]" value="<?php echo $row_products['Unit']; ?>" />
          </td><td align="right" class="td-grey">
          <input <?php echo $style; ?> name="price[]" type="text" class="tarea-price" id="price[]" onfocus="if(this.value=='0.00'){this.value=''}" onblur="if(this.value==''){this.value='0.00'}" value="<?php echo $row_products['Price']; ?>" />
          </td><td align="right" class="td-grey">R<?php echo $row_products['Total']; ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td colspan="3" rowspan="5" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue-dark">Sub Total</td>
          <td align="right" class="td-header-blue-dark">R<?php qs_subtotal($con, $quoteno); ?></td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue">VAT</td>
          <td align="right" class="td-header-blue">R<?php qs_vat($con, $quoteno, $row_transport['Price']); ?></td>
        </tr>
        <tr>
          <td align="right" class="td-header-blue-dark">Total </td>
          <td align="right" class="td-header-blue-dark">R<?php qs_total($con, $quoteno, $row_transport['Price']); ?></td>
        </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="6" align="right">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /> 
          <?php if($_POST['transport'] >= 0.01){ ?>
          <input name="create" type="submit" class="btn-red" id="submit" value="Submit Quote" />
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
<?php mysqli_close($con); ?>