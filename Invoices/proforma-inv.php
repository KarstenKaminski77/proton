<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

if(isset($_GET['Id'])){
	
	$id = $_GET['Id'];
	$quoteno = $_GET['QuoteNo'];
	
	mysqli_query($con, "UPDATE tbl_proforma SET Status = '2' WHERE Id = '$id'")or die(mysqli_error($con));
	
	header('Location: ../Supply/po-no.php?Id='. $quoteno);
}

// Suppliers Query
$query_proforma = "
SELECT
	tbl_companies.CompanyName,
	tbl_companies.Telephone,
	tbl_proforma.Id,
	tbl_proforma.QuoteNo,
	tbl_proforma.Date,
	tbl_proforma.QuoteNo,
	tbl_proforma.PDF,
	tbl_companies.AccountsName,
	tbl_companies.AccountsEmail
FROM
	tbl_companies
INNER JOIN tbl_proforma ON tbl_proforma.CompanyId = tbl_companies.Id
WHERE
	tbl_proforma.`Status` = '1' AND Type = '2'
ORDER BY
	tbl_proforma.Date DESC";
	
$query_proforma = mysqli_query($con, $query_proforma)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_proforma);

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
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Proforma Invoices</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <?php if($numrows >= 1){ ?>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="60" class="td-header-blue-dark">No.</td>
          <td width="60" class="td-header-blue-dark">Quote No.</td>
          <td class="td-header-blue-dark">Company Name</td>
          <td width="172" class="td-header-blue-dark">Email</td>
          <td width="82" class="td-header-blue-dark">Date</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          <td width="22" align="center" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php while($row_proforma = mysqli_fetch_array($query_proforma)){ ?>
        <tr>
          <td class="td-grey"><?php echo $row_proforma['Id']; ?></td>
          <td class="td-grey"><?php echo $row_proforma['QuoteNo']; ?></td>
          <td class="td-grey"><?php echo $row_proforma['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_proforma['AccountsEmail']; ?></td>
          <td class="td-grey"><?php echo $row_proforma['Date']; ?></td>
          <td align="center" class="td-grey" title="View">
            <a href="../fpdf/pdf-proforma.php?Id=<?php echo $row_proforma['Id']; ?>&Preview&Type=2" target="_blank" class="pdf"></a>          </td>
          <td align="center" class="td-grey" title="View">
            <a href="proforma-inv.php?Id=<?php echo $row_proforma['Id']; ?>&QuoteNo=<?php echo $row_proforma['QuoteNo']; ?>" class="approve" title="Paid"></a>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td colspan="9">
          <input name="sourceid" type="hidden" id="sourceid" value="<?php echo $sourceid; ?>" /></td>
        </tr>
      </table>
      <?php } else { ?>
      <div align="center" class="welcome">Currently no Proforma Invoices...</div>
<?php } ?>
    </form>
  </div>
</div>
<div id="footer">Proton Chemicals | Developed By <a href="http://www.kwd.co.za" class="footer-link">KWD</a></div>
</body>
</html>