<?php
// Connect To The Database
session_start();

require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

$id = $_GET['Edit'];

if(isset($_POST)){

	$name = $_POST['name'];
	$grade = $_POST['grade'];
	$size = $_POST['size'];
	$code = $_POST['code'];

	// Insert
	if(isset($_POST['insert'])){

		// Data Sheet
		$target_path = "../../data/";

		$target_path = $target_path . basename($_FILES['data']['name']);

		$data = basename($_FILES['data']['name']);

		if(move_uploaded_file($_FILES['data']['tmp_name'], $target_path)){

			$data = ",'$data'";
			$col = ',DataSheet';

		}

		// Data Sheet
		$target_path = "../../cao/";

		$target_path = $target_path . basename($_FILES['cao']['name']);

		$cao = basename($_FILES['cao']['name']);

		if(move_uploaded_file($_FILES['cao']['tmp_name'], $target_path)){

			$cao = ",'$cao'";
			$col2 = ',CAO';

		}

		mysqli_query($con, "INSERT INTO tbl_products (Name,Grade,PackSize,Code $col $col2) VALUES ('$name','$grade','$size','$code' $data $cao)")or die(mysqli_error($co));

		// Get Product Id
		$query_products = mysqli_query($con, "SELECT * FROM tbl_products ORDER BY Id DESC LIMIT 1")or die(mysqli_error($con));
		$row_products = mysqli_fetch_array($query_products);

		$productid = $row_products['Id'];

		// Insert Into Relational Table
		for($i=0;$i<count($_POST['industry']);$i++){

			$industryid = $_POST['industry'][$i];

			mysqli_query($con, "INSERT INTO tbl_product_industry_relation (ProductId,IndustryId) VALUES ('$productid','$industryid')")or die(mysqli_error($con));
		}

		header('Location: index.php');
	}

	// Update
	if(isset($_POST['update'])){

		$id = $_GET['Edit'];

		// Data Sheet
		$target_path = "../../data/";

		$target_path = $target_path . basename($_FILES['data']['name']);

		$data_sheet = basename($_FILES['data']['name']);

		if(move_uploaded_file($_FILES['data']['tmp_name'], $target_path)){

			$data = ", DataSheet = '$data_sheet'";

		}

		// CAO
		$target_path = "../../cao/";

		$target_path = $target_path . basename($_FILES['cao']['name']);

		$cao = basename($_FILES['cao']['name']);

		if(move_uploaded_file($_FILES['cao']['tmp_name'], $target_path)){

			$cao = ", CAO = '$cao'";

		}

		mysqli_query($con, "UPDATE tbl_products SET Name = '$name', Grade = '$grade', PackSize = '$size', Code = '$code' $data $cao WHERE Id = '$id'")or die(mysqli_error($con));

		mysqli_query($con, "DELETE FROM tbl_product_industry_relation WHERE ProductId = '$id'")or die(mysqli_error($con));

		// Insert Into Relational Table
		for($i=0;$i<count($_POST['industry']);$i++){

			$industryid = $_POST['industry'][$i];

			mysqli_query($con, "INSERT INTO tbl_product_industry_relation (ProductId,IndustryId) VALUES ('$id','$industryid')")or die(mysqli_error($con));
		}
	}
}

if(isset($_GET['Delete'])){

	$id = $_GET['Delete'];

	mysqli_query($con, "DELETE FROM tbl_products WHERE Id = '$id'")or die(mysqli_error($con));

	mysqli_query($con, "DELETE FROM tbl_product_industry_relation WHERE ProductId = '$id'")or die(mysqli_error($con));

	header('Location: index.php');
}

$userid = $_COOKIE['userid'];

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

/////////////////////////////
/// PAGER //////////////////
///////////////////////////

$query_products = mysqli_query($con, "SELECT * FROM tbl_products")or die(mysqli_error($con));
$total_items = mysqli_num_rows($query_products);
$per_page = '500';

offset($total_items, $per_page);

$offset = $_SESSION['offset'];
$pages = $_SESSION['pages'];

if($_POST['filter'] != 'Search...'){

	$filter = $_POST['filter'];

	$where = "WHERE tbl_products.Name LIKE '%$filter%' OR tbl_products.Code LIKE '%$filter%'";
}

$query_list = mysqli_query($con, "SELECT * FROM tbl_products $where ORDER BY Name ASC LIMIT 500 OFFSET $offset")or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_products WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

$query_industries = mysqli_query($con, "SELECT * FROM tbl_industries ORDER BY Industry ASC")or die(mysqli_error($con));
$numrows_industries = mysqli_num_rows($query_industries);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proton Chem</title>
<link href="../../css/layout.css" rel="stylesheet" type="text/css" />
<link href="../../fonts/3543835926.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../sdmenu/blue/sdmenu.css" />
<script type="text/javascript" src="../../sdmenu/sdmenu.js"></script>
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

</head>

<body>
<div id="header">
  <div id="logo">
   <a class="close" href="<?php echo $_SERVER['../../REQUEST_URI'] .'?Logout'; ?>"></a>
   <div id="tab-user">Last Login: <span class="font-normal"><?php echo date('d-m-Y H:i', strtotime($row_user['LastLogin'])); ?></span></div>
   <div id="tab-user"><?php echo $row_user['User']; ?></div>
  </div>
