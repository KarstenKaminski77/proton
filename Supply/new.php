<?php
// Connect To The Database
require_once('../functions/db-connect.php');

require_once('../functions/functions.php');

restrict();

logout($con);

$userid = $_COOKIE['userid'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$mysql_query = "
SELECT
	proton_db.tbl_companies.CompanyName,
	proton_db.tbl_product_company_relation.CompanyId,
	proton_db.tbl_companies.BuyerName,
	proton_db.tbl_companies.BuyerEmail
FROM
	proton_db.tbl_product_company_relation
INNER JOIN proton_db.tbl_companies ON proton_db.tbl_product_company_relation.CompanyId = proton_db.tbl_companies.Id
WHERE
	proton_db.tbl_product_company_relation.Buyer = '1'
GROUP BY
	proton_db.tbl_product_company_relation.CompanyId";

$query_buyer = mysqli_query($con, $mysql_query)or die(mysqli_error($con));

$sql = "
	SELECT
		tbl_products.`Name`,
		tbl_industries.Industry,
		tbl_product_industry_relation.ProductId,
		tbl_product_industry_relation.IndustryId
	FROM
		tbl_product_industry_relation
	INNER JOIN tbl_industries ON tbl_product_industry_relation.IndustryId = tbl_industries.Id
	INNER JOIN tbl_products ON tbl_product_industry_relation.ProductId = tbl_products.Id
	ORDER BY
		tbl_products.`Name` ASC";

$query_products = mysqli_query($con, $sql)or die(mysqli_error($con));
$numrows_products = mysqli_num_rows($query_products);

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

<script>
function ShowHide(chk,txt)
    {
        if(document.getElementById(chk).checked)
            document.getElementById(txt).style.visibility='';
        else
            document.getElementById(txt).style.visibility='hidden';
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
    <form id="form1" name="form1" method="post" action="qued.php">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Supply Products</td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="../Admin/Industries/index.php" class="breadcumbs">Supply New Product</a></td>
                  <td><img src="../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
          </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="757" class="td-right">

		  <?php
		  $i = 0;
		  $c = 0;
		  $a = 0;
		  while($row_products = mysqli_fetch_array($query_products)){
		  $i++;
		  $c++;
		  $a++;

		  $curvalue = NULL;

		  $curValue = $row_products['Industry'];

		  if ($i % 3 == 0) {

			  $id = 'industry-list-source-odd';

		  } else {

			  $id = 'industry-list-source';
		  }

		  if($i == $numrows_products){

			  $style = 'style="border:none; margin-bottom:20px"';
		  }

			  if($curValue != $prevValue){

				  $i = 1;

				  $id = 'industry-list-source';

					echo '<input name="buyer" type="hidden" id="buyer" value="4" />';
			}

			?>

            <!-- Products -->
            <div id="<?php echo $id; ?>" <?php echo $style; ?>>
            <label for="check_<?php echo $c; ?>">
              <table border="0" cellpadding="2" cellspacing="3">
                <tr>
                  <td width="140"><?php echo $row_products['Name']; ?>
                  <input name="id[]" type="hidden" id="id[]" value="<?php echo $row_products['ProductId']; ?>" /></td>
                  <td width="40">
                  <input name="qty[]" type="text" class="tarea-qty-source" id="qty_<?php echo $c; ?>" onfocus="if(this.value=='0'){this.value=''}" onblur="if(this.value=='kg'){this.value='0'}" value="0" style=" visibility:hidden; display:none" /></td>
                  <td width="15">
                  <input type="radio" name="product" id="check_<?php echo $c; ?>" value="<?php echo $row_products['ProductId']; ?>" <?php if($rows == 1){ echo 'checked="checked"'; } ?> title="Select Product" onchange="ShowHide('check_<?php echo $c; ?>','qty_<?php echo $c; ?>')" /></td>
                </tr>
              </table>
            </label>
            </div>
            <!-- End Products -->

          <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right"><input name="next" type="submit" class="btn" id="next" value="Next" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
