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

// Buyers Query
$query_rfq = "
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
	
$query_rfq = mysqli_query($con, $query_rfq)or die(mysqli_error($con));

$query_qs = "
SELECT
	tbl_qs.Id,
	tbl_qs.Date,
	tbl_qs.DeliveryTerms,
	tbl_qs.DeliveryDate,
	tbl_qs.PDF,
	tbl_qs.RFQId,
	tbl_qs_items.Qty,
	tbl_qs_items.Unit,
	tbl_qs_items.Price,
	tbl_qs_items.Total,
	tbl_qs_items.Date,
	tbl_products.`Name`
FROM
	tbl_qs
INNER JOIN tbl_qs_items ON tbl_qs.Id = tbl_qs_items.QuoteNo
INNER JOIN tbl_products ON tbl_qs_items.ProductId = tbl_products.Id
WHERE
	tbl_qs.RFQId = '$sourceid'";
	
$query_qs = mysqli_query($con, $query_qs)or die(mysqli_error($con));

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
	tbl_proforma.Id,
	tbl_proforma.RFQId,
	tbl_proforma.Date,
	tbl_proforma.PDF,
	tbl_proforma_items.ProductId,
	tbl_proforma_items.Qty,
	tbl_proforma_items.Unit,
	tbl_proforma_items.Price,
	tbl_proforma_items.Total,
	tbl_products.`Name`
FROM
	tbl_proforma
INNER JOIN tbl_proforma_items ON tbl_proforma.Id = tbl_proforma_items.ProformaNo
INNER JOIN tbl_products ON tbl_proforma_items.ProductId = tbl_products.Id
WHERE
	tbl_proforma.RFQId = '$sourceid'";
	
$query_proforma = mysqli_query($con, $query_proforma);

$query_po = "
SELECT
	tbl_po.Date,
	tbl_po.Id,
	tbl_companies.CompanyName,
	tbl_po.CompanyId,
	tbl_po.RFQNo
FROM
	tbl_po
INNER JOIN tbl_po_items ON tbl_po.Id = tbl_po_items.POId
INNER JOIN tbl_companies ON tbl_po_items.SupplierId = tbl_companies.Id
WHERE
	tbl_po.RFQNo = '$sourceid'
GROUP BY
	tbl_po_items.SupplierId";
	
$query_po = mysqli_query($con, $query_po)or die(mysqli_error($con));

$quoteno = $row_company['Id'];

$query_notes = "
SELECT
	tbl_transport_companies.Id,
	tbl_transport.Price,
	tbl_notes.POId AS PoNo,
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
	tbl_inv.Id,
	tbl_inv.RFQId,
	tbl_inv.Date,
	tbl_inv.PDF,
	tbl_inv_items.ProductId,
	tbl_inv_items.Qty,
	tbl_inv_items.Unit,
	tbl_inv_items.Price,
	tbl_inv_items.Total,
	tbl_products.`Name`
FROM
	tbl_inv
INNER JOIN tbl_inv_items ON tbl_inv.Id = tbl_inv_items.InvoiceNo
AND tbl_inv.Id = tbl_inv_items.InvoiceNo
INNER JOIN tbl_products ON tbl_inv_items.ProductId = tbl_products.Id
WHERE
	tbl_inv.RFQId = '$sourceid'";
	
$query_inv = mysqli_query($con, $query_inv);

$query_products = "
SELECT
	tbl_rfq_items.SourceId,
	tbl_rfq_items.ProductId,
	tbl_rfq_items.SupplierId,
	tbl_rfq_items.Qty,
	tbl_rfq_items.PDF,
	tbl_products.`Name`,
	tbl_products.`Code`,
	tbl_products.Grade,
	tbl_products.PackSize,
	tbl_companies.CompanyName
FROM
	tbl_rfq_items
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
WHERE
  tbl_rfq_items.SourceId = '$sourceid'";
 
$query_products = mysqli_query($con, $query_products)or die(mysqli_error($con));

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
            <td class="td-header" colspan="8">Sales Offer</td>
          </tr>
          <tr>
            <td colspan="2" class="td-header-blue">Product</td>
            <td width="100" class="td-header-blue">Qty</td>
            <td width="100" class="td-header-blue">Code</td>
            <td width="100" class="td-header-blue">Grade</td>
            <td width="100" class="td-header-blue">Pack Size</td>
            <td width="27" class="td-header-blue">&nbsp;</td>
            <td width="27" class="td-header-blue">&nbsp;</td>
          </tr>
        <?php while($row_products = mysqli_fetch_array($query_products)){ ?>
          <tr>
            <td colspan="2" class="td-grey"><?php echo $row_products['CompanyName']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_products['Qty']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_products['Code']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_products['Grade']; ?></td>
            <td width="100" class="td-grey"><?php echo $row_products['PackSize']; ?></td>
            <td width="27" class="td-grey">
            <a href="../fpdf/pdf-rfq-preview.php?Id=<?php echo $row_products['SourceId']; ?>&Supplier=<?php echo $row_products['SupplierId']; ?>" class="pdf" target="_blank"></a>
            </td><td width="27" class="td-grey">
            <?php if($row_products['Qty'] >= 1){ ?>
            <a href="archive-customer.php?Id=<?php echo $row_products['SourceId']; ?>&Supplier=<?php echo $row_products['SupplierId']; ?>" class="search"></a>
            <?php } ?>
            </td>
          </tr>
          <?php } ?>
        </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
<?php mysqli_close($con); ?>