<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$quoteno = $_GET['Id'];

$query = "
SELECT
	tbl_qs.Id,
	tbl_qs.CompanyId,
	tbl_companies.Account
FROM
	tbl_companies
INNER JOIN tbl_qs ON tbl_qs.CompanyId = tbl_companies.Id
WHERE
	tbl_qs.Id = '$quoteno'";
	
$query = mysqli_query($con, $query)or die(mysqli_error($con));

if(isset($_POST['po-no'])){
	
	$po = $_POST['po-no'];
	$delivery_date = $_POST['delivery-date'];
	$terms_d = $_POST['terms-d'];
	$terms_p = $_POST['terms-p'];
	
	mysqli_query($con, "UPDATE tbl_qs SET PO = '$po', DeliveryDate = '$delivery_date', DeliveryTerms = '$terms_d', PaymentTerms = '$terms_p', Status = '3' WHERE Id = '$quoteno'")or die(mysqli_error($con));
	
	$query = "
	SELECT
	   tbl_qs.Id,
	   tbl_transport.TransporterId
	FROM
	   tbl_qs
	INNER JOIN tbl_transport ON tbl_qs.Id = tbl_transport.QuoteNo
	WHERE
	   tbl_qs.Id = '$quoteno'";
	
	$query = mysqli_query($con, $query)or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$transportid = $row['TransporterId'];
	
	mysqli_query($con, "INSERT INTO tbl_notes (QuoteNo,TranporterId) VALUES ('$quoteno','$transportid')")or die(mysqli_error($con));
	
	$query = mysqli_query($con, "SELECT * FROM tbl_notes ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
	$row = mysqli_fetch_array($query);
	
	$noteid = $row['Id'];
	
	header('Location: ../fpdf/pdf-pick-up-supply.php?Id='. $noteid .'&Type=2');
}

if(isset($_GET['Reject'])){
	
	$quoteno = $_GET['Reject'];
	
	mysqli_query($con, "UPDATE tbl_qs SET Status = '4' WHERE Id = '$qouteno'")or die(mysqli_error($con));
}

// Suppliers Query
$quoteno = $_GET['Id'];

$query_qued = "
  SELECT
	  tbl_qs.Id,
	  tbl_qs.RFQId,
	  tbl_qs.OldRFQId,
	  tbl_qs.CompanyId,
	  tbl_qs.Date,
	  tbl_qs.DeliveryTerms,
	  tbl_qs.DeliveryDate,
	  tbl_qs.ExpiryDate,
	  tbl_qs.PaymentTerms,
	  tbl_qs.SpecialConditions,
	  tbl_qs.PDF,
	  tbl_qs.`Status`,
	  tbl_qs.Type,
	  tbl_qs.PO,
	  tbl_qs.Currency,
	  tbl_companies.CompanyName
  FROM
	  tbl_qs
  INNER JOIN tbl_companies ON tbl_qs.CompanyId = tbl_companies.Id
  WHERE
	  tbl_qs.Id = '$quoteno'";
$query_qued = mysqli_query($con, $query_qued)or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_qued);

$quoteno = $_GET['Id'];

$query_qs = mysqli_query($con, "SELECT * FROM tbl_qs WHERE Id = '$quoteno'")or die(mysqli_error($con));
$row_qs = mysqli_fetch_array($query_qs);
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

<link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="jquery-ui-sliderAccess.js"></script>

<link href="date.css" rel="stylesheet" type="text/css" />
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
    <form id="form1" name="form1" method="post" action="">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Supply Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Submitted</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
      <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td colspan="3" class="td-header-blue-dark">PO Number</td>
        </tr>
        <tr>
          <td colspan="3" class="td-grey"><input name="po-no" type="text" class="tarea-100" id="po-no" value="<?php echo $row_qs['PO']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td width="253" class="td-header-blue-dark">Delivery Date</td>
          <td width="253" class="td-header-blue-dark">Delivery Terms</td>
          <td width="253" class="td-header-blue-dark">Payment Terms</td>
        </tr>
        <?php 
		$i = 0;
		while($row_qued = mysqli_fetch_array($query_qued)){ 
		$i++;
		?>
        <tr>
          <td colspan="3" class="td-header-blue"><?php echo $row_qued['CompanyName']; ?>
          <input name="companyid[]" type="hidden" id="companyid[]" value="<?php echo $row_qued['SupplierId']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-grey">
          <input name="delivery-date" type="text" class="tarea-100" id="delivery-date-<?php echo $i; ?>" value="<?php echo $row_qs['DeliveryDate']; ?>" /></td>
          <script type="text/javascript">
		    $('#delivery-date-<?php echo $i; ?>').datepicker({
			  dateFormat: "yy-mm-dd"
			});
          </script>
          <td class="td-grey">
            <input name="terms-d" type="text" class="tarea-100" id="terms-d" value="<?php echo $row_qs['DeliveryTerms']; ?>" />
          </td>
          <td class="td-grey">
            <input name="terms-p" type="text" class="tarea-100" id="terms-p" value="<?php echo $row_qs['PaymentTerms']; ?>" />
        </td></tr>
        <?php } ?>
        <tr>
          <td colspan="3" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" align="right"><input name="next" type="submit" class="btn" id="next" value="Next" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
<?php mysqli_close($con); ?>