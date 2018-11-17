<?php
// Connect To The Database
require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

$id = $_GET['Edit'];

if(isset($_POST)){
		
	if(isset($_POST['insert'])){
		
		$name = $_POST['name'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];
		$email = $_POST['email'];
		$telephone = $_POST['telephone'];
		$fax = $_POST['fax'];
		$mobile = $_POST['mobile'];
		$vat = $_POST['vat'];
		
		mysqli_query($con, "INSERT INTO tbl_transport_companies (Name,ContactName,Address,Email,Telephone,Fax,Mobile) 
		VALUES ('$name','$contact','$address','$email','$telephone','$fax','$mobile')")or die(mysqli_error($con));
	}
		
	if(isset($_POST['update'])){
		
		$name = $_POST['name'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];
		$email = $_POST['email'];
		$telephone = $_POST['telephone'];
		$fax = $_POST['fax'];
		$mobile = $_POST['mobile'];
		$vat = $_POST['vat'];
		
		mysqli_query($con, "UPDATE tbl_transport_companies SET Name = '$name', ContactName = '$contact', Address = '$address', Email = '$email', Telephone = '$telephone', Fax = '$fax', Mobile = '$mobile'  WHERE Id = '$id'")or die(mysqli_error($con));
	}
}

if(isset($_GET['Delete'])){
	
	$id = $_GET['Delete'];
	
	mysqli_query($con, "DELETE FROM tbl_transport_companies WHERE Id = '$id'")or die(mysqli_error($con));
	
	header('Location: index.php');
}

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

$query_form = mysqli_query($con, "SELECT * FROM tbl_proton WHERE Id = '1'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

/////////////////////////////
/// PAGER //////////////////
///////////////////////////

$query_transport = mysqli_query($con, "SELECT * FROM tbl_transport_companies")or die(mysqli_error($con));
$total_items = mysqli_num_rows($query_transport);
$per_page = '5';

offset($total_items, $per_page);

$offset = $_SESSION['offset']; 
$pages = $_SESSION['pages'];

if($_POST['filter'] != 'Search...'){
	
	$filter = $_POST['filter'];
	
	$where = "WHERE tbl_transport_companies.Name LIKE '%$filter%' OR tbl_transport_companies.ContactName LIKE '%$filter%' OR tbl_transport_companies.Telephone LIKE '%$filter%' OR tbl_transport_companies.Email LIKE '%$filter%'";
}

$query_list = mysqli_query($con, "SELECT * FROM tbl_transport_companies $where ORDER BY Name ASC LIMIT 5 OFFSET $offset")or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_transport_companies WHERE Id = '$id'")or die(mysqli_error($con));
$row_form = mysqli_fetch_array($query_form);

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
    <form id="form1" name="form1" method="post" action="">
      <div id="breadcrumbs">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
          <tr>
            <td colspan="2" class="tab"><table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><a href="../../index.php" class="breadcumbs">Home</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td>Administration</td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                  <td><a href="index.php" class="breadcumbs">Transport Companies</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
        </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" class="td-left"><em><strong>Company Name</strong></em></td>
          <td width="757" class="td-right"><input name="name" type="text" class="tarea-100" id="name" value="<?php echo $row_form['Name']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Contact Name</em></td>
          <td class="td-right"><input name="contact" type="text" class="tarea-100" id="contact" value="<?php echo $row_form['ContactName']; ?>" /></td>
        </tr>
        <tr>
          <td valign="top" class="td-left"><em>Address</em></td>
          <td class="td-right"><textarea name="address" rows="4" class="tarea-100" id="address"><?php echo $row_form['Address']; ?></textarea></td>
        </tr>
        <tr>
          <td class="td-left"><em>Email</em></td>
          <td class="td-right"><input name="email" type="text" class="tarea-100" id="email" value="<?php echo $row_form['Email']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Telephone</em></td>
          <td class="td-right"><input name="telephone" type="text" class="tarea-100" id="telephone" value="<?php echo $row_form['Telephone']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Fax</em></td>
          <td class="td-right"><input name="fax" type="text" class="tarea-100" id="fax" value="<?php echo $row_form['Fax']; ?>" /></td>
        </tr>
        <tr>
          <td class="td-left"><em>Mobile</em></td>
          <td class="td-right"><input name="mobile" type="text" class="tarea-100" id="mobile" value="<?php echo $row_form['Mobile']; ?>" /></td>
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
                <td class="td-header">Company Name</td>
                <td width="120" class="td-header">Contact Person</td>
                <td width="120" class="td-header">Telephone</td>
                <td width="120" class="td-header">Email</td>
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
                <td><?php echo $row_list['ContactName']; ?></td>
                <td><?php echo $row_list['Telephone']; ?></td>
                <td><?php echo $row_list['Email']; ?></td>
                <?php 
		  
		  if(isset($_GET['Page']) || isset($_GET['Edit'])){
			  
			  $var = '&';
			  
		  } else {
			  
			  $var = '?';
		  }
		  ?>
                <td width="20"><a href="index.php?Delete=<?php echo $row_list['Id']; ?>" class="delete"></a></td>
                <td width="20"><a href="index.php?Edit=<?php echo $row_list['Id']; ?>" class="edit"></a></td>
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
            </table></td>
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