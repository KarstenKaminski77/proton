<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_form = mysqli_query($con, "SELECT * FROM tbl_companies WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

if(isset($_POST['next']) && empty($_POST['buyer'])){

	if(empty($_POST['buyer'])){

		$buyer = '?Buyer';
	}
	header('Location: new.php'.$buyer);

	exit();

} else {

	if(isset($_POST['next'])){

		// Request RFQ From Industry
		//if(isset($_POST['industry'])){



			$industry = $_POST['industry'];
			$companyid = $_POST['buyer'];
			$date = date('Y-m-d');

			mysqli_query($con, "INSERT INTO tbl_rfq (CompanyId,Date,Status,Type) VALUES ('$companyid','$date','1','1')")or die(mysqli_error($con));

			$query_sourcing = mysqli_query($con, "SELECT * FROM tbl_rfq ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
			$row_sourcing = mysqli_fetch_array($query_sourcing);

			$sourceid = $row_sourcing['Id'];
			$ids = $_POST['product'];
			$string = '';

			for($j=0; $j<count($ids);$j++){

				if($j + 1 == count($ids)){

					$string .= $ids[$j];

				} else {

					$string .= $ids[$j] . ',';
				}
			}


			$sql = "
			SELECT
				pir.ProductId,
				cir.CompanyId,
			    cir.IndustryId
			FROM
				tbl_product_industry_relation pir
				join tbl_company_industry_relation cir on pir.IndustryId = cir.IndustryId
			WHERE pir.ProductId IN($string);
			";

			$query_industries = mysqli_query($con, $sql)or die(mysqli_error($con));
			while($row_industries = mysqli_fetch_array($query_industries)){

				$supplierid = $row_industries['CompanyId'];

				for($c=0;$c<count($_POST['qty']);$c++){

					if($_POST['qty'][$c] != '0'){

						$productid = $_POST['id'][$c];
						$qty = $_POST['qty'][$c];

						mysqli_query($con, "INSERT INTO tbl_rfq_items (SourceId,SupplierId,ProductId,Qty,RFQ,Date)
						VALUES ('$sourceid','$supplierid','$productid','$qty','1','$date')") or die(mysqli_error($con));
					}
				}
			}

			header('Location: rfq.php?Id='. $sourceid .'&Company='. $companyid);

		// Request RFQ Individualy
		// } else {
		//
		// 	$companyid = $_POST['buyer'];
		// 	$date = date('Y-m-d');
		//
		// 	mysqli_query($con, "INSERT INTO tbl_rfq (CompanyId,Date,Status,Type) VALUES ('$companyid','$date','1','1')")or die(mysqli_error($con));
		//
		// 	$query_sourcing = mysqli_query($con, "SELECT * FROM tbl_rfq ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
		// 	$row_sourcing = mysqli_fetch_array($query_sourcing);
		//
		// 	$sourceid = $row_sourcing['Id'];
		//
		// 	for($c=0;$c<count($_POST['qty']);$c++){
		//
		// 		if($_POST['qty'][$c] != '0'){
		//
		// 			$productid = $_POST['id'][$c];
		// 			$qty = $_POST['qty'][$c];
		//
		// 			//var_dump($productid .' - '. $qty); die('dead');
		//
		// 			$query_suppliers = mysqli_query($con, "SELECT * FROM tbl_product_company_relation WHERE ProductId = '$productid' AND Supplier = '1'
		// 			GROUP BY CompanyId")or die(mysqli_error($con));
		// 			while($row_suppliers = mysqli_fetch_array($query_suppliers)){
		//
		// 				$supplierid = $row_suppliers['CompanyId'];
		//
		// 				mysqli_query($con, "INSERT INTO tbl_rfq_items (SourceId,SupplierId,ProductId,Qty,RFQ,Date)
		// 				VALUES ('$sourceid','$supplierid','$productid','$qty','0','$date')") or die(mysqli_error($con));
		//
		// 			}
		// 		}
		// 	}
		//
		// 	header('Location: rfq.php?Id='. $sourceid .'&Company='. $companyid);
		// }
	}
}

if(isset($_POST['itemid'])){

	for($i=0;$i<count($_POST['itemid']);$i++){

		$id = $_POST['itemid'][$i];
		$productid = $_POST['productid'][$i];
		$unit = $_POST['unit'][$i];
		$packsize = $_POST['packsize'][$i];
		$currency = $_POST['currency'][$i];

		mysqli_query($con, "UPDATE tbl_rfq_items SET Unit = '$unit', Currency = '$currency' WHERE Id = '$id'")or die(mysqli_error($con));
		mysqli_query($con, "UPDATE tbl_products SET PackSize = '$packsize' WHERE Id = '$productid'")or die(mysqli_error($con));
	}
}

if(isset($_POST['buyer'])){

	$buyerid = $_POST['buyer'];

} else {

	$buyerid = $_GET['Company'];
}

if(isset($_GET['Id'])){

	$sourceid = $_GET['Id'];
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
	tbl_products.PackSize,
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

<link href="../css/form-elements.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
function OnSubmitForm(){

	if(document.pressed == 'Calculate'){

		document.myform.action ="rfq.php?Id=<?php echo $_GET['Id']; ?>&Company=<?php echo $_GET['Company']; ?>";
	} else

	if(document.pressed == 'Submit Request For Quotes'){

		document.myform.action ="../fpdf/pdf-rfq.php";
	}

	return true;
}
</script>

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
    <form id="myform" name="myform" method="post" onsubmit="return OnSubmitForm();" >
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Source Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">RFQ</a></td>
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
          <td colspan="6" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <?php while($row_suppliers = mysqli_fetch_array($query_suppliers)){ ?>
        <tr>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['CompanyName']; ?></td>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['SalesName']; ?></td>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['SalesEmail']; ?></td>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['Telephone']; ?></td>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['Mobile']; ?></td>
          <td class="td-header-blue-dark"><?php echo $row_suppliers['Date']; ?></td>
        </tr>
        <tr>
          <td colspan="2" class="td-header-blue">Product</td>
          <td class="td-header-blue">Qty</td>
          <td class="td-header-blue">Unit</td>
          <td class="td-header-blue">Pack Size</td>
          <td class="td-header-blue">Currency</td>
        </tr>
        <?php

		$supplierid = $row_suppliers['SupplierId'];
		$query_products = "
		SELECT
		  tbl_rfq_items.SourceId,
		  tbl_rfq_items.ProductId,
		  tbl_rfq_items.SupplierId,
		  tbl_rfq_items.Qty,
		  tbl_rfq_items.Id,
		  tbl_products.`Name`,
		  tbl_rfq_items.Currency,
		  tbl_rfq_items.Unit,
		  tbl_products.Grade,
		  tbl_products.PackSize
		FROM
		  tbl_rfq_items
		INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
		WHERE
		  tbl_rfq_items.SourceId = '$sourceid' AND
		  tbl_rfq_items.SupplierId = '$supplierid'";

		$selected = $_POST['supplier'];

		$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));
		while($row_products = mysqli_fetch_array($query_products)){
		?>
        <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
          <td colspan="2" class="td-grey"><?php echo $row_products['Name']; ?>
          <input type="hidden" name="itemid[]" id="itemid[]" value="<?php echo $row_products['Id']; ?>" />
          <input type="hidden" name="productid[]" id="productid[]" value="<?php echo $row_products['ProductId']; ?>" /></td>
          <td class="td-grey"><?php echo $row_products['Qty']; ?></td>
          <td class="td-grey"><input name="unit[]" type="text" class="tarea-100" id="unit[]" value="<?php echo $row_products['Unit']; ?>" /></td>
          <td class="td-grey"><input name="packsize[]" type="text" class="tarea-100" id="packsize[]" value="<?php echo $row_products['PackSize']; ?>" /></td>
          <td class="td-grey"><input name="currency[]" type="text" class="tarea-100" id="currency[]" value="<?php echo $row_products['Currency']; ?>" /></td>
        </tr>
        <?php } ?>
        <tr>
        <?php
		if(in_array($row_suppliers['SupplierId'], $selected)){

			$checked = 'checked="checked"';

		} else {

			$checked = '';
		}

		//if(isset($_POST['industry'])){

			$checked = 'checked="checked"';

	//	}
		?>
          <td colspan="6" align="right"><input name="supplier[]" type="checkbox" class="styled" id="supplier[]" value="<?php echo $row_suppliers['SupplierId']; ?>" <?php echo $checked; ?> /></td>
        </tr>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="6" align="right">
          <input type="hidden" name="supplierid" id="supplierid" value="<?php echo $row_rfq['SupplierId']; ?>" />
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" />
          <?php if(isset($_POST['calc'])){ ?>
          <input name="button" type="submit" class="btn-red" id="button" onclick="document.pressed=this.value" value="Submit Request For Quotes" />
          <?php } ?>
          <input name="calc" type="submit" class="btn" id="calc" onclick="document.pressed=this.value" value="Save" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
