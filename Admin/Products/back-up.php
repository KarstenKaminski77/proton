<?php
// Connect To The Database
require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

$id = $_GET['Edit'];

if(isset($_POST)){
	
	$company = $_POST['company'];
	$physical_address = $_POST['phys-address'];
	$postal_address = $_POST['postal-address'];
	$delivery_address = $_POST['delivery-address'];
	$telephone = $_POST['telephone'];
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
	
	// Insert New Product
	if(isset($_POST['insert'])){
		
		mysqli_query($con, "INSERT INTO tbl_companies (CompanyName,PhysicalAddress,PostalAddress,DeliveryAddress,Telephone,Mobile,BuyerName,BuyerEmail,SalesName,SalesEmail,
		AccountsName,AccountsEmail,VAT,Supplier,Customer)
		VALUES
	   ('$company','$physical_address','$postal_address','$delivery_address','$telephone','$mobile','$buyer_name','$buyer_email','$sales_name','$sales_email','$accounts_name','$accounts_email','$vat','$supplier','$customer')")or die(mysqli_error($co));
		
		// Get Company Id
		$query_company = mysqli_query($con, "SELECT * FROM tbl_companies ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
		$row_company = mysqli_fetch_array($query_company);
		
		$companyid = $row_company['Id'];
		
		// Insert Company & Products To Relational Table
		$query_products = mysqli_query($con, "SELECT tbl_products.`Name`, tbl_industries.Industry, tbl_product_industry_relation.ProductId, tbl_product_industry_relation.IndustryId
	    FROM tbl_product_industry_relation
		INNER JOIN tbl_industries ON tbl_product_industry_relation.IndustryId = tbl_industries.Id
		INNER JOIN tbl_products ON tbl_product_industry_relation.ProductId = tbl_products.Id
		ORDER BY tbl_industries.Industry ASC, tbl_products.`Name` ASC")or die(mysqli_error($con));
		while($row_products = mysqli_fetch_array($query_products)){
			
			$productid = $row_products['ProductId'];
			$industryid = $row_products['IndustryId'];
			
			mysqli_query($con, "INSERT INTO tbl_product_company_relation (ProductId,IndustryId,CompanyId) VALUES ('$productid','$industryid','$companyid')")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Buyer
		for($i=0;$i<count($_POST['buyer']);$i++){
			
			$buyer = $_POST['buyer'][$i];
			$industry = $_POST['industry'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Buyer = '1' WHERE IndustryId = '$industry' AND ProductId = '$buyer' AND CompanyId = '$companyid'")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Supplier
		for($i=0;$i<count($_POST['supplier']);$i++){
			
			$supplier = $_POST['supplier'][$i];
			$industry = $_POST['industry'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Supplier = '1' WHERE IndustryId = '$industry' AND ProductId = '$supplier' AND CompanyId = '$companyid'")or die(mysqli_error($con));
		}
		
		header('Location: index.php');
		
	}
	
	// Update Product
	if(isset($_POST['update'])){
		
		$id = $_GET['Edit'];
		
		mysqli_query($con, "UPDATE tbl_companies SET CompanyName = '$company', PhysicalAddress = '$physical_address', PostalAddress = '$postal_address', DeliveryAddress = '$delivery_address', 
		Telephone = '$telephone', Mobile = '$mobile', BuyerName = '$buyer_name', BuyerEmail = '$buyer_email', SalesName = '$sales_name', SalesEmail = '$sales_email', AccountsName = '$accounts_name', 
		AccountsEmail = '$accounts_email', VAT = '$vat', Supplier = '$supplier', Customer = '$customer' WHERE Id = '$id'")or die(mysqli_error($con));
		
		mysqli_query($con, "UPDATE tbl_product_company_relation SET Buyer = '0', Supplier = '0' WHERE CompanyId = '$id'")or die(mysqli_error($con));
		
		// Update Relational Table - Buyer
		for($i=0;$i<count($_POST['buyer']);$i++){
			
			$buyer = $_POST['buyer'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Buyer = '1' WHERE ProductId = '$buyer' AND CompanyId = '$id'")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Supplier
		for($i=0;$i<count($_POST['supplier']);$i++){
			
			$supplier = $_POST['supplier'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Supplier = '1' WHERE ProductId = '$supplier' AND CompanyId = '$id'")or die(mysqli_error($con));
		}
	}
	
	// Delete Product
	if(isset($_GET['Delete'])){
		
		$id = $_GET['Delete'];
		
		mysqli_query($con, "DELETE FROM tbl_companies WHERE Id = '$id'")or die(mysqli_error($con));
		
		mysqli_query($con, "DELETE FROM tbl_product_company_relation WHERE CompanyId = '$id'")or die(mysqli_error($con));
		
		header('Location: index.php');
	}
}

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

/////////////////////////////
/// PAGER //////////////////
///////////////////////////

$query_companies = mysqli_query($con, "SELECT * FROM tbl_companies")or die(mysqli_error($con));
$total_items = mysqli_num_rows($query_companies);
$per_page = '50';

offset($total_items, $per_page);

$offset = $_SESSION['offset']; 
$pages = $_SESSION['pages'];

if(!empty($_POST['filter'])){
	
	$filter = $_POST['filter'];
	
	$where = "WHERE tbl_companies.CompanyName LIKE '%$filter%'";
}

$query_list = mysqli_query($con, "SELECT * FROM tbl_companies $where ORDER BY CompanyName ASC")or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

$query_products = mysqli_query($con, "SELECT tbl_products.`Name`, tbl_industries.Industry, tbl_product_industry_relation.ProductId, tbl_product_industry_relation.IndustryId
FROM tbl_product_industry_relation
INNER JOIN tbl_industries ON tbl_product_industry_relation.IndustryId = tbl_industries.Id
INNER JOIN tbl_products ON tbl_product_industry_relation.ProductId = tbl_products.Id
ORDER BY tbl_industries.Industry ASC, tbl_products.`Name` ASC")or die(mysqli_error($con));
$numrows_products = mysqli_num_rows($query_products);
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
          <td><a href="index.php" class="breadcumbs">Clients | Suppliers</a></td>
          <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
          </tr>
        </table></td>
      </tr>
  </table>
</div>
<table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" class="td-left"><em><strong>Company Name</strong></em></td>
          <td width="757" class="td-right"><input name="company" type="text" class="tarea-100" id="company" value="<?php default_value($_POST['company'], $row_form['CompanyName']); ?>" /></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Physical Address</strong></em></td>
          <td class="td-right"><textarea name="phys-address" rows="4" class="tarea-100" id="phys-address"><?php default_value($_POST['physical-address'], $row_form['PhysicalAddress']); ?></textarea></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Postal Address</strong></em></td>
          <td class="td-right"><textarea name="postal-address" rows="4" class="tarea-100" id="postal-address"><?php default_value($_POST['postal-address'], $row_form['PostalAddress']); ?></textarea></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em><strong>Delivery Address</strong></em></td>
          <td class="td-right"><textarea name="delivery-address" rows="4" class="tarea-100" id="delivery-address"><?php default_value($_POST['delivery-address'], $row_form['DeliveryAddress']); ?></textarea></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Telephone</strong></em></td>
          <td class="td-right"><input name="telephone" type="text" class="tarea-100" id="telephone" value="<?php default_value($_POST['telephone'], $row_form['Telephone']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Mobile</strong></em></td>
          <td class="td-right"><input name="mobile" type="text" class="tarea-100" id="mobile" value="<?php default_value($_POST['mobile'], $row_form['Mobile']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Buyer Name</strong></em></td>
          <td class="td-right"><input name="buyer-name" type="text" class="tarea-100" id="buyer-name" value="<?php default_value($_POST['buyer-name'], $row_form['BuyerName']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Buyer Email</strong></em></td>
          <td class="td-right"><input name="buyer-email" type="text" class="tarea-100" id="buyer-email"  value="<?php default_value($_POST['buyer-email'], $row_form['BuyerEmail']); ?>"/></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Sales Name</strong></em></td>
          <td class="td-right"><input name="sales-name" type="text" class="tarea-100" id="sales-name" value="<?php default_value($_POST['sales-name'], $row_form['SalesName']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Sales Email</strong></em></td>
          <td class="td-right"><input name="sales-email" type="text" class="tarea-100" id="sales-email" value="<?php default_value($_POST['sales-email'], $row_form['SalesEmail']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Accounts Name</strong></em></td>
          <td class="td-right"><input name="accounts-name" type="text" class="tarea-100" id="accounts-name" value="<?php default_value($_POST['accounts-name'], $row_form['AccountsName']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>Accounts Email</strong></em></td>
          <td class="td-right"><input name="accounts-email" type="text" class="tarea-100" id="accounts-email" value="<?php default_value($_POST['accounts-email'], $row_form['AccountsEmail']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em><strong>VAT</strong></em></td>
          <td class="td-right"><input name="vat" type="text" class="tarea-100" id="vat" value="<?php default_value($_POST['vat'], $row_form['VAT']); ?>" /></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em>Products</em></td>
          <td class="td-right">
          <?php 
		  $i = 1;
		  $c = 1;
		  $a = 1;
		  while($row_products = mysqli_fetch_array($query_products)){ 
		  $i++;
		  $c++;
		  $a++;
		  		  
		  $curvalue = NULL;
		  			  
		  $curValue = $row_products['Industry'];
			  
		  if ($i % 2 == 0) {
			  
			  $id = 'product-list-odd';
			  
		  } else {
			  
			  $id = 'product-list';
		  }
		  
		  if($i == $numrows_products){
			  
			  $style = 'style="border:none; margin-bottom:20px"';
		  }
			  
			  if($curValue != $prevValue){
				  
				  $i = 1;
				  
				  $id = 'product-list';
				  
		  ?>
          
            <!-- Industry -->
            <div id="industry-header">
            <label for="industry_<?php echo $a; ?>">
              <table width="100%" border="0" cellpadding="2" cellspacing="3">
                <tr>
                  <td><?php echo $row_products['Industry']; ?>
                  </td>
                  <td align="right"><div id="industry-check"></div></td>
                </tr>
              </table>
              </label>
            </div>
            <!-- End Industry -->
            
            <?php
			}
			$prevValue = $curValue;
			
			$companyid = $_GET['Edit'];
			$industryid = $row_products['IndustryId'];
			$productid = $row_products['ProductId'];
			
			$query = mysqli_query($con, "SELECT * FROM tbl_product_company_relation WHERE CompanyId = '$companyid' AND ProductId = '$productid' AND IndustryId = '$industryid'")or die(mysqli_error($con));
			$row = mysqli_fetch_array($query); 
			?>            
          <input name="industry[]" type="hidden" id="industry[]" value="<?php echo $row_products['IndustryId']; ?>" />
          <div id="<?php echo $id; ?>" <?php echo $style; ?>>
            <table border="0" cellpadding="2" cellspacing="3">
              <tr>
                <td width="120"><?php echo $row_products['Name']; ?></td>
                <td width="20">&nbsp;</td>
                <td><label for="check_1_<?php echo $c; ?>">Supplier</label></td>
                <td width="15"><input type="checkbox" name="supplier[]" id="check_1_<?php echo $c; ?>" value="<?php echo $row_products['ProductId']; ?>" <?php if($row['Supplier'] == 1){ echo 'checked="checked"'; } ?> /></td>
                <td width="40" align="right"><label for="check_2_<?php echo $c; ?>">Buyer</label></td>
                <td width="15"><input type="checkbox" name="buyer[]" id="check_2_<?php echo $c; ?>" value="<?php echo $row_products['ProductId']; ?>" <?php if($row['Buyer'] == 1){ echo 'checked="checked"'; } ?> /></td>
                </tr>
            </table>
          </div>
<?php } ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" align="right"><?php if(isset($_GET['Edit'])){ ?>
            <input name="update" type="submit" class="btn" id="update" value="Update" />
            <?php } else { ?>
            <input name="insert" type="submit" class="btn" id="insert" value="Insert" />
            <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <?php if($numrows >= 1){ ?>
        <tr>
          <td colspan="2"><div id="list-border">
            <table width="100%" border="0" cellpadding="4" cellspacing="1">
              <tr class="td-header">
                <td>Company</td>
                <td>Telephone</td>
                <td>mobile</td>
                <td>&nbsp;</td>
                <td width="20">&nbsp;</td>
                </tr>
              <tr>
                <td colspan="3"><input name="filter" type="text" class="tarea-search" id="filter" onfocus="if(this.value=='Search...'){this.value=''}" onblur="if(this.value==''){this.value='Search...'}" value="Search..." /></td>
                <td><input name="search" type="submit" class="search" id="search" value="" /></td>
                <td width="20">&nbsp;</td>
                </tr>
              <?php while($row_list = mysqli_fetch_array($query_list)){ ?>
              <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
                <td width="200"><?php echo $row_list['CompanyName']; ?></td>
                <td width="200"><?php echo $row_list['Telephone']; ?></td>
                <td><?php echo $row_list['Mobile']; ?></td>
                <?php 
		  
		  if(isset($_GET['Page'])){
			  
			  $var = '&';
			  
		  } else {
			  
			  $var = '?';
		  }
		  ?>
                <td width="20"><a href="index.php?Delete=<?php echo $row_list['Id']; ?>" class="delete"></a></td>
                <td width="20"><a href="index.php?Edit=<?php echo $row_list['Id']; ?>" class="edit"></a></td>
                </tr>
              <?php } ?>
              </table>
          </div>
              <table border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td colspan="3" align="center"><div id="pager">
                  <?php pager($pages); ?>
                </div></td>
              </tr>
            </table>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="2"><p>&nbsp;</p></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer">Proton Chemicals | Developed By <a href="http://www.kwd.co.za" class="footer-link">KWD</a></div>
</body>
</html>