</div>
<div id="container">
  <?php include('../../menu.php'); ?>
  <div id="right-container">

      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Administration</td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="index.php" class="breadcumbs">Products</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
          </table>
      </div>

      <form action="import.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2">
            </td>
          </tr>
          <?php if(isset($_GET['Success'])){ ?>
          <tr>
            <td colspan="2">

            <div id="banner-success">
               Data successfully imported.<br />
               <?php echo $_SESSION['insert']; ?> Records Inserted<br />
               <?php echo $_SESSION['update']; ?> Records Updated
            </div>

            </td>
          </tr>
          <?php } ?>
          <tr>
            <td width="114" class="td-left"><em>XL File</em></td>
            <td width="643" valign="middle" class="td-right"><input name="csv" type="file" class="tarea-100" id="csv" /></td>
          </tr>
          <tr>
            <td colspan="2" align="right"><input name="insert" type="submit" class="btn" id="insert" value="Import Spreadsheet" style="margin-top:10px" />
              </td>
          </tr>
          <tr>
            <td colspan="2" align="right">&nbsp;</td>
          </tr>
        </table>
      </form>

      <form id="form2" name="form2" method="post" action="" enctype="multipart/form-data">

        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" class="td-left"><em><strong>Product Name</strong></em></td>
          <td width="757" class="td-right"><input name="name" type="text" class="tarea-100" id="name" value="<?php default_value($_POST['name'], $row_form['Name']); ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Grade</em></td>
          <td width="757" class="td-right"><input name="grade" type="text" class="tarea-100" id="grade" value="<?php default_value($_POST['grade'], $row_form['Grade']); ?>" /></td>
          </tr>
        <tr>
          <td class="td-left"><em>Pack Size</em></td>
          <td width="757" class="td-right"><input name="size" type="text" class="tarea-100" id="size" value="<?php default_value($_POST['size'], $row_form['PackSize']); ?>" /></td>
          </tr>
        <tr>
          <td class="td-left"><em>Product Code</em></td>
          <td width="757" class="td-right"><input name="code" type="text" class="tarea-100" id="code" value="<?php default_value($_POST['code'], $row_form['Code']); ?>" /></td>
          </tr>
        <tr>
          <td class="td-left">Data Sheet</td>
          <td class="td-right"><input name="data" type="file" class="tarea-100" id="data" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>CAO</em></td>
          <td class="td-right"><input name="cao" type="file" class="tarea-100" id="cao" /></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em>Industries</em></td>
          <td class="td-right"><?php
		  $i = 0;
		  while($row_industries = mysqli_fetch_array($query_industries)){
		  $i++;

		  if ($i % 3 == 0) {

			  $id = 'industry-list-odd';

		  } else {

			  $id = 'industry-list';
		  }

		  if($i == $numrows_industries){

			  $style = 'style="border:none"';
		  }

		  if(isset($_GET['Edit'])){

			  $productid = $_GET['Edit'];
			  $industryid = $row_industries['Id'];

			  $query = mysqli_query($con, "SELECT * FROM tbl_product_industry_relation WHERE ProductId = '$productid' AND IndustryId = '$industryid'")or die(mysqli_error($con));
			  $rows = mysqli_num_rows($query);
		  }
		  ?>
            <div id="<?php echo $id; ?>" <?php echo $style; ?>>
              <table border="0" cellpadding="2" cellspacing="3">
                <tr>
                  <td width="140"><label for="check_<?php echo $i; ?>"><?php echo $row_industries['Industry']; ?></label></td>
                  <td width="15"><input type="checkbox" name="industry[]" id="check_<?php echo $i; ?>" value="<?php echo $row_industries['Id']; ?>" <?php if($rows == 1){ echo 'checked="checked"'; } ?> /></td>
                </tr>
              </table>
            </div>
            <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right"><?php if(isset($_GET['Edit'])){ ?>
            <input name="update" type="submit" class="btn" id="update" value="Update" />
            <?php } else { ?>
            <input name="insert" type="submit" class="btn" id="insert" value="Insert" />
            <?php } ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><div id="list-border">
            <table width="100%" border="0" cellpadding="4" cellspacing="1">
              <tr>
                <td class="td-header">Product Name</td>
                <td width="120" class="td-header">Grade</td>
                <td width="120" class="td-header">Pack Size</td>
                <td width="120" class="td-header">Code</td>
                <td class="td-header">&nbsp;</td>
                <td width="20" class="td-header">&nbsp;</td>
                </tr>
              <tr>
                <td colspan="4"><input name="filter" type="text" class="tarea-search" id="filter" onfocus="if(this.value=='Search...'){this.value=''}" onblur="if(this.value==''){this.value='Search...'}" value="Search..." /></td>
                <td><input name="search" type="submit" class="search" id="search" value="" /></td>
                <td width="20">&nbsp;</td>
                </tr>
              <?php while($row_list = mysqli_fetch_array($query_list)){ ?>
              <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
                <td><?php echo $row_list['Name']; ?></td>
                <td><?php echo $row_list['Grade']; ?></td>
                <td><?php echo $row_list['PackSize']; ?></td>
                <td><?php echo $row_list['Code']; ?></td>
                <?php

		  if(isset($_GET['Page']) || isset($_GET['Edit'])){

			  $var = '&';

		  } else {

			  $var = '?';
		  }
		  ?>
                <td width="20"><a href="<?php echo $_SERVER['REQUEST_URI'] . $var; ?>Delete=<?php echo $row_list['Id']; ?>" class="delete"></a></td>
                <td width="20"><a href="<?php echo $_SERVER['REQUEST_URI'] . $var; ?>Edit=<?php echo $row_list['Id']; ?>" class="edit"></a></td>
                </tr>
              <?php } ?>
              </table>
          </div>
              <table border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td colspan="3" align="center"><div id="pager">
                  <?php pager($pages); ?>
                </div></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="2"><p>&nbsp;</p></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<div id="footer"></div>
</body>
</html>
