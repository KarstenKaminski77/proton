<?php
// Connect To The Database
require_once('../../functions/db-connect.php');

require_once('../../functions/functions.php');

restrict();

logout($con);

$id = $_GET['Edit'];

if(isset($_POST)){
	
	$industry = $_POST['industry'];
	
	if(isset($_POST['insert'])){
		
		mysqli_query($con, "INSERT INTO tbl_industries (Industry) VALUES ('$industry')")or die(mysqli_error($co));
	}
	
	if(isset($_POST['update'])){
		
		$id = $_GET['Edit'];
		
		mysqli_query($con, "UPDATE tbl_industries SET Industry = '$industry' WHERE Id = '$id'")or die(mysqli_error($con));
	}
}

if(isset($_GET['Delete'])){
	
	$id = $_GET['Delete'];
	
	mysqli_query($con, "DELETE FROM tbl_industries WHERE Id = '$id'")or die(mysqli_error($con));
}

$userid = $_COOKIE['userid']; 

$query_user = mysqli_query($con, "SELECT * FROM tbl_users WHERE Id = '$userid'")or die(mysqli_error($con));
$row_user = mysqli_fetch_array($query_user);

/////////////////////////////
/// PAGER //////////////////
///////////////////////////

$query_industries = mysqli_query($con, "SELECT * FROM tbl_industries")or die(mysqli_error($con));
$total_items = mysqli_num_rows($query_industries);
$per_page = '10';

offset($total_items, $per_page);

$offset = $_SESSION['offset']; 
$pages = $_SESSION['pages'];

if(!empty($_POST['filter'])){
	
	$filter = $_POST['filter'];
	
	$where = "WHERE tbl_industries.Industry LIKE '%$filter%'";
}

$query_list = mysqli_query($con, "SELECT * FROM tbl_industries $where ORDER BY Industry ASC LIMIT 10 OFFSET $offset")or die(mysqli_error($con));
$numrows = mysqli_num_rows($query_list);

$query_form = mysqli_query($con, "SELECT * FROM tbl_industries WHERE Id = '$id'")or die(mysqli_error($con));
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
                  <td><a href="index.php" class="breadcumbs">Industries</a></td>
                  <td><img src="../../images/icons/bread-crumb.png" width="45" height="35" /></td>
                </tr>
            </table></td>
          </tr>
          </table>
      </div>
        <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
          <td width="130" class="td-left"><em><strong>Industry Name</strong></em></td>
          <td width="757" class="td-right"><input name="industry" type="text" class="tarea-100" id="industry" value="<?php default_value($_POST['industry'], $row_form['Industry']); ?>" /></td>
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
        <?php if($numrows >= 1){ ?>
        <tr>
          <td colspan="2"><div id="list-border">
            <table width="100%" border="0" cellpadding="4" cellspacing="1">
              <tr class="td-header">
                <td>Industry Name</td>
                <td>&nbsp;</td>
                <td width="20">&nbsp;</td>
                </tr>
              <tr>
                <td><input name="filter" type="text" class="tarea-search" id="filter" onfocus="if(this.value=='Search...'){this.value=''}" onblur="if(this.value==''){this.value='Search...'}" value="Search..." /></td>
                <td><input name="search" type="submit" class="search" id="search" value="" /></td>
                <td width="20">&nbsp;</td>
                </tr>
              <?php while($row_list = mysqli_fetch_array($query_list)){ ?>
              <tr class="<?php echo ($ac_sw1++%2==0)?"even":"odd"; ?>" onmouseover="this.oldClassName = this.className; this.className='over';" onmouseout="this.className = this.oldClassName;">
                <td><?php echo $row_list['Industry']; ?></td>
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
        <?php } ?>
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