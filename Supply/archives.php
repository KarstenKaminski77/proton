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

if(isset($_GET['Id'])){
	
	$sourceid = $_GET['Id'];
}

// Suppliers Query
$query_archives = "
SELECT
	tbl_rfq.Id,
	tbl_rfq.Date,
	tbl_products.`Name`,
	tbl_rfq.Offer,
	tbl_companies.CompanyName
FROM
tbl_rfq
INNER JOIN tbl_rfq_items ON tbl_rfq.Id = tbl_rfq_items.SourceId
INNER JOIN tbl_products ON tbl_rfq_items.ProductId = tbl_products.Id
INNER JOIN tbl_companies ON tbl_rfq_items.SupplierId = tbl_companies.Id
WHERE
	tbl_rfq.Type = 2
AND tbl_rfq.`Status` = 3 AND tbl_rfq.Offer = '1'
GROUP BY
	tbl_rfq_items.SourceId
ORDER BY
	tbl_rfq.Date DESC";
	
$query_archives = mysqli_query($con, $query_archives)or die(mysqli_error($con));

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
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td class="td-header-blue-dark">Buyer</td>
          <td width="226" class="td-header-blue-dark">Product</td>
          <td width="226" class="td-header-blue-dark">Date</td>
          <td width="22" class="td-header-blue-dark">&nbsp;</td>
          </tr>
        <?php while($row_archives = mysqli_fetch_array($query_archives)){ ?>
        <tr>
          <td class="td-grey"><?php echo $row_archives['CompanyName']; ?></td>
          <td class="td-grey"><?php echo $row_archives['Name']; ?></td>
          <td class="td-grey"><?php echo $row_archives['Date']; ?></td>
          <td class="td-grey"><a href="archive-details.php?Id=<?php echo $row_archives['Id']; ?>" class="search"></a></td>
        <?php } ?>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
<?php mysqli_close($con); ?>