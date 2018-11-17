<?php
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

$sourceid = $_GET['Id'];
$supplierid = $_GET['Supplier'];

$query_qs = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_qs.Id,
	tbl_qs.Date,
	tbl_qs.RFQId,
	tbl_qs_items.Qty,
	tbl_qs_items.Unit,
	tbl_qs_items.Price,
	tbl_qs_items.Total,
	tbl_qs_items.SupplierId,
	tbl_products.`Name`,
	tbl_companies.CompanyName
FROM
	tbl_rfq_items
INNER JOIN tbl_qs ON tbl_rfq_items.Id = tbl_qs.OldRFQId
INNER JOIN tbl_qs_items ON tbl_qs.Id = tbl_qs_items.QuoteNo
INNER JOIN tbl_products ON tbl_qs_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_qs_items.SupplierId = tbl_companies.Id
WHERE
	tbl_rfq_items.SourceId = '$sourceid' AND tbl_rfq_items.SupplierId = '$supplierid'";
	
$query_qs = mysqli_query($con, $query_qs)or die(mysqli_error($con));
$row_qs = mysqli_fetch_array($query_qs);

$query_company = "
SELECT
	tbl_qs.Id,
	tbl_qs.RFQId,
	tbl_companies.CompanyName,
	tbl_proforma.Id AS ProformaId,
	tbl_notes.Id AS NotesId,
	tbl_inv.Id AS InvoiceId
FROM
	tbl_qs
INNER JOIN tbl_companies ON tbl_qs.CompanyId = tbl_companies.Id
INNER JOIN tbl_proforma ON tbl_qs.RFQId = tbl_proforma.RFQId
INNER JOIN tbl_notes ON tbl_qs.Id = tbl_notes.QuoteNo
INNER JOIN tbl_inv ON tbl_qs.Id = tbl_inv.QuoteNo
WHERE
	tbl_qs.RFQId = '$sourceid'";
	
$query_company = mysqli_query($con, $query_company)or die(mysqli_error($con));
$row_company = mysqli_fetch_array($query_company);

$query_proforma = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_qs.Id,
	tbl_qs.Date,
	tbl_qs.RFQId,
	tbl_proforma.Id AS ProformaId,
	tbl_proforma.RFQId,
	tbl_proforma.Date,
	tbl_proforma_items.Qty,
	tbl_proforma_items.Unit,
	tbl_proforma_items.Price,
	tbl_proforma_items.Retail,
	tbl_proforma_items.Total,
	tbl_products.`Name`,
	tbl_companies.CompanyName
FROM
	tbl_rfq_items
INNER JOIN tbl_qs ON tbl_rfq_items.Id = tbl_qs.OldRFQId
INNER JOIN tbl_proforma ON tbl_qs.RFQId = tbl_proforma.RFQId
INNER JOIN tbl_proforma_items ON tbl_proforma.Id = tbl_proforma_items.ProformaNo
INNER JOIN tbl_products ON tbl_proforma_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_proforma.CompanyId = tbl_companies.Id
WHERE
	tbl_rfq_items.SourceId = '$sourceid'
AND tbl_rfq_items.SupplierId = '$supplierid'";
	
$query_proforma = mysqli_query($con, $query_proforma);

$quoteno = $row_qs['Id'];

$query_notes = "
SELECT
	tbl_transport_companies.Id,
	tbl_transport.Price,
	tbl_notes.Id AS PoNo,
	tbl_notes.DeliveryNote,
	tbl_notes.PickUpSlip,
	tbl_transport_companies.`Name`
FROM
	tbl_notes
INNER JOIN tbl_transport ON tbl_notes.QuoteNo = tbl_transport.QuoteNo
INNER JOIN tbl_transport_companies ON tbl_transport.TransporterId = tbl_transport_companies.Id
WHERE tbl_notes.QuoteNo = '$quoteno'";

$query_notes = mysqli_query($con, $query_notes)or die(mysqli_error($con));

$query_inv = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.SupplierId,
	tbl_inv.Id,
	tbl_qs.Date,
	tbl_qs.RFQId,
	tbl_inv.RFQId,
	tbl_inv.Date,
	tbl_inv_items.Qty,
	tbl_inv_items.Unit,
	tbl_inv_items.Price,
	tbl_inv_items.Retail,
	tbl_inv_items.Total,
	tbl_products.`Name`,
	tbl_companies.CompanyName
FROM
	tbl_rfq_items
INNER JOIN tbl_qs ON tbl_rfq_items.Id = tbl_qs.OldRFQId
INNER JOIN tbl_inv ON tbl_qs.RFQId = tbl_inv.RFQId
INNER JOIN tbl_inv_items ON tbl_inv.Id = tbl_inv_items.InvoiceNo
INNER JOIN tbl_products ON tbl_inv_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_inv.CompanyId = tbl_companies.Id
WHERE
	tbl_rfq_items.SourceId = '$sourceid'
AND tbl_rfq_items.SupplierId = '$supplierid'";
	
