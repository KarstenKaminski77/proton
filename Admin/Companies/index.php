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
	$proton_account = $_POST['proton-account'];
	
	// Insert New Product
	if(isset($_POST['insert'])){
		
		mysqli_query($con, "INSERT INTO tbl_companies (CompanyName,PhysicalAddress,PostalAddress,DeliveryAddress,Telephone,Mobile,BuyerName,BuyerEmail,SalesName,SalesEmail,
		AccountsName,AccountsEmail,VAT,Supplier,Customer,Account)
		VALUES
		('$company','$physical_address','$postal_address','$delivery_address','$telephone','$mobile','$buyer_name','$buyer_email','$sales_name','$sales_email','$accounts_name','$accounts_email','$vat','$supplier','$customer','$proton_account')")or die(mysqli_error($co));
		
		// Get Company Id
		$query_company = mysqli_query($con, "SELECT * FROM tbl_companies ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
		$row_company = mysqli_fetch_array($query_company);
		
		$companyid = $row_company['Id'];
		
		for($i=0;$i<count($_POST['industry']);$i++){
			
			$industry = $_POST['industry'][$i];
			
			mysqli_query($con, "INSERT INTO tbl_company_industry_relation (CompanyId,IndustryId) VALUES ('$companyid','$industry')")or die(mysqli_error($con));
		}
		
		// Insert Company & Products To Relational Table
		$query_products = mysqli_query($con, "SELECT * FROM tbl_products ORDER BY Name ASC")or die(mysqli_error($con));
		while($row_products = mysqli_fetch_array($query_products)){
			
			$productid = $row_products['Id'];
			
			$query_industries = mysqli_query($con, "SELECT * FROM tbl_product_industry_relation WHERE ProductId = '$productid'")or die(mysqli_error($con));
			while($row_industries = mysqli_fetch_array($query_industries)){
				
				$industryid = $row_industries['IndustryId'];
				
				mysqli_query($con, "INSERT INTO tbl_product_company_relation (IndustryId,ProductId,CompanyId) VALUES ('$industryid','$productid','$companyid')")or die(mysqli_error($con));
				
			}
		}
		
		// Update Relational Table - Buyer
		for($i=0;$i<count($_POST['buyer']);$i++){
			
			$buyer = $_POST['buyer'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Buyer = '1' WHERE ProductId = '$buyer' AND CompanyId = '$companyid'")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Supplier
		for($i=0;$i<count($_POST['supplier']);$i++){
			
			$supplier = $_POST['supplier'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Supplier = '1' WHERE ProductId = '$supplier' AND CompanyId = '$companyid'")or die(mysqli_error($con));
		}
		
		header('Location: index.php');
		
	}
	
	// Update Product
	if(isset($_POST['update'])){
		
		$id = $_GET['Edit'];
		
		mysqli_query($con, "UPDATE tbl_companies SET CompanyName = '$company', PhysicalAddress = '$physical_address', PostalAddress = '$postal_address', DeliveryAddress = '$delivery_address', 
		Telephone = '$telephone', Mobile = '$mobile', BuyerName = '$buyer_name', BuyerEmail = '$buyer_email', SalesName = '$sales_name', SalesEmail = '$sales_email', AccountsName = '$accounts_name', 
		AccountsEmail = '$accounts_email', VAT = '$vat', Supplier = '$supplier', Customer = '$customer', Account = '$proton_account' WHERE Id = '$id'")or die(mysqli_error($con));
		
		mysqli_query($con, "UPDATE tbl_product_company_relation SET Supplier = '0', Buyer = '0' WHERE CompanyId = '$id'")or die(mysqli_error($con));
		
		// Industries
		mysqli_query($con, "DELETE FROM tbl_company_industry_relation WHERE CompanyId = '$id'")or die(mysqli_error($con));
		
		for($i=0;$i<count($_POST['industry']);$i++){
			
			$industry = $_POST['industry'][$i];
			
			mysqli_query($con, "INSERT INTO tbl_company_industry_relation (CompanyId,IndustryId) VALUES ('$id','$industry')")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Buyer
		for($i=0;$i<count($_POST['buyer']);$i++){
			
			$buyer = $_POST['buyer'][$i];
			
			mysqli_query($con, "UPDATE tbl_product_company_relation SET Buyer = '1' WHERE ProductId = '$buyer' AND CompanyId = '$id'")or die(mysqli_error($con));
		}
		
		// Update Relational Table - Supplier
		for($i=0;$i<count($_POST['supplier']);$i++){
			
			$supplier = $_POST['supplier'][$i]; 
			
			$query_check = mysqli_query($con, "SELECT * FROM tbl_product_company_relation WHERE ProductId = '$supplier' AND CompanyId = '$id'")or die(mysqli_error($con));
			$check_rows = mysqli_num_rows($query_check);
			
			if($check_rows == 0){
				
				mysqli_query($con, "INSERT INTO tbl_product_company_relation (Supplier,ProductId,CompanyId)
				values ('1','$supplier','$id')")or die(mysqli_error($con));
				
			} else {
				
				mysqli_query($con, "UPDATE tbl_product_company_relation SET Supplier = '1' WHERE ProductId = '$supplier' AND CompanyId = '$id'")or die(mysqli_error($con));
				
			}
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

$where = '';

if($_POST['filter'] != 'Search By Company...'){
	
	$filter = $_POST['filter'];
	
	$where .= "AND tbl_companies.CompanyName LIKE '%$filter%' ";
}

if(!empty($_POST['dd-list'])){
	
	$where .= "AND tbl_product_company_relation.ProductId = '". $_POST['dd-list'] ."' ";
	
	if(isset($_POST['buyer-list'])){
		
		$where .= "AND tbl_product_company_relation.Buyer = '1' ";
	}
	
	if(isset($_POST['supplier-list'])){
		
		$where .= "AND tbl_product_company_relation.Supplier = '1' ";
	}
}

$query_list = "
	SELECT
		tbl_companies.Id,
		tbl_companies.CompanyName,
		tbl_companies.PhysicalAddress,
		tbl_companies.PostalAddress,
		tbl_companies.DeliveryAddress,
		tbl_companies.Telephone,
		tbl_companies.Fax,
		tbl_companies.Mobile,
		tbl_companies.BuyerName,
		tbl_companies.BuyerEmail,
		tbl_companies.SalesName,
		tbl_companies.SalesEmail,
		tbl_companies.AccountsName,
		tbl_companies.AccountsEmail,
		tbl_companies.VAT,
		tbl_companies.Supplier,
		tbl_companies.Customer,
		tbl_companies.Account,
		tbl_product_company_relation.Buyer,
		tbl_product_company_relation.Supplier,
		tbl_product_company_relation.ProductId
	FROM
		tbl_companies
	INNER JOIN tbl_product_company_relation ON tbl_companies.Id = tbl_product_company_relation.CompanyId
	WHERE
		tbl_companies.Id != '4' $where
	GROUP BY
		tbl_companies.Id
		ORDER BY
		CompanyName ASC";
$query_list = mysqli_query($con, $query_list)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

$query_products = mysqli_query($con, "SELECT * FROM tbl_products ORDER BY Name ASC")or die(mysqli_error($con));
$numrows_products = mysqli_num_rows($query_products);

$query_dd = mysqli_query($con, "SELECT * FROM tbl_products ORDER BY Name ASC")or die(mysqli_error($con));

$query_industries = mysqli_query($con, "SELECT * FROM tbl_industries ORDER BY Industry ASC")or die(mysqli_error($con));
$numrows_industries = mysqli_num_rows($query_industries);

$search_p = $_POST['dd-list'];

$query_search_p = mysqli_query($con, "SELECT * FROM tbl_products WHERE Id = '$search_p'")or die(mysqli_error($con));
$row_search_p = mysqli_fetch_array($query_search_p);
$numrows_search = mysqli_num_rows($query_search_p);
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

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript">

  $(document).ready(function () {
	  $(".toggler").click(function (e) {
		  e.preventDefault();
		  $('.row' + $(this).attr('data-row')).toggle();
	  });
  });
  
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

<form action="import.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td colspan="2">
      </td>
    </tr>
    <?php if(isset($_GET['Success'])){ ?>
    <tr>
      <td colspan="2">
      
      <div id="banner-success">
         Data successfully imported.<br />
         <?php echo $_SESSION['insert']; ?> Records Inserted<br />
         <?php echo $_SESSION['update']; ?> Records Updated
      </div>
      
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="114" class="td-left"><em>Import Companies</em></td>
      <td width="643" valign="middle" class="td-right"><input name="csv" type="file" class="tarea-100" id="csv" /></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input name="insert" type="submit" class="btn" id="insert" value="Import Spreadsheet" style="margin-top:10px" />
        </td>
    </tr>
    <tr>
      <td colspan="2" align="right">&nbsp;</td>
    </tr>
  </table>
</form>

<form action="import-industries.php" method="post" enctype="multipart/form-data" name="form2" id="form2">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr>
      <td colspan="2">
      </td>
    </tr>
    <?php if(isset($_GET['IndustrySuccess'])){ ?>
    <tr>
      <td colspan="2">
      
      <div id="banner-success">
         Data successfully imported.<br />
         <?php echo $_SESSION['insert']; ?> Records Inserted<br />
         <?php echo $_SESSION['update']; ?> Records Updated
      </div>
      
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td width="114" class="td-left"><em>Import Iindustries</em></td>
      <td width="643" valign="middle" class="td-right"><input name="csv" type="file" class="tarea-100" id="csv" /></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input name="insert" type="submit" class="btn" id="insert" value="Import Spreadsheet" style="margin-top:10px" />
        </td>
    </tr>
    <tr>
      <td colspan="2" align="right">&nbsp;</td>
    </tr>
  </table>
</form>

<form id="form3" name="form3" method="post" action="">
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
          <td class="td-left"><em>Proton Account</em></td>
          <td class="td-right">
            <label>
              <input type="radio" name="proton-account" value="1" id="proton-account_0" <?php if($row_form['Account'] == 1){ echo 'checked="checked"'; } ?> />
              Yes</label>
            <label>
              <input type="radio" name="proton-account" value="0" id="proton-account_1" <?php if($row_form['Account'] == 0){ echo 'checked="checked"'; } ?> />
              No</label>
          </td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em>Industries</em></td>
          <td class="td-right"><?php 
		  $i = 0;
		  while($row_industries = mysqli_fetch_array($query_industries)){ 
		  $i++;
		  
		  if ($i % 3 == 0) {
			  
			  $id = 'industry-list-odd';
			  
		  } else {
			  
			  $id = 'industry-list';
		  }
		  
		  if($i == $numrows_industries){
			  
			  $style = 'style="border:none"';
		  }
		  
		  if(isset($_GET['Edit'])){
			  
			  $companyid = $_GET['Edit'];
			  $industryid = $row_industries['Id'];
			  
			  $query = mysqli_query($con, "SELECT * FROM tbl_company_industry_relation WHERE CompanyId = '$companyid' AND IndustryId = '$industryid'")or die(mysqli_error($con));
			  $row = mysqli_fetch_array($query);
		  }
		  ?>
            <div id="<?php echo $id; ?>" <?php echo $style; ?>>
              <table border="0" cellpadding="2" cellspacing="3">
                <tr>
                  <td width="140" valign="top"><label class="label" for="industry_<?php echo $i; ?>"><?php echo $row_industries['Industry']; ?></label></td>
                  <td width="15" valign="top"><input type="checkbox" name="industry[]" id="industry_<?php echo $i; ?>" value="<?php echo $row_industries['Id']; ?>" <?php if($row['IndustryId'] == $row_industries['Id']){ echo 'checked="checked"'; } ?> /></td>
                </tr>
              </table>
            </div>
          <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" valign="top" class="td-left"><em><a href="#" class="toggler" data-row="Allocate" style="color:#818284; text-decoration:none">Products</a></em></td>
        </tr>
        <tr class="rowAllocate" style="display: none">
          <td colspan="2" valign="top" class="td-right">
            <?php 
		  $i = 1;
		  while($row_products = mysqli_fetch_array($query_products)){ 
		  $i++;
		  
		  if ($i % 2 == 0) {
			  
			  $id = 'product-list';
			  
		  } else {
			  
			  $id = 'product-list-odd';
		  }
		  
		  if($i == $numrows_products){
			  
			  $style = 'style="border:none"';
		  }
		  
		  if(isset($_GET['Edit'])){
			  
			  $companyid = $_GET['Edit'];
			  $productid = $row_products['Id'];
			  
			  $query = mysqli_query($con, "SELECT * FROM tbl_product_company_relation WHERE CompanyId = '$companyid' AND ProductId = '$productid'")or die(mysqli_error($con));
			  $row = mysqli_fetch_array($query);
		  }
		  ?>
            <div id="<?php echo $id; ?>" <?php echo $style; ?>>
              <table border="0" cellpadding="2" cellspacing="3">
                <tr>
                  <td width="170" valign="top"title="<?php echo $row_products['Name']; ?>"><?php echo word_limit($row_products['Name'], 25); ?></td>
                  <td width="5" valign="top">&nbsp;</td>
                  <td valign="top"><label for="check_1_<?php echo $i; ?>">Supplier</label></td>
                  <td width="15" valign="top"><input type="checkbox" name="supplier[]" id="check_1_<?php echo $i; ?>" value="<?php echo $row_products['Id']; ?>" <?php if($row['Supplier'] == 1){ echo 'checked="checked"'; } ?> /></td>
                  <td width="40" align="right" valign="top"><label for="industry[]">Buyer</label></td>
                  <td width="15" valign="top"><input type="checkbox" name="buyer[]" id="check_2_<?php echo $i; ?>" value="<?php echo $row_products['Id']; ?>" <?php if($row['Buyer'] == 1){ echo 'checked="checked"'; } ?> /></td>
                </tr>
              </table>
            </div>
  <?php } ?>          </td>
        </tr>
        <tr>
          <td colspan="2" align="right"><a name="btm" id="btm"></a>
            <?php if(isset($_GET['Edit'])){ ?>
            <input name="update" type="submit" class="btn" id="update" value="Update" />
            <?php } else { ?>
            <input name="insert" type="submit" class="btn" id="insert" value="Insert" />
            <?php } ?></td>
        </tr>
        <?php if($numrows_search > 0){ ?>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
        <?php
		$supplier = "";
		
		if($_POST['supplier'] == 1){
			
			$supplier = ", Supplier";
		}
		$buyer = "";
		
		if($_POST['buyer'] == 1){
			
			$buyer = ", Buyer";
		}
		?>
          <td colspan="2" class="welcome"><strong>Search:</strong> <em><?php echo $row_search_p['Name'] . $supplier . $buyer; ?></em></td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><div id="list-border">
            <table width="100%" border="0" cellpadding="4" cellspacing="1">
              <tr>
                <td class="td-header">Company</td>
                <td width="200" class="td-header">Telephone</td>
                <td width="200" class="td-header">mobile</td>
                <td width="20" class="td-header">&nbsp;</td>
                <td width="20" class="td-header">&nbsp;</td>
              </tr>
              <tr>
                <td><input name="filter" type="text" class="tarea-search" id="filter" onfocus="if(this.value=='Search...'){this.value=''}" onblur="if(this.value==''){this.value='Search By Company...'}" value="Search By Company..." /></td>
                <td>
                <select name="dd-list" class="tarea-search" id="dd-list">
                  <option value="">Search By Product</option>
                  <?php while($row_dd = mysqli_fetch_array($query_dd)){ ?>
                    <option value="<?php echo $row_dd['Id']; ?>"><?php echo $row_dd['Name']; ?></option>
                  <?php } ?>
                </select></td>
                <td>
                  <label>
                    <input type="checkbox" name="supplier-list" value="1" id="type_0" />
                    <span class="tarea-search">Supplier</span></label>
                  <label>
                    &nbsp; 
                    &nbsp; 
                    <input type="checkbox" name="buyer-list" value="1" id="type_1" />
                    <span class="tarea-search">                    Buyer</span></label>
                </td>
                <td><input name="search" type="submit" class="search" id="search" value="" /></td>
                <td width="20"><a href="reset.php" class="delete" title="Reset"></a></td>
              </tr>
              <?php while($row_list = mysqli_fetch_array($query_list)){ ?>
              <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
                <td><?php echo $row_list['CompanyName']; ?></td>
                <td><?php echo $row_list['Telephone']; ?></td>
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
        <tr>
          <td colspan="2"><p>&nbsp;</p></td>
        </tr>
    </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>