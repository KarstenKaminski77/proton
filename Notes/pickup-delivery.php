<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(isset($_POST['PO'])){
	
	$po = $_GET['PO'];
	$quoteno = $_GET['Id'];
	
	mysqli_query($con, "UPDATE tbl_qs SET PO = '$po', Status = '3' WHERE Id = '$quoteno'")or die(mysqli_error($con));
}

if(isset($_GET['Reject'])){
	
	$quoteno = $_GET['Reject'];
	
	mysqli_query($con, "UPDATE tbl_qs SET Status = '4' WHERE Id = '$couteno'")or die(mysqli_error($con));
}

// Suppliers Query
$status = $_GET['Status'];

$query_po = "
SELECT
	tbl_po.Id,
	tbl_po.QuoteNo,
	tbl_companies.CompanyName,
	tbl_transport_companies. NAME,
	tbl_notes.DeliveryNote,
	tbl_notes.PickUpSlip,
	tbl_notes.Date
FROM
	tbl_po
INNER JOIN tbl_po_items ON tbl_po.Id = tbl_po_items.POId
INNER JOIN tbl_companies ON tbl_po_items.SupplierId = tbl_companies.Id
INNER JOIN tbl_notes ON tbl_po.Id = tbl_notes.POId
INNER JOIN tbl_transport_companies ON tbl_notes.TranporterId = tbl_transport_companies.Id
WHERE
	tbl_notes. STATUS = '1'
GROUP BY
	tbl_po.QuoteNo
ORDER BY
	tbl_notes.Date DESC";
	
$query_po = mysqli_query($con, $query_po)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_po);

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
                  <td>Source Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Pick Up Slips | Delivery Notes</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if(isset($_GET['Success'])){ ?>
      <div id="banner-success">Pick Up Slip & Delivery Note Successfully Sent.....</div>
      <?php } ?>
      <?php if($numrows >= 1){ ?>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="65" class="td-header-blue-dark">P0 No.</td>
          <td width="65" class="td-header-blue-dark">Quote No</td>
          <td width="252" class="td-header-blue-dark">Supplier</td>
          <td width="252" class="td-header-blue-dark">Transporter</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php while($row_po = mysqli_fetch_array($query_po)){ ?>
        <tr>
          <td class="td-grey"><?php echo $row_po['Id']; ?></td>
          <td class="td-grey"><?php echo $row_po['QuoteNo']; ?></td>
          <td class="td-grey"><?php echo $row_po['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_po['Name']; ?></td>
          <td class="td-grey"><?php echo $row_po['Date']; ?></td>
          <td width="22" align="center" class="td-grey" title="View">
            <a href="../fpdf/pdf-pick-up.php?Id=<?php echo $row_po['Id']; ?>&Preview" target="_blank" class="pdf" title="Pick Up Slip"></a>
          </td>
          <td width="22" align="center" class="td-grey" title="View">
            <a href="../fpdf/pdf-delivery.php?Id=<?php echo $row_po['Id']; ?>&Preview" class="pdf" title="Delivery Note" target="_blank"></a>
          </td>
          </tr>
        <?php } ?>
        <tr>
          <td colspan="7">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /></td>
        </tr>
      </table>
      <?php } else { ?>
      <div align="center" class="welcome">Currently no order notes...</div>
<?php } ?>
    </form>
  </div>
</div>
<div id="footer">Proton Chemicals | Developed By <a href="http://www.kwd.co.za" class="footer-link">KWD</a></div>
</body>
</html>