$query_inv = mysqli_query($con, $query_inv);

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
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Sales Archives</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
        <table width="755" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="6">&nbsp;</td>
          </tr>
          <tr>
            <td class="td-header" colspan="6">Quotation</td>
          </tr>
          <tr>
            <td colspan="6" class="td-header-blue-dark"><?php echo $row_qs['CompanyName']; ?></td>
          </tr>
          <tr>
            <td class="td-header-blue">Product</td>
            <td width="70" class="td-header-blue">Qty</td>
            <td width="70" class="td-header-blue">Unit</td>
            <td width="100" class="td-header-blue">Price</td>
            <td width="100" class="td-header-blue">Total</td>
            <td width="22" class="td-header-blue">&nbsp;</td>
          </tr>
          <tr>
            <td class="td-grey"><?php echo $row_qs['Name']; ?></td>
            <td class="td-grey"><?php echo $row_qs['Qty']; ?></td>
            <td class="td-grey"><?php echo $row_qs['Unit']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_qs['Price']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_qs['Total']; ?></td>
            <td width="22" class="td-grey">
            <a href="../fpdf/pdf-qs.php?Id=<?php echo $row_qs['Id']; ?>&Preview&Type=2" target="_blank" class="pdf"></a>
            </td>
          </tr>
          <tr>
            <td colspan="5" class="td-grey"><?php echo $row_qs['Notes']; ?></td>
            <td class="td-grey">&nbsp;</td>
          </tr>
      </table>
<table width="755" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="7">&nbsp;</td>
          </tr>
          <tr>
            <td width="760" class="td-header" colspan="7">Proforma Invoice</td>
          </tr>
          <tr>
            <td colspan="7" class="td-header-blue-dark"><?php echo $row_qs['CompanyName']; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="td-header-blue">Product</td>
            <td width="70" class="td-header-blue">Qty</td>
            <td width="70" class="td-header-blue">Unit</td>
            <td width="100" class="td-header-blue">Price</td>
            <td width="100" class="td-header-blue">Total</td>
            <td width="22" class="td-header-blue">&nbsp;</td>
          </tr>
          <?php 
		  
		  $i = 0;
		  $rows = mysqli_num_rows($query_proforma);
		  
		  while($row_proforma = mysqli_fetch_array($query_proforma)){ 
		  
		  $i++;
		  ?>
          <tr>
            <td colspan="2" class="td-grey"><?php echo $row_proforma['Name']; ?></td>
            <td class="td-grey"><?php echo $row_proforma['Qty']; ?></td>
            <td class="td-grey"><?php echo $row_proforma['Unit']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_proforma['Price']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_proforma['Total']; ?></td>
            <?php if($i == 1){ ?>
            <td width="22" valign="top" class="td-grey" rowspan="<?php echo $rows; ?>">
            <a href="../fpdf/pdf-proforma.php?Id=<?php echo $row_proforma['ProformaId']; ?>&Preview&Type=2" target="_blank" class="pdf"></a>
            </td>
          <?php } ?>
          <?php } ?>
          </tr>
      </table>
<table width="755" border="0" align="center" cellpadding="0" cellspacing="1">
      <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td class="td-header" colspan="3">Pick Up Slip | Delivery Note</td>
          </tr>
          <tr>
            <td width="711" class="td-header-blue">Transport Company</td>
            <td width="22" align="center" class="td-header-blue">P</td>
            <td width="22" align="center" class="td-header-blue">D</td>
          </tr>
          <?php while($row_notes = mysqli_fetch_array($query_notes)){ ?>
          <tr>
            <td class="td-grey"><?php echo $row_notes['Name']; ?></td>
            <td class="td-grey"><a href="../fpdf/pdf-pick-up-supply.php?Id=<?php echo $row_notes['PoNo']; ?>&amp;Preview" target="_blank" class="pdf"></a></td>
            <td class="td-grey"><a href="../fpdf/pdf-delivery-supply.php?Id=<?php echo $row_notes['PoNo']; ?>&Preview" target="_blank" class="pdf"></a></td>
          </tr>
          <?php } ?>
          </table>
<table width="755" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="7">&nbsp;</td>
          </tr>
          <tr>
            <td class="td-header" colspan="7">Tax Invoice</td>
          </tr>
          <tr>
            <td colspan="7" class="td-header-blue-dark"><?php echo $row_qs['CompanyName']; ?></td>
          </tr>
          <tr>
            <td colspan="2" class="td-header-blue">Product</td>
            <td width="70" class="td-header-blue">Qty</td>
            <td width="70" class="td-header-blue">Unit</td>
            <td width="100" class="td-header-blue">Price</td>
            <td width="100" class="td-header-blue">Total</td>
            <td width="22" class="td-header-blue">&nbsp;</td>
          </tr>
          <?php 
		  
		  $i = 0;
		  
		  while($row_inv = mysqli_fetch_array($query_inv)){ 
		  
		  $rows = mysqli_num_rows($query_inv);
		  $i++;
		  
		  ?>
          <tr>
            <td colspan="2" class="td-grey"><?php echo $row_inv['Name']; ?></td>
            <td class="td-grey"><?php echo $row_inv['Qty']; ?></td>
            <td class="td-grey"><?php echo $row_inv['Unit']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_inv['Price']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_inv['Total']; ?></td>
            <?php if($i == 1){ ?>
            <td width="22" valign="top" class="td-grey" rowspan="<?php echo $rows; ?>">
            <a href="../fpdf/pdf-inv-supply.php?Id=<?php echo $row_inv['Id']; ?>&Preview" target="_blank" class="pdf"></a>
            </td>
            <?php } ?>
          <?php } ?>
          </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
<?php mysqli_close($con); ?>