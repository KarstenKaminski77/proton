<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

// Suppliers Query
$status = $_GET['Status'];

$query_rfq = "
SELECT
	tbl_rfq.Id,
	tbl_rfq.CompanyId,
	tbl_rfq.Date,
	tbl_rfq_items.Qty,
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_companies.Mobile,
	tbl_companies.BuyerName,
	tbl_companies.BuyerEmail,
	tbl_rfq.`Status`,
	tbl_rfq.Type,
	tbl_rfq_items.SupplierId,
	tbl_qs.Id AS QuoteNo
FROM
	tbl_rfq
INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
INNER JOIN tbl_qs ON tbl_rfq_items.SourceId = tbl_qs.RFQId
WHERE
	tbl_rfq.`Status` = 2
AND tbl_rfq.Type = 2
GROUP BY
	tbl_rfq_items.SupplierId
ORDER BY
	tbl_rfq.Date DESC";
	
$query_rfq = mysqli_query($con, $query_rfq)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_rfq);

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
      <?php 
	  if($_GET['Status'] == 1){
		  
		  $breadcrumb = 'RFQ';
		  
	  } else {
		  
		  $breadcrumb = 'Submitted Offers';
	  }
	  ?>	  
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Supply Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs"><?php echo $breadcrumb; ?></a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">Offer Successfully Sent.....</div>
      <?php } ?>
      <?php if($numrows >= 1){ ?>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="51" class="td-header-blue-dark">PR No.</td>
          <td width="152" class="td-header-blue-dark">Enquired By</td>
          <td width="177" class="td-header-blue-dark">Company Name</td>
          <td width="122" class="td-header-blue-dark">Email</td>
          <td width="101" class="td-header-blue-dark">Telephone</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="25" align="center" class="td-header-blue-dark">&nbsp;</td>
        </tr>
        <?php while($row_rfq = mysqli_fetch_array($query_rfq)){ ?>
        <tr>
          <td class="td-grey"><?php echo $row_rfq['Id']; ?></td>
          <td class="td-grey"><?php echo $row_rfq['BuyerName']; ?></td>
          <td class="td-grey"><?php echo $row_rfq['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_rfq['BuyerEmail']; ?></td>
          <td class="td-grey"><?php echo $row_rfq['Telephone']; ?></td>
          <td class="td-grey"><?php echo $row_rfq['Date']; ?></td>
          <td align="center" class="td-grey" title="View"><a href="qs-calc.php?Id=<?php echo $row_rfq['QuoteNo']; ?>" class="edit"></a></td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="7">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /></td>
        </tr>
      </table>
      <?php } else { ?>
      <?php if($_GET['Status'] == 1){ ?>
      <div align="center" class="welcome">Currently No RFQ's...</div>
      <?php } else { ?>
      <div align="center" class="welcome">Currently No Awaiting Quotations...</div>
      <?php } ?>
      <?php } ?>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>