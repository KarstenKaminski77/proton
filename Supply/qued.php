<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(isset($_POST['next'])){
	
	$companyid = $_POST['buyer'];
	$date = date('Y-m-d');
				
	mysqli_query($con, "INSERT INTO tbl_rfq (CompanyId,Date,Status,Type,Offer) VALUES ('$companyid','$date','1','2','1')")or die(mysqli_error($con));
				
	$query_rfq = mysqli_query($con, "SELECT * FROM tbl_rfq ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row_rfq = mysqli_fetch_array($query_rfq);
				
	$sourceid = $row_rfq['Id'];
		
	$productid = $_POST['product'];
						
	$query_suppliers = mysqli_query($con, "SELECT * FROM tbl_product_company_relation WHERE ProductId = '$productid' AND Buyer = '1' GROUP BY CompanyId")or die(mysqli_error($con));
	while($row_suppliers = mysqli_fetch_array($query_suppliers)){
			
		$supplierid = $row_suppliers['CompanyId'];
			
		mysqli_query($con, "INSERT INTO tbl_rfq_items (SourceId,SupplierId,ProductId,Qty,RFQ,Date) 
		VALUES ('$sourceid','$supplierid','$productid','$qty','1','$date')") or die(mysqli_error($con));
	}
		
	$query = mysqli_query($con, "SELECT * FROM tbl_rfq_items WHERE SourceId = '$sourceid'")or die(mysqli_error($con));
	while($row = mysqli_fetch_array($query)){
			
		$id = $row['Id'];
			
		mysqli_query($con, "INSERT INTO tbl_offers (RFQId) VALUES ('$id')")or die(mysqli_error($con));
	}
}

// Suppliers Query
$query_qued = "
SELECT
	tbl_rfq.Id,
	tbl_rfq.Date,
	tbl_products.`Name`,
	tbl_rfq_items.Unit,
	tbl_products.PackSize,
	tbl_products.`Code`,
	tbl_products.Grade,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.ProductId
FROM
	tbl_rfq
INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
WHERE
	tbl_rfq.Type = '2'
AND tbl_rfq.`Status` = '1'
GROUP BY
	tbl_rfq_items.SourceId
ORDER BY
	tbl_rfq.Date DESC";
	
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
    <form id="form1" name="form1" method="post" action="../fpdf/pdf-rfq.php">
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
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">Quotation No <?php echo $_GET['Success']; ?> successfully sent.....</div>
      <?php } ?>
      <?php if(isset($_GET['ProformaSuccess'])){ ?>
      <div id="banner-success">Proforma Invoice Successfully Sent.....</div>
      <?php } ?>
      <?php if($numrows >= 1){ ?>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="60" class="td-header-blue-dark">No.</td>
          <td class="td-header-blue-dark">Product Name</td>
          <td width="172" class="td-header-blue-dark">Code</td>
          <td width="172" class="td-header-blue-dark">Pack Size</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php 
		
		while($row_qued = mysqli_fetch_array($query_qued)){ 
		
		$rfqid = $row_qued['Id'];
		
		$query = mysqli_query($con, "SELECT tbl_rfq.Id, tbl_offers.Closed 
		FROM tbl_rfq
        INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
        INNER JOIN tbl_offers ON tbl_rfq_items.Id = tbl_offers.RFQId
        WHERE tbl_rfq.Id = '$rfqid' AND tbl_offers.Closed = 0")or die(mysqli_error($con));
		$rows = mysqli_num_rows($query);
		
		if($rows >= 1){
		?>
        <tr>
          <td class="td-grey"><?php echo $row_qued['Id']; ?></td>
          <td class="td-grey"><?php echo $row_qued['Name']; ?></td>
          <td class="td-grey"><?php echo $row_qued['Code']; ?></td>
          <td class="td-grey"><?php echo $row_qued['PackSize']; ?></td>
          <td class="td-grey"><?php echo $row_qued['Date']; ?></td>
          <td align="center" class="td-grey" title="View">
            <a href="qued-details.php?Id=<?php echo $row_qued['Id']; ?>" class="search"></a>
          </td>
          </tr>
        <?php } ?>
        <?php } ?>
        <tr>
          <td colspan="7">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /></td>
        </tr>
      </table>
      <?php } else { ?>
      <div align="center" class="welcome">Currently no submitted quotes...</div>
<?php } ?>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>