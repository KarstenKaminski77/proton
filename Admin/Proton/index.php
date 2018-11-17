<?php
// Connect To The Database
require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

if(isset($_POST)){
	
	$company = $_POST['company'];
	$physical_address = $_POST['phys-address'];
	$postal_address = $_POST['postal-address'];
	$delivery_address = $_POST['delivery-address'];
	$telephone = $_POST['telephone'];
	$fax = $_POST['fax'];
	$mobile = $_POST['mobile'];
	$buyer_name = $_POST['buyer-name'];
	$buyer_email = $_POST['buyer-email'];
	$sales_name = $_POST['sales-name'];
	$sales_email = $_POST['sales-email'];
	$accounts_name = $_POST['accounts-name'];
	$accounts_email = $_POST['accounts-email'];
	$vat = $_POST['vat'];
	$supplier = $_POST['supplier'];
	$customer = $_POST['customer'];
	$proton_account = $_POST['proton-account'];
		
	// Update Product
	if(isset($_POST['update'])){
				
		mysqli_query($con, "UPDATE tbl_companies SET CompanyName = '$company', PhysicalAddress = '$physical_address', PostalAddress = '$postal_address', DeliveryAddress = '$delivery_address', 
		Telephone = '$telephone', Fax = '$fax', Mobile = '$mobile', BuyerName = '$buyer_name', BuyerEmail = '$buyer_email', SalesName = '$sales_name', SalesEmail = '$sales_email', AccountsName = '$accounts_name', 
		AccountsEmail = '$accounts_email', VAT = '$vat', Supplier = '$supplier', Customer = '$customer', Account = '$proton_account' WHERE Id = '4'")or die(mysqli_error($con));
		
	}
}
$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_form = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '4'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../../fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="../../sdmenu/sdmenu.js"></script>
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

</head>

<body>
<div id="header">
  <div id="logo">
   <a class="close" href="<?php echo $_SERVER['../../REQUEST_URI'] .'?Logout'; ?>"></a>
   <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
   <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../../menu.php'); ?>
  <div id="right-container">
    <form id="form1" name="form1" method="post" action="">
<div id="breadcrumbs">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><a href="../../index.php" class="breadcumbs">Home</a></td>
          <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
          <td>Administration</td>
          <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
          <td><a href="index.php" class="breadcumbs">Proton Details</a></td>
          <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
          </tr>
        </table></td>
      </tr>
  </table>
</div>
<table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" class="td-left"><em><strong>Company Name</strong></em></td>
          <td width="757" class="td-right"><input name="company" type="text" class="tarea-100" id="company" value="<?php echo $row_form['CompanyName']; ?>" /></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Physical Address</strong></em></td>
          <td class="td-right"><textarea name="phys-address" rows="4" class="tarea-100" id="phys-address"><?php echo $row_form['PhysicalAddress']; ?></textarea></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Postal Address</strong></em></td>
          <td class="td-right"><textarea name="postal-address" rows="4" class="tarea-100" id="postal-address"><?php echo $row_form['PostalAddress']; ?></textarea></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Delivery Address</strong></em></td>
          <td class="td-right"><textarea name="delivery-address" rows="4" class="tarea-100" id="delivery-address"><?php echo $row_form['DeliveryAddress']; ?></textarea></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Telephone</strong></em></td>
          <td class="td-right"><input name="telephone" type="text" class="tarea-100" id="telephone" value="<?php echo $row_form['Telephone']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Fax</em></td>
          <td class="td-right"><input name="fax" type="text" class="tarea-100" id="fax" value="<?php echo $row_form['Fax']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Mobile</strong></em></td>
          <td class="td-right"><input name="mobile" type="text" class="tarea-100" id="mobile" value="<?php echo $row_form['Mobile']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Buyer Name</strong></em></td>
          <td class="td-right"><input name="buyer-name" type="text" class="tarea-100" id="buyer-name" value="<?php echo $row_form['BuyerName']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Buyer Email</strong></em></td>
          <td class="td-right"><input name="buyer-email" type="text" class="tarea-100" id="buyer-email"  value="<?php echo $row_form['BuyerEmail']; ?>"/></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Sales Name</strong></em></td>
          <td class="td-right"><input name="sales-name" type="text" class="tarea-100" id="sales-name" value="<?php echo $row_form['SalesName']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Sales Email</strong></em></td>
          <td class="td-right"><input name="sales-email" type="text" class="tarea-100" id="sales-email" value="<?php echo $row_form['SalesEmail']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Accounts Name</strong></em></td>
          <td class="td-right"><input name="accounts-name" type="text" class="tarea-100" id="accounts-name" value="<?php echo $row_form['AccountsName']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Accounts Email</strong></em></td>
          <td class="td-right"><input name="accounts-email" type="text" class="tarea-100" id="accounts-email" value="<?php echo $row_form['AccountsEmail']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>VAT</strong></em></td>
          <td class="td-right"><input name="vat" type="text" class="tarea-100" id="vat" value="<?php echo $row_form['VAT']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="2" align="right">
            <input name="update" type="submit" class="btn" id="update" value="Update" />
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>